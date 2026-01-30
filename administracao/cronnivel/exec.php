<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

header('Content-Encoding: none');

GSecurity::verificarPermissao("CRONNIVEL");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Cron de atualização de estrutura organizacional", true);
$header->addMenu("CRONNIVEL", "Cron de atualização de estrutura organizacional", "Execute para realizar a atualização de estrutura organizacional");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

include_once(__DIR__ . "/../../crons/cronnivel.php");

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>