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
        $filtro_agrupamento = $arrParam["filtro_agrupamento"];
        if (!seNuloOuVazioOuMenosUm($filtro_agrupamento)) {
            $ordenacao = $arrParam["ordenacao"];
            if (!seNuloOuVazioOuMenosUm($ordenacao)) {
                try {
                    $link = null;
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
                    $filtro_periodo = $arrParam["filtro_periodo"];
                    $arrData = explode(" - ", $filtro_periodo);
                    $filter->addClause("AND (ace_dti_datahora BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");

                    switch ($ordenacao) {
                        case 'DC': // Data Crescente
                            $filter->setOrder(array("ace_dti_datahora" => "ASC"));
                            break;
                        case 'DD': // Data Decrescente
                            $filter->setOrder(array("ace_dti_datahora" => "DESC"));
                            break;
                        case 'HC': // Horario Crescente
                            $filter->setOrder(array("HORA" => "ASC"));
                            break;
                        case 'HD': // Horario Decrescente
                            $filter->setOrder(array("HORA" => "DESC"));
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
                        $query[] = "SELECT COUNT(DISTINCT(a.ace_int_codigo)) AS QTD, e.esc_int_codigo, e.esc_var_nome AS ESCOLA ";
                        $query[] = "FROM ava_acesso a ";
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
                        $query[] = "SELECT COUNT(DISTINCT(a.ace_int_codigo)) AS QTD, u.usu_int_codigo, u.usu_var_nome AS USUARIO ";
                        $query[] = "FROM ava_acesso a ";
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
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'D') {
                        $filter->setGroupBy("DATE_FORMAT(a.ace_dti_datahora, '%d/%m/%Y')");

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(DISTINCT(a.usu_int_codigo)) AS QTD, DATE_FORMAT(a.ace_dti_datahora, '%d/%m/%Y') AS DATA, a.ace_dti_datahora ";
                        $query[] = "FROM ava_acesso a ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = a.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlTotal = new GDbMysql();
                        $query[0] = "SELECT COUNT(*) ";
                        $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Data", "Quantidade", "Percentual");
                            $arrFormats = array("text-align: center", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                                $arrDados[] = array("DATA" => $mysql->res["DATA"], "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'H') {
                        $filter->setGroupBy("DATE_FORMAT(a.ace_dti_datahora, '%H')");

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(DISTINCT(a.usu_int_codigo)) AS QTD, DATE_FORMAT(a.ace_dti_datahora, '%H') AS HORA ";
                        $query[] = "FROM ava_acesso a ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = a.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlTotal = new GDbMysql();
                        $query[0] = "SELECT COUNT(*) ";
                        $total = $mysqlTotal->executeValue(implode("", $query) . $filter->getWhere(true), $filter->getParam());

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Horário", "Quantidade", "Percentual");
                            $arrFormats = array("text-align: center", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $total), 2) . '%';
                                $arrDados[] = array("HORA" => $mysql->res["HORA"] . ":00", "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
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
        $aviso = "Favor selecionar o tipo de acesso.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>