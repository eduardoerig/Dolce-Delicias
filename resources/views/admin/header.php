<?php declare(strict_types=1); use App\Core\Csrf; use App\Models\AdminUi;
$current=$entity??'';
$nav=['produtos','promocoes','categorias','unidades'];
?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e(($pageTitle??'Painel').' — Dolce Delícias') ?></title>
<link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Figtree:wght@400;500;600;700&family=Baloo+2:wght@800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
<script type="module" src="/assets/js/admin.js"></script>
</head>
<body class="adm">
<a class="skip-link" href="#conteudo">Pular para o conteúdo</a>
<div class="adm-shell">
 <aside class="adm-side" id="menu-lateral">
  <a class="adm-brand" href="/admin" aria-label="Painel Dolce Delícias, início"><?= dd_logo('adm-logo') ?><span>Painel</span></a>
  <nav class="adm-nav" aria-label="Painel">
   <a href="/admin" <?= $current===''&&($pageTitle??'')==='Início'?'aria-current="page"':'' ?>><?= AdminUi::icon('home') ?>Início</a>
   <?php foreach($nav as $key): ?>
    <a href="/admin/<?= e($key) ?>" <?= $current===$key?'aria-current="page"':'' ?>><?= AdminUi::icon(AdminUi::ENTITIES[$key][2]) ?><?= e(AdminUi::plural($key)) ?></a>
   <?php endforeach ?>
  </nav>
  <div class="adm-side-foot">
   <a class="adm-nav-link" href="/" target="_blank" rel="noopener"><?= AdminUi::icon('external') ?>Ver o site</a>
   <?php if($user['perfil']==='ADMIN'): ?><a class="adm-nav-link" href="/admin/usuarios" <?= $current==='usuarios'?'aria-current="page"':'' ?>><?= AdminUi::icon('user') ?>Acessos</a><?php endif ?>
   <form method="post" action="/logout" class="adm-account">
    <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
    <span class="adm-avatar" aria-hidden="true"><?= e(mb_strtoupper(mb_substr($user['nome'],0,1))) ?></span>
    <span class="adm-account-name"><?= e($user['nome']) ?></span>
    <button class="adm-icon-btn" type="submit" title="Sair" aria-label="Sair"><?= AdminUi::icon('logout') ?></button>
   </form>
  </div>
 </aside>
 <div class="adm-body">
  <header class="adm-topbar">
   <a class="adm-brand" href="/admin" aria-label="Painel Dolce Delícias, início"><?= dd_logo('adm-logo') ?></a>
   <button class="adm-icon-btn" type="button" data-menu-toggle aria-controls="menu-lateral" aria-expanded="false" aria-label="Abrir menu"><?= AdminUi::icon('menu') ?></button>
  </header>
  <main id="conteudo" class="adm-main">
<?php if(isset($_SESSION['flash'])): ?><div class="adm-toast" role="status"><?= AdminUi::icon('check') ?><span><?= e($_SESSION['flash']) ?></span></div><?php unset($_SESSION['flash']);endif ?>
<?php if(isset($_SESSION['flash_error'])): [$flashText,$flashHref]=$_SESSION['flash_error']; ?><div class="adm-alert" role="alert"><?= AdminUi::icon('alert') ?><div><strong><?= e($flashText) ?></strong> <a href="<?= e($flashHref) ?>">Abrir cadastro</a></div></div><?php unset($_SESSION['flash_error']);endif ?>
