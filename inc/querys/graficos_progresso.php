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

    $filtro_escola = '';
    if ($arrParam["filtro_escola"] != "") {
        $filtro_escola = implode(",", $arrParam["filtro_escola"]);
    }
    if ($filtro_escola != '') {
        $filter = new GFilter();
        if (count($arrParam["filtro_escola"]) != buscarQtdEscolas()) {
            $filter->addClause("AND u.esc_int_codigo IN ($filtro_escola) ");
        }
        if (count($arrParam["filtro_curso"]) != buscarQtdCursos()) {
            $filter->addClause("AND c.cur_int_codigo IN ($filtro_curso) ");
        }
        $filter->setGroupBy("CURSO");
        $filter->setOrder(array("CURSO" => "ASC"));
        $mysql = new GDbMysql();
        $query = array();
        $query[] = "SELECT c.cur_var_nome AS CURSO, COUNT(m.mat_int_codigo) AS TOTAL_MATRICULAS, ROUND(AVG(m.mat_dec_percentual), 2) AS PROGRESSO_MEDIO_PERCENTUAL ";
        $query[] = "FROM ava_curso c ";
        $query[] = "INNER JOIN ava_matricula m ON (c.cur_int_codigo = m.cur_int_codigo) ";
        $query[] = "INNER JOIN ava_usuario u ON (m.usu_int_codigo = u.usu_int_codigo) ";
        $arrDados = $mysql->executeListArray(implode("", $query) . $filter->getWhere(false), $filter->getParam(), array("CURSO", "TOTAL_MATRICULAS", "PROGRESSO_MEDIO_PERCENTUAL"));
        $aviso = null;
        $arrTitulos = array("Curso", "Quantidade Alunos", "Percentual Médio");
    } else {
        $aviso = "Favor selecionar ao menos uma escola.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>