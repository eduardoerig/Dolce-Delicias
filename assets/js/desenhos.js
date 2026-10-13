// Desenhos usados quando falta foto.

const crosta = 'stroke="#a9561a" stroke-width="1.8" stroke-linejoin="round"';

const GLIFOS = {
  salgado: `<path d="M32 7c8 10 15 19 15 28a15 15 0 0 1-30 0c0-9 7-18 15-28Z" fill="#e39a3b" ${crosta}/><path d="M25 33c-1 5 0 10 4 13" fill="none" stroke="#f7c46a" stroke-width="3.5" stroke-linecap="round"/><circle cx="37" cy="35" r="1.4" fill="#b8661f"/><circle cx="40" cy="42" r="1.2" fill="#b8661f"/><circle cx="33" cy="46" r="1.2" fill="#b8661f"/>`,
  assado: `<path d="M9 38c0-10 10-17 23-17s23 7 23 17-10 13-23 13S9 48 9 38Z" fill="#e7a446" ${crosta}/><path d="M20 31l5 7M31 28l5 8M42 31l4 7" fill="none" stroke="#f7c46a" stroke-width="3" stroke-linecap="round"/>`,
  lanche: `<path d="M12 30c0-9 9-14 20-14s20 5 20 14Z" fill="#e7a446" ${crosta}/><rect x="11" y="30" width="42" height="5" rx="2.5" fill="#fce24c" stroke="#c08a2e" stroke-width="1.4"/><rect x="12" y="35" width="40" height="5" rx="2.5" fill="#e1051e"/><path d="M12 41h40v3a6 6 0 0 1-6 6H18a6 6 0 0 1-6-6Z" fill="#e39a3b" ${crosta}/><circle cx="25" cy="22" r="1" fill="#fbf4ee"/><circle cx="33" cy="20" r="1" fill="#fbf4ee"/><circle cx="40" cy="23" r="1" fill="#fbf4ee"/>`,
  doce: '<path d="M19 33h26l-3 20a3 3 0 0 1-3 3H25a3 3 0 0 1-3-3Z" fill="#e1051e" stroke="#9e0315" stroke-width="1.6"/><path d="M26 34l1 21M32 34v21M38 34l-1 21" stroke="#ff6b7a" stroke-width="1.6"/><path d="M17 33c-2-6 3-10 7-9 1-6 8-9 12-6 5-2 10 2 9 7 4 0 6 4 4 8Z" fill="#fbf4ee" stroke="#c9a27e" stroke-width="1.6" stroke-linejoin="round"/><circle cx="34" cy="14" r="4" fill="#e1051e" stroke="#9e0315" stroke-width="1.4"/>',
  bebida: '<path d="M38 6l-4 14" stroke="#e1051e" stroke-width="3" stroke-linecap="round"/><path d="M20 18h24l-3 34a4 4 0 0 1-4 4H27a4 4 0 0 1-4-4Z" fill="#fbf4ee" stroke="#a9561a" stroke-width="1.8" stroke-linejoin="round"/><path d="M21.4 30h21.2l-1.8 21.6a3 3 0 0 1-3 2.8H26.2a3 3 0 0 1-3-2.8Z" fill="#f2a33a"/><circle cx="28" cy="38" r="1.6" fill="#fce24c"/><circle cx="35" cy="44" r="1.3" fill="#fce24c"/>',
  almoco: '<circle cx="32" cy="34" r="22" fill="#fff" stroke="#c5ac98" stroke-width="1.8"/><circle cx="32" cy="34" r="15" fill="none" stroke="#eadacb" stroke-width="1.4"/><path d="M20 34c0-6 5-10 11-10 0 6-1 12-11 10Z" fill="#fbf4ee" stroke="#d8c3ad" stroke-width="1.2"/><circle cx="38" cy="30" r="6" fill="#6b3a1f"/><path d="M28 40c3 4 9 5 13 1" fill="none" stroke="#5f9e4a" stroke-width="4" stroke-linecap="round"/>',
  combo: `<ellipse cx="32" cy="40" rx="27" ry="13" fill="#fbf4ee" stroke="#c5ac98" stroke-width="1.8"/><path d="M20 25c4 5 7 9 7 13a7 7 0 0 1-14 0c0-4 3-8 7-13Z" fill="#e39a3b" ${crosta}/><circle cx="33" cy="37" r="7" fill="#e7a446" ${crosta}/><circle cx="45" cy="39" r="7" fill="#f2d48a" stroke="#c08a2e" stroke-width="1.6"/><circle cx="43" cy="38" r="1.3" fill="#d9a441"/><circle cx="47" cy="41" r="1.1" fill="#d9a441"/>`,
  paoQueijo: '<circle cx="32" cy="34" r="18" fill="#f2d48a" stroke="#c08a2e" stroke-width="1.8"/><path d="M22 28c3-4 8-6 12-5" fill="none" stroke="#fbe7b0" stroke-width="3" stroke-linecap="round"/><circle cx="27" cy="36" r="2.6" fill="#d9a441"/><circle cx="37" cy="30" r="2" fill="#d9a441"/><circle cx="36" cy="41" r="2.3" fill="#d9a441"/><circle cx="25" cy="44" r="1.5" fill="#d9a441"/>',
  pizza: `<circle cx="32" cy="34" r="19" fill="#e7a446" ${crosta}/><circle cx="32" cy="34" r="14" fill="#e1051e"/><path d="M22 30c4-3 7 2 11-1s6 3 10 1M21 39c4-2 8 3 12 0s7 2 10 0" fill="none" stroke="#fce24c" stroke-width="3.2" stroke-linecap="round"/><circle cx="27" cy="34" r="1.8" fill="#2a1710"/><circle cx="38" cy="36" r="1.8" fill="#2a1710"/><circle cx="33" cy="27" r="1.6" fill="#5f9e4a"/>`,
  empada: `<path d="M14 34h36l-4 16a4 4 0 0 1-4 3H22a4 4 0 0 1-4-3Z" fill="#e7a446" ${crosta}/><path d="M20 36l1 16M27 36l.5 17M34 36v17M41 36l-.5 17" stroke="#c97b2d" stroke-width="1.4"/><ellipse cx="32" cy="33" rx="19" ry="9" fill="#eab35a" stroke="#a9561a" stroke-width="1.8" stroke-dasharray="3 2"/><ellipse cx="32" cy="32" rx="9" ry="4" fill="#f7c46a"/>`,
  esfirra: `<path d="M32 12 54 50H10Z" fill="#e7a446" ${crosta}/><path d="M32 26 43 45H21Z" fill="#c2412c"/><circle cx="30" cy="38" r="1.6" fill="#7a2e12"/><circle cx="35" cy="41" r="1.4" fill="#7a2e12"/><circle cx="33" cy="33" r="1.2" fill="#5f9e4a"/>`,
  cookie: '<circle cx="32" cy="33" r="18" fill="#d9a05a" stroke="#8a4a18" stroke-width="1.8"/><circle cx="25" cy="28" r="2.8" fill="#4a2512"/><circle cx="37" cy="26" r="2.4" fill="#4a2512"/><circle cx="35" cy="38" r="3" fill="#4a2512"/><circle cx="24" cy="39" r="2.2" fill="#4a2512"/><circle cx="42" cy="34" r="1.8" fill="#4a2512"/>',
  bolo: '<path d="M10 44 52 26v16L10 54Z" fill="#fbf4ee" stroke="#c9a27e" stroke-width="1.6" stroke-linejoin="round"/><path d="M10 44 52 26l-6-8L10 34Z" fill="#7a3a1a" stroke="#4a2512" stroke-width="1.6" stroke-linejoin="round"/><path d="M10 44 52 26v5L10 49Z" fill="#e1051e"/><circle cx="44" cy="17" r="3.4" fill="#e1051e" stroke="#9e0315" stroke-width="1.2"/>',
  cafe: '<path d="M14 26h30v12a13 13 0 0 1-13 13h-4a13 13 0 0 1-13-13Z" fill="#fbf4ee" stroke="#a9561a" stroke-width="1.8" stroke-linejoin="round"/><path d="M44 30h3a5 5 0 0 1 0 10h-4" fill="none" stroke="#a9561a" stroke-width="1.8"/><ellipse cx="29" cy="27" rx="14" ry="3.5" fill="#6b3a1f"/><path d="M24 20c-2-3 2-5 0-8M32 20c-2-3 2-5 0-8" fill="none" stroke="#c5ac98" stroke-width="2" stroke-linecap="round"/><ellipse cx="29" cy="54" rx="20" ry="3.5" fill="#eadacb"/>',
  lata: '<rect x="20" y="12" width="24" height="42" rx="4" fill="#e1051e" stroke="#9e0315" stroke-width="1.8"/><path d="M20 18h24M20 48h24" stroke="#c9c9c9" stroke-width="3"/><path d="M24 30c4 3 12-3 16 0" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/>',
};

