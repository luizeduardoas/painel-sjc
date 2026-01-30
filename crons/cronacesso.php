<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/acessos/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de acesso iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de acesso',
    'id' => 'cron',
    'col' => 10,
    'fa' => 'cogs'
));
echo $form->open("form");
echo '<fieldset>';
echo '<dl class="dl-horizontal">';
$totalRetornado = 0;
try {
    $totais = '';
    $ini = microtime(true);
    $qtd = atualizarAcesso($fp);
    $fim = microtime(true);
    $totalRetornado = $qtd['totalRetornado'];
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de acesso: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de acesso: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 10));

// <editor-fold defaultstate="collapsed" desc="Atualizar acesso">
function atualizarAcesso($fp) {
    $count = array('inserido' => 0, 'erro' => 0, 'totalRetornado' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR ACESSO - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();

        $qryCurso = "(SELECT cur_int_codigo FROM ava_curso WHERE cur_int_courseid = ?)";
        $qryUsuario = "(SELECT usu_int_codigo FROM ava_usuario WHERE usu_int_userid = ?)";

        $mysql = new GDbMysql();
        $ultimo = $mysql->executeValue("SELECT IFNULL(MAX(ace_dti_datahora), '2022-01-01 00:00:00') FROM ava_acesso;");
        echo '<dt class="text-warning">Última atualização</dt><dd class="text-warning">' . $ultimo . '</dd>';
        ob_flush();
        flush();
        
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT action, target, userid, courseid, datahora FROM vw_painel_acessos WHERE datahora > ? ORDER BY datahora ASC LIMIT " . SYS_LIMIT_DADOS_CRON . ";", array("s", $ultimo));
        $count['totalRetornado'] = $mysqlMoodle->numRows();
        echo '<dt class="text-info">Acessos:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $identificador = 'Action: ' . $mysqlMoodle->res["action"] . ' - Target: ' . $mysqlMoodle->res["target"] . ' - Userid: ' . $mysqlMoodle->res["userid"] . ' - Courseid: ' . $mysqlMoodle->res["courseid"] . ' - DataHora: ' . $mysqlMoodle->res["datahora"];
                $ace_var_tipo = null;
                if ($mysqlMoodle->res["target"] == 'user') {
                    if ($mysqlMoodle->res["action"] == 'loggedin') {
                        $ace_var_tipo = 'EA';
                    } else if ($mysqlMoodle->res["action"] == 'loggedout') {
                        $ace_var_tipo = 'SA';
                    }
                } else if ($mysqlMoodle->res["target"] == 'course') {
                    if ($mysqlMoodle->res["action"] == 'viewed') {
                        $ace_var_tipo = 'EC';
                    }
                }
                if (!seNuloOuVazio($ace_var_tipo)) {
                    $mysql->execute("INSERT IGNORE INTO ava_acesso (usu_int_codigo, cur_int_codigo, ace_dti_criacao, ace_dti_datahora, ace_var_tipo) VALUES ($qryUsuario,$qryCurso,NOW(),?,?);", array("iiss", $mysqlMoodle->res["userid"], $mysqlMoodle->res["courseid"], $mysqlMoodle->res["datahora"], $ace_var_tipo), false);
                    $count['inserido']++;
                    echo '<dt class="text-success">' . $count['inserido'] . ' - Inserido</dt><dd class="text-success">' . $identificador . '</dd>';
                    fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['inserido'] . ' - Inserido - ' . $identificador . "\n");
                    ob_flush();
                    flush();
                }
            }
        }
    } catch (GDbException $e) {
        $count['erro']++;
        echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">' . $e->getError() . ' </dd>';
        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - ' . $e->getError() . "\n");
    }
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR ACESSO - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de acesso finalizado', $totais);

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
<?php
if ($totalRetornado == SYS_LIMIT_DADOS_CRON) {
    echo 'jQuery.gDisplay.loadStart("HTML");window.location.reload();';
}
?>
    });
</script>