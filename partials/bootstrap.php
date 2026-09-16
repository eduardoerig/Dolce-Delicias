<?php

declare(strict_types=1);

/**
 * =============================================================================
 * BOOTSTRAP — carrega os dados e define os helpers usados pelos templates.
 * =============================================================================
 * Deve ser o primeiro require de toda página (index.php, produto.php, carrinho.php).
 */

if (defined('DD_BASE')) {
    return;
}

define('DD_BASE', dirname(__DIR__));

/* -----------------------------------------------------------------------------
 * FONTES DE DADOS
 * >>> INTEGRAÇÃO FUTURA <<<
 * Estas duas funções são o único ponto de contato entre o site e os dados.
 * Para plugar um back-end, troque o `require` por uma consulta ao banco/API que
 * devolva um array no mesmo formato — nada mais no projeto precisa mudar.
 * -------------------------------------------------------------------------- */

function dd_produtos(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = require DD_BASE . '/data/products.php';
    }
    return $cache;
}

function dd_unidades(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = require DD_BASE . '/data/units.php';
    }
    return $cache;
}

function dd_promocoes(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = require DD_BASE . '/data/promocoes.php';
    }
    return $cache;
}

function dd_empresa(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = require DD_BASE . '/data/empresa.php';
    }
    return $cache;
}

/**
 * A unidade que recebe o pedido do site — sempre a matriz.
 *
 * REGRA DE NEGÓCIO: pedido feito pelo site vai só para a matriz. As outras lojas
 * atendem por WhatsApp direto e têm o catálogo delas em PDF. É a mesma regra que
 * matriz() aplica em assets/js/cart.js — se um dia cada loja receber pedido, os
 * dois lugares mudam juntos.
 *
 * @return array|null null quando não há nenhuma unidade cadastrada.
 */
function dd_matriz(): ?array
{
    $unidades = dd_unidades();

    foreach ($unidades as $unidade) {
        if (!empty($unidade['matriz'])) {
            return $unidade;
        }
    }

    return $unidades[0] ?? null;
}

/* -----------------------------------------------------------------------------
 * CATÁLOGOS EM PDF
 * O arquivo pode não existir ainda (ver catalogos/README.md), então todo lugar
 * que oferece download passa por aqui antes de desenhar o link.
 * -------------------------------------------------------------------------- */

/**
 * O PDF existe no disco?
 *
 * @param string|null $caminho caminho absoluto do site, como '/catalogos/matriz.pdf'
 */
function dd_tem_pdf(?string $caminho): bool
{
    $caminho = trim((string) $caminho);

    return $caminho !== '' && is_file(DD_BASE . '/' . ltrim($caminho, '/'));
}

/**
 * Catálogo da rede inteira — não pertence a nenhuma unidade, por isso o caminho
 * é fixo aqui em vez de sair de data/units.php.
 *
 * >>> INTEGRAÇÃO FUTURA <<<
 * Com back-end, devolva a URL vinda do banco.
 *
 * @return string|null null quando o arquivo não existe: quem chama esconde o item.
 */
function dd_catalogo_completo(): ?string
{
    return dd_tem_pdf('/catalogos/completo.pdf') ? '/catalogos/completo.pdf' : null;
}

/* -----------------------------------------------------------------------------
 * PROMOÇÕES — RF-14 (quarta-feira) e RF-20 (baixa temporada)
 *
 * A agenda de cada oferta é um array 'quando'; o formato está documentado em
 * data/promocoes.php. Aqui só se responde a duas perguntas: "vale hoje?" e
 * "deve aparecer na página hoje?" — que NÃO são a mesma pergunta.
 * -------------------------------------------------------------------------- */

/** A promoção está valendo na data informada? */
function dd_promocao_vigente(array $promocao, ?DateTimeInterface $quando = null): bool
{
    $quando = $quando ?? new DateTimeImmutable('today');
    $agenda = (array) ($promocao['quando'] ?? []);

    return match ((string) ($agenda['tipo'] ?? 'sempre')) {
        'semanal' => in_array((int) $quando->format('w'), array_map('intval', $agenda['dias'] ?? []), true),
        'mensal'  => in_array((int) $quando->format('n'), array_map('intval', $agenda['meses'] ?? []), true),
        'periodo' => dd_dentro_do_periodo($agenda, $quando),
        default   => true, // 'sempre'
    };
}