// Escolhe o desenho pelo nome do produto e, se não achar, pela categoria.
function iconeProduto(categoria, nome) {
  const c = semAcento(categoria || '');
  const n = semAcento(nome || '');
  let desenho = GLIFOS.assado;

  if (n.includes('pao de queijo')) desenho = GLIFOS.paoQueijo;
  else if (n.includes('pizza')) desenho = GLIFOS.pizza;
  else if (n.includes('empad')) desenho = GLIFOS.empada;
  else if (n.includes('esfirr')) desenho = GLIFOS.esfirra;
  else if (n.includes('cookie')) desenho = GLIFOS.cookie;
  else if (n.includes('bolo')) desenho = GLIFOS.bolo;
  else if (n.includes('cafe')) desenho = GLIFOS.cafe;
  else if (n.includes('coca') || n.includes('lata')) desenho = GLIFOS.lata;
  else if (n.includes('hamburg') || n.includes('sandu')) desenho = GLIFOS.lanche;
  else if (n.includes('cupcake')) desenho = GLIFOS.doce;
  else if (c.includes('salgad')) desenho = GLIFOS.salgado;
  else if (c.includes('doce')) desenho = GLIFOS.doce;
  else if (c.includes('bebid')) desenho = GLIFOS.bebida;
  else if (c.includes('almoc')) desenho = GLIFOS.almoco;
  else if (c.includes('combo')) desenho = GLIFOS.combo;
  else if (c.includes('lanche')) desenho = GLIFOS.lanche;

  return '<svg viewBox="0 0 64 64" aria-hidden="true">' + desenho + '</svg>';
}
