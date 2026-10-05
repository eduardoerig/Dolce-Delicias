<?php
declare(strict_types=1);
namespace App\Models;
/**
 * Apresentação do painel: nomes, seções dos formulários, rótulos amigáveis e ícones.
 * A validação continua em Entity::config(); aqui só muda como cada campo aparece.
 */
final class AdminUi {
 /** Nome singular, plural e ícone de cada cadastro. */
 public const ENTITIES=[
  'produtos'=>['produto','Produtos','box'],
  'promocoes'=>['promoção','Promoções','tag'],
  'categorias'=>['categoria','Categorias','folder'],
  'unidades'=>['unidade','Unidades','store'],
  'usuarios'=>['acesso','Acessos','user'],
 ];
 /** Valores gravados no banco => texto que a pessoa lê. */
 public const OPTIONS=[
  'tipo_unidade'=>['MATRIZ'=>'Matriz','FILIAL'=>'Filial'],
  'tipo_desconto'=>['PERCENTUAL'=>'Porcentagem','VALOR_FIXO'=>'Reais'],
  'tipo_agenda'=>['SEMPRE'=>'Sempre','SEMANAL'=>'Dias da semana','MENSAL'=>'Meses do ano','PERIODO'=>'Entre datas'],
  'perfil'=>['ADMIN'=>'Administrador','GESTOR'=>'Gestor (sem acesso a Acessos)'],
 ];
 /** Campos de texto longo que ocupam a linha inteira do formulário. */
 public const WIDE=['endereco','horario_funcionamento','tempo_preparo','mapa_url','avaliacao_url'];
 /** Etiquetas calculadas pelo sistema a partir de "Como é vendido"; não aparecem para edição. */
 public const SYSTEM_TAGS=['atacado','varejo'];
 public const DAYS=[0=>'Dom',1=>'Seg',2=>'Ter',3=>'Qua',4=>'Qui',5=>'Sex',6=>'Sáb'];
 public const MONTHS=[1=>'Jan',2=>'Fev',3=>'Mar',4=>'Abr',5=>'Mai',6=>'Jun',7=>'Jul',8=>'Ago',9=>'Set',10=>'Out',11=>'Nov',12=>'Dez'];

 /** Rótulo e ajuda de cada campo; o que não estiver aqui usa o rótulo de Entity. */
 private const FIELDS=[
  'produtos'=>[
   'nome'=>['Nome do produto','Como aparece no site. Ex.: Coxinha de frango.'],
   'id_categoria'=>['Categoria',''],
   'descricao'=>['Descrição','Uma ou duas frases sobre o produto.'],
   'preco'=>['Preço','Valor da embalagem inteira.'],
   'rotulo_preco'=>['Esse preço vale para','Ex.: 100 unidades, 1 unidade, 45 peças.'],
   'pedido_minimo'=>['Pedido mínimo','Menor quantidade de peças que o cliente pode pedir.'],
   'passo_quantidade'=>['Vende de quanto em quanto','Ex.: 25 permite pedir 50, 75, 100…'],
   'destaque'=>['Destaque','Aparece primeiro no catálogo.'],
   'ativo'=>['Ativo','Desligado, o produto some do site.'],
   'slug'=>['Endereço da página','Gerado pelo nome. Só mude se souber o que está fazendo.'],
  ],
  'categorias'=>[
   'nome'=>['Nome da categoria','Ex.: Salgados, Doces, Bebidas.'],
   'ativa'=>['Ativa','Desligada, esconde todos os produtos dela.'],
   'slug'=>['Endereço','Gerado pelo nome.'],
  ],
  'unidades'=>[
   'nome'=>['Nome da unidade',''],
   'tipo_unidade'=>['Tipo','A matriz recebe os pedidos do site.'],
   'endereco'=>['Endereço',''],
   'telefone'=>['Telefone',''],
   'whatsapp'=>['WhatsApp','Com código do país e DDD. Ex.: 55 11 98765-4321.'],
   'horario_funcionamento'=>['Horário','Ex.: Seg a sáb, 6h às 20h.'],
   'tempo_preparo'=>['Prazo das encomendas','Ex.: Pedidos com 48h de antecedência.'],
   'descricao'=>['Descrição','Aparece na página de unidades.'],
   'mapa_url'=>['Link do Google Maps',''],
   'avaliacao_url'=>['Link para avaliações',''],
   'ativa'=>['Ativa','Desligada, some do site.'],
   'slug'=>['Endereço','Gerado pelo nome.'],
  ],
  'promocoes'=>[
   'nome'=>['Nome da promoção','Ex.: Quarta do Salgado.'],
   'selo'=>['Selo','Texto curto no card. Ex.: 20% OFF.'],
   'descricao'=>['Regras','Explique a condição para o cliente.'],
   'tipo_desconto'=>['Tipo de desconto',''],
   'valor_desconto'=>['Desconto',''],
   'tipo_agenda'=>['Quando vale',''],
   'dias_semana'=>['Dias da semana',''],
   'meses'=>['Meses',''],
   'data_inicio'=>['A partir de','Em branco, já vale.'],
   'data_fim'=>['Até','Em branco, não tem data para acabar.'],
   'ativa'=>['Ativa','Desligada, fica como rascunho.'],
   'slug'=>['Endereço','Gerado pelo nome.'],
  ],
  'usuarios'=>[
   'nome'=>['Nome',''],
   'login'=>['Login','Usado para entrar. Sem espaços.'],
   'senha'=>['Senha','Mínimo de 12 caracteres. Em branco mantém a atual.'],
   'perfil'=>['Perfil',''],
   'ativo'=>['Ativo','Desligado, não consegue entrar no painel.'],
  ],
 ];

