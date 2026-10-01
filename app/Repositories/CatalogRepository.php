<?php
declare(strict_types=1);
namespace App\Repositories;
final class CatalogRepository {
 public function __construct(private Repository $repo) {}
 public static function numbers(string|array $s): array {return is_array($s)?$s:array_map('intval',array_filter(explode(',',trim($s,'{}')),fn($v)=>$v!==''));}
 public function products(): array {
  $rows=$this->repo->query('SELECT p.*, c.nome categoria FROM produtos p JOIN categorias c USING(id_categoria) WHERE p.ativo AND c.ativa ORDER BY p.id_produto')->fetchAll();
  $flavors=[];$tags=[];$units=[];$matrix=[];
  foreach($this->repo->query('SELECT * FROM produto_sabores ORDER BY ordem')->fetchAll() as $s) $flavors[$s['id_produto']][]=$s['nome'];
  foreach($this->repo->query('SELECT pt.id_produto,t.nome FROM produto_tags pt JOIN tags t USING(id_tag)')->fetchAll() as $s) $tags[$s['id_produto']][]=$s['nome'];
  foreach($this->repo->query("SELECT pu.id_produto,u.slug,u.tipo_unidade FROM produto_unidade pu JOIN unidades u USING(id_unidade) WHERE pu.disponivel AND u.ativa")->fetchAll() as $s) {
   $units[$s['id_produto']][]=$s['slug'];if($s['tipo_unidade']==='MATRIZ')$matrix[$s['id_produto']]=true;
  }
  return array_map(static function($p) use($flavors,$tags,$units,$matrix) {
   $id=$p['id_produto'];$t=array_values(array_diff($tags[$id]??[],['atacado','varejo']));
   if($p['linha']!=='BALCAO')$t[]='atacado';if($p['linha']!=='ENCOMENDA')$t[]='varejo';
   return ['id'=>$id,'slug'=>$p['slug'],'nome'=>$p['nome'],'descricao'=>$p['descricao']??'','categoria'=>$p['categoria'],'imagem'=>$p['imagem'],'destaque'=>$p['destaque'],'linha'=>$p['linha'],
    'precos'=>[['valor'=>(float)$p['preco'],'por'=>$p['rotulo_preco'],'minPedido'=>$p['pedido_minimo'],'passo'=>$p['passo_quantidade']]],
    'tags'=>$t,'sabores'=>$flavors[$id]??[],'unidades'=>$units[$id]??[],'disponivel'=>$matrix[$id]??false];
  },$rows);
 }
 public function units(): array {
  return array_map(static fn($u)=>[
   'id'=>$u['id_unidade'],'slug'=>$u['slug'],'nome'=>$u['nome'],'endereco'=>$u['endereco']??'', 'telefone'=>$u['telefone']??'',
   'whatsapp'=>$u['whatsapp']??'','horario'=>$u['horario_funcionamento']??'','preparo'=>$u['tempo_preparo']??'',
   'sobre'=>$u['descricao']??'','mapaUrl'=>$u['mapa_url']??'','imagem'=>$u['imagem'],'avaliacao'=>$u['avaliacao_url']??'',
   'catalogoPdf'=>$u['pdf_url']?'/catalogos/'.$u['slug'].'/download':'','matriz'=>$u['tipo_unidade']==='MATRIZ','canais'=>json_decode($u['canais'],true)??[]
  ],$this->repo->query('SELECT * FROM unidades WHERE ativa ORDER BY id_unidade')->fetchAll());
 }
 public function promotions(bool $public=true): array {
  $rows=$this->repo->query('SELECT * FROM promocoes ORDER BY id_promocao')->fetchAll();
  foreach($rows as &$p) {
   $p['dias_semana']=self::numbers($p['dias_semana']);$p['meses']=self::numbers($p['meses']);
   // Only effective combinations: promotion × product × unit intersect availability and attendance.
   $pairs=$this->repo->query("SELECT DISTINCT pr.id_produto,u.slug,u.nome FROM promocao_produto pp JOIN produtos pr USING(id_produto) JOIN categorias c USING(id_categoria) JOIN produto_unidade pu USING(id_produto) JOIN unidades u USING(id_unidade) JOIN promocao_unidade pm ON pm.id_unidade=u.id_unidade AND pm.id_promocao=pp.id_promocao WHERE pp.id_promocao=? AND pr.ativo AND c.ativa AND u.ativa AND pu.disponivel AND (?='AMBOS' OR pr.linha='AMBOS' OR pr.linha=?)",[$p['id_promocao'],$p['tipo_atendimento'],$p['tipo_atendimento']])->fetchAll();
   $p['pares']=$pairs;$p['id']=$p['id_promocao'];$p['titulo']=$p['nome'];$p['texto']=$p['descricao']??'';$p['ativo']=$p['ativa'];
   $p['quando']=['tipo'=>strtolower($p['tipo_agenda']),'dias'=>$p['dias_semana'],'meses'=>$p['meses'],'de'=>$p['data_inicio'],'ate'=>$p['data_fim']];
  }unset($p);
  return $public?array_values(array_filter($rows,fn($p)=>$p['ativa'] && $p['pares'])):$rows;
 }
 public function pdf(string $slug): ?string {
  return $this->repo->query('SELECT pdf_url FROM unidades WHERE slug=? AND ativa',[$slug])->fetchColumn()?:null;
 }
}
