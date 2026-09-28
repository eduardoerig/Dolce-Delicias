<?php

declare(strict_types=1);

/**
 * =============================================================================
 * SANITY — leitura do conteúdo cadastrado no Studio (studio-dolce-delícias/)
 * =============================================================================
 * Produtos, categorias, unidades e promoções são editados no Sanity Studio e
 * lidos aqui pela API HTTP de consulta (GROQ), sem SDK e sem token: o dataset
 * é público e só devolve documentos PUBLICADOS.
 *
 * Cada consulta devolve os campos no MESMO formato dos arrays de data/*.php,
 * então o resto do site não sabe de onde os dados vieram.
 *
 * Se o Sanity estiver fora do ar, ou a consulta falhar, dd_sanity() devolve
 * null e o bootstrap cai para os arquivos de data/*.php. É a rede de segurança,
 * não a fonte: o que vale é o que está publicado no Studio.
 * -------------------------------------------------------------------------- */

const DD_SANITY_PROJECT_ID  = 'qup78m3c';
const DD_SANITY_DATASET     = 'production';
const DD_SANITY_API_VERSION = '2026-09-28'; // data fixa de propósito — não atualizar sozinha

/**
 * Executa uma consulta GROQ e devolve o `result`, ou null se algo der errado.
 * Usa o CDN da API (apicdn): rápido e com cache curto, adequado para o site.
 */
function dd_sanity(string $groq, array $params = []): ?array
{
    $query = ['query' => $groq];
    foreach ($params as $nome => $valor) {
        $query['$' . $nome] = json_encode($valor, JSON_UNESCAPED_UNICODE);
    }

    $url = sprintf(
        'https://%s.apicdn.sanity.io/v%s/data/query/%s?%s',
        DD_SANITY_PROJECT_ID,
        DD_SANITY_API_VERSION,
        DD_SANITY_DATASET,
        http_build_query($query, '', '&', PHP_QUERY_RFC3986)
    );

    $corpo = @file_get_contents($url, false, stream_context_create([
        'http' => ['timeout' => 5, 'ignore_errors' => true, 'header' => "Accept: application/json\r\n"],
    ]));
    if ($corpo === false) {
        error_log('[sanity] sem resposta da API');
        return null;
    }

    $json = json_decode($corpo, true);
    if (!is_array($json) || !array_key_exists('result', $json) || !is_array($json['result'])) {
        error_log('[sanity] resposta inesperada: ' . substr($corpo, 0, 300));
        return null;
    }

    return $json['result'];
}

/* -----------------------------------------------------------------------------
 * CONSULTAS — uma por arquivo de data/*.php que elas substituem
 * -------------------------------------------------------------------------- */

/** Foto do Sanity já redimensionada pelo CDN de imagens. */
const DD_SANITY_IMG = '.asset->url + "?w=1200&auto=format&fit=max"';

/**
 * Mesmo formato de data/products.php. A linha (atacado/varejo) e as restrições
 * marcadas voltam como tags, que é como o site as lê. A ordem é a arrastada no
 * Studio (categoria, depois produto), e é ela que define a ordem dos botões de
 * filtro (dd_categorias()).
 */
function dd_sanity_produtos(): ?array
{
    return dd_sanity('*[_type == "product" && defined(slug.current)]
        | order(categoria->orderRank asc, orderRank asc, nome asc) {
            "id": _id,
            "slug": slug.current,
            nome,
            descricao,
            "categoria": categoria->nome,
            "imagem": imagem' . DD_SANITY_IMG . ',
            "destaque": coalesce(destaque, false),
            "precos": precos[]{ valor, por, minPedido, passo, rotulo },
            "sabores": coalesce(sabores, []),
            "tags": array::compact([linha] + coalesce(restricoes, []) + coalesce(tags, [])),
            "unidades": coalesce(unidades[]->slug.current, []),
            "disponivel": coalesce(disponivel, true)
        }');
}

/** Mesmo formato de data/units.php. */
function dd_sanity_unidades(): ?array
{
    return dd_sanity('*[_type == "store" && defined(slug.current)]
        | order(orderRank asc, nome asc) {
            "id": _id,
            "slug": slug.current,
            nome,
            "endereco": coalesce(endereco, ""),
            "whatsapp": coalesce(whatsapp, ""),
            "horario": coalesce(horario, ""),
            "preparo": coalesce(preparo, ""),
            "sobre": coalesce(sobre, ""),
            "mapaUrl": coalesce(mapaUrl, ""),
            "imagem": imagem' . DD_SANITY_IMG . ',
            "catalogoPdf": coalesce(catalogoPdf, ""),
            "avaliacao": coalesce(avaliacao, ""),
            "canais": coalesce(canais[]{ nome, url }, []),
            "matriz": coalesce(matriz, false)
        }');
}

/** Mesmo formato de data/promocoes.php. */
function dd_sanity_promocoes(): ?array
{
    return dd_sanity('*[_type == "promotion" && defined(slug.current)]
        | order(_createdAt asc) {
            "id": _id,
            "slug": slug.current,
            titulo,
            "selo": coalesce(selo, ""),
            "texto": coalesce(texto, ""),
            "quando": coalesce(quando{ tipo, dias, meses, de, ate }, { "tipo": "sempre" }),
            "ativo": coalesce(ativo, true)
        }');
}
