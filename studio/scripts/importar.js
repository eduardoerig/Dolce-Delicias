import {getCliClient} from 'sanity/cli';
import {readFile} from 'node:fs/promises';
import {fileURLToPath} from 'node:url';
import {createReadStream} from 'node:fs';
const client=getCliClient({apiVersion:'2025-02-19'}), root=new URL('../../',import.meta.url);
const ler=async n=>JSON.parse(await readFile(new URL(`dados/${n}.json`,root),'utf8'));
const ref=(t,s,i)=>({_type:'reference',_key:String(i),_ref:`${t}-${s}`});
const slugify=s=>s.normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');
const [produtos,unidades,promocoes]=await Promise.all(['produtos','unidades','promocoes'].map(ler));
async function salvar(type,original,ordem){
 const id=`${type}-${original.slug}`;
 if(await client.getDocument(id)){console.log('Preservado:',id);return;}
 const doc={...original,_id:id,_type:type,ordem,slug:{_type:'slug',current:original.slug}};
 if('imagem' in doc){
  doc.imagemLocal=doc.imagem;delete doc.imagem;
  if(doc.imagemLocal){
   const path=fileURLToPath(new URL(doc.imagemLocal,root));
   try{await readFile(path);}catch(e){if(e.code!=='ENOENT')throw e;console.warn('Foto ausente:',doc.imagemLocal);doc.imagemLocal=null;}
   if(doc.imagemLocal){const asset=await client.assets.upload('image',createReadStream(path));doc.foto={_type:'image',asset:{_type:'reference',_ref:asset._id}};}
  }
 }
 if(type==='produto')doc.categoria=ref('categoria',slugify(original.categoria),0);
 if(doc.unidades)doc.unidades=doc.unidades.map((s,i)=>ref('unidade',s,i));
 if(doc.produtos)doc.produtos=doc.produtos.map((s,i)=>ref('produto',s,i));
 await client.createIfNotExists(doc);console.log('Importado:',id);
}
for(const [i,nome]of [...new Set(produtos.map(p=>p.categoria))].entries())await client.createIfNotExists({_id:`categoria-${slugify(nome)}`,_type:'categoria',nome,slug:{_type:'slug',current:slugify(nome)},ordem:i});
for(const [i,u]of unidades.entries())await salvar('unidade',u,i);
for(const [i,p]of produtos.entries())await salvar('produto',p,i);
for(const [i,p]of promocoes.entries())await salvar('promocao',p,i);
await client.createIfNotExists({_id:'catalogo-importado',_type:'importacao',concluida:true});
console.log('Importação concluída. Documentos existentes foram preservados.');
