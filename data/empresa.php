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
 * >>> TUDO QUE ESTÁ MARCADO COM "PREENCHER" É PLACEHOLDER <<<
 * Nada aqui é informação real sobre a padaria. Datas, números e trajetória
 * precisam vir do cliente — inventar história de empresa é pior que deixar o
 * espaço em branco.
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
 *   'portfolio'      array   RF-22: serviços, cada um 'titulo', 'texto', 'itens'
 *   'empresas'       array   RF-19: 'chamada', 'texto', 'itens', 'comoFunciona'
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
            'PREENCHER: um parágrafo sobre quem é a Dolce Delícias hoje — o que a padaria faz, para quem, e o que ela entrega de diferente de uma padaria de esquina.',
            'PREENCHER: um segundo parágrafo sobre a atuação — as regiões e cidades atendidas, o tipo de cliente (escolas, faculdades, empresas, festas de família) e o que a empresa quer ser nos próximos anos.',
        ],

        // RNF-11: os valores da marca — comida feita com amor, pouco
        // industrializada, buscando ser saudável. Estes três vieram do
        // requisito; confirme o texto com o cliente.
        'valores' => [
            [
                'titulo' => 'Feito com amor',
                'texto'  => 'PREENCHER: o que isso quer dizer na prática — massa aberta na hora, receita da família, quem está na cozinha.',
            ],
            [
                'titulo' => 'Pouco industrializado',
                'texto'  => 'PREENCHER: o que a padaria faz em vez de comprar pronto, e quais ingredientes ela não usa.',
            ],
            [
                'titulo' => 'Buscando ser saudável',
                'texto'  => 'PREENCHER: as opções assadas, integrais, veganas ou sem lactose — e o que ainda está sendo desenvolvido.',
            ],
        ],
    ],

    /* ---------------------------------------------------------------------
     * OS NÚMEROS DA MARCA
     * O número de unidades NÃO entra aqui: ele é contado de data/units.php em
     * sobre.php, senão os dois divergem no dia em que abrir a próxima loja.
     * ------------------------------------------------------------------ */
    'numeros' => [
        ['valor' => 'PREENCHER', 'rotulo' => 'anos de história'],
        ['valor' => 'PREENCHER', 'rotulo' => 'salgados por dia'],
        ['valor' => 'PREENCHER', 'rotulo' => 'escolas e empresas atendidas'],
    ],

    /* ---------------------------------------------------------------------
     * RF-21 — HISTÓRIA
     * Ordem cronológica: o primeiro item é o começo de tudo.
     * ------------------------------------------------------------------ */
    'historia' => [
        [
            'ano'    => 'PREENCHER',
            'titulo' => 'O começo',
            'texto'  => 'PREENCHER: como a Dolce Delícias nasceu — quem começou, onde, e o que vendia no primeiro dia.',
        ],
        [
            'ano'    => 'PREENCHER',
            'titulo' => 'A primeira loja',
            'texto'  => 'PREENCHER: quando saiu de casa e virou ponto, e o que mudou no dia a dia da família.',
        ],
        [
            'ano'    => 'PREENCHER',
            'titulo' => 'As encomendas',
            'texto'  => 'PREENCHER: quando as escolas e as empresas começaram a pedir por cento, e como isso virou o carro-chefe.',
        ],
        [
            'ano'    => 'PREENCHER',
            'titulo' => 'Hoje',
            'texto'  => 'PREENCHER: onde a padaria chegou — quantas lojas, quantas pessoas na equipe, o que se faz de melhor.',
        ],
    ],

    /* ---------------------------------------------------------------------
     * RF-22 — PORTFÓLIO
     * 'itens' é opcional: entra como lista de marcadores dentro do cartão.
     * ------------------------------------------------------------------ */
    'portfolio' => [
        [
            'titulo' => 'Encomendas por cento',
            'texto'  => 'PREENCHER: o carro-chefe — salgados assados e fritos, doces e mini lanches, para festa, escola e evento.',
            'itens'  => ['Salgados fritos', 'Assados', 'Mini lanches', 'Doces de festa'], // PREENCHER
        ],
        [
            'titulo' => 'Coffee break e eventos',
            'texto'  => 'PREENCHER: o serviço para empresa e faculdade — bandeja montada, quantas pessoas atende, se inclui bebida.',
            'itens'  => ['Bandeja montada', 'Café e sucos', 'Montagem no local'], // PREENCHER
        ],
        [
            'titulo' => 'Padaria e balcão',
            'texto'  => 'PREENCHER: o que se acha na loja todo dia — pães, bolos, lanches, almoço executivo.',
            'itens'  => ['Pães e bolos', 'Lanches', 'Almoço executivo', 'Cafeteria'], // PREENCHER
        ],
        [
            'titulo' => 'Lanche escolar',
            'texto'  => 'PREENCHER: o atendimento a escolas — entrega recorrente, cardápio combinado, porção individual.',
            'itens'  => ['Entrega recorrente', 'Porção individual'], // PREENCHER
        ],
    ],

    /* ---------------------------------------------------------------------
     * RF-19 — PARA EMPRESAS E INDÚSTRIAS
     * ------------------------------------------------------------------ */
    'empresas' => [
        'chamada' => 'Sua empresa, abastecida todo mês',

        'texto' => 'PREENCHER: o que a Dolce Delícias oferece para empresa e indústria — volume que dá conta, frequência de entrega, prazo de faturamento, se emite nota, se tem contrato mensal.',

        'itens' => [
            [
                'titulo' => 'Coffee break de reunião',
                'texto'  => 'PREENCHER: tamanho mínimo, o que vem na bandeja, prazo para pedir.',
            ],
            [
                'titulo' => 'Lanche de turno',
                'texto'  => 'PREENCHER: o atendimento a indústria — entrega recorrente, horário, embalagem individual.',
            ],
            [
                'titulo' => 'Confraternização',
                'texto'  => 'PREENCHER: festa de fim de ano e comemoração de equipe, do orçamento à montagem.',
            ],
        ],

        // Os passos do atendimento B2B. Ficam aqui porque são texto de negócio,
        // não estrutura de página.
        'comoFunciona' => [
            'PREENCHER: como pedir um orçamento (o que a empresa precisa informar).',
            'PREENCHER: em quanto tempo a matriz responde com a proposta.',
            'PREENCHER: como fica o combinado de entrega e pagamento.',
        ],
    ],
];
