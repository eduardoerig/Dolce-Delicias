<?php

declare(strict_types=1);

/**
 * =============================================================================
 * A EMPRESA — conteúdo institucional da Dolce Delícias
 * =============================================================================
 *
 * Alimenta sobre.php inteira. Dois requisitos moram aqui:
 *
 *   RF-22  portfólio — serviços, produtos e locais de atuação
 *   RF-31  institucional — quem somos, atuação e objetivos
 *
 * RF-21 (história) também, quando houver datas reais: a chave 'historia' está
 * vazia de propósito, e a seção some do site enquanto estiver assim.
 *
 * Está em data/ e não escrito dentro do template pelo mesmo motivo do catálogo:
 * quem vai reescrever esses textos é o cliente, não quem mexe em PHP. Acrescentar
 * um marco na linha do tempo ou um serviço no portfólio é copiar um bloco.
 *
 * >>> DE ONDE VEM CADA TEXTO <<<
 * Real, do perfil oficial @dolcedeliciasoficial no Instagram (out/2026): a
 * legenda de "Quem somos" e a assinatura (post de nov/2025), Toledo e Biopark,
 * os seguidores, as frentes do portfólio (festas, cantinas e Lancheira Feliz,
 * lanches lúdicos para crianças, café) e o @ do perfil.
 * De exemplo, ainda para confirmar com o cliente: os textos dos valores e o
 * detalhe de cada frente do portfólio.
 *
 * >>> FORA DE ESCOPO <<<
 * RF-32 (divulgação de novos locais e expansão) e RF-33 (fábrica de congelados)
 * não entram. Se voltarem ao escopo, viram duas chaves novas neste arquivo e uma
 * seção em sobre.php — nada mais precisa mudar.
 *
 * -----------------------------------------------------------------------------
 * ESQUEMA
 * -----------------------------------------------------------------------------
 *   'institucional'  array   RF-31: 'chamada', 'texto' (parágrafos),
 *                            'assinatura', 'valores'
 *   'numeros'        array   fatos curtos do herói: 'valor' + 'rotulo'
 *   'historia'       array   RF-21: marcos, cada um 'ano', 'titulo', 'texto'
 *   'portfolio'      array   RF-22: frentes, cada uma 'titulo' e 'texto'
 *   'contato'        array   'instagram' (só o @, sem a arroba) e 'lugares'
 *
 * Lista vazia esconde a seção correspondente em sobre.php — nenhuma seção fica
 * com título e nada embaixo.
 */

return [

    /* ---------------------------------------------------------------------
     * RF-31 — INSTITUCIONAL
     * ------------------------------------------------------------------ */
    'institucional' => [
        'chamada' => 'Comida de verdade, feita todo dia',

        // Texto oficial: legenda do post da marca no Instagram
        // (instagram.com/p/DQu_5Q7DAPS, nov/2025). A frase final do post
        // virou a 'assinatura', logo abaixo.
        'texto' => [
            'A Dolce Delícias é uma empresa que nasceu do zero, construída com dedicação, amor pela cozinha e muita vontade de fazer acontecer. O que começou como pequenas receitas feitas em casa virou uma marca reconhecida pelos seus pães, mini pizzas e salgados artesanais.',
            'Cada produto carrega o sabor do esforço, da paixão e do sonho que se tornou realidade.',
        ],

        // A assinatura da marca, do mesmo post.
        'assinatura' => 'Feita do zero, feita com coração',

        // RNF-11: os valores da marca — comida feita com amor, pouco
        // industrializada, buscando ser saudável. Estes três vieram do
        // requisito; confirme o texto com o cliente.
        'valores' => [
            [
                'titulo' => 'Feito com amor',
                'texto'  => 'Cada salgado é fechado à mão, com receita de casa e o mesmo cuidado de quando tudo cabia numa cozinha só.',
            ],
            [
                'titulo' => 'Pouco industrializado',
                'texto'  => 'A massa e o recheio são feitos na nossa cozinha. Não revendemos salgado congelado de fábrica.',
            ],
            [
                'titulo' => 'Buscando ser saudável',
                'texto'  => 'Já temos opções assadas e integrais, e seguimos testando receitas mais leves.',
            ],
        ],
    ],

    /* ---------------------------------------------------------------------
     * FATOS DO HERÓI
     * Só o que dá para conferir no perfil oficial. Atualize os seguidores de
     * vez em quando.
     * ------------------------------------------------------------------ */
    'numeros' => [
        ['valor' => 'Toledo', 'rotulo' => 'e Biopark'],
        ['valor' => '+4 mil', 'rotulo' => 'seguidores no Instagram'],
        ['valor' => 'Do zero', 'rotulo' => 'receita e massa feitas na casa'],
    ],

    /* ---------------------------------------------------------------------
     * RF-21 — HISTÓRIA
     * Ordem cronológica: o primeiro item é o começo de tudo. Vazia até o
     * cliente mandar as datas reais (a seção não aparece no site).
     * Exemplo de marco:
     *   ['ano' => '2019', 'titulo' => 'O começo', 'texto' => '...'],
     * ------------------------------------------------------------------ */
    'historia' => [],

    /* ---------------------------------------------------------------------
     * RF-22 — PORTFÓLIO
     * As três frentes que aparecem no perfil oficial. Os detalhes de cada
     * texto ainda são de exemplo.
     * ------------------------------------------------------------------ */
    'portfolio' => [
        [
            'titulo' => 'Salgados para festas',
            'texto'  => 'Salgados fritos e assados, mini pizzas e pães por cento, para aniversário, confraternização e evento. Peça pelo cardápio de festas.',
        ],
        [
            'titulo' => 'Lanche escolar',
            'texto'  => 'Cantinas e refeitórios de escola e a Lancheira Feliz, com lanches lúdicos pensados para as crianças.',
        ],
        [
            'titulo' => 'Pães e café',
            'texto'  => 'Pães, mini pizzas e, agora, café para acompanhar o pedido.',
        ],
    ],

    /* ---------------------------------------------------------------------
     * CONTATO
     * O WhatsApp não fica aqui: é o da matriz, cadastrado no painel.
     * ------------------------------------------------------------------ */
    'contato' => [
        'instagram' => 'dolcedeliciasoficial',
        'lugares'   => 'Toledo e Biopark',
    ],
];
