<?php

require_once("../../inc/global.php");

if (GSecurity::verificarPermissaoAjax("PERFILTROCA")) {
    try {
        $mysql = new GDbMysql();
        $mysql->autoCommit(false);
        $mysql->execute("DELETE FROM perfil_troca WHERE pef_int_codigo = ?;", array("i", $_POST["pef_int_codigo"]), false);
        if ($_POST["perfis"] != '') {
            foreach ($_POST["perfis"] as $perfis) {
                $mysql->execute("INSERT INTO perfil_troca (pef_int_codigo, pef_int_codigo_troca) VALUES (?, ?);", array("ii", $_POST["pef_int_codigo"], $perfis), false);
            }
        }
        $mysql->commit();
        $return["status"] = true;
        $return["msg"] = 'Trocas de perfis atualizadas com sucesso!';
        if ($return["status"]) {
            salvarEvento('S', $return["msg"], json_encode($_POST["perfis"], JSON_UNESCAPED_UNICODE));
        } else {
            salvarEvento('A', $return["msg"], json_encode($_POST["perfis"], JSON_UNESCAPED_UNICODE));
        }
    } catch (GDbException $e) {
        $mysql->rollback();
        $return["status"] = false;
        $return["msg"] = 'Não foi possível atualizar essas trocas de perfis!<div style="display:none;">' . $e->getError() . '</div>';
        salvarEvento('E', $e->getErrorLog(), json_encode($_POST["perfis"], JSON_UNESCAPED_UNICODE));
    }
    $mysql->close();
    echo json_encode($return);
}
?>