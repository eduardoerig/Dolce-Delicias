<?php
declare(strict_types=1);
namespace App\Models;
final class Promotion {
 public static function vigente(array $p, \DateTimeInterface $date): bool {
  if (!($p['ativa']??$p['ativo']??false)) return false;
  $day=$date->format('Y-m-d');
  if ((!empty($p['data_inicio']) && $day<$p['data_inicio']) || (!empty($p['data_fim']) && $day>$p['data_fim'])) return false;
  return match($p['tipo_agenda']) {
   'SEMANAL'=>in_array((int)$date->format('w'),$p['dias_semana'],true),
   'MENSAL'=>in_array((int)$date->format('n'),$p['meses'],true),
   'PERIODO','SEMPRE'=>true,
   default=>false
  };
 }
 public static function visivel(array $p, \DateTimeInterface $date): bool {
  if (!($p['ativa']??false)) return false;
  if ((!empty($p['data_inicio']) && $date->format('Y-m-d')<$p['data_inicio']) || (!empty($p['data_fim']) && $date->format('Y-m-d')>$p['data_fim'])) return false;
  return $p['tipo_agenda']==='SEMANAL' || self::vigente($p,$date);
 }
 /**
  * O que o painel precisa para dizer se a promoção aparece no site hoje e, se não, por quê.
  * Espera a linha de CatalogRepository::promotions(false), com 'pares' já calculados.
  */
 public static function checklist(array $p, \DateTimeInterface $date): array {
  $agenda=self::visivel(['ativa'=>true]+$p,$date);
  $items=[
   'ativa'=>[(bool)$p['ativa'],'Promoção ativa','Ligue "Ativa" para publicar.'],
   'pares'=>[!empty($p['pares']),'Produto disponível numa unidade marcada','Marque um produto ativo e uma unidade onde ele é vendido, com atendimento compatível.'],
   'agenda'=>[$agenda,'Dentro da agenda','Hoje está fora do período escolhido.'],
   'desconto'=>[(float)$p['valor_desconto']>0,'Desconto preenchido','Sem desconto, o card aparece sem valor.'],
  ];
  return ['items'=>$items,'no_site'=>$items['ativa'][0] && $items['pares'][0] && $agenda,'vigente'=>self::vigente($p,$date)];
 }
}
