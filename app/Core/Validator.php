<?php
declare(strict_types=1);
namespace App\Core;
use App\Models\Entity;
final class Validator {
 public static function validate(string $entity,array $input,?int $id): array {
  $data=[];$errors=[];
  if(isset($input['nome']) && empty($input['slug']) && $entity!=='usuarios') $input['slug']=self::slug((string)$input['nome']);
  foreach(Entity::config($entity)['fields'] as $key=>$field) {
   [$label,$type]=$field;
   $v=$input[$key]??'';
   if(is_array($v)) {$errors[$key]='Valor inválido.';continue;}
   $v=trim((string)$v);
   if($type==='bool') {$data[$key]=isset($input[$key]) && in_array($v,['1','on','true'],true);continue;}
   if(($field[3]??false) && $v==='') $errors[$key]='Campo obrigatório.';
   if(in_array($type,['text','textarea','url','phone','password'],true) && mb_strlen($v)>($field[2]??10000)) $errors[$key]='Texto maior que o limite de '.($field[2]??10000).' caracteres.';
   if($type==='select' && !in_array($v,$field[2],true)) $errors[$key]='Selecione uma opção válida.';
   if($type==='money' && !preg_match('/^\d{1,8}([.,]\d{1,2})?$/D',$v)) $errors[$key]='Informe um valor positivo com até duas casas decimais.';
   if($type==='money') $v=str_replace(',','.',$v);
   if(in_array($type,['integer','category'],true) && (!ctype_digit($v) || (int)$v<1 || (int)$v>2147483647)) $errors[$key]='Informe um inteiro positivo.';
   if($type==='url' && $v!=='' && (!filter_var($v,FILTER_VALIDATE_URL)||!in_array(parse_url($v,PHP_URL_SCHEME),['https','http'],true))) $errors[$key]='Use uma URL http ou https válida.';
   if($type==='phone' && $v!=='' && !preg_match('/^\d{10,15}$/D',$v)) $errors[$key]='Informe de 10 a 15 dígitos.';
   if($type==='date' && $v!=='') {
    $d=\DateTimeImmutable::createFromFormat('!Y-m-d',$v);
    if(!$d || $d->format('Y-m-d')!==$v) $errors[$key]='Data inválida.';
   }
   if(in_array($type,['days','months'],true)) {
    $values=$v===''?[]:array_map('trim',explode(',',$v));$lo=$type==='days'?0:1;$hi=$type==='days'?6:12;
    foreach($values as $n) if(!ctype_digit($n)||(int)$n<$lo||(int)$n>$hi) $errors[$key]='Valores fora do intervalo permitido.';
    $v=array_values(array_unique(array_map('intval',$values)));
   }
   $data[$key]=$type==='date' && $v===''?null:$v;
  }
  if(isset($data['slug']) && !preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/D',$data['slug'])) $errors['slug']='Use letras minúsculas sem acentos, números e hífens.';
  if($entity==='usuarios') {
   if(($id===null||$data['senha']!=='') && (strlen($data['senha'])<12||strlen($data['senha'])>72)) $errors['senha']='Use entre 12 e 72 bytes.';
   if(!preg_match('/^[a-zA-Z0-9_.@-]+$/D',$data['login'])) $errors['login']='Login inválido.';
   $data['login']=strtolower($data['login']);
  }
  if($entity==='promocoes') {
   if($data['tipo_desconto']==='PERCENTUAL' && (float)$data['valor_desconto']>100) $errors['valor_desconto']='Percentual máximo: 100%.';
   if($data['data_inicio'] && $data['data_fim'] && $data['data_fim']<$data['data_inicio']) $errors['data_fim']='A data final deve ser igual ou posterior à inicial.';
   $type=$data['tipo_agenda'];
   if($type==='SEMANAL' && !$data['dias_semana']) $errors['dias_semana']='Informe pelo menos um dia.';
   if($type==='MENSAL' && !$data['meses']) $errors['meses']='Informe pelo menos um mês.';
   if($type!=='SEMANAL' && $data['dias_semana']) $errors['dias_semana']='Preencha somente na agenda semanal.';
   if($type!=='MENSAL' && $data['meses']) $errors['meses']='Preencha somente na agenda mensal.';
   if($type==='PERIODO' && !$data['data_inicio'] && !$data['data_fim']) $errors['data_inicio']='Informe pelo menos um limite do período.';
   if($type==='SEMPRE' && ($data['data_inicio']||$data['data_fim'])) $errors['tipo_agenda']='Agenda sempre não aceita limites de datas.';
  }
  if($errors) throw new ValidationException($errors);
  return $data;
 }
 public static function slug(string $v): string {return trim(preg_replace('/[^a-z0-9]+/','-',dd_ascii($v)),'-');}
 public static function ids(mixed $value): array {
  if(!is_array($value)) throw new ValidationException(['relacoes'=>'Seleção inválida.']);
  foreach($value as $id) if(!is_scalar($id)||!ctype_digit((string)$id)||(int)$id<1) throw new ValidationException(['relacoes'=>'Seleção inválida.']);
  return array_values(array_unique(array_map('intval',$value)));
 }
}
