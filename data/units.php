<?php

declare(strict_types=1);

/**
 * =============================================================================
 * UNIDADES DA DOLCE DELÍCIAS — matriz + 5 lojas
 * =============================================================================
 *
 * Alimenta a seção "Nossas unidades", o seletor de unidade do topo e o link de
 * WhatsApp do checkout. O array inteiro também é publicado como JSON dentro da
 * página (<script id="units-data">) para o JavaScript montar o link do WhatsApp
 * da unidade escolhida.
 *
 * >>> TUDO QUE ESTÁ MARCADO COM "PREENCHER" É PLACEHOLDER <<<
 * Substitua pelos dados reais antes de publicar. Em especial:
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
 *   'slug'        string  usado no seletor e no nome do PDF
 *   'nome'        string
 *   'endereco'    string
 *   'whatsapp'    string  só dígitos: 55 + DDD + número
 *   'horario'     string
 *   'mapaUrl'     string
 *   'imagem'      string
 *   'catalogoPdf' string
 *   'matriz'      bool    exatamente uma unidade deve ter true
 */

return [
    [
        'id'          => 1,
        'slug'        => 'matriz',
        'nome'        => 'Matriz — PREENCHER bairro',
        'endereco'    => 'PREENCHER: Rua Exemplo, 000 — Centro, Cidade/UF',
        'whatsapp'    => '55000000000', // PREENCHER
        'horario'     => 'Seg a sex, 6h às 20h · Sáb e dom, 6h às 14h', // PREENCHER
        'mapaUrl'     => 'https://maps.google.com/?q=PREENCHER', // PREENCHER
        'imagem'      => '/assets/img/unidades/matriz.jpg', // PREENCHER
        'catalogoPdf' => '/catalogos/matriz.pdf',
        'matriz'      => true,
    ],

    [
        'id'          => 2,
        'slug'        => 'unidade-2',
        'nome'        => 'Unidade 2 — PREENCHER bairro',
        'endereco'    => 'PREENCHER: Av. Exemplo, 000 — Bairro, Cidade/UF',
        'whatsapp'    => '55000000000', // PREENCHER
        'horario'     => 'Seg a sáb, 6h às 20h', // PREENCHER
        'mapaUrl'     => 'https://maps.google.com/?q=PREENCHER', // PREENCHER
        'imagem'      => '/assets/img/unidades/unidade-2.jpg', // PREENCHER
        'catalogoPdf' => '/catalogos/unidade-2.pdf',
        'matriz'      => false,
    ],

    [
        'id'          => 3,
        'slug'        => 'unidade-3',
        'nome'        => 'Unidade 3 — PREENCHER bairro',
        'endereco'    => 'PREENCHER: Rua Exemplo, 000 — Bairro, Cidade/UF',
        'whatsapp'    => '55000000000', // PREENCHER
        'horario'     => 'Seg a sáb, 6h às 20h', // PREENCHER
        'mapaUrl'     => 'https://maps.google.com/?q=PREENCHER', // PREENCHER
        'imagem'      => '/assets/img/unidades/unidade-3.jpg', // PREENCHER
        'catalogoPdf' => '/catalogos/unidade-3.pdf',
        'matriz'      => false,
    ],

    [
        'id'          => 4,
        'slug'        => 'unidade-4',
        'nome'        => 'Unidade 4 — PREENCHER bairro',
        'endereco'    => 'PREENCHER: Rua Exemplo, 000 — Bairro, Cidade/UF',
        'whatsapp'    => '55000000000', // PREENCHER
        'horario'     => 'Seg a sáb, 6h às 20h', // PREENCHER
        'mapaUrl'     => 'https://maps.google.com/?q=PREENCHER', // PREENCHER
        'imagem'      => '/assets/img/unidades/unidade-4.jpg', // PREENCHER
        'catalogoPdf' => '/catalogos/unidade-4.pdf',
        'matriz'      => false,
    ],

    [
        'id'          => 5,
        'slug'        => 'unidade-5',
        'nome'        => 'Unidade 5 — PREENCHER bairro',
        'endereco'    => 'PREENCHER: Av. Exemplo, 000 — Bairro, Cidade/UF',
        'whatsapp'    => '55000000000', // PREENCHER
        'horario'     => 'Seg a sáb, 6h às 20h', // PREENCHER
        'mapaUrl'     => 'https://maps.google.com/?q=PREENCHER', // PREENCHER
        'imagem'      => '/assets/img/unidades/unidade-5.jpg', // PREENCHER
        'catalogoPdf' => '/catalogos/unidade-5.pdf',
        'matriz'      => false,
    ],

    [
        'id'          => 6,
        'slug'        => 'unidade-6',
        'nome'        => 'Unidade 6 — PREENCHER bairro',
        'endereco'    => 'PREENCHER: Rua Exemplo, 000 — Bairro, Cidade/UF',
        'whatsapp'    => '55000000000', // PREENCHER
        'horario'     => 'Seg a sáb, 6h às 20h', // PREENCHER
        'mapaUrl'     => 'https://maps.google.com/?q=PREENCHER', // PREENCHER
        'imagem'      => '/assets/img/unidades/unidade-6.jpg', // PREENCHER
        'catalogoPdf' => '/catalogos/unidade-6.pdf',
        'matriz'      => false,
    ],
];
