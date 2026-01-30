<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/escolas/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de escolas iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de escolas',
    'id' => 'cron',
    'col' => 8,
    'fa' => 'cogs'
));
echo $form->open("form");
echo '<fieldset>';
echo '<dl class="dl-horizontal">';
try {
    $totais = '';
    $ini = microtime(true);
    $qtd = atualizarEscolas($fp);
    $fim = microtime(true);
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Retirados: ' . $qtd['retirado'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de escolas: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de escolas: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 8));

// <editor-fold defaultstate="collapsed" desc="Atualizar escolas">
function atualizarEscolas($fp) {
    $count = array('inserido' => 0, 'retirado' => 0, 'erro' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR ESCOLAS - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT DISTINCT(institution) AS nome FROM mdl_user WHERE suspended = 0 AND deleted = 0 AND institution IS NOT NULL AND institution <> '' ORDER BY institution");
        echo '<dt class="text-info">Moodle:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $arrMoodle[] = trim($mysqlMoodle->res["nome"]);
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Painel...</dd>';
        ob_flush();
        flush();
        $arrEscola = array();
        $mysql = new GDbMysql();
        $mysql->execute("SELECT esc_int_codigo, esc_var_nome FROM escola ORDER BY esc_var_nome");
        echo '<dt class="text-info">Painel:</dt><dd class="text-info">' . $mysql->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysql->numRows()) {
            while ($mysql->fetch()) {
                $arrEscola[] = trim($mysql->res["esc_var_nome"]);
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Moodle e Painel...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrEscola as $escola) {
            $existe = false;
            foreach ($arrMoodle as $moodle) {
                if ($moodle == $escola) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("DELETE FROM escola WHERE esc_var_nome = ?;", array("s", $escola), false);
                    $count['retirado']++;
                    echo '<dt class="text-success">' . $count['retirado'] . ' - Retirado</dt><dd class="text-success">' . 'Escola: ' . $escola . '</dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['retirado'] . ' - Retirado - Escola: ' . $escola . "\n");
                } catch (GDbException $e) {
                    $count['erro']++;
                    echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">Escola: ' . $escola . ' </dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - Escola: ' . $escola . "\n");
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
            foreach ($arrEscola as $escola) {
                if ($moodle == $escola) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("INSERT INTO escola (esc_var_nome) VALUES (?);", array("s", $moodle), false);
                    $count['inserido']++;
                    echo '<dt class="text-success">' . $count['inserido'] . ' - Inserido</dt><dd class="text-success">' . 'Escola: ' . $moodle . '</dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['inserido'] . ' - Inserido - Escola: ' . $moodle . "\n");
                } catch (GDbException $e) {
                    $count['erro']++;
                    echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">Escola: ' . $moodle . ' </dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - Escola: ' . $moodle . "\n");
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
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR ESCOLAS - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de escolas finalizado', $totais);

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