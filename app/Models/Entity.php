<?php
declare(strict_types=1);
namespace App\Models;
use App\Core\HttpException;
final class Entity {
 public static function config(string $name): array {
  // Fixed identifiers: never interpolate request-provided table/column names.
  $all=[
   'produtos'=>['id'=>'id_produto','status'=>'ativo','new'=>'novo','fields'=>[
    'nome'=>['Nome','text',120,true], 'slug'=>['Slug','text',140,true], 'id_categoria'=>['Categoria','category',0,true],
    'descricao'=>['Descrição','textarea',10000], 'preco'=>['Preço da embalagem (R$)','money',0,true],
    'rotulo_preco'=>['Embalagem (ex.: 100 unidades)','text',60,true], 'pedido_minimo'=>['Pedido mínimo em peças','integer',0,true],
    'passo_quantidade'=>['Passo de quantidade','integer',0,true], 'linha'=>['Atendimento','select',['ENCOMENDA','BALCAO','AMBOS']],
    'destaque'=>['Destaque','bool'], 'ativo'=>['Ativo','bool']]],
   'categorias'=>['id'=>'id_categoria','status'=>'ativa','new'=>'nova','fields'=>['nome'=>['Nome','text',80,true],'slug'=>['Slug','text',100,true],'ativa'=>['Ativa','bool']]],
   'unidades'=>['id'=>'id_unidade','status'=>'ativa','new'=>'nova','fields'=>[
    'nome'=>['Nome','text',120,true],'slug'=>['Slug','text',140,true],'tipo_unidade'=>['Tipo','select',['MATRIZ','FILIAL']],
    'endereco'=>['Endereço','text',255], 'telefone'=>['Telefone','text',20], 'whatsapp'=>['WhatsApp com país e DDD (somente dígitos)','phone',20],
    'horario_funcionamento'=>['Horário de funcionamento','text',120], 'tempo_preparo'=>['Tempo de preparo','text',120],
    'descricao'=>['Descrição','textarea',10000], 'mapa_url'=>['Link do mapa','url',500], 'avaliacao_url'=>['Link de avaliação','url',500], 'ativa'=>['Ativa','bool']]],
   'promocoes'=>['id'=>'id_promocao','status'=>'ativa','new'=>'nova','fields'=>[
    'nome'=>['Nome','text',120,true], 'slug'=>['Slug','text',140,true], 'descricao'=>['Regra da promoção','textarea',10000], 'selo'=>['Selo','text',60],
    'tipo_desconto'=>['Tipo de desconto','select',['PERCENTUAL','VALOR_FIXO']], 'valor_desconto'=>['Valor do desconto','money',0,true],
    'tipo_atendimento'=>['Atendimento','select',['BALCAO','ENCOMENDA','AMBOS']], 'tipo_agenda'=>['Agenda','select',['SEMANAL','MENSAL','PERIODO','SEMPRE']],
    'dias_semana'=>['Dias da semana: 0=domingo a 6=sábado, separados por vírgula','days',0], 'meses'=>['Meses: 1 a 12, separados por vírgula','months',0],
    'data_inicio'=>['Data inicial','date',0], 'data_fim'=>['Data final','date',0], 'ativa'=>['Ativa','bool']]],
   'usuarios'=>['id'=>'id_usuario','status'=>'ativo','new'=>'novo','fields'=>[
    'nome'=>['Nome','text',120,true], 'login'=>['Login','text',80,true], 'senha'=>['Senha (mínimo 12 caracteres; em branco mantém a atual)','password',72],
    'perfil'=>['Perfil','select',['GESTOR','ADMIN']], 'ativo'=>['Ativo','bool']]]
  ];
  return $all[$name]??throw new HttpException(404,'Cadastro não encontrado.');
 }
}
