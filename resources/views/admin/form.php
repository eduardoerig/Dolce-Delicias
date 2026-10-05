<?php declare(strict_types=1); use App\Core\Csrf; use App\Models\AdminUi;
$pageTitle=$id?($record['nome']??AdminUi::plural($entity)):AdminUi::newLabel($entity); require __DIR__.'/header.php';
$fields=$config['fields'];
$defaults=['tipo_unidade'=>'FILIAL','tipo_desconto'=>'PERCENTUAL','tipo_agenda'=>'SEMPRE','perfil'=>'GESTOR','rotulo_preco'=>'1 unidade','pedido_minimo'=>'1','passo_quantidade'=>'1'];
$value=function(string $key) use($record,$fields,$id,$defaults) {
 $type=$fields[$key][1]??'text';
 if($type==='password') return '';
 if(array_key_exists($key,$record)) return $record[$key];
 if($type==='bool') return $id===null && $key!=='destaque';
 return $defaults[$key]??'';
};
$label=fn(string $key)=>AdminUi::label($entity,$key,$fields[$key][0]??ucfirst($key));
$required=fn(string $key)=>(($fields[$key][3]??false) && $key!=='slug') || ($key==='senha' && $id===null);
$error=fn(string $key)=>isset($errors[$key])?'<p class="adm-field-error" id="'.e($key).'-erro">'.e($errors[$key]).'</p>':'';
$describedBy=fn(string $key)=>trim((AdminUi::hint($entity,$key)!==''?$key.'-ajuda ':'').(isset($errors[$key])?$key.'-erro':''));
$head=function(string $key,bool $legend=false) use($label,$required,$fields): string {
 $optional=!$required($key) && !in_array($key,['senha','slug','data_inicio','data_fim'],true) && !in_array($fields[$key][1]??'',['select','category'],true);
 $text=e($label($key)).($optional?' <span class="adm-optional">opcional</span>':'');
 return $legend?'<legend>'.$text.'</legend>':'<label for="'.e($key).'">'.$text.'</label>';
};
$hint=fn(string $key)=>AdminUi::hint($entity,$key)!==''?'<p class="adm-hint" id="'.e($key).'-ajuda">'.e(AdminUi::hint($entity,$key)).'</p>':'';

