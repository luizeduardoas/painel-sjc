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

    $filtro_escola = '';
    if ($arrParam["filtro_escola"] != "") {
        $filtro_escola = implode(",", $arrParam["filtro_escola"]);
    }
    if ($filtro_escola != '') {
        $filter = new GFilter();
        if (count($arrParam["filtro_escola"]) != buscarQtdEscolas()) {
            $filter->addClause("AND EXISTS (SELECT 1 FROM ava_usuario u WHERE u.usu_int_codigo = a.usu_int_codigo AND u.esc_int_codigo IN ($filtro_escola) ) ");
        }
        if (count($arrParam["filtro_curso"]) != buscarQtdCursos()) {
            $filter->addClause("AND a.cur_int_codigo IN ($filtro_curso) ");
        }
        $filter->addClause("AND (mat_dti_inicio <= '" . GF::formatarData($arrData[0]) . " 23:59:59' AND COALESCE(mat_dti_termino,'2030-01-02 23:59:59') >= '" . GF::formatarData($arrData[0]) . " 00:00:00') ");
        $filter->setGroupBy("CURSO");
        $filter->setOrder(array("CURSO" => "ASC"));
        $mysql = new GDbMysql();
        $arrDados = $mysql->executeListArray("SELECT c.cur_var_nome AS CURSO, COUNT(DISTINCT(a.usu_int_codigo)) AS QTD FROM ava_matricula a INNER JOIN ava_curso c on (c.cur_int_codigo = a.cur_int_codigo) " . $filter->getWhere(false), $filter->getParam(), array("CURSO", "QTD"));
        $aviso = null;
        $arrTitulos = array("Curso", "Quantidade");
    } else {
        $aviso = "Favor selecionar ao menos uma escola.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
//}
?>