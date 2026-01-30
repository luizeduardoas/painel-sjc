<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/matriculas/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de matrícula iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de matrículas',
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
    $qtd = atualizarMatricula($fp);
    $fim = microtime(true);
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Retirados: ' . $qtd['retirado'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de matrícula: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de matrícula: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 6));

// <editor-fold defaultstate="collapsed" desc="Atualizar matrícula">
function atualizarMatricula($fp) {
    $count = array('inserido' => 0, 'retirado' => 0, 'erro' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR MATRÍCULA - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT * FROM vw_painel_matriculas");
        echo '<dt class="text-info">Moodle:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $arrMoodle[] = array("courseid" => $mysqlMoodle->res["courseid"], "userid" => $mysqlMoodle->res["userid"], "inicio" => $mysqlMoodle->res["inicio"], "termino" => seVazioRetorneNulo($mysqlMoodle->res["termino"]));
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Painel...</dd>';
        $arrMatricula = array();
        $mysql = new GDbMysql();
        $query = "SELECT c.cur_int_courseid, u.usu_int_userid, m.mat_dti_inicio, m.mat_dti_termino ";
        $query .= "FROM ava_matricula m ";
        $query .= "INNER JOIN ava_usuario u ON (u.usu_int_codigo = m.usu_int_codigo) ";
        $query .= "INNER JOIN ava_curso c ON (c.cur_int_codigo = m.cur_int_codigo) ";
        $query .= "ORDER BY c.cur_int_courseid, u.usu_int_userid;";
        $mysql->execute($query);
        echo '<dt class="text-info">Painel:</dt><dd class="text-info">' . $mysql->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysql->numRows()) {
            while ($mysql->fetch()) {
                $arrMatricula[] = array("courseid" => $mysql->res["cur_int_courseid"], "userid" => $mysql->res["usu_int_userid"], "inicio" => $mysql->res["mat_dti_inicio"], "termino" => seVazioRetorneNulo($mysql->res["mat_dti_termino"]));
            }
        }

        $qryCurso = "(SELECT cur_int_codigo FROM ava_curso WHERE cur_int_courseid = ?)";
        $qryUsuario = "(SELECT usu_int_codigo FROM ava_usuario WHERE usu_int_userid = ?)";

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Moodle e Painel...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrMatricula as $matricula) {
            $existe = false;
            foreach ($arrMoodle as $moodle) {
                if ($moodle["courseid"] == $matricula["courseid"] && $moodle["userid"] == $matricula["userid"] && $moodle["inicio"] == $matricula["inicio"] && $moodle["termino"] == $matricula["termino"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Cód. Curso: ' . $matricula["courseid"] . ' - Cód. Usuário: ' . $matricula["userid"] . ' - Início: ' . $matricula["inicio"] . ' - Término: ' . formataDadoVazio($matricula["termino"]);
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("DELETE FROM ava_matricula WHERE cur_int_codigo = $qryCurso AND usu_int_codigo = $qryUsuario;", array("ii", $matricula["courseid"], $matricula["userid"]), false);
                    $count['retirado']++;
                    echo '<dt class="text-success">' . $count['retirado'] . ' - Retirado</dt><dd class="text-success">' . $identificador . '</dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['retirado'] . ' - Retirado - ' . $identificador . "\n");
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
            foreach ($arrMatricula as $matricula) {
                if ($moodle["courseid"] == $matricula["courseid"] && $moodle["userid"] == $matricula["userid"] && $moodle["inicio"] == $matricula["inicio"] && $moodle["termino"] == $matricula["termino"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Cód. Curso: ' . $moodle["courseid"] . ' - Cód. Usuário: ' . $moodle["userid"] . ' - Início: ' . $moodle["inicio"] . ' - Término: ' . formataDadoVazio($moodle["termino"]);
                try {
                    $mysql = new GDbMysql();
                    $qtd = $mysql->executeValue("SELECT COUNT(*) FROM ava_matricula WHERE cur_int_codigo = $qryCurso AND usu_int_codigo = $qryUsuario", array("ii", $moodle["courseid"], $moodle["userid"]));
                    if ($qtd > 0) {
                        $mysql->execute("UPDATE ava_matricula SET mat_dti_inicio = ?, mat_dti_termino = ? WHERE cur_int_codigo = $qryCurso AND usu_int_codigo = $qryUsuario;", array("ssii", $moodle["inicio"], $moodle["termino"], $moodle["courseid"], $moodle["userid"]), false);
                        $count['alterado']++;
                        echo '<dt class="text-success">' . $count['alterado'] . ' - Alterado</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['alterado'] . ' - Alterado - ' . $identificador . "\n");
                    } else {
                        $mysql->execute("INSERT INTO ava_matricula (cur_int_codigo, usu_int_codigo, mat_dti_inicio, mat_dti_termino, mat_dti_criacao) VALUES ($qryCurso,$qryUsuario,?,?,NOW());", array("iiss", $moodle["courseid"], $moodle["userid"], $moodle["inicio"], $moodle["termino"]), false);
                        $count['inserido']++;
                        echo '<dt class="text-success">' . $count['inserido'] . ' - Inserido</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['inserido'] . ' - Inserido - ' . $identificador . "\n");
                    }
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
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR MATRÍCULA - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de matrícula finalizado', $totais);

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