<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");

$filtro_periodo = $arrParam["filtro_periodo"];
$arrData = explode(" - ", $filtro_periodo);

//$dataInicio = new DateTime(GF::formatarData($arrData[0]));
//$dataFim = new DateTime(GF::formatarData($arrData[1]));
//$intervalo = $dataInicio->diff($dataFim);
//if ($intervalo->days > 90) {
//    $aviso = "O período máximo permitido é de 90 dias.";
//} else {
$filtro_curso = '';
if ($arrParam["filtro_curso"] != "") {
    $filtro_curso = implode(",", $arrParam["filtro_curso"]);
}
if ($filtro_curso != '') {

    $filtro_tipo = $arrParam["filtro_tipo"];
    if (!seNuloOuVazioOuMenosUm($filtro_tipo)) {
        $filter = new GFilter();
        $filter->addFilter("AND", "ace_var_tipo", "=", "s", $filtro_tipo);
        if (count($arrParam["filtro_curso"]) != locaQtdCursos()) {
            $filter->addClause("AND EXISTS (SELECT 1 FROM ava_matricula m WHERE m.usu_int_codigo = a.usu_int_codigo AND m.cur_int_codigo IN ($filtro_curso) ) ");
            if ($filtro_tipo == 'EC') {
                $filter->addClause("AND a.cur_int_codigo IN ($filtro_curso) ");
            }
        } else {
            $filter->addClause("AND EXISTS (SELECT 1 FROM ava_matricula m WHERE m.usu_int_codigo = a.usu_int_codigo ) ");
        }
        $filter->addClause("AND (a.ace_dti_datahora BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");
        $filter->setGroupBy("DATA");
        $filter->setOrder(array("MIN(a.ace_dti_datahora)" => "ASC"));
        $mysql = new GDbMysql();
        $arrDados = $mysql->executeListArray("SELECT DATE_FORMAT(a.ace_dti_datahora, '%d/%m/%Y') AS DATA, COUNT(DISTINCT(a.usu_int_codigo)) AS QTD FROM ava_acesso a " . $filter->getWhere(false), $filter->getParam(), array("DATA", "QTD"));
        $aviso = null;
        $arrTitulos = array("Data", "Quantidade");
    } else {
        $aviso = "Favor selecionar o tipo de acesso.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
//}
?>