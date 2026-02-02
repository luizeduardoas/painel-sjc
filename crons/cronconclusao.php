<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
//set_time_limit(SYS_TIME_LIMIT);
set_time_limit(3600); // 1 hora

$root = ROOT_LOGS . "crons/conclusoes/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de conclusão iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de conclusão',
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
    $qtd = atualizarConclusao($fp);
    $fim = microtime(true);
    $totalRetornado = $qtd['totalRetornado'];
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de conclusão: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de conclusão: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 10));

// <editor-fold defaultstate="collapsed" desc="Atualizar conclusão">
function atualizarConclusao($fp) {
    $count = array('inserido' => 0, 'erro' => 0, 'totalRetornado' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR CONCLUSÃO - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();

        $qryCurso = "(SELECT cur_int_codigo FROM ava_curso WHERE cur_int_courseid = ?)";
        $qryUsuario = "(SELECT usu_int_codigo FROM ava_usuario WHERE usu_int_userid = ?)";
        $qryModulo = "(SELECT mod_int_codigo FROM ava_modulo WHERE mod_int_course_module_id = ?)";

        $mysql = new GDbMysql();
        $ultimo = $mysql->executeValue("SELECT IFNULL(MAX(con_dti_datahora), '2022-01-01 00:00:00') FROM ava_conclusao;");

        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT modulo, userid, courseid, datahora, visualizado, concluido, coursemoduleid FROM vw_painel_conclusoes WHERE datahora > ? ORDER BY datahora ASC LIMIT " . SYS_LIMIT_DADOS_CRON . ";", array("s", $ultimo));
        $count['totalRetornado'] = $mysqlMoodle->numRows();
        echo '<dt class="text-info">Conclusões:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $identificador = 'Módulo: ' . $mysqlMoodle->res["modulo"] . ' - Visualizado: ' . $mysqlMoodle->res["visualizado"] . ' - Concluido: ' . $mysqlMoodle->res["concluido"] . ' - Userid: ' . $mysqlMoodle->res["userid"] . ' - Courseid: ' . $mysqlMoodle->res["courseid"] . ' - Coursemoduleid: ' . $mysqlMoodle->res["coursemoduleid"] . ' - DataHora: ' . $mysqlMoodle->res["datahora"];
                $mysql->execute("INSERT IGNORE INTO ava_conclusao (usu_int_codigo, cur_int_codigo, mod_int_codigo, con_dti_criacao, con_dti_datahora, con_var_modulo, con_cha_visualizado, con_cha_concluido) VALUES ($qryUsuario,$qryCurso,$qryModulo,NOW(),?,?,?,?);", array("iiissis", $mysqlMoodle->res["userid"], $mysqlMoodle->res["courseid"], $mysqlMoodle->res["coursemoduleid"], $mysqlMoodle->res["datahora"], substr($mysqlMoodle->res["modulo"], 0, 200), $mysqlMoodle->res["visualizado"], $mysqlMoodle->res["concluido"]), false);
                $count['inserido']++;
                echo '<dt class="text-success">' . $count['inserido'] . ' - Inserido</dt><dd class="text-success">' . $identificador . '</dd>';
                fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['inserido'] . ' - Inserido - ' . $identificador . "\n");
                ob_flush();
                flush();
            }
        }
    } catch (GDbException $e) {
        $count['erro']++;
        echo '<dt class="text-danger">' . $count['erro'] . ' - Erro</dt><dd class="text-danger">' . $e->getError() . ' </dd>';
        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['erro'] . ' - ERRO - ' . $e->getError() . "\n");
    }
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR CONCLUSÃO - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de conclusão finalizado', $totais);

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