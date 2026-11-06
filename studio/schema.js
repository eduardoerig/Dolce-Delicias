const field = (name,title,type='string',extra={}) => ({name,title,type,...extra});
const required = Rule => Rule.required();
const text = (name,title) => field(name,title,'string',{validation:required});
const slug = field('slug','Identificador (não altere após publicar)','slug',{options:{source:doc=>doc.nome||doc.titulo},validation:required});
const refs = (name,title,to) => field(name,title,'array',{of:[{type:'reference',to:[{type:to}]}]});
const strings = (name,title) => field(name,title,'array',{of:[{type:'string'}]});
const photo = field('foto','Foto','image',{options:{hotspot:true}});
const order = field('ordem','Ordem de exibição','number',{initialValue:0});
export const schemaTypes = [
 {name:'categoria',title:'Categorias',type:'document',fields:[text('nome','Nome'),slug,order]},
 {name:'produto',title:'Produtos',type:'document',initialValue:{disponivel:true,destaque:false,tags:[],sabores:[],unidades:[]},fields:[
 text('nome','Nome'),slug,field('descricao','Descrição','text',{validation:required}),
 field('categoria','Categoria','reference',{to:[{type:'categoria'}],validation:required}),photo,
 field('imagemLocal','Foto local de reserva','string',{readOnly:true}),
 field('destaque','Mostrar nos destaques','boolean'),field('disponivel','Disponível para compra','boolean'),order,
 field('preco','Preço e quantidades','object',{validation:required,fields:[
 field('valor','Preço da embalagem (R$)','number',{validation:R=>R.required().positive()}),text('por','Embalagem (ex.: 100 unidades)'),
 field('minPedido','Pedido mínimo (peças)','number',{validation:R=>R.integer().positive()}),field('passo','Incremento de quantidade (peças)','number',{validation:R=>R.integer().positive()})]}),
 strings('tags','Etiquetas'),strings('sabores','Sabores'),refs('unidades','Lojas que vendem (vazio = todas)','unidade')]},
 {name:'unidade',title:'Unidades / Lojas',type:'document',fields:[text('nome','Nome'),slug,field('matriz','Matriz (mantenha apenas uma)','boolean'),order,
 text('endereco','Endereço'),field('telefone','Telefone'),field('whatsapp','WhatsApp com DDI e DDD'),field('horario','Horário'),field('preparo','Prazo de preparo'),field('sobre','Sobre','text'),field('mapaUrl','Link do mapa','url'),photo,
 field('imagemLocal','Foto local de reserva','string',{readOnly:true}),field('avaliacao','Avaliação'),field('catalogoPdf','Caminho do catálogo local'),field('catalogo','Enviar catálogo PDF','file',{options:{accept:'application/pdf'}}),
 field('canais','Canais','array',{of:[{type:'object',fields:[text('nome','Nome'),field('url','Link','url')]}]})]},
 {name:'promocao',title:'Promoções',type:'document',initialValue:{ativa:false,quando:{tipo:'sempre',dias:[],meses:[]}},fields:[text('titulo','Título'),slug,field('selo','Selo (ex.: 15% OFF)'),field('texto','Descrição','text'),field('ativa','Ativa','boolean'),order,
 field('quando','Agenda','object',{validation:required,fields:[field('tipo','Frequência','string',{validation:required,options:{list:[{title:'Sempre',value:'sempre'},{title:'Semanal',value:'semanal'},{title:'Mensal',value:'mensal'},{title:'Período',value:'periodo'}]}}),
 field('dias','Dias da semana','array',{of:[{type:'number'}],options:{list:['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'].map((title,value)=>({title,value}))}}),
 field('meses','Meses','array',{of:[{type:'number'}],options:{list:Array.from({length:12},(_,i)=>({title:String(i+1),value:i+1}))}}),field('de','Início','date'),field('ate','Fim','date',{validation:R=>R.custom((value,ctx)=>!value||!ctx.parent?.de||value>=ctx.parent.de||'O fim deve ser posterior ao início')})]}),
 refs('produtos','Produtos participantes','produto'),refs('unidades','Lojas participantes (vazio = todas)','unidade')],preview:{select:{title:'titulo'}}}
];
