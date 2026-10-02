/**
 * assets/js/heroi-cento.js — o prato do herói da home rolando (GSAP).
 *
 * O prato entra pela esquerda, girando como uma roda, e para na posição que o
 * CSS já define (metade à mostra). O giro acompanha a distância: uma volta a
 * cada 2πr percorridos, então ele parece rolar de verdade em vez de deslizar.
 * A mesma curva no x e na rotação mantém essa conta em todos os quadros,
 * inclusive no leve recuo do fim. O texto aparece em seguida, linha a linha.
 *
 * Ao rolar a página, o prato continua rodando para a direita, preso à
 * rolagem (ScrollTrigger com scrub).
 *
 * Quem pediu menos movimento não vê nada disso: gsap.matchMedia só cria as
 * animações com prefers-reduced-motion: no-preference, e o CSS sozinho deixa
 * o prato parado no lugar. A classe "cento-anima" (posta no <html> pelo
 * herói) esconde prato e texto até o primeiro quadro; aqui ela sai.
 */

const { gsap, ScrollTrigger } = window;
const heroi = document.querySelector('[data-heroi-cento]');
const prato = heroi?.querySelector('[data-cento-prato]');
const raiz = document.documentElement;

if (!gsap || !ScrollTrigger || !heroi || !prato) {
  raiz.classList.remove('cento-anima');
} else {
  gsap.registerPlugin(ScrollTrigger);

  const imagem = prato.querySelector('img');
  const revela = heroi.querySelectorAll('[data-cento-revela]');
  const graus = (distancia, diametro) => (distancia / (diametro / 2)) * (180 / Math.PI);

  const mm = gsap.matchMedia();

  mm.add('(prefers-reduced-motion: no-preference)', () => {
    // Medido na posição final, antes de qualquer transform do GSAP: de onde
    // até onde o prato rola, saindo todo para fora da borda esquerda.
    const diametro = prato.offsetWidth;
    const inicioX = -(prato.getBoundingClientRect().left - heroi.getBoundingClientRect().left + diametro);

    const entrada = gsap.timeline({
      defaults: { ease: 'power3.out' },
      onStart: () => raiz.classList.remove('cento-anima'),
    });

    entrada
      .fromTo(prato,
        { x: inicioX, rotation: graus(inicioX, diametro), autoAlpha: 1 },
        { x: 0, rotation: 0, autoAlpha: 1, duration: 1.9, ease: 'back.out(1.15)' })
      .fromTo(revela,
        { y: 18, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.7, stagger: 0.08, clearProps: 'transform' },
        0.6);

    // Ao sair da tela, o prato segue rodando para a direita: meio diâmetro,
    // com o giro proporcional.
    gsap.to(imagem, {
      x: () => imagem.offsetWidth * 0.5,
      rotation: () => graus(imagem.offsetWidth * 0.5, imagem.offsetWidth),
      ease: 'none',
      scrollTrigger: {
        trigger: heroi,
        // Começa já no primeiro pixel rolado (o herói fica abaixo do topo fixo).
        start: () => `top ${heroi.getBoundingClientRect().top + window.scrollY}px`,
        end: 'bottom top',
        scrub: 0.6,
        invalidateOnRefresh: true,
      },
    });
  });
}
