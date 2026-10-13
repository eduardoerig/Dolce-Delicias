// Animação do topo da home: o prato de salgados entra rolando pela esquerda.
// Quem pediu "menos movimento" no sistema vê o prato parado.

const heroi = document.querySelector('[data-heroi-cento]');
const prato = document.querySelector('[data-cento-prato]');
const querMovimento = window.matchMedia('(prefers-reduced-motion: no-preference)').matches;

if (window.gsap && heroi && prato && querMovimento) {
  gsap.registerPlugin(ScrollTrigger);

  const tamanho = prato.offsetWidth;
  const inicio = -(prato.getBoundingClientRect().left + tamanho);

  // Quantos graus o prato gira para andar uma distância (como uma roda).
  function giro(distancia) {
    return (distancia / (tamanho / 2)) * (180 / Math.PI);
  }

  gsap.fromTo(prato,
    { x: inicio, rotation: giro(inicio), autoAlpha: 1 },
    { x: 0, rotation: 0, duration: 1.9, ease: 'back.out(1.15)' });

  gsap.fromTo('[data-cento-revela]',
    { y: 18, autoAlpha: 0 },
    { y: 0, autoAlpha: 1, duration: 0.7, delay: 0.6, stagger: 0.08 });

  // Ao rolar a página, o prato continua rodando para a direita.
  gsap.to(prato.querySelector('img'), {
    x: tamanho / 2,
    rotation: giro(tamanho / 2),
    ease: 'none',
    scrollTrigger: { trigger: heroi, start: 'top top', end: 'bottom top', scrub: 0.6 },
  });
}

document.documentElement.classList.remove('cento-anima');
