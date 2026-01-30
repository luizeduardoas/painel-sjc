<?php

require_once("../../inc/global.php");

if (GSecurity::verificarPermissaoAjax("PERMISSAOPERFIL")) {
    try {
        $mysql = new GDbMysql(); 
        $mysql->autoCommit(false);
        $mysql->execute("DELETE FROM perfil_permissao WHERE pef_int_codigo = ?;", array("i", $_POST["pef_int_codigo"]), false);
        if ($_POST["permissoes"] != '') {
            foreach ($_POST["permissoes"] as $permissao) {
                $mysql->execute("INSERT INTO perfil_permissao (pef_int_codigo, pem_var_codigo) VALUES (?, ?);", array("is", $_POST["pef_int_codigo"], $permissao), false);
            }
        }
        $query = "INSERT INTO perfil_permissao (pef_int_codigo, pem_var_codigo) ";
        $query .= "SELECT pp.pef_int_codigo, p.pem_var_vinculo ";
        $query .= "FROM permissao p ";
        $query .= "INNER JOIN perfil_permissao pp ON (p.pem_var_codigo = pp.pem_var_codigo) ";
        $query .= "WHERE pp.pef_int_codigo = ? ";
        $query .= "AND p.pem_var_vinculo <> NULL ";
        $query .= "AND p.pem_var_vinculo NOT IN (SELECT pp2.pem_var_codigo FROM perfil_permissao pp2 WHERE pp2.pef_int_codigo = pp.pef_int_codigo)";
        $mysql->execute($query, array("i", $_POST["pef_int_codigo"]), false);

        $mysql->commit();

        $usuarioDao = new UsuarioDao();
        $usuarioDao->carregarPermissoes();

        $return["status"] = true;
        $return["msg"] = 'Permissões do perfil atualizadas com sucesso!';
        if ($return["status"]) {
            salvarEvento('S', $return["msg"], json_encode($_POST["permissoes"], JSON_UNESCAPED_UNICODE));
        } else {
            salvarEvento('A', $return["msg"], json_encode($_POST["permissoes"], JSON_UNESCAPED_UNICODE));
        }
    } catch (GDbException $e) {
        $mysql->rollback();
        $return["status"] = false;
        $return["msg"] = 'Não foi possível atualizar essas permissões!<div style="display:none;">' . $e->getError() . '</div>';
        salvarEvento('E', $e->getErrorLog(), json_encode($_POST["permissoes"], JSON_UNESCAPED_UNICODE));
    }
    $mysql->close();
    echo json_encode($return);
}
?>