// Animação do "Como encomendar": o celular entra e as mensagens chegam uma por uma.
// Quem pediu "menos movimento" no sistema vê a conversa completa, parada.

const secaoZap = document.querySelector('[data-encomendas]');
const celular = document.querySelector('[data-zap-celular]');
const mensagens = document.querySelectorAll('[data-zap-msg]');
const digitando = document.querySelector('[data-zap-digitando]');
const queroMovimento = window.matchMedia('(prefers-reduced-motion: no-preference)').matches;

if (window.gsap && secaoZap && celular && queroMovimento) {
  gsap.registerPlugin(ScrollTrigger);

  // Começa quando a seção aparece na tela, uma vez só.
  const cena = gsap.timeline({
    scrollTrigger: { trigger: secaoZap, start: 'top 65%', once: true },
  });

  cena.fromTo(celular,
    { xPercent: -55, rotation: -14, transformOrigin: '0% 100%', autoAlpha: 1 },
    { xPercent: 0, rotation: 0, duration: 1.2, ease: 'power3.out' });

  // Pedido sai, loja "digita", loja responde, loja manda a foto.
  cena.fromTo(mensagens[0], { autoAlpha: 0, y: 12 }, { autoAlpha: 1, y: 0, duration: 0.45 }, 0.9);
  cena.set(digitando, { display: 'flex' }, 1.6);
  cena.set(digitando, { display: 'none' }, 2.8);
  cena.fromTo(mensagens[1], { autoAlpha: 0, y: 12 }, { autoAlpha: 1, y: 0, duration: 0.45 }, 2.8);
  cena.fromTo(mensagens[2], { autoAlpha: 0, y: 12 }, { autoAlpha: 1, y: 0, duration: 0.45 }, 3.5);
}

document.documentElement.classList.remove('zap-anima');
