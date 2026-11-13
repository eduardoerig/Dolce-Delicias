/**
 * Servidor local que imita o que a Vercel faz com o vercel.json:
 * serve a pasta `public/`, aplica os redirects e os rewrites do Studio.
 *
 * Uso:  npm run build   (uma vez)
 *       npm run dev     ->  http://localhost:3000/
 *
 * Não substitui o teste na Vercel, mas pega o grosso dos problemas.
 */
import { createServer } from 'node:http';
import { readFile, stat } from 'node:fs/promises';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const raiz = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..', 'public');
const porta = Number(process.env.PORT) || 3000;

const TIPOS = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.mjs': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.svg': 'image/svg+xml',
  '.webp': 'image/webp',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.ico': 'image/x-icon',
  '.pdf': 'application/pdf',
  '.webmanifest': 'application/manifest+json',
  '.woff2': 'font/woff2',
  '.map': 'application/json; charset=utf-8',
};

// Mesmas regras de vercel.json (só as que existem hoje).
const REDIRECTS = [
  ['/produtos', '/'],
  ['/unidades', '/pages/unidades.html'],
  ['/sobre', '/pages/sobre.html'],
  ['/carrinho', '/pages/carrinho.html'],
];

function ehArquivoEstaticoDoStudio(caminho) {
  return /^\/studio\/(static\/|favicon\.(ico|svg)$|apple-touch-icon\.png$|manifest\.webmanifest$)/.test(
    caminho,
  );
}

// O mesmo rewrite do vercel.json: /sanity/... vai para a API do Sanity.
async function repassarParaSanity(caminhoComQuery, resposta) {
  // Tira o prefixo /sanity: é ele que o vercel.json consome no rewrite.
  const destino = 'https://wdisx8zs.api.sanity.io' + caminhoComQuery.replace(/^\/sanity/, '');
  const r = await fetch(destino);
  const corpo = Buffer.from(await r.arrayBuffer());
  resposta.writeHead(r.status, {
    'Content-Type': r.headers.get('content-type') ?? 'application/json; charset=utf-8',
    'Cache-Control': 'no-store',
  });
  resposta.end(corpo);
  console.log(`${r.status} (proxy Sanity) ${caminhoComQuery.slice(0, 80)}`);
}

function resolver(caminhoOriginal) {
  let caminho = caminhoOriginal;

  for (const [de, para] of REDIRECTS) {
    if (caminho === de) return { redirect: para };
  }

  if (caminho === '/studio') return { arquivo: '/studio/index.html' };

  if (caminho.startsWith('/studio/') && !ehArquivoEstaticoDoStudio(caminho)) {
    return { arquivo: '/studio/index.html' };
  }

  if (caminho.startsWith('/static/')) {
    caminho = '/studio' + caminho;
  }

  if (caminho.endsWith('/')) caminho += 'index.html';

  return { arquivo: caminho };
}

async function lerArquivo(caminho) {
  const completo = path.join(raiz, decodeURIComponent(caminho));
  if (!completo.startsWith(raiz)) return null; // não sai da pasta public
  try {
    const info = await stat(completo);
    if (info.isDirectory()) return lerArquivo(caminho.replace(/\/?$/, '/') + 'index.html');
    return await readFile(completo);
  } catch {
    return null;
  }
}

createServer(async (pedido, resposta) => {
  const caminho = new URL(pedido.url ?? '/', 'http://localhost').pathname;

  if (caminho.startsWith('/sanity/')) {
    await repassarParaSanity(pedido.url, resposta);
    return;
  }

  const destino = resolver(caminho);

  if (destino.redirect) {
    resposta.writeHead(308, { Location: destino.redirect });
    resposta.end();
    return;
  }

  const conteudo = await lerArquivo(destino.arquivo);
  if (!conteudo) {
    const erro = await lerArquivo('/pages/404.html');
    resposta.writeHead(404, { 'Content-Type': TIPOS['.html'] });
    resposta.end(erro ?? '404');
    console.log(`404 ${caminho}`);
    return;
  }

  const tipo = TIPOS[path.extname(destino.arquivo).toLowerCase()] ?? 'application/octet-stream';
  resposta.writeHead(200, { 'Content-Type': tipo });
  resposta.end(conteudo);
  console.log(`200 ${caminho}`);
}).listen(porta, () => {
  console.log(`Loja:  http://localhost:${porta}/`);
  console.log(`Studio: http://localhost:${porta}/studio`);
});
