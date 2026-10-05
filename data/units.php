<?php

declare(strict_types=1);

// LEGADO: fonte histórica exclusiva do seed. Produção consulta PostgreSQL.
// Os comentários originais abaixo descrevem a versão anterior.

/**
 * =============================================================================
 * UNIDADES DA DOLCE DELÍCIAS — matriz + 5 lojas
 * =============================================================================
 *
 * Alimenta a página de unidades (unidades.php), o menu de catálogos do topo e o
 * link de WhatsApp do checkout. O array inteiro também é publicado como JSON dentro
 * da página (<script id="units-data">) para o JavaScript montar o link do WhatsApp
 * da matriz, que é quem recebe pedido pelo site.
 *
 * >>> BAIRROS, ENDEREÇOS E TEXTOS SÃO FICTÍCIOS <<<
 * Foram inventados para o site não ficar com buracos. Substitua pelos dados
 * reais antes de publicar (o que está marcado com "A FAZER" ainda falta). Em
 * especial:
 *   - 'whatsapp'    : número real, só dígitos, com 55 + DDD (ex.: 5511987654321).
 *                     Enquanto for 55000000000, o botão de WhatsApp abre um chat
 *                     inválido — é de propósito, para ninguém publicar sem trocar.
 *   - 'mapaUrl'     : link do Google Maps da loja.
 *   - 'imagem'      : foto da fachada. Se o arquivo não existir, o site desenha
 *                     um placeholder no lugar.
 *   - 'catalogoPdf' : PDF do catálogo daquela unidade, em /catalogos/.
 *                     Veja catalogos/README.md.
 *
 * >>> INTEGRAÇÃO FUTURA <<<
 * Trocar este `return [...]` por uma consulta ao banco mantém o site funcionando
 * sem nenhuma outra alteração, desde que o formato dos campos seja o mesmo.
 *
 * -----------------------------------------------------------------------------
 * ESQUEMA
 * -----------------------------------------------------------------------------
 *   'id'          int
 *   'slug'        string  âncora em unidades.php e nome do arquivo PDF
 *   'nome'        string
 *   'endereco'    string
 *   'whatsapp'    string  só dígitos: 55 + DDD + número
 *   'horario'     string  RF-29. Texto livre — aparece em unidades.php, no rodapé
 *                         e na confirmação do pedido (partials/pedido-validacao.php)
 *   'preparo'     string  RF-30. Tempo mínimo para o pedido ficar pronto. Texto
 *                         livre, exibido junto do horário. Vazio esconde a linha.
 *   'sobre'       string  parágrafo de apresentação, mostrado em unidades.php
 *   'mapaUrl'     string
 *   'imagem'      string
 *   'catalogoPdf' string
 *   'avaliacao'   string  RF-27. Link onde o cliente avalia o ATENDIMENTO desta
 *                         loja (Google Maps, por exemplo). Vazio: o rodapé cai
 *                         para o WhatsApp da matriz.
 *   'canais'      array   RF-26. Outras plataformas de venda da loja, cada uma
 *                         ['nome' => string, 'url' => string]. Lista vazia
 *                         esconde o bloco inteiro — nada de link quebrado.
 *   'matriz'      bool    exatamente uma unidade deve ter true
 */

