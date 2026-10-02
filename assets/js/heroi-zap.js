/**
 * assets/js/heroi-zap.js — o herói da home ganhando vida (GSAP).
 *
 * Uma cena só, na chegada: o braço entra pela esquerda girando a partir do
 * ombro (fora da tela, no canto de baixo), o texto aparece, e a conversa
 * acontece na tela do celular: o pedido sai, a loja "digita", responde e
 * manda a foto do cento; os vistos do pedido ficam azuis quando a loja lê.
 *
 * Ao rolar a página, o celular sobe um pouco mais devagar que ela
 * (ScrollTrigger com scrub): no celular, isso mostra mais da conversa.
 *
 * Quem pediu menos movimento não vê nada disso: gsap.matchMedia só cria as
 * animações com prefers-reduced-motion: no-preference, e o CSS sozinho mostra
 * a conversa completa. A classe "zap-anima" (posta no <html> pelo herói)
 * esconde celular, mensagens e texto até o primeiro quadro; aqui ela sai.
 */

const { gsap, ScrollTrigger } = window;
const heroi = document.querySelector('[data-heroi-zap]');
const celular = heroi?.querySelector('[data-zap-celular]');
const raiz = document.documentElement;

if (!gsap || !ScrollTrigger || !heroi || !celular) {
  raiz.classList.remove('zap-anima');
} else {
  gsap.registerPlugin(ScrollTrigger);

  const revela = heroi.querySelectorAll('[data-zap-revela]');
  const [pedido, resposta, foto] = heroi.querySelectorAll('[data-zap-msg]');
  const digitando = heroi.querySelector('[data-zap-digitando]');

  const mm = gsap.matchMedia();

  mm.add('(prefers-reduced-motion: no-preference)', () => {
    // Uma mensagem chegando: cresce do canto de onde vem o balão.
    const chega = (balao, lado) => [balao,
      { autoAlpha: 0, scale: 0.6, y: 12, transformOrigin: `${lado} bottom` },
      { autoAlpha: 1, scale: 1, y: 0, duration: 0.45, ease: 'back.out(1.6)' }];

    const cena = gsap.timeline({
      onStart: () => raiz.classList.remove('zap-anima'),
    });

    cena
      .fromTo(celular,
        { xPercent: -55, rotation: -14, transformOrigin: '0% 100%', autoAlpha: 1 },
        { xPercent: 0, rotation: 0, autoAlpha: 1, duration: 1.2, ease: 'power3.out' })
      .fromTo(revela,
        { y: 18, autoAlpha: 0 },
        { y: 0, autoAlpha: 1, duration: 0.7, stagger: 0.08, ease: 'power3.out', clearProps: 'transform' },
        0.25);

    if (pedido) cena.fromTo(...chega(pedido, 'right'), 1.15);
    if (digitando) {
      cena
        .set(digitando, { display: 'flex' }, 1.8)
        .fromTo(digitando, { autoAlpha: 0, scale: 0.6, transformOrigin: 'left bottom' }, { autoAlpha: 1, scale: 1, duration: 0.3 }, 1.8)
        .set(digitando, { display: 'none' }, 3);
    }
    if (pedido) cena.call(() => pedido.classList.add('zap-lido'), null, 2.2);
    if (resposta) cena.fromTo(...chega(resposta, 'left'), 3);
    if (foto) cena.fromTo(...chega(foto, 'left'), 3.7);

    // Os vistos começam cinza (entregue) só quando há animação.
    pedido?.classList.remove('zap-lido');

    gsap.to(celular, {
      yPercent: -6,
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

    return () => pedido?.classList.add('zap-lido');
  });
}
