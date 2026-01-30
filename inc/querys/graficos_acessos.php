<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");

$filtro_curso = '';
if ($arrParam["filtro_curso"] != "") {
    $filtro_curso = implode(",", $arrParam["filtro_curso"]);
}
if ($filtro_curso != '') {

    $filtro_tipo = $arrParam["filtro_tipo"];
    if (!seNuloOuVazioOuMenosUm($filtro_tipo)) {
        $filter = new GFilter();

        $filter->addFilter("AND", "ace_var_tipo", "=", "s", $filtro_tipo);

        $filter->addClause("AND EXISTS (SELECT 1 FROM ava_curso c WHERE a.cur_int_codigo = c.cur_int_codigo AND c.cur_int_courseid IN ($filtro_curso) ) ");

        $filtro_periodo = $arrParam["filtro_periodo"];
        $arrData = explode(" - ", $filtro_periodo);
        $filter->addClause("AND (ace_dti_datahora BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");

        $filterTotal = new GFilter();
        $filterTotal->addClause("AND EXISTS (SELECT 1 FROM ava_curso c WHERE a.cur_int_codigo = c.cur_int_codigo AND c.cur_int_courseid IN ($filtro_curso) ) ");

        $mysql = new GDbMysql();
        $qtdAcesso = $mysql->executeValue("SELECT COUNT(DISTINCT(usu_int_codigo)) FROM ava_acesso a " . $filter->getWhere(true), $filter->getParam());
        $qtdUsuario = $mysql->executeValue("SELECT COUNT(DISTINCT(usu_int_codigo)) FROM ava_matricula a " . $filterTotal->getWhere(true), $filterTotal->getParam());

        //$percentual = porcentagem_nx($qtdAcesso, $qtdUsuario);
        $percentual = mt_rand(0, 10000) / 100;

        $aviso = null;
        $arrTitulos = array("Data", "Percentual");
        $arrDados[] = array("DATA" => date("d/m/Y"), "QTD" => $percentual);
    } else {
        $aviso = "Favor selecionar o tipo de acesso.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>