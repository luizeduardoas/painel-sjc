<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/nivel/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de estrutura organizacional iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de estrutura organizacional',
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
    $qtd = atualizarNiveis($fp);
    $fim = microtime(true);
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Alterados: ' . $qtd['alterado'] . ' - Retirados: ' . $qtd['retirado'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de estrutura organizacional: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de estrutura organizacional: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 6));

// <editor-fold defaultstate="collapsed" desc="Atualizar estrutura organizacional">
function atualizarNiveis($fp) {
    $count = array('inserido' => 0, 'alterado' => 0, 'retirado' => 0, 'erro' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR NIVEIS - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT id, nome, hierarquia, nivel, pai, codigos, CASE visivel WHEN 1 THEN 'S' ELSE 'N' END AS visivel FROM moodle.vw_categorias ORDER BY hierarquia, nivel");
        echo '<dt class="text-info">Moodle:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $arrMoodle[] = array("id" => $mysqlMoodle->res["id"], "nome" => $mysqlMoodle->res["nome"], "nivel" => $mysqlMoodle->res["nivel"], "hierarquia" => $mysqlMoodle->res["hierarquia"], "pai" => seVazioRetorneZero($mysqlMoodle->res["pai"]), "visivel" => $mysqlMoodle->res["visivel"]);
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Painel...</dd>';
        ob_flush();
        flush();
        $arrNivel = array();
        $mysql = new GDbMysql();
        $mysql->execute("SELECT niv_int_codigo, niv_var_identificador, niv_var_nome, niv_int_nivel, niv_var_identificador_pai, niv_var_hierarquia, niv_cha_visivel FROM nivel ORDER BY niv_int_nivel, niv_var_nome");
        echo '<dt class="text-info">Painel:</dt><dd class="text-info">' . $mysql->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysql->numRows()) {
            while ($mysql->fetch()) {
                $arrNivel[] = array("id" => $mysql->res["niv_var_identificador"], "nome" => $mysql->res["niv_var_nome"], "nivel" => $mysql->res["niv_int_nivel"], "hierarquia" => $mysql->res["niv_var_hierarquia"], "pai" => seVazioRetorneZero($mysql->res["niv_var_identificador_pai"]), "visivel" => $mysql->res["niv_cha_visivel"]);
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Moodle e Painel...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrNivel as $nivel) {
            $existe = false;
            foreach ($arrMoodle as $moodle) {
                if ($moodle["id"] == $nivel["id"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Id: ' . $nivel["id"] . ' - Nome: ' . $nivel["nome"] . ' - Nível: ' . $nivel["nivel"] . ' - Hierarquia: ' . $nivel["hierarquia"] . ' - Pai: ' . $nivel["pai"] . ' - Visível: ' . $nivel["visivel"];
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("DELETE FROM nivel WHERE niv_var_identificador = ?;", array("s", $nivel["id"]), false);
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
            foreach ($arrNivel as $nivel) {
                if ($moodle["id"] == $nivel["id"] && $moodle["nome"] == $nivel["nome"] && $moodle["nivel"] == $nivel["nivel"] && $moodle["hierarquia"] == $nivel["hierarquia"] && $moodle["pai"] == $nivel["pai"] && $moodle["visivel"] == $nivel["visivel"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Id: ' . $moodle["id"] . ' - Nome: ' . $moodle["nome"] . ' - Nível: ' . $moodle["nivel"] . ' - Hierarquia: ' . $moodle["hierarquia"] . ' - Pai: ' . $moodle["pai"] . ' - Visível: ' . $moodle["visivel"];
                try {
                    $mysql = new GDbMysql();
                    $qtd = $mysql->executeValue("SELECT COUNT(*) FROM nivel WHERE niv_var_identificador = ?", array("s", $moodle["id"]));
                    if ($qtd > 0) {
                        $mysql->execute("UPDATE nivel SET niv_var_nome = ?, niv_int_nivel = ?, niv_var_hierarquia = ?, niv_var_identificador_pai = ?, niv_cha_visivel = ? WHERE niv_var_identificador = ?;", array("sissss", $moodle["nome"], $moodle["nivel"], $moodle["hierarquia"], $moodle["pai"], $moodle["visivel"], $moodle["id"]), false);
                        $count['alterado']++;
                        echo '<dt class="text-success">' . $count['alterado'] . ' - Alterado</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['alterado'] . ' - Alterado - ' . $identificador . "\n");
                    } else {
                        $mysql->execute("INSERT INTO nivel (niv_var_identificador, niv_var_nome, niv_int_nivel, niv_var_hierarquia, niv_var_identificador_pai, niv_cha_visivel) VALUES (?,?,?,?,?,?);", array("ssisss", $moodle["id"], $moodle["nome"], $moodle["nivel"], $moodle["hierarquia"], $moodle["pai"], $moodle["visivel"]), false);
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
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR NIVEIS - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de estrutura organizacional finalizado', $totais);

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