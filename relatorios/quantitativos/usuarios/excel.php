<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");

$filtro_tipo = $_GET["filtro_tipo"];

include_once(__DIR__ . "/../../querys/usuarios.php");

if (count($arrDados)) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=usuarios.xls");
    header("Pragma: no-cache");
    echo arrayToTable($arrTitulos, $arrDados, $arrFooter);
} else {
    echo '<h1>Nenhum dado encontrado para exportação</h1>';
}
?>