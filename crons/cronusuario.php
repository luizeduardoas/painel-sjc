<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/usuarios/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de atualização de usuário iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de atualização de usuário',
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
    $qtd = atualizarUsuario($fp);
    $fim = microtime(true);
    $resumo = 'Inseridos: ' . $qtd['inserido'] . ' - Alterados: ' . $qtd['alterado'] . ' - Retirados: ' . $qtd['retirado'] . ' - Erros: ' . $qtd['erro'];
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $resumo . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $resumo . '<br/>';
    $tempo = $fim > $ini ? round(((double) $fim - (double) $ini), 3) : '0.999';
    echo '<dt class="text-info">Tempo de Execução</dt><dd class="text-info"><i>' . $tempo . '</i> segundos</dd>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de atualização de usuário: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de atualização de usuário: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getTraceAsString(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 6));

// <editor-fold defaultstate="collapsed" desc="Atualizar usuário">
function atualizarUsuario($fp) {
    $count = array('inserido' => 0, 'alterado' => 0, 'retirado' => 0, 'erro' => 0);
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR USUÁRIO - INÍCIO ' . "\n");
    try {
        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Moodle...</dd>';
        ob_flush();
        flush();
        $arrMoodle = array();
        $mysqlMoodle = new GDbMysqlMoodle();
        $mysqlMoodle->execute("SELECT * FROM vw_painel_usuarios");
        echo '<dt class="text-info">Moodle:</dt><dd class="text-info">' . $mysqlMoodle->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysqlMoodle->numRows()) {
            while ($mysqlMoodle->fetch()) {
                $arrMoodle[] = array("id" => $mysqlMoodle->res["userid"], "escola" => seVazioRetorneNulo($mysqlMoodle->res["esc_int_codigo"]), "cpf" => seVazioRetorneNulo($mysqlMoodle->res["cpf"]), "matricula" => seVazioRetorneNulo(substr($mysqlMoodle->res["matricula"],0, 20)), "nome" => seVazioRetorneNulo($mysqlMoodle->res["nome"]), "cargo" => seVazioRetorneNulo($mysqlMoodle->res["cargo"]), "funcao" => seVazioRetorneNulo($mysqlMoodle->res["funcao"]), "email" => seVazioRetorneNulo($mysqlMoodle->res["email"]));
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando no Painel...</dd>';
        ob_flush();
        flush();
        $arrUsuario = array();
        $mysql = new GDbMysql();
        $mysql->execute("SELECT * FROM ava_usuario ORDER BY usu_int_userid ASC");
        echo '<dt class="text-info">Painel:</dt><dd class="text-info">' . $mysql->numRows() . '</dd>';
        ob_flush();
        flush();
        if ($mysql->numRows()) {
            while ($mysql->fetch()) {
                $arrUsuario[] = array("id" => $mysql->res["usu_int_userid"], "escola" => seVazioRetorneNulo($mysql->res["esc_int_codigo"]), "cpf" => seVazioRetorneNulo($mysql->res["usu_var_cpf"]), "matricula" => seVazioRetorneNulo($mysql->res["usu_var_matricula"]), "nome" => seVazioRetorneNulo($mysql->res["usu_var_nome"]), "cargo" => seVazioRetorneNulo($mysql->res["usu_var_cargo"]), "funcao" => seVazioRetorneNulo($mysql->res["usu_var_funcao"]), "email" => seVazioRetorneNulo($mysql->res["usu_var_email"]));
            }
        }

        echo '<dt class="text-warning">=></dt><dd class="text-warning">Calculando diferenças entre o Moodle e Painel...</dd>';
        ob_flush();
        flush();
        $nenhum = true;
        foreach ($arrUsuario as $usuario) {
            $existe = false;
            foreach ($arrMoodle as $moodle) {
                if ($moodle["id"] == $usuario["id"]/* && $moodle["escola"] == $usuario["escola"] && $moodle["cpf"] == $usuario["cpf"] &&
                  $moodle["matricula"] == $usuario["matricula"] && $moodle["nome"] == $usuario["nome"] && $moodle["cargo"] == $usuario["cargo"] &&
                  $moodle["funcao"] == $usuario["funcao"] && $moodle["email"] == $usuario["email"] */) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Id: ' . $usuario["id"] . ' - Nome: ' . $usuario["nome"] . ' - CPF: ' . $usuario["cpf"] . ' - Matrícula: ' . $usuario["matricula"] . ' - Cargo: ' . $usuario["cargo"] . ' - Função: ' . $usuario["funcao"] . ' - Email: ' . $usuario["email"];
                try {
                    $mysql = new GDbMysql();
                    $mysql->execute("DELETE FROM ava_usuario WHERE usu_int_userid = ?;", array("s", $usuario["id"]), false);
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
            foreach ($arrUsuario as $usuario) {
                if ($moodle["id"] == $usuario["id"] && $moodle["escola"] == $usuario["escola"] && $moodle["cpf"] == $usuario["cpf"] &&
                        $moodle["matricula"] == $usuario["matricula"] && $moodle["nome"] == $usuario["nome"] && $moodle["cargo"] == $usuario["cargo"] &&
                        $moodle["funcao"] == $usuario["funcao"] && $moodle["email"] == $usuario["email"]) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $nenhum = false;
                $identificador = 'Id: ' . $moodle["id"] . ' - Nome: ' . $moodle["nome"] . ' - CPF: ' . $moodle["cpf"] . ' - Matrícula: ' . $moodle["matricula"] . ' - Cargo: ' . $moodle["cargo"] . ' - Função: ' . $moodle["funcao"] . ' - Email: ' . $moodle["email"];
                try {
                    $mysql = new GDbMysql();
                    $qtd = $mysql->executeValue("SELECT COUNT(*) FROM ava_usuario WHERE usu_int_userid = ?", array("s", $moodle["id"]));
                    if ($qtd > 0) {
                        $mysql->execute("UPDATE IGNORE ava_usuario SET esc_int_codigo = ?, usu_var_cpf = ?, usu_var_matricula = ?, usu_var_nome = ?, usu_var_cargo = ?, usu_var_funcao = ?, usu_var_email = ?  WHERE usu_int_userid = ?;", array("isssssss", $moodle["escola"], $moodle["cpf"], $moodle["matricula"], $moodle["nome"], $moodle["cargo"], $moodle["funcao"], $moodle["email"], $moodle["id"]), false);
                        $count['alterado']++;
                        echo '<dt class="text-success">' . $count['alterado'] . ' - Alterado</dt><dd class="text-success">' . $identificador . '</dd>';
                        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $count['alterado'] . ' - Alterado - ' . $identificador . "\n");
                    } else {
                        $mysql->execute("INSERT IGNORE INTO ava_usuario (esc_int_codigo, usu_int_userid, usu_var_cpf, usu_var_matricula, usu_var_nome, usu_var_cargo, usu_var_funcao, usu_var_email) VALUES (?,?,?,?,?,?,?,?);", array("isssssss", $moodle["escola"], $moodle["id"], $moodle["cpf"], $moodle["matricula"], $moodle["nome"], $moodle["cargo"], $moodle["funcao"], $moodle["email"]), false);
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
    fwrite($fp, date("d/m/Y H:i:s") . ' - ATUALIZAR USUÁRIO - TÉRMINO ' . "\n");
    return $count;
}

// </editor-fold>

salvarEvento('S ', 'Cron de atualização de usuário finalizado', $totais);

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