<?php

declare(strict_types=1);

/**
 * =============================================================================
 * CATÁLOGO DA DOLCE DELÍCIAS
 * =============================================================================
 *
 * Esta é a ÚNICA fonte de produtos do site. Todo o catálogo, a busca, os filtros
 * de categoria e a página de produto são gerados a partir daqui — não existe
 * lista de categorias fixa em lugar nenhum do código.
 *
 * >>> INTEGRAÇÃO FUTURA <<<
 * Quando existir back-end, troque este `return [...]` por uma consulta ao banco
 * ou por uma chamada de API que devolva um array com o MESMO formato. Nenhuma
 * outra parte do site precisa mudar.
 *
 * -----------------------------------------------------------------------------
 * ESQUEMA DE CADA PRODUTO
 * -----------------------------------------------------------------------------
 *   'id'         int     obrigatório. Identificador estável.
 *   'slug'       string  obrigatório. Vira a URL: produto.php?slug=pao-de-queijo
 *   'nome'       string  obrigatório.
 *   'descricao'  string  obrigatório. 1–2 frases; aparece no card e na página.
 *   'categoria'  string  obrigatório. AS CATEGORIAS SÃO DERIVADAS DESTE CAMPO.
 *                        Escrever uma categoria nova aqui já cria o filtro dela.
 *                        A ordem dos botões segue a ordem de aparição no array.
 *   'imagem'     string  caminho a partir da raiz do site. Se o arquivo não
 *                        existir, o site desenha um placeholder — pode deixar
 *                        apontando para a foto que ainda vai chegar.
 *   'destaque'   bool    true = ganha selo "mais pedido" e entra na vitrine.
 *   'precos'     array   lista de faixas de preço. Cada faixa:
 *                          'valor'     float  preço em reais DA EMBALAGEM inteira
 *                          'por'       string "100 unidades" | "unidade" | "kg" | "45 unidades"
 *                          'minPedido' int?   pedido mínimo, EM PEÇAS (opcional)
 *                          'passo'     int?   incremento do seletor de quantidade
 *                                             (opcional — se faltar, o site calcula)
 *                          'rotulo'    string? nome da variação, quando houver
 *                                              mais de uma faixa (ex.: "Caixa 200")
 *   'sabores'    array?  lista de strings. Aparece na página do produto.
 *   'tags'       array?  livre. 'atacado' e 'varejo' alimentam o seletor
 *                        Encomendas / Balcão do catálogo.
 *                        RF-24: 'vegano' e 'sem lactose' viram chips de filtro
 *                        no catálogo. Escrever a tag num produto já cria o chip;
 *                        a lista fica em dd_restricoes(), em partials/bootstrap.php.
 *   'unidades'   array?  RF-25. Slugs das lojas que vendem este item (o slug vem
 *                        de data/units.php). NÃO afeta a interface: o catálogo do
 *                        site é único e o preço também (RF-06 revisado). Existe
 *                        para a geração futura do PDF por unidade. Ausente ou
 *                        vazio significa "todas as lojas".
 *   'disponivel' bool?   default true. false = card fica esmaecido e sem botão.
 *
 * -----------------------------------------------------------------------------
 * COMO ADICIONAR UM PRODUTO
 * -----------------------------------------------------------------------------
 * Copie qualquer bloco abaixo, troque 'id' e 'slug' por valores únicos e salve.
 * Nada mais precisa ser tocado.
 *
 * ATENÇÃO: os itens abaixo são uma AMOSTRA (o cardápio real é bem maior e o
 * cliente ainda vai enviar o restante). As descrições e os sabores marcados com
 * "conferir" são texto de exemplo — confirme com a padaria antes de publicar.
 */

