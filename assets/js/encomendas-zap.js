/**
 * assets/js/encomendas-zap.js — o celular do "Como encomendar" (GSAP).
 *
 * Quando a seção aparece na tela, uma cena só, uma vez: o braço entra pela
 * esquerda girando a partir do ombro (fora da tela, no canto de baixo) e a
 * conversa acontece: o pedido sai, a loja "digita", os vistos do pedido ficam
 * azuis, a loja responde e manda a foto do cento.
 *
 * Quem pediu menos movimento não vê nada disso: gsap.matchMedia só cria as
 * animações com prefers-reduced-motion: no-preference, e o CSS sozinho mostra
 * a conversa completa. A classe "zap-anima" (posta no <html> pela seção)
 * esconde celular e mensagens até o GSAP assumir; aqui ela sai.
 */

const { gsap, ScrollTrigger } = window;
const secao = document.querySelector('[data-encomendas]');
const celular = secao?.querySelector('[data-zap-celular]');
const raiz = document.documentElement;

if (!gsap || !ScrollTrigger || !secao || !celular) {
  raiz.classList.remove('zap-anima');
} else {
  gsap.registerPlugin(ScrollTrigger);

  const [pedido, resposta, foto] = secao.querySelectorAll('[data-zap-msg]');
  const digitando = secao.querySelector('[data-zap-digitando]');

  const mm = gsap.matchMedia();

  mm.add('(prefers-reduced-motion: no-preference)', () => {
    // Uma mensagem chegando: cresce do canto de onde vem o balão.
    const chega = (balao, lado) => [balao,
      { autoAlpha: 0, scale: 0.6, y: 12, transformOrigin: `${lado} bottom` },
      { autoAlpha: 1, scale: 1, y: 0, duration: 0.45, ease: 'back.out(1.6)' }];

    // Os vistos começam cinza (entregue) só quando há animação.
    pedido?.classList.remove('zap-lido');

    const cena = gsap.timeline({
      scrollTrigger: { trigger: secao, start: 'top 65%', once: true },
    });

    cena.fromTo(celular,
      { xPercent: -55, rotation: -14, transformOrigin: '0% 100%', autoAlpha: 1 },
      { xPercent: 0, rotation: 0, autoAlpha: 1, duration: 1.2, ease: 'power3.out' });

    if (pedido) cena.fromTo(...chega(pedido, 'right'), 0.9);
    if (digitando) {
      cena
        .set(digitando, { display: 'flex' }, 1.6)
        .fromTo(digitando, { autoAlpha: 0, scale: 0.6, transformOrigin: 'left bottom' }, { autoAlpha: 1, scale: 1, duration: 0.3 }, 1.6)
        .set(digitando, { display: 'none' }, 2.8);
    }
    if (pedido) cena.call(() => pedido.classList.add('zap-lido'), null, 2);
    if (resposta) cena.fromTo(...chega(resposta, 'left'), 2.8);
    if (foto) cena.fromTo(...chega(foto, 'left'), 3.5);

    // Os estados iniciais já estão aplicados (immediateRender): a classe
    // que escondia tudo pode sair.
    raiz.classList.remove('zap-anima');

    return () => pedido?.classList.add('zap-lido');
  });

  raiz.classList.remove('zap-anima');
}
