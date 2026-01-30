<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

header('Content-Encoding: none');

GSecurity::verificarPermissao("CRONMATRICULA");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Cron de atualização de matrícula", true);
$header->addMenu("CRONMATRICULA", "Cron de atualização de matrícula", "Execute para realizar a atualização de matrícula");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

include_once(__DIR__ . "/../../crons/cronmatricula.php");

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>