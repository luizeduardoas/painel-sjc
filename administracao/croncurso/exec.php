<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

header('Content-Encoding: none');

GSecurity::verificarPermissao("CRONCURSO");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Cron de atualização de curso", true);
$header->addMenu("CRONCURSO", "Cron de atualização de curso", "Execute para realizar a atualização de curso");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

include_once(__DIR__ . "/../../crons/croncurso.php");

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>