/** 'periodo' com 'de' e/ou 'ate' em AAAA-MM-DD. Ponta ausente = aberta. */
function dd_dentro_do_periodo(array $agenda, DateTimeInterface $quando): bool
{
    $dia = $quando->format('Y-m-d');
    $de  = trim((string) ($agenda['de'] ?? ''));
    $ate = trim((string) ($agenda['ate'] ?? ''));

    return ($de === '' || $dia >= $de) && ($ate === '' || $dia <= $ate);
}

/**
 * As promoções que devem APARECER na página hoje, já enriquecidas.
 *
 * A regra de exibição não é a de vigência: uma oferta semanal se divulga a
 * semana toda (quem planeja a encomenda na segunda precisa saber da quarta),
 * enquanto uma campanha de julho anunciada em março é só ruído. Ver o comentário
 * em data/promocoes.php.
 *
 * Cada item ganha:
 *   'vigente'  bool    vale hoje
 *   'agenda'   string  "Toda quarta-feira", "Em julho e janeiro"…
 *
 * As vigentes vêm primeiro; o resto mantém a ordem do cadastro.
 */
function dd_promocoes_visiveis(?DateTimeInterface $quando = null): array
{
    $quando  = $quando ?? new DateTimeImmutable('today');
    $lista   = [];

    foreach (dd_promocoes() as $promocao) {
        if (($promocao['ativo'] ?? true) === false) {
            continue;
        }

        $tipo    = (string) ($promocao['quando']['tipo'] ?? 'sempre');
        $vigente = dd_promocao_vigente($promocao, $quando);

        // Só as datadas somem fora da janela. Ver o docblock.
        if (!$vigente && in_array($tipo, ['mensal', 'periodo'], true)) {
            continue;
        }

        $promocao['vigente'] = $vigente;
        $promocao['agenda']  = dd_agenda_em_texto((array) ($promocao['quando'] ?? []));
        $lista[] = $promocao;
    }

    // A vigente sobe; o resto mantém a ordem do cadastro, porque a ordenação do
    // PHP 8 é estável — não precisa de critério de desempate.
    usort($lista, static fn (array $a, array $b): int => (int) $b['vigente'] <=> (int) $a['vigente']);

    return $lista;
}

/** A agenda em português: "Toda quarta-feira", "Em julho e janeiro". */
function dd_agenda_em_texto(array $agenda): string
{
    $juntar = static function (array $itens): string {
        if (count($itens) <= 1) {
            return (string) ($itens[0] ?? '');
        }
        $ultimo = array_pop($itens);
        return implode(', ', $itens) . ' e ' . $ultimo;
    };

    switch ((string) ($agenda['tipo'] ?? 'sempre')) {
        case 'semanal':
            $nomes = ['domingo', 'segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado'];
            $dias  = array_map(static fn ($d): string => $nomes[(int) $d] ?? '', (array) ($agenda['dias'] ?? []));
            $dias  = array_values(array_filter($dias));
            return $dias === [] ? '' : 'Toda ' . $juntar($dias);

        case 'mensal':
            $nomes = [1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                      'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'];
            $meses = array_map(static fn ($m): string => $nomes[(int) $m] ?? '', (array) ($agenda['meses'] ?? []));
            $meses = array_values(array_filter($meses));
            return $meses === [] ? '' : 'Em ' . $juntar($meses);

        case 'periodo':
            $formatar = static function (string $iso): string {
                $data = DateTimeImmutable::createFromFormat('Y-m-d', $iso);
                return $data ? $data->format('d/m') : '';
            };
            $de  = $formatar(trim((string) ($agenda['de'] ?? '')));
            $ate = $formatar(trim((string) ($agenda['ate'] ?? '')));

            if ($de !== '' && $ate !== '') {
                return 'De ' . $de . ' a ' . $ate;
            }
            return $ate !== '' ? 'Até ' . $ate : ($de !== '' ? 'A partir de ' . $de : '');

        default:
            return 'O ano inteiro';
    }
}

/* -----------------------------------------------------------------------------
 * SAÍDA SEGURA
 * -------------------------------------------------------------------------- */