/** Desenha um campo de Entity::config pelo tipo. */
$render=function(string $key,bool $quietLegend=false) use($entity,$fields,$value,$head,$hint,$error,$describedBy,$required,$choices): void {
 [, $type]=$fields[$key];$v=$value($key);$max=(int)($fields[$key][2]??0);$desc=$describedBy($key);
 $aria=$desc!==''?' aria-describedby="'.e($desc).'"':'';$inv=$desc!=='' && str_contains($desc,'-erro')?' aria-invalid="true"':'';
 // Dinheiro aparece no formato brasileiro (o Validator aceita vírgula).
 if($type==='money' && is_string($v) && preg_match('/^\d+\.\d{1,2}$/D',$v)) $v=str_replace('.',',',$v);
 if($type==='select' && count($fields[$key][2])<=4) {
  // Três ou mais opções ocupam a linha toda para não quebrar no meio do grupo.
  $legend=$head($key,true);if($quietLegend)$legend=str_replace('<legend>','<legend class="sr-only">',$legend);
  echo '<fieldset class="adm-field'.(count($fields[$key][2])>2?' is-wide':'').'">'.$legend.'<div class="adm-seg">';
  foreach($fields[$key][2] as $opt) echo '<label><input type="radio" name="'.e($key).'" value="'.e($opt).'"'.((string)$v===$opt?' checked':'').'><span>'.e(AdminUi::option($key,$opt)).'</span></label>';
  echo '</div>'.$hint($key).$error($key).'</fieldset>';return;
 }
 echo '<div class="adm-field'.($type==='textarea'||in_array($key,AdminUi::WIDE,true)?' is-wide':'').'">'.$head($key);
 if($type==='category') {
  echo '<select id="'.e($key).'" name="'.e($key).'" required'.$aria.$inv.'><option value="">Escolha…</option>';
  foreach($choices['categorias'] as $c) echo '<option value="'.(int)$c['id_categoria'].'"'.((string)$v===(string)$c['id_categoria']?' selected':'').'>'.e($c['nome'].(!$c['ativa']?' (oculta)':'')).'</option>';
  echo '</select>';
 } elseif($type==='select') {
  echo '<select id="'.e($key).'" name="'.e($key).'"'.$aria.$inv.'>';
  foreach($fields[$key][2] as $opt) echo '<option value="'.e($opt).'"'.((string)$v===$opt?' selected':'').'>'.e(AdminUi::option($key,$opt)).'</option>';
  echo '</select>';
 } elseif($type==='textarea') {
  echo '<textarea id="'.e($key).'" name="'.e($key).'" rows="3" maxlength="'.$max.'"'.$aria.$inv.'>'.e(is_string($v)?$v:'').'</textarea>';
 } else {
  $input=match($type){'password'=>'password','date'=>'date','url'=>'url','integer'=>'number','phone'=>'tel',default=>'text'};
  $attrs=' id="'.e($key).'" name="'.e($key).'" type="'.$input.'" value="'.e(is_scalar($v)?(string)$v:'').'"'.($required($key)?' required':'').$aria.$inv;
  if(in_array($type,['text','url','password'],true)) $attrs.=' maxlength="'.$max.'"';
  if($type==='phone') $attrs.=' inputmode="tel" autocomplete="off" placeholder="55 11 98765-4321"';
  if($type==='integer') $attrs.=' min="1" step="1" inputmode="numeric"';
  if($type==='money') $attrs.=' inputmode="decimal" placeholder="0,00"';
  if($type==='password') $attrs.=' autocomplete="new-password"';
  if($key==='login') $attrs.=' autocomplete="off" autocapitalize="none" spellcheck="false"';
  $prefix=$type==='money'?($key==='valor_desconto'?'':'R$'):'';
  $suffix=$key==='valor_desconto'?'<span class="adm-affix" data-discount-unit>'.(($value('tipo_desconto'))==='VALOR_FIXO'?'R$':'%').'</span>':(in_array($key,['pedido_minimo','passo_quantidade'],true)?'<span class="adm-affix">peças</span>':'');
  if($prefix||$suffix) echo '<div class="adm-input-group">'.($prefix?'<span class="adm-affix">'.$prefix.'</span>':'').'<input'.$attrs.'>'.$suffix.'</div>';
  else echo '<input'.$attrs.'>';
 }
 if($key==='whatsapp' && is_string($v) && $v!=='' && AdminUi::placeholderPhone($v)) echo '<p class="adm-field-warning">Número de exemplo. Troque pelo WhatsApp real da unidade.</p>';
 echo $hint($key).$error($key).'</div>';
};

/** Lista de marcar (unidades/produtos) com busca quando é longa. */
$relation=function(string $relation) use($record,$choices,$entity): void {
 $key=$relation==='unidades'?'id_unidade':'id_produto';
 $checked=array_map('intval',is_array($record[$relation]??null)?$record[$relation]:[]);
 $items=$choices[$relation];$long=count($items)>8;
 echo '<fieldset class="adm-field is-wide" data-checklist><legend class="sr-only">'.($relation==='unidades'?'Unidades':'Produtos').'</legend>';
 echo '<div class="adm-checklist-bar">';
 if($long) echo '<input type="search" class="adm-checklist-search" placeholder="Filtrar…" aria-label="Filtrar lista" data-checklist-filter>';
 echo '<button type="button" class="adm-link" data-checklist-all>Marcar todos</button><button type="button" class="adm-link" data-checklist-none>Limpar</button></div>';
 echo '<div class="adm-checklist">';
 foreach($items as $c) {
  $off=!($c['ativa']??$c['ativo']??true);
  echo '<label data-name="'.e(mb_strtolower(dd_ascii($c['nome']))).'"><input type="checkbox" name="'.e($relation).'[]" value="'.(int)$c[$key].'"'.(in_array((int)$c[$key],$checked,true)?' checked':'').'><span>'.e($c['nome']).($off?' <em>(inativo)</em>':'').'</span></label>';
 }
 echo '</div></fieldset>';
};

