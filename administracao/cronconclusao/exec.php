<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

header('Content-Encoding: none');

GSecurity::verificarPermissao("CRONCONCLUSAO");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);

$header = new GHeader("Cron de atualização de conclusão", true);
$header->addMenu("CRONCONCLUSAO", "Cron de atualização de conclusão", "Execute para realizar a atualização de conclusão");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

include_once(__DIR__ . "/../../crons/cronconclusao.php");

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>