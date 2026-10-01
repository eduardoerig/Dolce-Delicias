<?php declare(strict_types=1); use App\Core\Csrf; ?>
<!doctype html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Entrar — Dolce Delícias</title>
<link rel="icon" href="/assets/img/logo.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Figtree:wght@400;500;600;700&family=Baloo+2:wght@800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="adm adm-login">
<main class="login-card">
 <a class="adm-login-logo" href="/" aria-label="Dolce Delícias, ir para o site"><?= dd_logo('adm-logo-lg') ?></a>
 <h1>Entrar no painel</h1>
 <?php if($error): ?><p class="adm-alert" role="alert"><?= e($error) ?></p><?php endif ?>
 <form method="post" action="/login">
  <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
  <div class="adm-field"><label for="login">Login</label><input id="login" name="login" autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="80" required autofocus></div>
  <div class="adm-field"><label for="senha">Senha</label><input id="senha" type="password" name="senha" autocomplete="current-password" maxlength="72" required></div>
  <button class="adm-btn adm-btn-primary adm-btn-block" type="submit">Entrar</button>
 </form>
 <a class="adm-back" href="/"><?= \App\Models\AdminUi::icon('back') ?>Voltar ao site</a>
</main>
</body>
</html>
