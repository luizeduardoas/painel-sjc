<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");


$arrParam = array();
foreach ($_POST as $key => $val) {
    $arrParam[$key] = $val;
}

$excel = false;
include_once(ROOT_SYS_INC . "querys/tabelas_naoacesso.php");

if (seNuloOuVazio($aviso)) {
    echo gerarTabela($arrTitulos, $arrDados, $arrFooter, $arrFormats, $tituloCentral, $arrStyleTitulos, $styleTituloCentral, $link);
} else {
    echo carregarMensagem("A", $aviso, 12, false);
}
?>