$chips=function(string $key,array $options) use($value,$label,$error): void {
 $selected=AdminUi::numbers($value($key));
 echo '<fieldset class="adm-field is-wide"><legend>'.e($label($key)).'</legend><div class="adm-chips">';
 foreach($options as $n=>$text) echo '<label><input type="checkbox" name="'.e($key).'[]" value="'.$n.'"'.(in_array($n,$selected,true)?' checked':'').'><span>'.e($text).'</span></label>';
 echo '</div>'.$error($key).'</fieldset>';
};

$special=function(string $key) use($entity,$record,$render,$relation,$chips,$value,$error,$id): void {
 switch($key) {
  case 'unidades': case 'produtos': $relation($key);return;
  case 'sabores':
   echo '<div class="adm-field"><label for="sabores">Sabores <span class="adm-optional">opcional</span></label><textarea id="sabores" name="sabores" rows="4" placeholder="Um por linha">'.e(is_string($record['sabores']??null)?$record['sabores']:'').'</textarea>'.$error('sabores').'</div>';return;
  case 'tags':
   // 'atacado'/'varejo' são calculadas pelo "Como é vendido"; mostrá-las aqui só confundiria.
   $tags=array_filter(array_map('trim',explode(',',is_string($record['tags']??null)?$record['tags']:'')),fn($t)=>$t!=='' && !in_array(mb_strtolower($t),AdminUi::SYSTEM_TAGS,true));
   echo '<div class="adm-field"><label for="tags">Etiquetas <span class="adm-optional">opcional</span></label><input id="tags" name="tags" value="'.e(implode(', ',$tags)).'" placeholder="vegano, sem lactose" aria-describedby="tags-ajuda"><p class="adm-hint" id="tags-ajuda">Separe por vírgula. Vegano, sem lactose, sem glúten e sem carne viram filtro no site.</p>'.$error('tags').'</div>';return;
  case 'imagem':
   $img=dd_imagem($record['imagem']??null);
   echo '<div class="adm-field is-wide"><label class="adm-upload" for="imagem"><span class="adm-upload-preview" data-preview>'.($img?'<img src="'.e($img).'" alt="Foto atual">':AdminUi::icon('image')).'</span><span class="adm-upload-text"><strong>'.($img?'Trocar foto':'Escolher foto').'</strong><small>JPG, PNG ou WebP, até 5 MB.</small></span></label><input class="sr-only" id="imagem" type="file" name="imagem" accept="image/jpeg,image/png,image/webp" data-preview-input>'.$error('imagem').'</div>';return;
  case 'pdf_url':
   $has=$id && !empty($record['pdf_url']);
   echo '<div class="adm-field is-wide"><label class="adm-upload" for="pdf_url"><span class="adm-upload-preview">'.AdminUi::icon('file').'</span><span class="adm-upload-text"><strong>'.($has?'Trocar catálogo PDF':'Enviar catálogo PDF').'</strong><small data-file-name>Até 10 MB.</small></span></label><input class="sr-only" id="pdf_url" type="file" name="pdf_url" accept="application/pdf" data-file-input>';
   if($has && !empty($record['ativa'])) echo '<p class="adm-hint"><a href="/catalogos/'.e($record['slug']).'/download">Baixar catálogo atual</a></p>';
   echo $error('pdf_url').'</div>';return;
  case 'agenda':
   // A seção já se chama "Quando vale": a legenda fica só para leitor de tela.
   $render('tipo_agenda',true);
   $t=$value('tipo_agenda');
   echo '<div class="adm-agenda" data-agenda="SEMANAL"'.($t==='SEMANAL'?'':' hidden').'>';$chips('dias_semana',AdminUi::DAYS);echo '</div>';
   echo '<div class="adm-agenda" data-agenda="MENSAL"'.($t==='MENSAL'?'':' hidden').'>';$chips('meses',AdminUi::MONTHS);echo '</div>';
   echo '<div class="adm-agenda adm-grid" data-agenda="PERIODO SEMANAL MENSAL"'.(in_array($t,['PERIODO','SEMANAL','MENSAL'],true)?'':' hidden').'>';$render('data_inicio');$render('data_fim');echo '</div>';
   return;
  default: $render($key);
 }
};
$general=array_diff_key($errors,$config['fields']+['sabores'=>1,'tags'=>1,'imagem'=>1,'pdf_url'=>1]);
?>
<a class="adm-back" href="/admin/<?= e($entity) ?>"><?= AdminUi::icon('back') ?><?= e(AdminUi::plural($entity)) ?></a>
<div class="adm-head">
 <h1><?= e($id?($record['nome']??'Editar'):AdminUi::newLabel($entity)) ?></h1>
 <?php if($id && $entity==='produtos' && !empty($record['slug']) && !empty($record['ativo'])): ?><a class="adm-btn adm-btn-sm" href="/produtos/<?= e($record['slug']) ?>" target="_blank" rel="noopener"><?= AdminUi::icon('external') ?>Ver no site</a><?php endif ?>
