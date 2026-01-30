<?php

require_once("../../inc/global.php");
GF::import(array("usuario"));

$usuario = getUsuarioSessao();
$usuario->setUsu_var_nome($_POST["usu_var_nome"]);
$usuario->setUsu_var_identificador($_POST["usu_var_identificador"]);
$usuario->setUsu_var_email($_POST["usu_var_email"]);
$usuario->setUsu_var_foto(seNuloOuVazio($_POST["usu_var_foto"]) ? null : $_POST["usu_var_foto"]);
$usuarioDao = new UsuarioDao();

switch ($_POST["acao"]) {
    case "meusDados":
        $result["status"] = true;
        if (isset($_FILES["usu_var_foto"])) {
            $result[] = fazerUpload($_FILES["usu_var_foto"], "usuario/", true, false);
            $result["status"] = $result[0]['status'] == 'success';
            $result["msg"] = $result[0]['message'];
            $usuario->setUsu_var_foto($result[0]["url"]);
        } else {
            $usuario->setUsu_var_foto($_POST["atual"]);
        }
        if ($result["status"]) {
            echo json_encode($usuarioDao->atualizar($usuario));
        } else {
            echo json_encode($result);
        }
        break;
    case "validarIdentificador":
        echo json_encode($usuarioDao->validarIdentificador($usuario));
        break;
    default:
        echo '{"status": false, "msg":"Ação inválida"}';
        break;
}
?>