/** Escapa para HTML. Use em TODA interpolação vinda dos dados. */
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* -----------------------------------------------------------------------------
 * TEXTO
 * Sem mbstring de propósito: o site roda numa instalação mínima de PHP.
 * -------------------------------------------------------------------------- */

/** "Pão de Queijo" => "pao de queijo". Minúsculas e sem acento. */
function dd_ascii(string $texto): string
{
    static $mapa = [
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
        'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
        'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
        'ç' => 'c', 'ñ' => 'n',
        'Á' => 'a', 'À' => 'a', 'Â' => 'a', 'Ã' => 'a', 'Ä' => 'a',
        'É' => 'e', 'È' => 'e', 'Ê' => 'e', 'Ë' => 'e',
        'Í' => 'i', 'Ì' => 'i', 'Î' => 'i', 'Ï' => 'i',
        'Ó' => 'o', 'Ò' => 'o', 'Ô' => 'o', 'Õ' => 'o', 'Ö' => 'o',
        'Ú' => 'u', 'Ù' => 'u', 'Û' => 'u', 'Ü' => 'u',
        'Ç' => 'c', 'Ñ' => 'n',
    ];

    // Tira o acento primeiro; o que sobra é ASCII, então strtolower dá conta.
    return strtolower(strtr($texto, $mapa));
}