</div>

<?php if($errors): ?>
 <div class="adm-alert" role="alert"><?= AdminUi::icon('alert') ?><div><strong>Não deu para salvar. Confira os campos marcados.</strong><?php if($general): ?><ul><?php foreach($general as $message): ?><li><?= e($message) ?></li><?php endforeach ?></ul><?php endif ?></div></div>
<?php endif ?>

<form method="post" enctype="multipart/form-data" class="adm-form" data-form>
 <input type="hidden" name="_csrf" value="<?= e(Csrf::token()) ?>">
 <div class="adm-form-main">
  <?php foreach(AdminUi::sections($entity) as [$title,$intro,$keys]): ?>
   <section class="adm-card">
    <h2><?= e($title) ?></h2><?php if($intro): ?><p class="adm-card-intro"><?= e($intro) ?></p><?php endif ?>
    <div class="adm-grid">
     <?php foreach($keys as $key) $special($key); ?>
    </div>
   </section>
  <?php endforeach ?>
 </div>

 <aside class="adm-form-side">
  <section class="adm-card">
   <h2>Publicação</h2>
   <?php foreach(AdminUi::statusFields($entity) as $key): $hintText=AdminUi::hint($entity,$key); ?>
    <label class="adm-toggle"><span><strong><?= e($label($key)) ?></strong><?php if($hintText): ?><small><?= e($hintText) ?></small><?php endif ?></span><input id="<?= e($key) ?>" type="checkbox" name="<?= e($key) ?>" value="1" role="switch" <?= $value($key)?'checked':'' ?>></label>
   <?php endforeach ?>
  </section>

  <?php if($entity==='promocoes'): ?>
   <section class="adm-card">
    <h2>Aparece no site?</h2>
    <?php if(!$checklist): ?>
     <p class="adm-card-intro">Salve para conferir.</p>
    <?php else: ?>
     <p class="adm-site-status <?= $checklist['no_site']?'is-on':'' ?>"><?= $checklist['no_site']?'Sim, está no site'.($checklist['vigente']?' e valendo hoje.':' (divulgando; vale nos dias marcados).'):'Ainda não.' ?></p>
     <ul class="adm-checks">
      <?php foreach($checklist['items'] as [$ok,$text,$fix]): ?>
       <li class="<?= $ok?'is-ok':'' ?>"><?= AdminUi::icon($ok?'check':'x') ?><span><?= e($text) ?><?php if(!$ok): ?><small><?= e($fix) ?></small><?php endif ?></span></li>
      <?php endforeach ?>
     </ul>
    <?php endif ?>
   </section>
  <?php endif ?>

  <?php if(isset($fields['slug'])): ?>
   <details class="adm-card adm-advanced" <?= isset($errors['slug'])?'open':'' ?>>
    <summary>Avançado</summary>
    <div class="adm-grid"><?php $render('slug'); ?></div>
   </details>
  <?php endif ?>
 </aside>

 <div class="adm-savebar">
  <a class="adm-btn" href="/admin/<?= e($entity) ?>">Cancelar</a>
  <button class="adm-btn adm-btn-primary" type="submit">Salvar</button>
 </div>
</form>
<?php require __DIR__.'/footer.php'; ?>
