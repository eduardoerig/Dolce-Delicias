document.addEventListener('submit',event=>{const form=event.target;if(form.dataset.confirm&&!window.confirm(form.dataset.confirm))event.preventDefault();});
const nome=document.querySelector('#nome');const slug=document.querySelector('#slug');
if(nome&&slug){let manual=Boolean(slug.value);slug.addEventListener('input',()=>manual=Boolean(slug.value));nome.addEventListener('input',()=>{if(!manual)slug.value=nome.value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');});}
