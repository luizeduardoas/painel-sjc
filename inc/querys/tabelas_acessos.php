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
        $filtro_escola = $arrParam["filtro_escola"];
        if (!seNuloOuVazioOuMenosUm($filtro_escola)) {
            try {

                $filter = new GFilter();

                $filter->addFilter("AND", "ace_var_tipo", "=", "s", $filtro_tipo);

                $filter->addClause("AND (ace.cur_int_codigo IS NULL OR EXISTS (SELECT 1 FROM ava_matricula mat INNER JOIN ava_curso cur ON (cur.cur_int_codigo = mat.cur_int_codigo) WHERE mat.usu_int_codigo = ace.usu_int_codigo AND ace.cur_int_codigo = cur.cur_int_codigo AND cur.cur_int_courseid IN ($filtro_curso) ) ) ");

                $filtro_periodo = $arrParam["filtro_periodo"];
                $arrData = explode(" - ", $filtro_periodo);
                $filter->addClause("AND (ace_dti_datahora BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");

                if ($filtro_escola == 'S' && seNuloOuVazioOuZeroOuMenosUm($arrParam["esc_int_codigo"])) {
                    $filterTotal = clone $filter;
                    $filter->setGroupBy("esc.esc_var_nome");
                    $filter->setOrder(array("QTD" => "DESC"));

                    $mysql = new GDbMysql();
                    $query = array();
                    $query[] = "SELECT COUNT(DISTINCT(ace.usu_int_codigo)) AS QTD, esc.esc_int_codigo, esc.esc_var_nome ";
                    $query[] = "FROM ava_acesso ace ";
                    $query[] = "INNER JOIN ava_usuario usu ON (usu.usu_int_codigo = ace.usu_int_codigo) ";
                    $query[] = "INNER JOIN escola esc ON (usu.esc_int_codigo = esc.esc_int_codigo) ";
                    $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                    $mysqlTotal = new GDbMysql();
                    $query[0] = "SELECT COUNT(*) ";
                    $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                    if ($mysql->numRows()) {
                        $aviso = null;
                        $arrTitulos = array("Escola", "Percentual");
                        $arrFormats = array("", "text-align: center");
                        $arrFooter = array();
                        $tituloCentral = false;
                        $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                        $styleTituloCentral = false;
                        while ($mysql->fetch()) {
                            $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                            $arrDados[] = array("ESCOLA" => '<a class="__pointer" onclick="carregarTabela(' . $mysql->res["esc_int_codigo"] . ');">' . $mysql->res["esc_var_nome"] . '</a>', "QTD" => $percentual);
                        }
                    } else {
                        $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                    }
                } else if ($filtro_escola == 'S') {
                    $filter->addFilter("AND", "usu.esc_int_codigo", "=", "i", $arrParam["esc_int_codigo"]);
                    $filterTotal = clone $filter;
                    $filter->setGroupBy("usu.usu_int_codigo");
                    $filter->setOrder(array("QTD" => "DESC"));

                    $mysql = new GDbMysql();
                    $query = array();
                    $query[] = "SELECT COUNT(DISTINCT(ace.ace_int_codigo)) AS QTD, usu.usu_int_codigo, usu.usu_var_nome ";
                    $query[] = "FROM ava_acesso ace ";
                    $query[] = "INNER JOIN ava_usuario usu ON (usu.usu_int_codigo = ace.usu_int_codigo) ";
                    $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                    $mysqlTotal = new GDbMysql();
                    $query[0] = "SELECT COUNT(*) ";
                    $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                    $mysqlEscola = new GDbMysql();
                    $escola = $mysqlEscola->executeValue("SELECT esc_var_nome FROM escola WHERE esc_int_codigo = ?", array("i", $arrParam["esc_int_codigo"]));

                    if ($mysql->numRows()) {
                        $aviso = null;
                        $arrTitulos = array("Usuário", "Percentual");
                        $arrFormats = array("", "text-align: center");
                        $arrFooter = array();
                        $tituloCentral = "Escola: <b>$escola</b>";
                        $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                        $styleTituloCentral = "text-align: center;color: #ffffff;";
                        while ($mysql->fetch()) {
                            $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                            $arrDados[] = array("USUARIO" => $mysql->res["usu_var_nome"], "QTD" => $percentual);
                        }
                    } else {
                        $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                    }
                } else {
                    $aviso = "A definir.";
                }
            } catch (GDbException $e) {
                echo '<pre>';
                var_dump($e);
                echo '</pre>';
            }
        } else {
            $aviso = "Favor selecionar se é para agrupar por escola ou não.";
        }
    } else {
        $aviso = "Favor selecionar o tipo de acesso.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>