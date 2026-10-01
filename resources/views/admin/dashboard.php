<?php declare(strict_types=1); use App\Models\AdminUi; $pageTitle='Início'; require __DIR__.'/header.php';
$firstName=explode(' ',trim($user['nome']))[0];
?>
<div class="adm-head">
 <div><h1>Olá, <?= e($firstName) ?></h1><p class="adm-lead">O que vamos fazer hoje?</p></div>
</div>

<section class="adm-actions" aria-label="Atalhos">
 <a class="adm-action" href="/admin/produtos/novo"><span class="adm-action-icon"><?= AdminUi::icon('plus') ?></span><span><strong>Novo produto</strong><small>Adicionar ao catálogo</small></span></a>
 <a class="adm-action" href="/admin/promocoes/nova"><span class="adm-action-icon"><?= AdminUi::icon('tag') ?></span><span><strong>Nova promoção</strong><small>Criar uma oferta</small></span></a>
 <a class="adm-action" href="/admin/produtos"><span class="adm-action-icon"><?= AdminUi::icon('box') ?></span><span><strong>Editar preços</strong><small>Ver todos os produtos</small></span></a>
</section>

<section class="adm-card" aria-labelledby="pendencias">
 <h2 id="pendencias">Precisa de atenção</h2>
 <?php if(!$attention): ?>
  <p class="adm-allgood"><?= AdminUi::icon('check') ?> Tudo certo por aqui.</p>
 <?php else: ?>
  <ul class="adm-todo">
   <?php foreach($attention as [$tone,$title,$text,$href,$cta]): ?>
    <li class="is-<?= e($tone) ?>">
     <span class="adm-todo-icon"><?= AdminUi::icon('alert') ?></span>
     <span class="adm-todo-text"><strong><?= e($title) ?></strong><small><?= e($text) ?></small></span>
     <a class="adm-btn adm-btn-sm" href="<?= e($href) ?>"><?= e($cta) ?></a>
    </li>
   <?php endforeach ?>
  </ul>
 <?php endif ?>
</section>

<section class="adm-stats" aria-label="Resumo">
 <a href="/admin/produtos?filtro=ativos"><strong><?= (int)$stats['produtos'] ?></strong><span>produtos no site</span></a>
 <a href="/admin/promocoes"><strong><?= (int)$stats['no_site'] ?></strong><span><?= (int)$stats['no_site']===1?'promoção no site':'promoções no site' ?></span></a>
 <a href="/admin/unidades"><strong><?= (int)$stats['unidades'] ?></strong><span>unidades ativas</span></a>
</section>
<?php require __DIR__.'/footer.php'; ?>