return [
    // =========================================================================
    // ENCOMENDAS / ATACADO — vendido por cento, para escolas, faculdades e festas
    // =========================================================================

    [
        'id'         => 1,
        'slug'       => 'pao-de-queijo',
        'nome'       => 'Pão de Queijo',
        'descricao'  => 'Massa de polvilho com queijo curado, assado na hora. Sai do forno crocante por fora e macio por dentro.',
        'categoria'  => 'Assados',
        'imagem'     => '/assets/img/produtos/pao-de-queijo.jpg', // FOTO REAL: substituir
        'destaque'   => true,
        'precos'     => [
            ['valor' => 120.00, 'por' => '100 unidades', 'minPedido' => 50],
        ],
        'tags'       => ['atacado', 'coffee break', 'sem carne'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 2,
        'slug'       => 'mini-pizza',
        'nome'       => 'Mini Pizza',
        'descricao'  => 'Disco individual com molho de tomate caseiro e muçarela. Vai ao forno e já pode ir para a bandeja.',
        'categoria'  => 'Assados',
        'imagem'     => '/assets/img/produtos/mini-pizza.jpg',
        'destaque'   => true,
        'precos'     => [
            ['valor' => 220.00, 'por' => '100 unidades'],
        ],
        'sabores'    => ['Muçarela', 'Calabresa', 'Frango com catupiry'], // conferir
        'tags'       => ['atacado', 'festa'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 3,
        'slug'       => 'empadinha',
        'nome'       => 'Empadinha',
        'descricao'  => 'Massa amanteigada que desmancha na boca, recheio generoso até a borda.',
        'categoria'  => 'Assados',
        'imagem'     => '/assets/img/produtos/empadinha.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 220.00, 'por' => '100 unidades'],
        ],
        'sabores'    => ['Frango', 'Palmito', 'Camarão'], // conferir
        'tags'       => ['atacado', 'festa'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 4,
        'slug'       => 'assadinhos',
        'nome'       => 'Assadinhos',
        'descricao'  => 'Mix de salgados assados em massa leve — a opção mais pedida por quem quer fugir da fritura.',
        'categoria'  => 'Assados',
        'imagem'     => '/assets/img/produtos/assadinhos.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 160.00, 'por' => '100 unidades'],
        ],
        'sabores'    => ['Frango', 'Carne', 'Presunto e queijo'], // conferir
        'tags'       => ['atacado', 'escola'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 5,
        'slug'       => 'mini-esfirras',
        'nome'       => 'Mini Esfirras',
        'descricao'  => 'Abertas, do tamanho de uma mordida, com recheio temperado na hora.',
        'categoria'  => 'Assados',
        'imagem'     => '/assets/img/produtos/mini-esfirras.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 160.00, 'por' => '100 unidades'],
        ],
        'sabores'    => ['Carne', 'Frango', 'Queijo'], // conferir
        'tags'       => ['atacado', 'festa'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 6,
        'slug'       => 'salgados-fritos',
        'nome'       => 'Salgados Fritos',
        'descricao'  => 'O cento clássico de festa. Você escolhe os sabores na hora da encomenda e a gente monta a bandeja.',
        'categoria'  => 'Salgados',
        'imagem'     => '/assets/img/produtos/salgados-fritos.jpg',
        'destaque'   => true,
        'precos'     => [
            ['valor' => 85.00, 'por' => '100 unidades'],
        ],
        'sabores'    => [
            'Coxinha de frango',
            'Bolinha de queijo',
            'Pastel de carne',
            'Palitinho de carne',
            'Enroladinho de salsicha',
            'Trouxinha de presunto e queijo',
            'Quibe',
        ],
        'tags'       => ['atacado', 'festa', 'mais vendido'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 7,
        'slug'       => 'combo-festa',
        'nome'       => 'Combo Festa',
        'descricao'  => '45 peças montadas: 25 salgados fritos, 10 mini pizzas e 10 empadinhas. Resolve a mesa pequena.',
        'categoria'  => 'Combos',
        'imagem'     => '/assets/img/produtos/combo-festa.jpg',
        'destaque'   => true,
        'precos'     => [
            ['valor' => 60.00, 'por' => '45 unidades'],
        ],
        'tags'       => ['atacado', 'festa', 'combo'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 8,
        'slug'       => 'mini-hamburguer',
        'nome'       => 'Mini Hambúrguer',
        'descricao'  => 'Pão brioche pequeno, hambúrguer artesanal e queijo derretido. Entregue montado e embalado.',
        'categoria'  => 'Lanches',
        'imagem'     => '/assets/img/produtos/mini-hamburguer.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 4.20, 'por' => 'unidade', 'minPedido' => 25],
        ],
        'tags'       => ['atacado', 'festa', 'faculdade'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 9,
        'slug'       => 'mini-sanduiche-pate-frango',
        'nome'       => 'Mini Sanduíche com Patê de Frango',
        'descricao'  => 'Pão macio sem casca e patê de frango feito na casa. Clássico de coffee break e reunião.',
        'categoria'  => 'Lanches',
        'imagem'     => '/assets/img/produtos/mini-sanduiche.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 3.20, 'por' => 'unidade', 'minPedido' => 25],
        ],
        'tags'       => ['atacado', 'coffee break'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 10,
        'slug'       => 'cookies',
        'nome'       => 'Cookies',
        'descricao'  => 'Casquinha crocante e miolo úmido, com gotas de chocolate meio amargo.',
        'categoria'  => 'Doces',
        'imagem'     => '/assets/img/produtos/cookies.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 120.00, 'por' => '100 unidades'],
        ],
        'tags'       => ['atacado', 'coffee break'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 11,
        'slug'       => 'cupcakes',
        'nome'       => 'Cupcakes',
        'descricao'  => 'Bolinho macio com cobertura em bico. A gente ajusta a cor do chantilly ao tema da festa.',
        'categoria'  => 'Doces',
        'imagem'     => '/assets/img/produtos/cupcakes.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 350.00, 'por' => '100 unidades', 'minPedido' => 30],
        ],
        'sabores'    => ['Baunilha', 'Chocolate', 'Red velvet'], // conferir
        'tags'       => ['atacado', 'festa', 'personalizado'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    // =========================================================================
    // BALCÃO / VAREJO — venda por unidade nas lojas
    // Este cardápio está INCOMPLETO de propósito: é o esqueleto para o cliente
    // preencher com o balcão inteiro (padaria, lanchonete e almoço).
    // =========================================================================

    [
        'id'         => 20,
        'slug'       => 'coxinha-balcao',
        'nome'       => 'Coxinha',
        'descricao'  => 'Frango desfiado temperado, massa fina e fritura na hora do pedido.',
        'categoria'  => 'Salgados',
        'imagem'     => '/assets/img/produtos/coxinha.jpg',
        'destaque'   => true,
        'precos'     => [
            ['valor' => 8.00, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 21,
        'slug'       => 'empadinha-balcao',
        'nome'       => 'Empadinha de Frango',
        'descricao'  => 'A mesma empadinha do cento, vendida quentinha na vitrine.',
        'categoria'  => 'Salgados',
        'imagem'     => '/assets/img/produtos/empadinha-balcao.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 9.00, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 22,
        'slug'       => 'fatia-de-bolo',
        'nome'       => 'Fatia de Bolo',
        'descricao'  => 'Fatia generosa do bolo do dia. Pergunte qual saiu hoje.',
        'categoria'  => 'Doces',
        'imagem'     => '/assets/img/produtos/fatia-de-bolo.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 4.00, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 23,
        'slug'       => 'cafe-expresso',
        'nome'       => 'Café Expresso',
        'descricao'  => 'Grão torrado na semana, extraído na xícara pequena.',
        'categoria'  => 'Bebidas',
        'imagem'     => '/assets/img/produtos/cafe-expresso.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 6.99, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão', 'vegano', 'sem lactose'], // conferir
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 24,
        'slug'       => 'suco-maracuja',
        'nome'       => 'Suco de Maracujá',
        'descricao'  => 'Polpa batida na hora, copo de 400 ml.',
        'categoria'  => 'Bebidas',
        'imagem'     => '/assets/img/produtos/suco-maracuja.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 8.00, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão', 'vegano', 'sem lactose'], // conferir
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 25,
        'slug'       => 'strogonoff-executivo',
        'nome'       => 'Strogonoff Executivo',
        'descricao'  => 'Prato do dia com arroz, batata palha e salada. Servido no almoço, enquanto durar.',
        'categoria'  => 'Almoço',
        'imagem'     => '/assets/img/produtos/strogonoff.jpg',
        'destaque'   => true,
        'precos'     => [
            ['valor' => 20.00, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão', 'almoço'],
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    [
        'id'         => 26,
        'slug'       => 'coca-cola-lata',
        'nome'       => 'Coca-Cola Lata',
        'descricao'  => 'Lata de 350 ml, sempre gelada.',
        'categoria'  => 'Bebidas',
        'imagem'     => '/assets/img/produtos/coca-lata.jpg',
        'destaque'   => false,
        'precos'     => [
            ['valor' => 6.00, 'por' => 'unidade'],
        ],
        'tags'       => ['varejo', 'balcão', 'vegano', 'sem lactose'], // conferir
        'unidades'   => ['matriz', 'unidade-1', 'unidade-2', 'unidade-3', 'unidade-4', 'unidade-5'], // PREENCHER: quais lojas vendem
        'disponivel' => true,
    ],

    // >>> PRÓXIMOS PRODUTOS ENTRAM AQUI <<<
    // Ainda faltam, entre outros: pães (francês, doce, integral), bolos inteiros,
    // tortas, doces de festa (brigadeiro, beijinho), sanduíches de balcão,
    // vitaminas, achocolatados e o resto do cardápio de almoço.
    // Basta copiar um bloco acima e trocar id, slug, nome, categoria e preço.
];