 /**
  * Seções do formulário. Cada item é uma chave de Entity ou um bloco especial:
  * unidades, produtos (vínculos), sabores, tags, imagem, pdf_url, agenda.
  * 'status' fica no cartão lateral; 'avancado' vai dentro de um <details>.
  */
 private const SECTIONS=[
  'produtos'=>[
   ['Sobre o produto','',['nome','id_categoria','descricao']],
   ['Preço e quantidade','',['preco','rotulo_preco','pedido_minimo','passo_quantidade']],
   ['Foto','',['imagem']],
   ['Onde vende','Marque as unidades que têm este produto.',['unidades']],
   ['Sabores e etiquetas','Opcional.',['sabores','tags']],
  ],
  'categorias'=>[['Categoria','',['nome']]],
  'unidades'=>[
   ['Sobre a unidade','',['nome','tipo_unidade','descricao']],
   ['Contato','',['whatsapp','telefone','endereco']],
   ['Funcionamento','',['horario_funcionamento','tempo_preparo']],
   ['Foto e catálogo','',['imagem','pdf_url']],
   ['Links','Opcional.',['mapa_url','avaliacao_url']],
  ],
  'promocoes'=>[
   ['A promoção','',['nome','selo','descricao']],
   ['Desconto','',['tipo_desconto','valor_desconto']],
   ['Quando vale','',['agenda']],
   ['Produtos','Quais produtos entram na promoção.',['produtos']],
   ['Unidades','Onde a promoção vale.',['unidades']],
  ],
  'usuarios'=>[['Dados de acesso','',['nome','login','senha','perfil']]],
 ];
 private const STATUS=['produtos'=>['ativo','destaque'],'categorias'=>['ativa'],'unidades'=>['ativa'],'promocoes'=>['ativa'],'usuarios'=>['ativo']];

 public static function singular(string $entity): string {return self::ENTITIES[$entity][0];}
 public static function plural(string $entity): string {return self::ENTITIES[$entity][1];}
 public static function sections(string $entity): array {return self::SECTIONS[$entity];}
 public static function statusFields(string $entity): array {return self::STATUS[$entity];}
 public static function label(string $entity,string $key,string $fallback=''): string {return self::FIELDS[$entity][$key][0]??$fallback;}
 public static function hint(string $entity,string $key): string {return self::FIELDS[$entity][$key][1]??'';}
 public static function option(string $key,string $value): string {return self::OPTIONS[$key][$value]??$value;}
 /** "Nova promoção", "Novo produto". */
 public static function newLabel(string $entity): string {return (Entity::config($entity)['new']==='nova'?'Nova ':'Novo ').self::singular($entity);}
 /** WhatsApp vazio ou de exemplo (ex.: 55000000000). */
 public static function placeholderPhone(?string $phone): bool {return !preg_match('/^\d{10,15}$/D',(string)$phone) || preg_match('/^\d{0,3}0{8,}$/D',(string)$phone)===1;}
 /** Lista de inteiros a partir de "1,3", "{1,3}" ou ['1','3']. */
 public static function numbers(mixed $value): array {
  if(is_array($value))return array_map('intval',array_filter($value,'is_scalar'));
  return array_map('intval',array_filter(explode(',',trim((string)$value,'{}')),fn($v)=>trim($v)!==''));
 }

 public static function icon(string $name,string $class='icon'): string {
  $paths=[
   'home'=>'<path d="M3 10.5 12 3l9 7.5"/><path d="M5 9.5V21h14V9.5"/><path d="M10 21v-6h4v6"/>',
   'box'=>'<path d="M21 8 12 3 3 8v8l9 5 9-5z"/><path d="m3 8 9 5 9-5"/><path d="M12 13v8"/>',
   'tag'=>'<path d="M20.6 13.4 13.4 20.6a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"/><circle cx="7.5" cy="7.5" r="1.5"/>',
   'folder'=>'<path d="M3 6a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>',
   'store'=>'<path d="M4 9 5.5 4h13L20 9"/><path d="M4 9h16v2a3 3 0 0 1-5.3 2 3 3 0 0 1-5.4 0A3 3 0 0 1 4 11z"/><path d="M5 13v8h14v-8"/><path d="M10 21v-5h4v5"/>',
   'user'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
   'external'=>'<path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M19 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1h5"/>',
   'plus'=>'<path d="M12 5v14M5 12h14"/>',
   'search'=>'<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
   'back'=>'<path d="m15 18-6-6 6-6"/>',
   'check'=>'<path d="m5 12 5 5 9-10"/>',
   'alert'=>'<path d="M12 3 2 20h20z"/><path d="M12 10v4M12 17h.01"/>',
   'image'=>'<rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m21 16-5-5-9 9"/>',
   'file'=>'<path d="M14 3H6a1 1 0 0 0-1 1v16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V8z"/><path d="M14 3v5h5"/>',
   'menu'=>'<path d="M4 7h16M4 12h16M4 17h16"/>',
   'logout'=>'<path d="M15 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1h-4"/><path d="m10 16-4-4 4-4"/><path d="M6 12h10"/>',
   'chevron'=>'<path d="m9 6 6 6-6 6"/>',
   'x'=>'<path d="M6 6l12 12M18 6 6 18"/>',
  ];
  return '<svg class="'.htmlspecialchars($class,ENT_QUOTES).'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name]??'').'</svg>';
 }
}
