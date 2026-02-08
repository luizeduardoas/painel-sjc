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

                    if (count($arrParam["filtro_curso"]) != buscarQtdCursos()) {
                        $filter->addClause("AND m.cur_int_codigo IN ($filtro_curso) ");
                    }
                    if (count($arrParam["filtro_escola"]) != buscarQtdEscolas()) {
                        $filter->addClause("AND u.esc_int_codigo IN ($filtro_escola) ");
                    }
                    if ($filtro_agrupamento == 'E' && seNuloOuVazioOuZeroOuMenosUm($arrParam["tipo"] ?? null)) {
                        $filter->setGroupBy("e.esc_int_codigo, e.esc_var_nome");
                        switch ($ordenacao) {
                            case 'QC': // Quantidade Crescente
                                $filter->setOrder(array("PROGRESSO_MEDIO_PERCENTUAL" => "ASC"));
                                break;
                            case 'QD': // Quantidade Decrescente
                                $filter->setOrder(array("PROGRESSO_MEDIO_PERCENTUAL" => "DESC"));
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

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT e.esc_int_codigo, e.esc_var_nome AS ESCOLA, COUNT(m.mat_int_codigo) AS TOTAL_MATRICULAS, ROUND(AVG(m.mat_dec_percentual), 2) AS PROGRESSO_MEDIO_PERCENTUAL ";
                        $query[] = "FROM escola e ";
                        $query[] = "INNER JOIN ava_usuario u ON (e.esc_int_codigo = u.esc_int_codigo) ";
                        $query[] = "INNER JOIN ava_matricula m ON (u.usu_int_codigo = m.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Escola", "Quantidade Alunos", "Percentual Médio");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $arrDados[] = array("ESCOLA" => '<a class="__pointer" onclick="carregarTabela(\'E\', ' . $mysql->res["esc_int_codigo"] . ');">' . $mysql->res["ESCOLA"] . '</a>', "QTD" => $mysql->res["TOTAL_MATRICULAS"], "PERC" => carregarBarraProgresso($mysql->res["PROGRESSO_MEDIO_PERCENTUAL"]));
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'E' && !seNuloOuVazioOuZeroOuMenosUm($arrParam["codigo"] ?? null)) {
                        $filter->addFilter("AND", "u.esc_int_codigo", "=", "i", $arrParam["codigo"]);
                        switch ($ordenacao) {
                            case 'QC': // Quantidade Crescente
                                $filter->setOrder(array("PERCENTUAL_PROGRESSO" => "ASC"));
                                break;
                            case 'QD': // Quantidade Decrescente
                                $filter->setOrder(array("PERCENTUAL_PROGRESSO" => "DESC"));
                                break;
                            case 'EC': // Escola Crescente
                                $filter->setOrder(array("NOME" => "ASC"));
                                break;
                            case 'ED': // Escola Decrescente
                                $filter->setOrder(array("NOME" => "DESC"));
                                break;
                            default:
                                break;
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT u.usu_int_codigo, u.usu_var_nome AS NOME, c.cur_var_nome AS CURSO, m.mat_int_qtd_concluida AS ATIVIDADES_FEITAS, c.cur_int_total_modulos AS TOTAL_ATIVIDADES, m.mat_dec_percentual AS PERCENTUAL_PROGRESSO, u.usu_var_cpf AS CPF, u.usu_var_cargo AS CARGO ";
                        $query[] = "FROM ava_usuario u ";
                        $query[] = "INNER JOIN ava_matricula m ON (u.usu_int_codigo = m.usu_int_codigo) ";
                        $query[] = "INNER JOIN ava_curso c ON (m.cur_int_codigo = c.cur_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlEscola = new GDbMysql();
                        $escola = $mysqlEscola->executeValue("SELECT esc_var_nome FROM escola WHERE esc_int_codigo = ?", array("i", $arrParam["codigo"]));

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Nome", "Curso", "CPF", "Cargo", "Módulos Totais", "Módulos Conclúidos", "Percentual");
                            $arrFormats = array("text-align: left", "text-align: left", "white-space: nowrap;text-align: center", "text-align: center", "white-space: nowrap;text-align: center", "white-space: nowrap;text-align: center", "white-space: nowrap;text-align: center");
                            $arrFooter = array();
                            $tituloCentral = "Escola: <b>$escola</b>";
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                            while ($mysql->fetch()) {
                                $arrDados[] = array("NOME" => $mysql->res["NOME"], "CURSO" => $mysql->res["CURSO"], "CPF" => $mysql->res["CPF"], "CARGO" => $mysql->res["CARGO"], "TOTAL_ATIVIDADES" => $mysql->res["TOTAL_ATIVIDADES"], "ATIVIDADES_FEITAS" => $mysql->res["ATIVIDADES_FEITAS"], "PERCENTUAL_PROGRESSO" => carregarBarraProgresso($mysql->res["PERCENTUAL_PROGRESSO"]));
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'C' && seNuloOuVazioOuZeroOuMenosUm($arrParam["tipo"] ?? null)) {
                        $filter->setGroupBy("c.cur_int_codigo, c.cur_var_nome");
                        switch ($ordenacao) {
                            case 'CC': // Curso Crescente
                                $filter->setOrder(array("CURSO" => "ASC"));
                                break;
                            case 'CD': // Curso Decrescente
                                $filter->setOrder(array("CURSO" => "DESC"));
                                break;
                            case 'QC': // Quantidade Crescente
                                $filter->setOrder(array("PROGRESSO_MEDIO_PERCENTUAL" => "ASC"));
                                break;
                            case 'QD': // Quantidade Decrescente
                                $filter->setOrder(array("PROGRESSO_MEDIO_PERCENTUAL" => "DESC"));
                                break;
                            default:
                                break;
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT c.cur_int_codigo, c.cur_var_nome AS CURSO, COUNT(m.mat_int_codigo) AS TOTAL_MATRICULAS, ROUND(AVG(m.mat_dec_percentual), 2) AS PROGRESSO_MEDIO_PERCENTUAL ";
                        $query[] = "FROM ava_curso c ";
                        $query[] = "INNER JOIN ava_matricula m ON (c.cur_int_codigo = m.cur_int_codigo) ";
                        $query[] = "INNER JOIN ava_usuario u ON (m.usu_int_codigo = u.usu_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());
                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Curso", "Quantidade Alunos", "Percentual Médio");
                            $arrFormats = array("", "text-align: center", "text-align: center");
                            $arrFooter = array();
                            $tituloCentral = false;
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = false;
                            while ($mysql->fetch()) {
                                $arrDados[] = array("CURSO" => '<a class="__pointer" onclick="carregarTabela(\'C\', ' . $mysql->res["cur_int_codigo"] . ');">' . $mysql->res["CURSO"] . '</a>', "QTD" => $mysql->res["TOTAL_MATRICULAS"], "PERC" => carregarBarraProgresso($mysql->res["PROGRESSO_MEDIO_PERCENTUAL"]));
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'C' && !seNuloOuVazioOuZeroOuMenosUm($arrParam["codigo"] ?? null)) {
                        $filter->addFilter("AND", "m.cur_int_codigo", "=", "i", $arrParam["codigo"]);
                        switch ($ordenacao) {
                            case 'CC': // Curso Crescente
                                $filter->setOrder(array("NOME" => "ASC"));
                                break;
                            case 'CD': // Curso Decrescente
                                $filter->setOrder(array("NOME" => "DESC"));
                                break;
                            case 'QC': // Quantidade Crescente
                                $filter->setOrder(array("PERCENTUAL_PROGRESSO" => "ASC"));
                                break;
                            case 'QD': // Quantidade Decrescente
                                $filter->setOrder(array("PERCENTUAL_PROGRESSO" => "DESC"));
                                break;
                            default:
                                break;
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT u.usu_int_codigo, u.usu_var_nome AS NOME, e.esc_var_nome AS ESCOLA, m.mat_int_qtd_concluida AS ATIVIDADES_FEITAS, c.cur_int_total_modulos AS TOTAL_ATIVIDADES, m.mat_dec_percentual AS PERCENTUAL_PROGRESSO, u.usu_var_cpf AS CPF, u.usu_var_cargo AS CARGO ";
                        $query[] = "FROM ava_matricula m ";
                        $query[] = "INNER JOIN ava_usuario u ON (m.usu_int_codigo = u.usu_int_codigo) ";
                        $query[] = "INNER JOIN escola e ON (u.esc_int_codigo = e.esc_int_codigo) ";
                        $query[] = "INNER JOIN ava_curso c ON (m.cur_int_codigo = c.cur_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());

                        $mysqlCurso = new GDbMysql();
                        $curso = $mysqlCurso->executeValue("SELECT cur_var_nome FROM ava_curso WHERE cur_int_codigo = ?", array("i", $arrParam["codigo"]));

                        if ($mysql->numRows()) {
                            $aviso = null;
                            $arrTitulos = array("Nome", "Escola", "CPF", "Cargo", "Módulos Totais", "Módulos Conclúidos", "Percentual");
                            $arrFormats = array("text-align: left", "text-align: left", "white-space: nowrap;text-align: center", "text-align: center", "white-space: nowrap;text-align: center", "white-space: nowrap;text-align: center", "white-space: nowrap;text-align: center");
                            $arrFooter = array();
                            $tituloCentral = "Curso: <b>$curso</b>";
                            $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                            $styleTituloCentral = "text-align: center;color: #ffffff;";
                            $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                            while ($mysql->fetch()) {
                                $arrDados[] = array("NOME" => $mysql->res["NOME"], "ESCOLA" => $mysql->res["ESCOLA"], "CPF" => $mysql->res["CPF"], "CARGO" => $mysql->res["CARGO"], "TOTAL_ATIVIDADES" => $mysql->res["TOTAL_ATIVIDADES"], "ATIVIDADES_FEITAS" => $mysql->res["ATIVIDADES_FEITAS"], "PERCENTUAL_PROGRESSO" => carregarBarraProgresso($mysql->res["PERCENTUAL_PROGRESSO"]));
                            }
                        } else {
                            $aviso = "Nenhum dado foi encontrado para os filtros selecionados.";
                        }
                    } else if ($filtro_agrupamento == 'A') {
                        switch ($ordenacao) {
                            case 'AC': // Aluno Crescente
                                $filter->setOrder(array("NOME" => "ASC"));
                                break;
                            case 'AD': // Aluno Decrescente
                                $filter->setOrder(array("NOME" => "DESC"));
                                break;
                            case 'QC': // Quantidade Crescente
                                $filter->setOrder(array("PERCENTUAL_PROGRESSO" => "ASC"));
                                break;
                            case 'QD': // Quantidade Decrescente
                                $filter->setOrder(array("PERCENTUAL_PROGRESSO" => "DESC"));
                                break;
                            default:
                                break;
                        }

                        $mysql = new GDbMysql();
                        $query = array();
                        $query[] = "SELECT u.usu_int_codigo, u.usu_var_nome AS NOME, e.esc_var_nome AS ESCOLA, c.cur_var_nome AS CURSO, m.mat_int_qtd_concluida AS ATIVIDADES_FEITAS, c.cur_int_total_modulos AS TOTAL_ATIVIDADES, m.mat_dec_percentual AS PERCENTUAL_PROGRESSO, u.usu_var_cpf AS CPF, u.usu_var_cargo AS CARGO ";
                        $query[] = "FROM ava_matricula m ";
                        $query[] = "INNER JOIN ava_usuario u ON (m.usu_int_codigo = u.usu_int_codigo) ";
                        $query[] = "INNER JOIN escola e ON (u.esc_int_codigo = e.esc_int_codigo) ";
                        $query[] = "INNER JOIN ava_curso c ON (m.cur_int_codigo = c.cur_int_codigo) ";
                        $mysql->execute(implode("", $query) . $filter->getWhere(false), $filter->getParam());
                        if ($mysql->numRows()) {
                            if ($mysql->numRows() < 1000 || $excel) {
                                $aviso = null;
                                $arrTitulos = array("Nome", "Escola", "Curso", "CPF", "Cargo", "Módulos Totais", "Módulos Conclúidos", "Percentual");
                                $arrFormats = array("text-align: left", "text-align: left", "text-align: left", "white-space: nowrap;text-align: center", "text-align: center", "white-space: nowrap;text-align: center", "white-space: nowrap;text-align: center", "white-space: nowrap;text-align: center");
                                $arrFooter = array();
                                $tituloCentral = false;
                                $arrStyleTitulos = array("background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;", "background: #f2f2f2;text-align: center;");
                                $styleTituloCentral = "text-align: center;color: #ffffff;";
                                $link = '<a class="btn-block text-center __pointer mt-2" onclick="carregarTabela(null, null);"><i class="fa fa-undo"></i> Voltar</a>';
                                while ($mysql->fetch()) {
                                    $arrDados[] = array("NOME" => $mysql->res["NOME"], "ESCOLA" => $mysql->res["ESCOLA"], "CURSO" => $mysql->res["CURSO"], "CPF" => $mysql->res["CPF"], "CARGO" => $mysql->res["CARGO"], "TOTAL_ATIVIDADES" => $mysql->res["TOTAL_ATIVIDADES"], "ATIVIDADES_FEITAS" => $mysql->res["ATIVIDADES_FEITAS"], "PERCENTUAL_PROGRESSO" => carregarBarraProgresso($mysql->res["PERCENTUAL_PROGRESSO"], $excel));
                                }
                            } else {
                                $aviso = "Essa consulta retorna mais de 1.000 registros, portanto não é possível exibi-la. Favor selecione os filtros para ser mais objetivo.";
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
        $aviso = "Favor selecionar ao menos uma escola.";
    }
} else {
    $aviso = "Favor selecionar ao menos um curso.";
}
?>