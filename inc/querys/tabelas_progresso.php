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

                    switch ($filtro_tipo) {
                        case "C":
                            $mod_cha_conclusao = " WHERE m2.mod_cha_conclusao = 'S' ";
                            $con_cha_concluido = " c.con_cha_concluido = 'S' ";
                            $filter->addClause("AND mod_cha_conclusao = 'S' ");
                            break;
                        case "V":
                            $mod_cha_conclusao = "";
                            $con_cha_concluido = " c.con_cha_visualizado = 'S' ";
                            break;
                        default:
                            break;
                    }

                    if (count($arrParam["filtro_curso"]) != locaQtdCursos()) {
                        $filter->addClause("AND a.cur_int_codigo IN ($filtro_curso) ");
                    }
                    $filtro_periodo = $arrParam["filtro_periodo"];
                    $arrData = explode(" - ", $filtro_periodo);
                    $filter->addClause("AND (con_dti_datahora BETWEEN '" . GF::formatarData($arrData[0]) . " 00:00:00' AND '" . GF::formatarData($arrData[1]) . " 23:59:59') ");

                    switch ($ordenacao) {
                        case 'CC': // Curso Crescente
                            $filter->setOrder(array("CURSO" => "ASC"));
                            break;
                        case 'CD': // Curso Decrescente
                            $filter->setOrder(array("CURSO" => "DESC"));
                            break;
                        case 'AC': // Aluno Crescente
                            $filter->setOrder(array("ALUNO" => "ASC"));
                            break;
                        case 'AD': // Aluno Decrescente
                            $filter->setOrder(array("ALUNO" => "DESC"));
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
                        $query[] = "SELECT COUNT(c.con_int_codigo) AS QTD, e.esc_int_codigo, e.esc_var_nome AS ESCOLA, ";
                        $query[] = "(SELECT COUNT(*) FROM ava_modulo m2 $mod_cha_conclusao) * COUNT(DISTINCT u.usu_int_codigo) AS TOTAL ";
                        $query[] = "FROM escola e ";
                        $query[] = "INNER JOIN ava_usuario u ON (e.esc_int_codigo = u.esc_int_codigo) ";
                        $query[] = "LEFT JOIN ava_conclusao c ON (u.usu_int_codigo = c.usu_int_codigo AND $con_cha_concluido) ";
                        $query[] = "INNER JOIN ava_modulo m ON (c.mod_int_codigo = m.mod_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Escola", "Quantidade", "Percentual");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $mysql->res["TOTAL"]), 2) . '%';
                                $arrDados[] = array("ESCOLA" => '<a class="__pointer" onclick="carregarTabela(\'E\', ' . $mysql->res["esc_int_codigo"] . ');">' . $mysql->res["ESCOLA"] . '</a>', "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'E' && !seNuloOuVazioOuZeroOuMenosUm($arrParam["codigo"] ?? null)) {
                        $filter->setGroupBy("u.usu_int_codigo, u.usu_var_nome, u.usu_var_cpf, u.usu_var_cargo, u.usu_var_funcao");
                        $filter->addFilter("AND", "u.esc_int_codigo", "=", "i", $arrParam["codigo"]);
                        if (substr($ordenacao, 1, 1) == 'C') {
                            $filter->setOrder(array("NOME" => "ASC"));
                        } else {
                            $filter->setOrder(array("NOME" => "DESC"));
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(c.con_int_codigo) AS QTD, u.usu_int_codigo, u.usu_var_nome AS NOME, u.usu_var_cpf AS CPF, u.usu_var_cargo AS CARGO, u.usu_var_funcao AS FUNCAO, ";
                        $query[] = "(SELECT COUNT(*) FROM ava_modulo m2 $mod_cha_conclusao) AS TOTAL ";
                        $query[] = "FROM ava_usuario u ";
                        $query[] = "LEFT JOIN ava_conclusao c ON (u.usu_int_codigo = c.usu_int_codigo AND $con_cha_concluido) ";
                        $query[] = "LEFT JOIN ava_modulo m ON (c.mod_int_codigo = m.mod_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlEscola = new GDbMysql();
                        $escola = $mysqlEscola->executeValue("SELECT esc_var_nome FROM escola WHERE esc_int_codigo = ?", array("i", $arrParam["codigo"]));

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Nome", "CPF", "Cargo", "Função", "Percentual");
                            $arrFormats = array("text-align: left", "text-align: center", "text-align: center", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = "Escola: <b>$escola</b>";
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $mysql->res["TOTAL"]), 2) . '%';
                                $arrDados[] = array("NOME" => $mysql->res["NOME"], "CPF" => $mysql->res["CPF"], "CARGO" => $mysql->res["CARGO"], "FUNCAO" => $mysql->res["FUNCAO"], "PERC" => $percentual);
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'C' && seNuloOuVazioOuZeroOuMenosUm($arrParam["tipo"] ?? null)) {
                        $filter->setGroupBy("cu.cur_int_codigo, cu.cur_var_nome");

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(c.con_int_codigo) AS QTD, cu.cur_int_codigo, cu.cur_var_nome AS CURSO, ";
                        $query[] = "(SELECT COUNT(*) FROM ava_modulo m2 $mod_cha_conclusao) * COUNT(DISTINCT u.usu_int_codigo) AS TOTAL ";
                        $query[] = "FROM ava_curso cu ";
                        $query[] = "INNER JOIN ava_matricula ma ON (cu.cur_int_codigo = ma.cur_int_codigo) ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = ma.usu_int_codigo) ";
                        $query[] = "LEFT JOIN ava_conclusao c ON (u.usu_int_codigo = c.usu_int_codigo AND $con_cha_concluido) ";
                        $query[] = "INNER JOIN ava_modulo m ON (c.mod_int_codigo = m.mod_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());
                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Curso", "Quantidade", "Percentual");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $mysql->res["TOTAL"]), 2) . '%';
                                $arrDados[] = array("CURSO" => '<a class="__pointer" onclick="carregarTabela(\'C\', ' . $mysql->res["cur_int_codigo"] . ');">' . $mysql->res["CURSO"] . '</a>', "QTD" => $mysql->res["QTD"], "PERC" => $percentual);
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'C' && !seNuloOuVazioOuZeroOuMenosUm($arrParam["codigo"] ?? null)) {
                        $filter->setGroupBy("u.usu_int_codigo, u.usu_var_nome, u.usu_var_cpf, u.usu_var_cargo, u.usu_var_funcao");
                        $filter->addFilter("AND", "ma.cur_int_codigo", "=", "i", $arrParam["codigo"]);
                        if (substr($ordenacao, 1, 1) == 'C') {
                            $filter->setOrder(array("NOME" => "ASC"));
                        } else {
                            $filter->setOrder(array("NOME" => "DESC"));
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT COUNT(c.con_int_codigo) AS QTD, u.usu_int_codigo, u.usu_var_nome AS NOME, u.usu_var_cpf AS CPF, u.usu_var_cargo AS CARGO, u.usu_var_funcao AS FUNCAO, ";
                        $query[] = "(SELECT COUNT(*) FROM ava_modulo m2 $mod_cha_conclusao) AS TOTAL ";
                        $query[] = "FROM ava_usuario u ";
                        $query[] = "INNER JOIN ava_matricula ma ON (u.usu_int_codigo = ma.usu_int_codigo) ";
                        $query[] = "LEFT JOIN ava_conclusao c ON (u.usu_int_codigo = c.usu_int_codigo AND ma.cur_int_codigo = c.cur_int_codigo AND $con_cha_concluido) ";
                        $query[] = "LEFT JOIN ava_modulo m ON (c.mod_int_codigo = m.mod_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlCurso = new GDbMysql();
                        $curso = $mysqlCurso->executeValue("SELECT cur_var_nome FROM ava_curso WHERE cur_int_codigo = ?", array("i", $arrParam["codigo"]));

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Nome", "CPF", "Cargo", "Função", "Percentual");
                            $arrFormats = array("text-align: left", "text-align: center", "text-align: center", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = "Curso: <b>$curso</b>";
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                            while ($mysql->fetch()) {
                                $percentual = round(porcentagem_nx($mysql->res["QTD"], $mysql->res["TOTAL"]), 2) . '%';
                                $arrDados[] = array("NOME" => $mysql->res["NOME"], "CPF" => $mysql->res["CPF"], "CARGO" => $mysql->res["CARGO"], "FUNCAO" => $mysql->res["FUNCAO"], "PERC" => $percentual);
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'A') {
                        if (substr($ordenacao, 1, 1) == 'C') {
                            $filter->setOrder(array("NOME" => "ASC"));
                        } else {
                            $filter->setOrder(array("NOME" => "DESC"));
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT DISTINCT(u.usu_int_codigo), u.usu_var_nome AS NOME, u.usu_var_cpf AS CPF, u.usu_var_cargo AS CARGO, u.usu_var_funcao AS FUNCAO ";
                        $query[] = "FROM ava_matricula m ";
                        $query[] = "INNER JOIN ava_usuario u ON (u.usu_int_codigo = m.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());
                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Nome", "CPF", "Cargo", "Função");
                            $arrFormats = array("text-align: left", "text-align: center", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            while ($mysql->fetch()) {
                                $arrDados[] = array("NOME" => $mysql->res["NOME"], "CPF" => $mysql->res["CPF"], "CARGO" => $mysql->res["CARGO"], "FUNCAO" => $mysql->res["FUNCAO"]);
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
        $aviso = "Favor selecionar o tipo de progresso.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>