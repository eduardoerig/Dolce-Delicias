<?php declare(strict_types=1); use App\Core\Csrf; use App\Models\AdminUi;
$pageTitle=AdminUi::plural($entity); require __DIR__.'/header.php';
$status=$config['status'];$hasThumb=in_array($entity,['produtos','unidades'],true);
$query=fn(array $extra)=>'?'.http_build_query(array_filter(['q'=>$search,'filtro'=>$filter==='todos'?null:$filter]+$extra,fn($v)=>$v!==null&&$v!==''));
$here=rtrim('/admin/'.$entity.$query(['page'=>$page>1?$page:null]),'?');
/** Agenda da promoção em português ("Toda quarta-feira"), com o tipo como reserva. */
$agenda=fn(array $r)=>dd_agenda_em_texto(['tipo'=>strtolower($r['tipo_agenda']),'dias'=>AdminUi::numbers($r['dias_semana']),'meses'=>AdminUi::numbers($r['meses']),'de'=>(string)$r['data_inicio'],'ate'=>(string)$r['data_fim']])?:AdminUi::option('tipo_agenda',$r['tipo_agenda']);
/** Detalhes curtos da linha, cada um no seu espaço. */
$subtitle=function(array $r) use($entity,$agenda): array {
 return array_values(array_filter(match($entity) {
  'produtos'=>[$r['categoria'],dd_moeda((float)$r['preco']).' por '.$r['rotulo_preco']],
  'categorias'=>[(int)$r['produtos_ativos'].((int)$r['produtos_ativos']===1?' produto ativo':' produtos ativos')],
  'unidades'=>[AdminUi::option('tipo_unidade',$r['tipo_unidade']),$r['endereco']??''],
  'promocoes'=>[(float)$r['valor_desconto']>0?($r['tipo_desconto']==='PERCENTUAL'?str_replace('.',',',rtrim(rtrim($r['valor_desconto'],'0'),'.')).'% de desconto':dd_moeda((float)$r['valor_desconto']).' de desconto'):'Sem desconto definido',$agenda($r)],
  'usuarios'=>[$r['login'],AdminUi::option('perfil',$r['perfil'])],
  default=>[],
 }));
};
?>
<div class="adm-head">
 <div><h1><?= e(AdminUi::plural($entity)) ?></h1></div>
 <a class="adm-btn adm-btn-primary" href="/admin/<?= e($entity) ?>/<?= e($config['new']) ?>"><?= AdminUi::icon('plus') ?><?= e(AdminUi::newLabel($entity)) ?></a>
</div>

<div class="adm-toolbar">
 <form class="adm-search" method="get" role="search">
  <?php if($filter!=='todos'): ?><input type="hidden" name="filtro" value="<?= e($filter) ?>"><?php endif ?>
  <label class="sr-only" for="q">Buscar</label>
  <?= AdminUi::icon('search') ?>
  <input id="q" name="q" type="search" maxlength="120" value="<?= e($search) ?>" placeholder="Buscar <?= e(mb_strtolower(AdminUi::plural($entity))) ?>…" data-auto-submit>
 </form>
 <nav class="adm-tabs" aria-label="Filtrar">
  <?php foreach($filters as $key=>$label): ?>
   <a href="<?= e(rtrim('/admin/'.$entity.'?'.http_build_query(array_filter(['q'=>$search,'filtro'=>$key==='todos'?null:$key])),'?')) ?>" <?= $filter===$key?'aria-current="true"':'' ?>><?= e($label) ?> <span><?= (int)$counts[$key] ?></span></a>
  <?php endforeach ?>
 </nav>
</div>

<?php if(!$result['rows']): ?>
 <div class="adm-card adm-empty">
  <?php if($search!==''||$filter!=='todos'): ?>
   <p><strong>Nada encontrado.</strong></p><p>Tente outra busca ou <a href="/admin/<?= e($entity) ?>">veja todos</a>.</p>
  <?php else: ?>
   <p><strong>Ainda não há <?= e(mb_strtolower(AdminUi::plural($entity))) ?> cadastrados.</strong></p><p><a class="adm-btn adm-btn-primary" href="/admin/<?= e($entity) ?>/<?= e($config['new']) ?>"><?= AdminUi::icon('plus') ?><?= e(AdminUi::newLabel($entity)) ?></a></p>
  <?php endif ?>
 </div>
<?php else: ?>
 <ul class="adm-list">
  <?php foreach($result['rows'] as $row): $id=(int)$row[$config['id']];$on=(bool)$row[$status];$img=$hasThumb?dd_imagem($row['imagem']??null):null; ?>
   <li class="adm-row<?= $on?'':' is-off' ?>">
    <?php if($hasThumb): ?>
     <span class="adm-thumb"><?php if($img): ?><img src="<?= e($img) ?>" alt="" loading="lazy"><?php else: ?><?= AdminUi::icon('image') ?><?php endif ?></span>
    <?php endif ?>
    <span class="adm-row-text">
     <a class="adm-row-link" href="/admin/<?= e($entity) ?>/<?= $id ?>/editar"><?= e($row['nome']) ?></a>
     <small class="adm-row-meta"><?php foreach($subtitle($row) as $part): ?><span><?= e($part) ?></span><?php endforeach ?></small>
    </span>
    <span class="adm-row-badges">
     <?php if($entity==='produtos' && $on && !empty($row['destaque'])): ?><span class="adm-badge is-accent">Destaque</span><?php endif ?>
     <?php if($entity==='produtos' && $on && !empty($row['indisponivel'])): ?><span class="adm-badge is-warning">Sem unidade</span><?php endif ?>
     <?php if($entity==='promocoes'): ?><span class="adm-badge <?= !empty($siteStatus[$id])?'is-success':'' ?>"><?= !empty($siteStatus[$id])?'No site':($on?'Fora do site':'Rascunho') ?></span><?php endif ?>
    </span>
    <?php if($entity==='usuarios' && $id===(int)$user['id_usuario']): // A própria conta não se desativa pelo painel. ?>
     <span class="adm-badge adm-row-toggle">Você</span>
    <?php else: ?>
    <form class="adm-row-toggle" method="post" action="/admin/<?= e($entity) ?>/<?= $id ?>/status">
     <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
     <input type="hidden" name="voltar" value="<?= e($here) ?>">
     <button class="adm-switch" type="submit" role="switch" aria-checked="<?= $on?'true':'false' ?>" aria-label="<?= e(($on?'Desativar ':'Ativar ').$row['nome']) ?>" title="<?= $on?'Ativo — clique para desativar':'Inativo — clique para ativar' ?>"><span></span></button>
    </form>
    <?php endif ?>
    <span class="adm-row-chevron" aria-hidden="true"><?= AdminUi::icon('chevron') ?></span>
   </li>
  <?php endforeach ?>
 </ul>
 <?php if($result['pages']>1): ?>
  <nav class="adm-pager" aria-label="Páginas">
   <?php if($page>1): ?><a class="adm-btn adm-btn-sm" href="<?= e($query(['page'=>$page-1])) ?>">Anterior</a><?php endif ?>
   <span>Página <?= $page ?> de <?= (int)$result['pages'] ?></span>
   <?php if($page<$result['pages']): ?><a class="adm-btn adm-btn-sm" href="<?= e($query(['page'=>$page+1])) ?>">Próxima</a><?php endif ?>
  </nav>
 <?php endif ?>
<?php endif ?>
<?php require __DIR__.'/footer.php'; ?>
