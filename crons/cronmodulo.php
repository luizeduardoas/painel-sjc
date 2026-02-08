<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/modulos/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de módulo iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de módulos',
    'id' => 'cron',
    'col' => 6,
    'fa' => 'cogs'
));
echo $form->open("form");
echo '<fieldset>';
echo '<dl class="dl-horizontal">';
try {
    $totais = '';
    $ini = microtime(true);
    $qtd = atualizarModulo($fp);
    $fim = microtime(true);
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Alterados: ' . $qtd['alterado'] . ' - Retirados: ' . $qtd['retirado'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de módulo: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de módulo: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 6));

// <editor-fold defaultstate="collapsed" desc="Atualizar módulo">
function atualizarModulo($fp) {
    $count = array('inserido' => 0, 'alterado' => 0, 'retirado' => 0, 'erro' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR MÓDULO - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT * FROM vw_modulos_cursos WHERE course_id > 1 ORDER BY course_id, course_module_id");
        echo '<dt class="text-info">Moodle:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $arrMoodle[] = array("courseid" => $mysqlMoodle->res["course_id"], "coursemoduleid" => $mysqlMoodle->res["course_module_id"], "modulo" => trim($mysqlMoodle->res["module_display_name"]), "sessao" => seVazioRetorneNulo(trim($mysqlMoodle->res["section_display_name"])), "tipo" => substr($mysqlMoodle->res["module_type_name"], 0, 50), "conclusao" => ($mysqlMoodle->res["completion_type"] > 0 ? 'S' : 'N'));
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Painel...</dd>';
        $arrModulo = array();
        $mysql = new GDbMysql();
        $query = "SELECT c.cur_int_courseid, m.mod_int_course_module_id, m.mod_var_nome, m.mod_var_tipo, m.mod_var_sessao, m.mod_cha_conclusao ";
        $query .= "FROM ava_modulo m ";
        $query .= "INNER JOIN ava_curso c ON (c.cur_int_codigo = m.cur_int_codigo) ";
        $query .= "ORDER BY c.cur_int_courseid, m.mod_int_course_module_id;";
        $mysql->execute($query);
        echo '<dt class="text-info">Painel:</dt><dd class="text-info">' . $mysql->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysql->numRows()) {
            while ($mysql->fetch()) {
                $arrModulo[] = array("courseid" => $mysql->res["cur_int_courseid"], "coursemoduleid" => $mysql->res["mod_int_course_module_id"], "modulo" => $mysql->res["mod_var_nome"], "sessao" => seVazioRetorneNulo($mysql->res["mod_var_sessao"]), "tipo" => $mysql->res["mod_var_tipo"], "conclusao" => $mysql->res["mod_cha_conclusao"]);
            }
        }

        $qryCurso = "(SELECT cur_int_codigo FROM ava_curso WHERE cur_int_courseid = ?)";

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Moodle e Painel...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrModulo as $modulo) {
            $existe = false;
            foreach ($arrMoodle as $moodle) {
                if ($moodle["courseid"] == $modulo["courseid"] && $moodle["coursemoduleid"] == $modulo["coursemoduleid"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Cód. Curso: ' . $modulo["courseid"] . ' - Cód. Módulo: ' . $modulo["coursemoduleid"] . ' - Tipo: ' . $modulo["tipo"] . ' - Nome: ' . $modulo["nome"];
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("DELETE FROM ava_modulo WHERE cur_int_codigo = $qryCurso AND mod_int_course_module_id = ?;", array("ii", $modulo["courseid"], $modulo["coursemoduleid"]), false);
                    $count['retirado']++;
                    echo '<dt class="text-success">' . $count['retirado'] . ' - Retirado</dt><dd class="text-success">' . $identificador . '</dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['retirado'] . ' - Retirado - ' . $identificador . "\n");
                    atualizarMatriculas($fp, $modulo["courseid"]);
                } catch (GDbException $e) {
                    $count['erro']++;
                    echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">' . $identificador . ' - ' . $e->getError() . ' </dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - ' . $identificador . ' - ' . $e->getError() . "\n");
                }
                ob_flush();
                flush();
            }
        }
        if ($nenhum) {
            echo '<dt class="text-info">Diferenças:</dt><dd class="text-info">Nenhuma</dd>';
            ob_flush();
            flush();
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Painel e Moodle...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrMoodle as $moodle) {
            $existe = false;
            foreach ($arrModulo as $modulo) {
                if ($moodle["courseid"] == $modulo["courseid"] && $moodle["coursemoduleid"] == $modulo["coursemoduleid"] && $moodle["modulo"] == $modulo["modulo"] && $moodle["sessao"] == $modulo["sessao"] && $moodle["tipo"] == $modulo["tipo"] && $moodle["conclusao"] == $modulo["conclusao"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Cód. Curso: ' . $moodle["courseid"] . ' - Cód. Módulo: ' . $moodle["coursemoduleid"] . ' - Tipo: ' . $moodle["tipo"] . ' - Nome: ' . $moodle["modulo"];
                try {
                    $mysql = new GDbMysql();
                    $qtd = $mysql->executeValue("SELECT COUNT(*) FROM ava_modulo WHERE cur_int_codigo = $qryCurso AND mod_int_course_module_id = ?", array("ii", $moodle["courseid"], $moodle["coursemoduleid"]));
                    if ($qtd > 0) {
                        $mysql->execute("UPDATE ava_modulo SET mod_var_nome = ?, mod_var_tipo = ?, mod_var_sessao = ?, mod_cha_conclusao = ? WHERE cur_int_codigo = $qryCurso AND mod_int_course_module_id = ?;", array("ssssii", $moodle["modulo"], $moodle["tipo"], $moodle["sessao"], $moodle["conclusao"], $moodle["courseid"], $moodle["coursemoduleid"]), false);
                        $count['alterado']++;
                        echo '<dt class="text-success">' . $count['alterado'] . ' - Alterado</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['alterado'] . ' - Alterado - ' . $identificador . "\n");
                    } else {
                        $mysql->execute("INSERT INTO ava_modulo (cur_int_codigo, mod_int_course_module_id, mod_var_nome, mod_var_tipo, mod_var_sessao, mod_cha_conclusao) VALUES ($qryCurso,?,?,?,?,?);", array("iissss", $moodle["courseid"], $moodle["coursemoduleid"], $moodle["modulo"], $moodle["tipo"], $moodle["sessao"], $moodle["conclusao"]), false);
                        $count['inserido']++;
                        echo '<dt class="text-success">' . $count['inserido'] . ' - Inserido</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['inserido'] . ' - Inserido - ' . $identificador . "\n");
                    }
                    atualizarMatriculas($fp, $moodle["courseid"]);
                } catch (GDbException $e) {
                    $count['erro']++;
                    echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">' . $identificador . ' - ' . $e->getError() . ' </dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - ' . $identificador . ' - ' . $e->getError() . "\n");
                }
                ob_flush();
                flush();
            }
        }
        if ($nenhum) {
            echo '<dt class="text-info">Diferenças:</dt><dd class="text-info">Nenhuma</dd>';
            ob_flush();
            flush();
        }
    } catch (GDbException $e) {
        $count['erro']++;
        echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">' . $e->getError() . ' </dd>';
        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - ' . $e->getError() . "\n");
    }
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR MÓDULO - TÉRMINO ' . "\n");
    return $count;
}

function atualizarMatriculas($fp, $courseid) {
    try {  
        $mysql = new GDbMysql();
        $cur_int_codigo = $mysql->executeValue("SELECT cur_int_codigo FROM ava_curso WHERE cur_int_courseid = ?", array("i", $courseid));
        
        $query = "UPDATE ava_curso c ";
        $query .= "SET c.cur_int_total_modulos = ( ";
        $query .= "     SELECT COUNT(*) FROM ava_modulo ";
        $query .= "     WHERE cur_int_codigo = ? AND mod_cha_conclusao = 'S' ";
        $query .= " ) ";
        $query .= "WHERE c.cur_int_codigo = ?;";        
        $mysql = new GDbMysql();
        $mysql->execute($query, array("ii", $cur_int_codigo, $cur_int_codigo), false);        
        
        $query = "UPDATE ava_matricula m ";
        $query .= "JOIN ava_curso c ON (m.cur_int_codigo = c.cur_int_codigo) ";
        $query .= "SET m.mat_dec_percentual = IF(c.cur_int_total_modulos > 0, (m.mat_int_qtd_concluida / c.cur_int_total_modulos) * 100, 0) ";
        $query .= "WHERE m.cur_int_codigo = ?; ";
        $mysql = new GDbMysql();
        $mysql->execute($query, array("i", $cur_int_codigo), false);
    } catch (GDbException $e) {
        echo '<dt class="text-danger">Erro</dt><dd class="text-danger">Atualização de Matrículas - ' . $e->getError() . ' </dd>';
        fwrite($fp, date("d/m/Y H:i:s") . ' - ERRO - Atualização de Matrículas - ' . $e->getError() . "\n");
    }
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de módulo finalizado', $totais);

ob_end_flush();

fwrite($fp, date("d/m/Y H:i:s") . " ------ TÉRMINO CRON -----\n");
fclose($fp);
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_voltar").click(function () {
            if (window.history.length > 1) {
                jQuery.gDisplay.loadStart(' HTML');
                window.history.back();
            }
        });
        if (window.history.length < 2) {
            jQuery("#btn_voltar").attr("disabled", "disabled");
        }
    });
</script>