/** Corta preservando caracteres multibyte inteiros (para a meta description). */
function dd_resumo(string $texto, int $limite = 155): string
{
    $letras = preg_split('//u', $texto, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    if (count($letras) <= $limite) {
        return $texto;
    }
    return rtrim(implode('', array_slice($letras, 0, $limite - 1))) . '…';
}

/* -----------------------------------------------------------------------------
 * PREÇO E QUANTIDADE
 * -------------------------------------------------------------------------- */

/** 120.0 => "R$ 120,00" */
function dd_moeda(float $valor): string
{
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Quantas peças cabem na embalagem descrita em 'por'.
 * "100 unidades" => 100 · "45 unidades" => 45 · "unidade" => 1 · "kg" => 1
 */
function dd_base_da_faixa(array $faixa): int
{
    if (preg_match('/(\d+)/', (string) ($faixa['por'] ?? ''), $m)) {
        return max(1, (int) $m[1]);
    }
    return 1;
}

/** "100 unidades" => "100 un" · "unidade" => "un" · "kg" => "kg" */
function dd_por_curto(string $por): string
{
    $curto = preg_replace('/\bunidades?\b/u', 'un', $por);
    return trim($curto === '' ? $por : $curto);
}

/**
 * Normaliza uma faixa de preço para tudo que a interface precisa saber.
 * É a única função que sabe converter "preço da embalagem" em "preço por peça".
 *
 *   valor       float  preço cheio da embalagem (ex.: 120.00)
 *   por         string rótulo original (ex.: "100 unidades")
 *   porCurto    string rótulo curto (ex.: "100 un")
 *   base        int    peças por embalagem (ex.: 100)
 *   unitario    float  preço de UMA peça (ex.: 1.20)
 *   min         int    quantidade mínima em peças
 *   passo       int    incremento do seletor de quantidade
 *   exibicao    string "R$ 120,00 / 100 un" — o texto que vai para o WhatsApp
 *   temMinimo   bool   se existe pedido mínimo declarado no cadastro
 */
function dd_faixa(array $faixa): array
{
    $valor = (float) ($faixa['valor'] ?? 0);
    $por   = (string) ($faixa['por'] ?? 'unidade');
    $base  = dd_base_da_faixa($faixa);

    $temMinimo = isset($faixa['minPedido']) && (int) $faixa['minPedido'] > 0;
    $min       = $temMinimo ? (int) $faixa['minPedido'] : $base;

    // Passo: o cadastro manda; se não mandar, usa o mínimo quando ele é menor
    // que a embalagem (ex.: pão de queijo a partir de 50 anda de 50 em 50) e
    // a embalagem no resto dos casos (o cento anda de 100 em 100).
    if (isset($faixa['passo']) && (int) $faixa['passo'] > 0) {
        $passo = (int) $faixa['passo'];
    } elseif ($temMinimo && $min < $base) {
        $passo = $min;
    } else {
        $passo = $base > 1 ? $base : 1;
    }

    $porCurto = dd_por_curto($por);

    return [
        'valor'     => $valor,
        'por'       => $por,
        'porCurto'  => $porCurto,
        'base'      => $base,
        'unitario'  => $base > 0 ? $valor / $base : $valor,
        'min'       => $min,
        'passo'     => $passo,
        'exibicao'  => dd_moeda($valor) . ' / ' . $porCurto,
        'temMinimo' => $temMinimo,
        'rotulo'    => (string) ($faixa['rotulo'] ?? ''),
    ];
}

/** Primeira faixa de preço de um produto, já normalizada. */
function dd_faixa_principal(array $produto): array
{
    $faixas = $produto['precos'] ?? [];
    return dd_faixa($faixas[0] ?? ['valor' => 0, 'por' => 'unidade']);
}

/* -----------------------------------------------------------------------------
 * CONSULTAS AO CATÁLOGO
 * -------------------------------------------------------------------------- */

function dd_produto_por_slug(string $slug): ?array
{
    foreach (dd_produtos() as $produto) {
        if (($produto['slug'] ?? '') === $slug) {
            return $produto;
        }
    }
    return null;
}

/**
 * Categorias DERIVADAS dos produtos, na ordem em que aparecem no array.
 * Escrever uma categoria nova em data/products.php já cria o botão de filtro.
 */
function dd_categorias(?array $produtos = null): array
{
    $categorias = [];
    foreach ($produtos ?? dd_produtos() as $produto) {
        $categoria = trim((string) ($produto['categoria'] ?? ''));
        if ($categoria !== '' && !in_array($categoria, $categorias, true)) {
            $categorias[] = $categoria;
        }
    }
    return $categorias;
}

/** Produtos da mesma categoria, sem repetir o próprio produto. */
function dd_relacionados(array $produto, int $limite = 3): array
{
    $lista = [];
    foreach (dd_produtos() as $candidato) {
        if (($candidato['slug'] ?? '') === ($produto['slug'] ?? '')) {
            continue;
        }
        if (($candidato['categoria'] ?? '') !== ($produto['categoria'] ?? '')) {
            continue;
        }
        if (($candidato['disponivel'] ?? true) === false) {
            continue;
        }
        $lista[] = $candidato;
        if (count($lista) >= $limite) {
            break;
        }
    }
    return $lista;
}

/** "atacado" | "varejo" | "" — lida na tag do produto, para o seletor Encomendas/Balcão. */
function dd_linha(array $produto): string
{
    $tags = array_map('strval', $produto['tags'] ?? []);
    if (in_array('atacado', $tags, true)) {
        return 'atacado';
    }
    if (in_array('varejo', $tags, true)) {
        return 'varejo';
    }
    return '';
}

function dd_rotulo_linha(string $linha): string
{
    return match ($linha) {
        'atacado' => 'Encomenda',
        'varejo'  => 'Balcão',
        default   => '',
    };
}

/** Texto minúsculo e sem acento usado pela busca no navegador. */
function dd_indice_busca(array $produto): string
{
    $partes = [
        $produto['nome'] ?? '',
        $produto['categoria'] ?? '',
        $produto['descricao'] ?? '',
        dd_rotulo_linha(dd_linha($produto)),
        implode(' ', $produto['sabores'] ?? []),
        implode(' ', $produto['tags'] ?? []),
    ];

    // Sem acento e em minúsculas: assim "pao de queijo" acha "Pão de Queijo".
    // O JS normaliza o que a pessoa digita do mesmo jeito (assets/js/ui.js).
    return dd_ascii(implode(' ', $partes));
}

/* -----------------------------------------------------------------------------
 * IMAGENS
 * -------------------------------------------------------------------------- */

/**
 * Devolve o caminho web da imagem se o arquivo existir de verdade; senão null.
 * É isso que permite deixar os caminhos das fotos já cadastrados em data/ antes
 * de as fotos chegarem: o site desenha um placeholder no lugar, sem quebrar.
 *
 * >>> INTEGRAÇÃO FUTURA <<<
 * Com back-end, troque por uma checagem de URL/CDN.
 */
function dd_imagem(?string $caminho): ?string
{
    if (!$caminho) {
        return null;
    }
    $arquivo = DD_BASE . '/' . ltrim($caminho, '/');
    return is_file($arquivo) ? $caminho : null;
}

/**
 * Ícone de linha para o placeholder, escolhido pela categoria do produto.
 * Retorna SVG inline em currentColor.
 */
function dd_icone_categoria(string $categoria): string
{
    $c = dd_ascii($categoria);

    $comum = 'fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"';

    // Coxinha / salgado frito: gota.
    $salgado = '<path ' . $comum . ' d="M32 8c7 9 12 15 12 23a12 12 0 0 1-24 0c0-8 5-14 12-23Z"/><path ' . $comum . ' d="M25 31c3 2 11 2 14 0"/>';
    // Doce: cupcake.
    $doce = '<path ' . $comum . ' d="M20 30h24l-3 22a3 3 0 0 1-3 3H26a3 3 0 0 1-3-3L20 30Z"/><path ' . $comum . ' d="M20 30c-1-6 4-8 6-7 0-6 6-9 10-6 4-2 9 1 8 6 3 0 5 3 4 7"/>';
    // Bebida: copo.
    $bebida = '<path ' . $comum . ' d="M22 16h20l-2 34a4 4 0 0 1-4 4h-8a4 4 0 0 1-4-4L22 16Z"/><path ' . $comum . ' d="M23 28h18"/>';
    // Almoço: prato e talher.
    $almoco = '<circle ' . $comum . ' cx="32" cy="34" r="16"/><circle ' . $comum . ' cx="32" cy="34" r="8"/>';
    // Combo: bandeja.
    $combo = '<rect ' . $comum . ' x="10" y="30" width="44" height="10" rx="5"/><circle ' . $comum . ' cx="22" cy="22" r="6"/><circle ' . $comum . ' cx="34" cy="20" r="7"/><circle ' . $comum . ' cx="45" cy="24" r="5"/>';
    // Assado / lanche / padrão: pãozinho com corte.
    $assado = '<path ' . $comum . ' d="M11 36c0-9 9-15 21-15s21 6 21 15-9 12-21 12-21-3-21-12Z"/><path ' . $comum . ' d="M22 30l4 6M32 28l4 7M42 30l3 6"/>';

    $glifo = match (true) {
        str_contains($c, 'salgad') => $salgado,
        str_contains($c, 'doce')   => $doce,
        str_contains($c, 'bebid')  => $bebida,
        str_contains($c, 'almoc')  => $almoco,
        str_contains($c, 'combo')  => $combo,
        default                    => $assado,
    };

    return '<svg viewBox="0 0 64 64" aria-hidden="true" focusable="false" class="h-14 w-14">' . $glifo . '</svg>';
}

/* -----------------------------------------------------------------------------
 * MARCA
 * -------------------------------------------------------------------------- */

/**
 * Logo em SVG inline (placa oval vermelha com os raios brancos).
 * Fica inline — e não como <img> — para herdar a fonte display da página.
 *
 * >>> INTEGRAÇÃO FUTURA <<<
 * Quando chegar o arquivo vetorial oficial da marca, troque este SVG por ele.
 */
function dd_logo(string $classes = 'h-11 w-auto'): string
{
    $raios = '';
    for ($angulo = 0; $angulo < 360; $angulo += 30) {
        $raios .= '<polygon points="0,0 320,-46 320,46" transform="rotate(' . $angulo . ')"/>';
    }

    return <<<SVG
<svg viewBox="0 0 300 160" class="{$classes}" role="img" aria-label="Dolce Delícias">
  <defs>
    <clipPath id="dd-oval"><ellipse cx="150" cy="80" rx="146" ry="76"/></clipPath>
  </defs>
  <ellipse cx="150" cy="80" rx="146" ry="76" fill="#E1051E"/>
  <g clip-path="url(#dd-oval)" fill="#ffffff" opacity="0.22">
    <g transform="translate(150,80)">{$raios}</g>
  </g>
  <ellipse cx="150" cy="80" rx="132" ry="63" fill="none" stroke="#ffffff" stroke-width="3.5"/>
  <text x="150" y="72" text-anchor="middle" fill="#ffffff" font-family="var(--font-logo, 'Trebuchet MS'), sans-serif" font-size="46" font-weight="800" font-style="italic">Dolce</text>
  <text x="150" y="116" text-anchor="middle" fill="#FCE24C" font-family="var(--font-logo, 'Trebuchet MS'), sans-serif" font-size="38" font-weight="800">delícias</text>
</svg>
SVG;
}
