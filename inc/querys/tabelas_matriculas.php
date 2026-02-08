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
        $filtro_agrupamento = $arrParam["filtro_agrupamento"];
        if (!seNuloOuVazioOuMenosUm($filtro_agrupamento)) {
            $ordenacao = $arrParam["ordenacao"];
            if (!seNuloOuVazioOuMenosUm($ordenacao)) {
                try {
                    $link = null;
                    $filter = new GFilter();
                    if (count($arrParam["filtro_escola"]) != buscarQtdEscolas()) {
                        $filter->addClause("AND u.esc_int_codigo IN ($filtro_escola) ");
                    }
                    if (count($arrParam["filtro_curso"]) != buscarQtdCursos()) {
                        $filter->addClause("AND a.cur_int_codigo IN ($filtro_curso) ");
                    }
                    $filtro_periodo = $arrParam["filtro_periodo"];
                    $arrData = explode(" - ", $filtro_periodo);
                    $filter->addClause("AND (mat_dti_inicio <= '" . GF::formatarData($arrData[0]) . " 23:59:59' AND COALESCE(mat_dti_termino,'2030-01-02 23:59:59') >= '" . GF::formatarData($arrData[0]) . " 00:00:00') ");

                    switch ($ordenacao) {
                        case 'CC': // Curso Crescente
                            $filter->setOrder(array("CURSO" => "ASC"));
                            break;
                        case 'CD': // Curso Decrescente
                            $filter->setOrder(array("CURSO" => "DESC"));
                            break;
                        case 'QC': // Quantidade Crescente
                            $filter->setOrder(array("QTD" => "ASC"));
                            break;
                        case 'QD': // Quantidade Decrescente
                            $filter->setOrder(array("QTD" => "DESC"));
                            break;
                        case 'EC': // Escola Crescente
                            $filter->setOrder(array("ESCOLA" => "ASC"));
                            break;
                        case 'ED': // Escola Decrescente
                            $filter->setOrder(array("ESCOLA" => "DESC"));
                            break;
                        default:
                            break;
                    }

                    if ($filtro_agrupamento == 'E' && seNuloOuVazioOuZeroOuMenosUm($arrParam["tipo"] ?? null)) {
                        $filter->setGroupBy("e.esc_int_codigo, e.esc_var_nome");

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(DISTINCT(a.mat_int_codigo)) AS QTD, e.esc_int_codigo, e.esc_var_nome AS ESCOLA ";
                        $query[] = "FROM ava_matricula a ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = a.usu_int_codigo) ";
                        $query[] = "INNER JOIN escola e ON (u.esc_int_codigo = e.esc_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlTotal = new GDbMysql();
                        $query[0] = "SELECT COUNT(*) ";
                        $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Escola", "Quantidade", "Percentual");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                                $arrDados[] = array("ESCOLA" => '<a class="__pointer" onclick="carregarTabela(\'E\', ' . $mysql->res["esc_int_codigo"] . ');">' . $mysql->res["ESCOLA"] . '</a>', "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                            $arrFooter[] = array("ESCOLA" => 'TOTAL', "QTD" => $total, "PERC" => '100%');
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'E' && !seNuloOuVazioOuZeroOuMenosUm($arrParam["codigo"] ?? null)) {
                        $filter->addFilter("AND", "u.esc_int_codigo", "=", "i", $arrParam["codigo"]);
                        $filter->setGroupBy("u.usu_int_codigo");
                        if (substr($ordenacao, 0, 1) == 'E') {
                            if (substr($ordenacao, 1, 1) == 'C') {
                                $filter->setOrder(array("USUARIO" => "ASC"));
                            } else {
                                $filter->setOrder(array("USUARIO" => "DESC"));
                            }
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(a.mat_int_codigo) AS QTD, u.usu_int_codigo, u.usu_var_nome AS USUARIO ";
                        $query[] = "FROM ava_matricula a ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = a.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlTotal = new GDbMysql();
                        $query[0] = "SELECT COUNT(*) ";
                        $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                        $mysqlEscola = new GDbMysql();
                        $escola = $mysqlEscola->executeValue("SELECT esc_var_nome FROM escola WHERE esc_int_codigo = ?", array("i", $arrParam["codigo"]));

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Usuário", "Quantidade", "Percentual");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = "Escola: <b>$escola</b>";
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                                $arrDados[] = array("USUARIO" => $mysql->res["USUARIO"], "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                            $arrFooter[] = array("USUARIO" => 'TOTAL', "QTD" => $total, "PERC" => '100%');
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'C' && seNuloOuVazioOuZeroOuMenosUm($arrParam["tipo"] ?? null)) {
                        $filter->setGroupBy("c.cur_int_codigo, c.cur_var_nome");

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(DISTINCT(a.mat_int_codigo)) AS QTD, c.cur_int_codigo, c.cur_var_nome AS CURSO ";
                        $query[] = "FROM ava_matricula a ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = a.usu_int_codigo) ";
                        $query[] = "INNER JOIN ava_curso c ON (a.cur_int_codigo = c.cur_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlTotal = new GDbMysql();
                        $query[0] = "SELECT COUNT(*) ";
                        $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Curso", "Quantidade", "Percentual");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                                $arrDados[] = array("CURSO" => '<a class="__pointer" onclick="carregarTabela(\'C\', ' . $mysql->res["cur_int_codigo"] . ');">' . $mysql->res["CURSO"] . '</a>', "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                            $arrFooter[] = array("CURSO" => 'TOTAL', "QTD" => $total, "PERC" => '100%');
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'C' && !seNuloOuVazioOuZeroOuMenosUm($arrParam["codigo"] ?? null)) {
                        $filter->addFilter("AND", "a.cur_int_codigo", "=", "i", $arrParam["codigo"]);
                        $filter->setGroupBy("u.usu_int_codigo");
                        if (substr($ordenacao, 0, 1) == 'C') {
                            if (substr($ordenacao, 1, 1) == 'C') {
                                $filter->setOrder(array("USUARIO" => "ASC"));
                            } else {
                                $filter->setOrder(array("USUARIO" => "DESC"));
                            }
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(a.mat_int_codigo) AS QTD, u.usu_int_codigo, u.usu_var_nome AS USUARIO ";
                        $query[] = "FROM ava_matricula a ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = a.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlTotal = new GDbMysql();
                        $query[0] = "SELECT COUNT(*) ";
                        $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                        $mysqlCurso = new GDbMysql();
                        $curso = $mysqlCurso->executeValue("SELECT cur_var_nome FROM ava_curso WHERE cur_int_codigo = ?", array("i", $arrParam["codigo"]));

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Usuário", "Quantidade", "Percentual");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = "Curso: <b>$curso</b>";
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                                $arrDados[] = array("USUARIO" => $mysql->res["USUARIO"], "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                            $arrFooter[] = array("USUARIO" => 'TOTAL', "QTD" => $total, "PERC" => '100%');
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else {
                        $aviso = "Favor selecionar o tipo de agrupamento.";
                    }
                } catch (GDbException $e) {
                    echo '<pre>';
                    var_dump($e);
                    echo '</pre>';
                }
            } else {
                $aviso = "Favor selecionar como deseja ordenar.";
            }
        } else {
            $aviso = "Favor selecionar como deseja agrupar.";
        }
    } else {
        $aviso = "Favor selecionar ao menos uma escola.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>