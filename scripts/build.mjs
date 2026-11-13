/**
 * Monta a pasta `public/` que a Vercel publica.
 *
 * O site principal continua sendo HTML, CSS e JS puros: nada aqui compila o
 * site, só copia os arquivos como estão. A única coisa que passa por um build
 * é o Sanity Studio, que precisa ser empacotado pelo `sanity build`.
 *
 * Resultado:
 *   public/index.html      -> loja
 *   public/pages, assets…  -> loja
 *   public/studio/         -> Sanity Studio (basePath /studio)
 *
 * Rodar localmente:  npm run build
 */
import { cp, mkdir, rm, stat } from 'node:fs/promises';
import { execFileSync } from 'node:child_process';
import { existsSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const raiz = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const pastaSite = path.join(raiz, 'public');
const pastaStudio = path.join(raiz, 'studio');

// Só o que a loja realmente usa. Se algum destes faltar, o build para: é melhor
// falhar aqui do que publicar um site sem CSS ou sem catálogo.
const ARQUIVOS_DA_LOJA = ['index.html', 'assets', 'pages', 'dados', 'catalogos'];

async function existe(caminho) {
  try {
    await stat(caminho);
    return true;
  } catch {
    return false;
  }
}

console.log('[build] limpando public/');
await rm(pastaSite, { recursive: true, force: true });
await mkdir(pastaSite, { recursive: true });

// 1. Site estático ---------------------------------------------------------
console.log('[build] copiando a loja');
for (const nome of ARQUIVOS_DA_LOJA) {
  const origem = path.join(raiz, nome);
  if (!(await existe(origem))) {
    throw new Error(`Arquivo obrigatório da loja não encontrado: ${nome}`);
  }
  await cp(origem, path.join(pastaSite, nome), { recursive: true });
  console.log(`  ok  ${nome}`);
}

// 2. Sanity Studio --------------------------------------------------------
// O `basePath: /studio` vem do studio/sanity.cli.js, por isso o bundle do
// Studio já aponta para /studio/static/... e pode ser servido em /studio.
console.log('[build] compilando o Sanity Studio');
execFileSync('npm run build', {
  cwd: pastaStudio,
  stdio: 'inherit',
  shell: true,
});

const distStudio = path.join(pastaStudio, 'dist');
if (!existsSync(path.join(distStudio, 'index.html'))) {
  throw new Error('O build do Studio não gerou studio/dist/index.html');
}

await cp(distStudio, path.join(pastaSite, 'studio'), { recursive: true });
console.log('  ok  studio');

// 3. Conferência final ----------------------------------------------------
for (const obrigatorio of [
  path.join(pastaSite, 'index.html'),
  path.join(pastaSite, 'assets', 'css', 'app.css'),
  path.join(pastaSite, 'studio', 'index.html'),
]) {
  if (!existsSync(obrigatorio)) {
    throw new Error(`publicação incompleta, faltou: ${obrigatorio}`);
  }
}

console.log('[build] pronto: public/');
