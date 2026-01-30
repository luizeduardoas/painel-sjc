<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

header('Content-Encoding: none');

GSecurity::verificarPermissao("CRONARQUIVOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Limpeza de arquivos não utilizados", true);
$header->addMenu("CRONARQUIVOS", "Cron de limpeza dos arquivos não utilizados", "Execute para realizar a limpeza dos arquivos não utilizados");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

include_once(__DIR__ . "/../../crons/cronarquivos.php");

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>