<?php

declare(strict_types=1);

/**
 * =============================================================================
 * PROMOÇÕES DA DOLCE DELÍCIAS
 * =============================================================================
 *
 * Duas coisas diferentes moram aqui, porque as duas são "uma oferta com data":
 *
 *   RF-14  a promoção de quarta-feira (e qualquer outra que se repita na semana)
 *   RF-20  as campanhas de baixa temporada — julho, dezembro e janeiro
 *
 * >>> TUDO QUE ESTÁ MARCADO COM "PREENCHER" É PLACEHOLDER <<<
 * As ofertas abaixo são exemplo. Confirme com a padaria o que vale de verdade,
 * qual o desconto e em quais itens, antes de publicar.
 *
 * >>> INTEGRAÇÃO FUTURA <<<
 * Trocar este `return [...]` por uma consulta ao banco mantém tudo funcionando,
 * desde que o formato dos campos seja o mesmo. É por aqui que a área
 * administrativa (RF-12) vai cadastrar promoção sem tocar em template.
 *
 * -----------------------------------------------------------------------------
 * ESQUEMA
 * -----------------------------------------------------------------------------
 *   'id'        int
 *   'slug'      string  identificador estável
 *   'titulo'    string  nome da promoção, aparece grande
 *   'selo'      string  o número que chama o olho: "20% OFF", "2 por R$ 15"
 *   'texto'     string  1–2 frases explicando a regra
 *   'quando'    array   QUANDO a oferta vale. Ver "AGENDA" abaixo.
 *   'ativo'     bool    false esconde sem apagar o cadastro
 *
 * -----------------------------------------------------------------------------
 * AGENDA — o campo 'quando'
 * -----------------------------------------------------------------------------
 *   ['tipo' => 'semanal', 'dias' => [3]]        dias da semana, 0=domingo … 6=sábado
 *   ['tipo' => 'mensal',  'meses' => [7,12,1]]  meses do ano, 1=janeiro … 12=dezembro
 *   ['tipo' => 'periodo', 'de' => '2026-12-01', 'ate' => '2026-12-24']
 *   ['tipo' => 'sempre']                        sem data, vale o ano inteiro
 *
 * REGRA DE EXIBIÇÃO (dd_promocoes_visiveis(), em partials/bootstrap.php):
 *
 *   semanal e sempre  →  aparecem TODO DIA, com selo "é hoje" no dia certo.
 *                        RF-14 pede DIVULGAÇÃO da promoção de quarta: anunciá-la
 *                        só na quarta não divulga nada — quem precisa saber é
 *                        quem está planejando a semana na segunda.
 *
 *   mensal e periodo  →  aparecem só dentro da janela. Anunciar a campanha de
 *                        julho em março não é divulgação, é ruído.
 */

return [
    [
        'id'    => 1,
        'slug'  => 'quarta-do-salgado',
        'titulo' => 'Quarta do salgado', // PREENCHER: nome real da promoção
        'selo'  => 'PREENCHER', // PREENCHER: "20% OFF", "2 por R$ 15"…
        'texto' => 'PREENCHER: o que entra na promoção, em quais unidades e se vale para encomenda ou só no balcão.',
        'quando' => ['tipo' => 'semanal', 'dias' => [3]], // 3 = quarta-feira
        'ativo' => true,
    ],

    // -------------------------------------------------------------------------
    // RF-20 — baixa temporada. Julho e janeiro são férias escolares e dezembro
    // esvazia as empresas: é quando o cento de salgado para de sair sozinho.
    // -------------------------------------------------------------------------
    [
        'id'    => 2,
        'slug'  => 'ferias-escolares',
        'titulo' => 'Combo de férias', // PREENCHER
        'selo'  => 'PREENCHER',
        'texto' => 'PREENCHER: a oferta de julho e janeiro, quando as escolas param. Ex.: combo de lanche para levar para casa.',
        'quando' => ['tipo' => 'mensal', 'meses' => [7, 1]],
        'ativo' => true,
    ],

    [
        'id'    => 3,
        'slug'  => 'festas-de-fim-de-ano',
        'titulo' => 'Encomendas de fim de ano', // PREENCHER
        'selo'  => 'PREENCHER',
        'texto' => 'PREENCHER: a campanha de dezembro — ceia, confraternização de empresa, bandeja de festa.',
        'quando' => ['tipo' => 'mensal', 'meses' => [12]],
        'ativo' => true,
    ],

    // >>> PRÓXIMAS PROMOÇÕES ENTRAM AQUI <<<
    // Copie um bloco acima, troque id e slug por valores únicos e ajuste 'quando'.
];
