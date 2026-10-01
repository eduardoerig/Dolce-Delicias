// Painel: comportamentos pequenos, todos opcionais (sem JS o painel continua funcionando).

document.addEventListener('submit', (event) => {
  const form = event.target;
  if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) event.preventDefault();
});

// Endereço (slug) acompanha o nome até a pessoa editar o campo à mão.
const nome = document.querySelector('#nome');
const slug = document.querySelector('#slug');
if (nome && slug) {
  let manual = Boolean(slug.value);
  slug.addEventListener('input', () => { manual = Boolean(slug.value); });
  nome.addEventListener('input', () => {
    if (!manual) slug.value = nome.value.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
  });
}

// Menu lateral no celular.
const shell = document.querySelector('.adm-shell');
const toggle = document.querySelector('[data-menu-toggle]');
const scrim = document.querySelector('[data-menu-close]');
const setMenu = (open) => {
  shell?.classList.toggle('menu-aberto', open);
  toggle?.setAttribute('aria-expanded', String(open));
  if (scrim) scrim.hidden = !open;
  if (open) document.querySelector('.adm-nav a')?.focus();
};
toggle?.addEventListener('click', () => setMenu(!shell.classList.contains('menu-aberto')));
scrim?.addEventListener('click', () => setMenu(false));
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape' && shell?.classList.contains('menu-aberto')) { setMenu(false); toggle?.focus(); }
});

// Busca da lista: envia sozinha depois de uma pausa na digitação.
const search = document.querySelector('[data-auto-submit]');
if (search) {
  let timer;
  search.addEventListener('input', () => {
    clearTimeout(timer);
    timer = setTimeout(() => search.form.requestSubmit(), 450);
  });
  if (search.value) { search.focus(); search.setSelectionRange(search.value.length, search.value.length); }
}

// Prévia da foto antes de salvar.
document.querySelectorAll('[data-preview-input]').forEach((input) => {
  input.addEventListener('change', () => {
    const file = input.files?.[0];
    const box = input.closest('.adm-field')?.querySelector('[data-preview]');
    if (!file || !box) return;
    const img = document.createElement('img');
    img.alt = 'Nova foto';
    img.src = URL.createObjectURL(file);
    box.replaceChildren(img);
    const title = input.closest('.adm-field').querySelector('.adm-upload-text strong');
    if (title) title.textContent = file.name;
  });
});
document.querySelectorAll('[data-file-input]').forEach((input) => {
  input.addEventListener('change', () => {
    const label = input.closest('.adm-field')?.querySelector('[data-file-name]');
    if (label && input.files?.[0]) label.textContent = input.files[0].name;
  });
});

// Promoção: mostra só os campos da agenda escolhida e troca % por R$.
const agendaRadios = document.querySelectorAll('input[name="tipo_agenda"]');
const syncAgenda = () => {
  const value = document.querySelector('input[name="tipo_agenda"]:checked')?.value;
  document.querySelectorAll('[data-agenda]').forEach((box) => {
    box.hidden = !box.dataset.agenda.split(' ').includes(value);
  });
};
agendaRadios.forEach((r) => r.addEventListener('change', syncAgenda));

document.querySelectorAll('input[name="tipo_desconto"]').forEach((r) => r.addEventListener('change', () => {
  const unit = document.querySelector('[data-discount-unit]');
  if (unit && r.checked) unit.textContent = r.value === 'VALOR_FIXO' ? 'R$' : '%';
}));

// Listas de marcar: filtrar, marcar todos (só os visíveis) e limpar.
document.querySelectorAll('[data-checklist]').forEach((box) => {
  const labels = [...box.querySelectorAll('.adm-checklist label')];
  const visible = () => labels.filter((l) => !l.hidden);
  box.querySelector('[data-checklist-filter]')?.addEventListener('input', (e) => {
    const q = e.target.value.normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase().trim();
    labels.forEach((l) => { l.hidden = q !== '' && !l.dataset.name.includes(q); });
  });
  box.querySelector('[data-checklist-all]')?.addEventListener('click', () => visible().forEach((l) => { l.querySelector('input').checked = true; }));
  box.querySelector('[data-checklist-none]')?.addEventListener('click', () => visible().forEach((l) => { l.querySelector('input').checked = false; }));
});

// Aviso ao sair com alterações não salvas.
const form = document.querySelector('[data-form]');
if (form) {
  let dirty = false;
  form.addEventListener('input', () => { dirty = true; });
  form.addEventListener('change', () => { dirty = true; });
  form.addEventListener('submit', () => { dirty = false; });
  window.addEventListener('beforeunload', (e) => { if (dirty) e.preventDefault(); });
}
