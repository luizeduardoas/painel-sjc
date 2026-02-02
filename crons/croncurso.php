<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/cursos/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de curso iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de curso',
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
    $qtd = atualizarCurso($fp);
    $fim = microtime(true);
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Retirados: ' . $qtd['retirado'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de curso: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de curso: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 6));

// <editor-fold defaultstate="collapsed" desc="Atualizar curso">
function atualizarCurso($fp) {
    $count = array('inserido' => 0, 'retirado' => 0, 'erro' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR CURSO - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT id, fullname, category, CASE visible WHEN 1 THEN 'S' ELSE 'N' END AS visivel FROM moodle.mdl_course WHERE id > 1 ORDER BY fullname");
        echo '<dt class="text-info">Moodle:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $arrMoodle[] = array("id" => $mysqlMoodle->res["id"], "nome" => trim($mysqlMoodle->res["fullname"]), "categoria" => $mysqlMoodle->res["category"], "visivel" => $mysqlMoodle->res["visivel"]);
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Painel...</dd>';
        ob_flush();
        flush();
        $arrCurso = array();
        $mysql = new GDbMysql();
        $mysql->execute("SELECT cur_int_codigo, niv_var_identificador, cur_var_nome, cur_int_courseid, cur_cha_visivel FROM ava_curso cur INNER JOIN nivel niv ON (niv.niv_int_codigo = cur.niv_int_codigo) ORDER BY cur_var_nome");
        echo '<dt class="text-info">Painel:</dt><dd class="text-info">' . $mysql->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysql->numRows()) {
            while ($mysql->fetch()) {
                $arrCurso[] = array("id" => $mysql->res["cur_int_courseid"], "nome" => trim($mysql->res["cur_var_nome"]), "categoria" => $mysql->res["niv_var_identificador"], "visivel" => $mysql->res["cur_cha_visivel"]);
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Moodle e Painel...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrCurso as $curso) {
            $existe = false;
            foreach ($arrMoodle as $moodle) {
                if ($moodle["id"] == $curso["id"] /* && $moodle["nome"] == $curso["nome"] && $moodle["categoria"] == $curso["categoria"] && $moodle["visivel"] == $curso["visivel"] */) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Id: ' . $curso["id"] . ' - Nome: ' . $curso["nome"] . ' - Categoria: ' . $curso["categoria"];
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("DELETE FROM ava_curso WHERE cur_int_courseid = ?;", array("i", $curso["id"]), false);
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
            foreach ($arrCurso as $curso) {
                if ($moodle["id"] == $curso["id"] && $moodle["nome"] == $curso["nome"] && $moodle["categoria"] == $curso["categoria"] && $moodle["visivel"] == $curso["visivel"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Id: ' . $moodle["id"] . ' - Nome: ' . $moodle["nome"] . ' - Categoria: ' . $moodle["categoria"] . ' - Visível: ' . $moodle["visivel"];
                try {
                    $mysql = new GDbMysql();
                    $qtd = $mysql->executeValue("SELECT COUNT(*) FROM ava_curso WHERE cur_int_courseid = ?", array("s", $moodle["id"]));
                    if ($qtd > 0) {
                        $mysql->execute("UPDATE ava_curso SET niv_int_codigo = (SELECT niv_int_codigo FROM nivel WHERE niv_var_identificador = ?), cur_var_nome = ?, cur_cha_visivel = ? WHERE cur_int_courseid = ?;", array("ssss", $moodle["categoria"], $moodle["nome"], $moodle["visivel"], $moodle["id"]), false);
                        $count['alterado']++;
                        echo '<dt class="text-success">' . $count['alterado'] . ' - Alterado</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['alterado'] . ' - Alterado - ' . $identificador . "\n");
                    } else {
                        $mysql->execute("INSERT INTO ava_curso (niv_int_codigo, cur_int_courseid, cur_var_nome, cur_cha_visivel) VALUES ((SELECT niv_int_codigo FROM nivel WHERE niv_var_identificador = ?),?,?,?);", array("ssss", $moodle["categoria"], $moodle["id"], $moodle["nome"], $moodle["visivel"]), false);
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
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR CURSO - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de curso finalizado', $totais);

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