return [
    [
        'id'          => 1,
        'slug'        => 'matriz',
        'nome'        => 'Matriz — Centro',
        'endereco'    => 'Rua das Palmeiras, 410 — Centro',
        'whatsapp'    => '5543999259373', // WhatsApp oficial (perfil @dolcedeliciasoficial): recebe os pedidos do site
        'horario'     => 'Seg a sex, 6h às 20h · Sáb e dom, 6h às 14h', // FICTÍCIO
        'preparo'     => 'Encomendas com 48 horas de antecedência', // FICTÍCIO
        'sobre'       => 'A primeira loja e a cozinha central da Dolce. É daqui que saem as encomendas do site.', // FICTÍCIO
        'mapaUrl'     => '', // FICTÍCIO: sem endereço real, sem link de mapa
        'imagem'      => '/assets/img/unidades/matriz.jpg', // FICTÍCIO
        'catalogoPdf' => '/catalogos/matriz.pdf',
        'avaliacao'   => '', // A FAZER: link de avaliação da loja (Google Maps)
        'canais'      => [], // A FAZER: [['nome' => 'iFood', 'url' => 'https://...']]
        'matriz'      => true,
    ],

    [
        'id'          => 2,
        'slug'        => 'unidade-1',
        'nome'        => 'Unidade 1 — Jardim América',
        'endereco'    => 'Av. Brasil, 1.250 — Jardim América',
        'whatsapp'    => '55000000000', // A FAZER: número real (o de exemplo não vira botão)
        'horario'     => 'Seg a sáb, 6h às 20h', // FICTÍCIO
        'preparo'     => 'Encomendas com 48 horas de antecedência', // FICTÍCIO
        'sobre'       => 'Atende as escolas do bairro com o lanche do recreio.', // FICTÍCIO
        'mapaUrl'     => '', // FICTÍCIO: sem endereço real, sem link de mapa
        'imagem'      => '/assets/img/unidades/unidade-1.jpg', // FICTÍCIO
        'catalogoPdf' => '/catalogos/unidade-1.pdf',
        'avaliacao'   => '', // A FAZER: link de avaliação da loja (Google Maps)
        'canais'      => [], // A FAZER: [['nome' => 'iFood', 'url' => 'https://...']]
        'matriz'      => false,
    ],

    [
        'id'          => 3,
        'slug'        => 'unidade-2',
        'nome'        => 'Unidade 2 — Vila Nova',
        'endereco'    => 'Rua Sete de Setembro, 88 — Vila Nova',
        'whatsapp'    => '55000000000', // A FAZER: número real (o de exemplo não vira botão)
        'horario'     => 'Seg a sáb, 6h às 20h', // FICTÍCIO
        'preparo'     => 'Encomendas com 48 horas de antecedência', // FICTÍCIO
        'sobre'       => 'Perto do terminal de ônibus: o movimento forte é no café da manhã, com pão de queijo e salgado assado saindo cedo.', // FICTÍCIO
        'mapaUrl'     => '', // FICTÍCIO: sem endereço real, sem link de mapa
        'imagem'      => '/assets/img/unidades/unidade-2.jpg', // FICTÍCIO
        'catalogoPdf' => '/catalogos/unidade-2.pdf',
        'avaliacao'   => '', // A FAZER: link de avaliação da loja (Google Maps)
        'canais'      => [], // A FAZER: [['nome' => 'iFood', 'url' => 'https://...']]
        'matriz'      => false,
    ],

    [
        'id'          => 4,
        'slug'        => 'unidade-3',
        'nome'        => 'Unidade 3 — Santa Mônica',
        'endereco'    => 'Rua das Acácias, 302 — Santa Mônica',
        'whatsapp'    => '55000000000', // A FAZER: número real (o de exemplo não vira botão)
        'horario'     => 'Seg a sáb, 6h às 20h', // FICTÍCIO
        'preparo'     => 'Encomendas com 48 horas de antecedência', // FICTÍCIO
        'sobre'       => 'Loja com mesas na calçada, café e salgado quente o dia todo.', // FICTÍCIO
        'mapaUrl'     => '', // FICTÍCIO: sem endereço real, sem link de mapa
        'imagem'      => '/assets/img/unidades/unidade-3.jpg', // FICTÍCIO
        'catalogoPdf' => '/catalogos/unidade-3.pdf',
        'avaliacao'   => '', // A FAZER: link de avaliação da loja (Google Maps)
        'canais'      => [], // A FAZER: [['nome' => 'iFood', 'url' => 'https://...']]
        'matriz'      => false,
    ],

    [
        'id'          => 5,
        'slug'        => 'unidade-4',
        'nome'        => 'Unidade 4 — Boa Vista',
        'endereco'    => 'Av. Independência, 2.040 — Boa Vista',
        'whatsapp'    => '55000000000', // A FAZER: número real (o de exemplo não vira botão)
        'horario'     => 'Seg a sáb, 6h às 20h', // FICTÍCIO
        'preparo'     => 'Encomendas com 48 horas de antecedência', // FICTÍCIO
        'sobre'       => 'A maior depois da matriz, com vitrine de doces.', // FICTÍCIO
        'mapaUrl'     => '', // FICTÍCIO: sem endereço real, sem link de mapa
        'imagem'      => '/assets/img/unidades/unidade-4.jpg', // FICTÍCIO
        'catalogoPdf' => '/catalogos/unidade-4.pdf',
        'avaliacao'   => '', // A FAZER: link de avaliação da loja (Google Maps)
        'canais'      => [], // A FAZER: [['nome' => 'iFood', 'url' => 'https://...']]
        'matriz'      => false,
    ],

    [
        'id'          => 6,
        'slug'        => 'unidade-5',
        'nome'        => 'Unidade 5 — Parque das Flores',
        'endereco'    => 'Rua dos Ipês, 57 — Parque das Flores',
        'whatsapp'    => '55000000000', // A FAZER: número real (o de exemplo não vira botão)
        'horario'     => 'Seg a sáb, 6h às 20h', // FICTÍCIO
        'preparo'     => 'Encomendas com 48 horas de antecedência', // FICTÍCIO
        'sobre'       => 'A loja mais nova, perto das faculdades. Café, salgado e lanche rápido entre as aulas.', // FICTÍCIO
        'mapaUrl'     => '', // FICTÍCIO: sem endereço real, sem link de mapa
        'imagem'      => '/assets/img/unidades/unidade-5.jpg', // FICTÍCIO
        'catalogoPdf' => '/catalogos/unidade-5.pdf',
        'avaliacao'   => '', // A FAZER: link de avaliação da loja (Google Maps)
        'canais'      => [], // A FAZER: [['nome' => 'iFood', 'url' => 'https://...']]
        'matriz'      => false,
    ],
];
