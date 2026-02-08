<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

$arrParam = array();
foreach ($_GET as $key => $val) {
    $arrParam[$key] = explode(",", $val);
}
foreach ($arrParam as $key => $val) {
    if (is_array($val) && count($val) === 1) {
        $arrParam[$key] = reset($val);
    }
}

$excel = true;
include_once(ROOT_SYS_INC . "querys/graficos_matriculas.php");

if (count($arrDados)) {
    header("Content-Type: application/vnd.ms-excel;");
    header("Content-Disposition: attachment; filename=graficos_matriculas-" . date("d-m-Y") . ".xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo arrayToTableRelatorio($arrTitulos, $arrDados);
} else {
    echo '<h1>Nenhum dado encontrado para exportação</h1>';
}
?>