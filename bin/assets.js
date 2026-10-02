import {cpSync,existsSync,mkdirSync} from 'node:fs';

// GSAP (animação do herói da home): os arquivos prontos de node_modules vão
// para assets/js/vendor, versionados como o app.css, para o site funcionar
// num host PHP simples sem rodar o build lá.
mkdirSync('assets/js/vendor',{recursive:true});
for (const arquivo of ['gsap.min.js','ScrollTrigger.min.js']) {
  const origem = `node_modules/gsap/dist/${arquivo}`;
  if (existsSync(origem)) cpSync(origem,`assets/js/vendor/${arquivo}`);
}

mkdirSync('public/assets',{recursive:true});
cpSync('assets','public/assets',{recursive:true});
