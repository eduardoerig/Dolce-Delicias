// Página de unidades: a matriz em destaque e um cartão para cada outra loja.
// Tudo vem do cadastro de unidades no Sanity.

iniciarPagina(function () {
  const outras = [];
  for (let i = 0; i < site.unidades.length; i++) {
    if (!site.unidades[i].matriz) {
      outras.push(site.unidades[i]);
    }
  }

  document.querySelector('[data-qtd-lojas]').textContent = site.unidades.length;
  document.querySelector('[data-qtd-outras]').textContent = 'Outras ' + outras.length + ' lojas';

  if (site.matriz) {
    document.querySelector('[data-matriz]').innerHTML = faixaDaMatriz(site.matriz);
  } else {
    document.getElementById('matriz').hidden = true;
  }

  let cartoes = '';
  for (let i = 0; i < outras.length; i++) {
    cartoes += cartaoDaLoja(outras[i]);
  }
  document.querySelector('[data-lojas]').innerHTML = cartoes;

  // Os cartões nascem depois que a página abriu: o navegador não rolou até o #unidade-1.
  if (location.hash) {
    const alvo = document.getElementById(decodeURIComponent(location.hash.slice(1)));
    if (alvo) {
      alvo.scrollIntoView();
    }
  }
});

// "Unidade 1 — Jardim América" vira título "Unidade 1" e bairro "Jardim América".
// Sem o "—" no nome, o bairro é o fim do endereço ("Av. Brasil, 1.250 — Jardim América").
function tituloEBairro(unidade) {
  const partes = unidade.nome.split(' — ');
  if (partes.length > 1) {
    return {titulo: partes[0], bairro: partes.slice(1).join(' — ')};
  }
  let bairro = '';
  const endereco = (unidade.endereco || '').split(' — ');
  if (endereco.length > 1) {
    bairro = endereco[endereco.length - 1];
  }
  return {titulo: unidade.nome, bairro: bairro};
}

function faixaDaMatriz(matriz) {
  const nomes = tituloEBairro(matriz);

  let foto = '';
  if (matriz.imagem) {
    foto = `
      <div class="matriz-faixa-foto">
        <img src="${limpar(caminhoArquivo(matriz.imagem))}" alt="Foto da ${limpar(matriz.nome)}" width="1600" height="1000" loading="lazy">
      </div>`;
  }

  let dados = '';
  if (matriz.endereco) {
    dados += `<div><dt>${ICONE_ENDERECO}Endereço</dt><dd>${limpar(matriz.endereco)}</dd></div>`;
  }
  if (matriz.horario) {
    dados += `<div><dt>${ICONE_HORARIO}Horário</dt><dd>${limpar(matriz.horario)}</dd></div>`;
  }
  if (matriz.preparo) {
    dados += `<div><dt>${ICONE_PRAZO}Prazo</dt><dd>${limpar(matriz.preparo)}</dd></div>`;
  }

  let whatsapp = '';
  const link = linkWhatsapp(matriz, 'Olá! Vim pelo site da Dolce Delícias e quero falar com a ' + matriz.nome + '.');
  if (link) {
    whatsapp = `<a href="${limpar(link)}" target="_blank" rel="noopener noreferrer" class="botao-pilula botao-pilula-claro"><span class="h-5 w-5">${ICONE_WHATSAPP}</span>WhatsApp</a>`;
  }

  let sobre = '';
  if (matriz.sobre) {
    sobre = `<p class="matriz-sobre">${limpar(matriz.sobre)}</p>`;
  }

  return `
    ${foto}
    <div class="matriz-faixa-texto">
      <p class="matriz-selo">Recebe os pedidos do site</p>
      <h2 id="titulo-matriz">${limpar(nomes.titulo)}</h2>
      <p class="matriz-bairro">${limpar(nomes.bairro)}</p>
      ${sobre}
      <dl class="matriz-dados">${dados}</dl>
      <div class="matriz-acoes">
        <a href="../index.html#catalogo" class="botao-pilula botao-pilula-amarelo">Fazer um pedido</a>
        ${whatsapp}
        <a href="${limpar(pdfCompleto())}" download class="matriz-link"><span class="h-4 w-4">${ICONE_PDF}</span>Catálogo em PDF</a>
      </div>
    </div>`;
}

function cartaoDaLoja(unidade) {
  const nomes = tituloEBairro(unidade);

  let dados = '';
  if (unidade.endereco) {
    dados += `<p class="loja-dado">${ICONE_ENDERECO}<span>${limpar(unidade.endereco)}</span></p>`;
  }
  if (unidade.horario) {
    dados += `<p class="loja-dado">${ICONE_HORARIO}<span>${limpar(unidade.horario)}</span></p>`;
  }

  // Sem número de verdade no cadastro, o botão fica apagado com "Em breve".
  let whatsapp = `<span class="loja-acao loja-acao-off">${ICONE_WHATSAPP}<span>Em breve</span></span>`;
  const link = linkWhatsapp(unidade, 'Olá! Vim pelo site da Dolce Delícias e quero falar com a ' + unidade.nome + '.');
  if (link) {
    whatsapp = `<a href="${limpar(link)}" target="_blank" rel="noopener noreferrer" class="loja-acao">${ICONE_WHATSAPP}<span>WhatsApp</span></a>`;
  }

  let catalogo = '';
  const pdf = linkDoPdf(unidade.catalogoPdf);
  if (pdf) {
    catalogo = `<a href="${limpar(pdf)}" download class="loja-acao">${ICONE_PDF}<span>Catálogo</span></a>`;
  }

  return `
    <li id="${limpar(unidade.slug)}" class="loja-cartao">
      <h3>${limpar(nomes.titulo)}</h3>
      <p class="loja-cartao-bairro">${limpar(nomes.bairro)}</p>
      ${dados}
      <div class="loja-acoes">${whatsapp}${catalogo}</div>
    </li>`;
}

const ICONE_ENDERECO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';
const ICONE_HORARIO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>';
const ICONE_PRAZO = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 22h14"/><path d="M5 2h14"/><path d="M17 22v-4.2a2 2 0 0 0-.6-1.4L12 12l-4.4 4.4a2 2 0 0 0-.6 1.4V22"/><path d="M7 2v4.2a2 2 0 0 0 .6 1.4L12 12l4.4-4.4a2 2 0 0 0 .6-1.4V2"/></svg>';
const ICONE_WHATSAPP = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20.5l1.7-5.4A8.5 8.5 0 1 1 21 11.5Z"/></svg>';
const ICONE_PDF = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12"/><path d="m7 11 5 5 5-5"/><path d="M5 21h14"/></svg>';
