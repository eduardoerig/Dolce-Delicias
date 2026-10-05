<?php

declare(strict_types=1);

/**
 * =============================================================================
 * A EMPRESA — conteúdo institucional da Dolce Delícias
 * =============================================================================
 *
 * Alimenta sobre.php inteira. Quatro requisitos moram aqui:
 *
 *   RF-21  história da empresa — origem, trajetória e evolução
 *   RF-22  portfólio — serviços, produtos e locais de atuação
 *   RF-31  institucional — quem somos, atuação e objetivos
 *   RF-19  divulgação para empresas e indústrias
 *
 * Está em data/ e não escrito dentro do template pelo mesmo motivo do catálogo:
 * quem vai reescrever esses textos é o cliente, não quem mexe em PHP. Acrescentar
 * um marco na linha do tempo ou um serviço no portfólio é copiar um bloco.
 *
 * >>> OS TEXTOS ABAIXO SÃO FICTÍCIOS <<<
 * Foram escritos para o site não ficar com buracos enquanto o cliente não
 * manda os dados reais. Datas, números, nomes e trajetória NÃO são da
 * Dolce Delícias: troque tudo pelo texto do cliente antes de publicar.
 *
 * >>> FORA DE ESCOPO <<<
 * RF-32 (divulgação de novos locais e expansão) e RF-33 (fábrica de congelados)
 * não entram. Se voltarem ao escopo, viram duas chaves novas neste arquivo e uma
 * seção em sobre.php — nada mais precisa mudar.
 *
 * -----------------------------------------------------------------------------
 * ESQUEMA
 * -----------------------------------------------------------------------------
 *   'institucional'  array   RF-31: 'chamada', 'texto' (parágrafos), 'valores'
 *   'numeros'        array   os números da marca: 'valor' + 'rotulo'
 *   'historia'       array   RF-21: marcos, cada um 'ano', 'titulo', 'texto'
 *   'portfolio'      array   RF-22: serviços, cada um 'titulo' e 'texto'
 *   'empresas'       array   RF-19: 'chamada', 'texto', 'itens'
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

        'texto' => [
            'A Dolce Delícias é uma padaria de família que virou cozinha de salgados. Hoje são seis lojas e uma cozinha central que prepara, todo dia, coxinha, esfirra, empada e pão de queijo para o balcão e para as encomendas por cento.',
            'Atendemos festas de família, escolas, faculdades e empresas da região. A massa é aberta na casa, o recheio é feito na nossa cozinha e o salgado frita ou assa perto da hora de sair. O plano é crescer sem perder isso.',
        ],

        // RNF-11: os valores da marca — comida feita com amor, pouco
        // industrializada, buscando ser saudável. Estes três vieram do
        // requisito; confirme o texto com o cliente.
        'valores' => [
            [
                'titulo' => 'Feito com amor',
                'texto'  => 'A receita da coxinha é a mesma desde a primeira fornada, e quem está na cozinha hoje aprendeu com quem começou.',
            ],
            [
                'titulo' => 'Pouco industrializado',
                'texto'  => 'Massa, recheio e molho são feitos aqui. Não compramos salgado congelado pronto para revender.',
            ],
            [
                'titulo' => 'Buscando ser saudável',
                'texto'  => 'Metade do cardápio é assada, e já há opções integrais. Uma linha sem lactose está em teste na matriz.',
            ],
        ],
    ],

    /* ---------------------------------------------------------------------
     * OS NÚMEROS DA MARCA
     * O número de unidades NÃO entra aqui: ele é contado de data/units.php em
     * sobre.php, senão os dois divergem no dia em que abrir a próxima loja.
     * ------------------------------------------------------------------ */
    'numeros' => [
        ['valor' => '18', 'rotulo' => 'anos de história'],
        ['valor' => '12 mil', 'rotulo' => 'salgados por dia'],
        ['valor' => '140', 'rotulo' => 'escolas e empresas atendidas'],
    ],

    /* ---------------------------------------------------------------------
     * RF-21 — HISTÓRIA
     * Ordem cronológica: o primeiro item é o começo de tudo.
     * ------------------------------------------------------------------ */
    'historia' => [
        [
            'ano'    => '2008',
            'titulo' => 'O começo',
            'texto'  => 'A família começa a fazer coxinha e empada na cozinha de casa, por encomenda, para festas de vizinhos e amigos.',
        ],
        [
            'ano'    => '2012',
            'titulo' => 'A primeira loja',
            'texto'  => 'As encomendas não cabem mais na cozinha de casa. Abre a loja do Centro, que hoje é a matriz, com balcão e café.',
        ],
        [
            'ano'    => '2016',
            'titulo' => 'As encomendas',
            'texto'  => 'Escolas e empresas começam a pedir salgado por cento toda semana. A matriz ganha uma cozinha só para as encomendas.',
        ],
        [
            'ano'    => '2026',
            'titulo' => 'Hoje',
            'texto'  => 'São seis lojas e cerca de 60 pessoas na equipe. A coxinha continua sendo o salgado mais pedido.',
        ],
    ],

    /* ---------------------------------------------------------------------
     * RF-22 — PORTFÓLIO
     * ------------------------------------------------------------------ */
    'portfolio' => [
        [
            'titulo' => 'Encomendas por cento',
            'texto'  => 'O carro-chefe: salgados fritos e assados, docinhos e mini lanches, montados em bandeja para festa, escola e evento.',
        ],
        [
            'titulo' => 'Coffee break e eventos',
            'texto'  => 'Bandejas de salgado, doce e pão de queijo para reunião, palestra e formatura, de 10 a 300 pessoas. Café e suco sob encomenda.',
        ],
        [
            'titulo' => 'Padaria e balcão',
            'texto'  => 'Nas seis lojas: salgado saindo quente, pão de queijo, bolo, lanche e café, todo dia.',
        ],
        [
            'titulo' => 'Lanche escolar',
            'texto'  => 'Lanche do recreio com cardápio combinado com a escola, porção individual e entrega nos dias marcados.',
        ],
    ],

    /* ---------------------------------------------------------------------
     * RF-19 — PARA EMPRESAS E INDÚSTRIAS
     * ------------------------------------------------------------------ */
    'empresas' => [
        'chamada' => 'Sua empresa, abastecida todo mês',

        'texto' => 'Para empresas e indústrias, a matriz monta um contrato mensal: entrega na frequência que a equipe precisa, nota fiscal e faturamento em até 28 dias.',

        'itens' => [
            [
                'titulo' => 'Coffee break de reunião',
                'texto'  => 'A partir de 10 pessoas: salgados, mini sanduíches e docinhos. Peça com 48 horas de antecedência.',
            ],
            [
                'titulo' => 'Lanche de turno',
                'texto'  => 'Kits individuais para a troca de turno, entregues no horário combinado, de segunda a sábado.',
            ],
            [
                'titulo' => 'Confraternização',
                'texto'  => 'Festa de fim de ano e comemoração de equipe: a matriz faz o orçamento e entrega a mesa montada.',
            ],
        ],
    ],
];
