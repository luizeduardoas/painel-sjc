<?php

require_once("../../inc/global.php");
GF::import(array("usuario", "perfil"));

$perfil = new Perfil();
if (isset($_POST["pef_int_codigo"]))
    $perfil->setPef_int_codigo($_POST["pef_int_codigo"]);

$usuario = new Usuario();
if (isset($_POST["usu_int_codigo"]))
    $usuario->setUsu_int_codigo($_POST["usu_int_codigo"]);
$usuario->setPerfil($perfil);
if (isset($_POST["usu_var_nome"]))
    $usuario->setUsu_var_nome($_POST["usu_var_nome"]);
if (isset($_POST["usu_var_identificador"]))
    $usuario->setUsu_var_identificador($_POST["usu_var_identificador"]);
if (isset($_POST["usu_var_email"]))
    $usuario->setUsu_var_email(strtolower($_POST["usu_var_email"]));
if (isset($_POST["usu_var_senha"]))
    $usuario->setUsu_var_senha(seVazioRetorneNulo($_POST["usu_var_senha"]));
if (isset($_POST["usu_cha_status"]))
    $usuario->setUsu_cha_status($_POST["usu_cha_status"]);
if (isset($_POST["usu_var_motivo"]))
    $usuario->setUsu_var_motivo(($_POST["usu_cha_status"] == 'A') ? null : $_POST["usu_var_motivo"]);
if (isset($_POST["usu_var_token"]))
    $usuario->setUsu_var_token(seVazioRetorneNulo($_POST["usu_var_token"]));
if (isset($_POST["usu_var_foto"]))
    $usuario->setUsu_var_foto(seVazioRetorneNulo($_POST["usu_var_foto"]));
if (isset($_POST["usu_cha_validado"]))
    $usuario->setUsu_cha_validado($_POST["usu_cha_validado"]);

$usuarioDao = new UsuarioDao();

switch ($_POST["acao"]) {
    case "ins":
        if (GSecurity::verificarPermissaoAjax("USUARIO_INS")) {
            $result["status"] = true;
            if (isset($_FILES["usu_var_foto"]) && $_FILES["usu_var_foto"] != 'undefined') {
                $result[] = fazerUpload($_FILES["usu_var_foto"], "usuario/", true, false);
                $result["status"] = $result[0]['status'] == 'success';
                $result["msg"] = $result[0]['message'];
                $usuario->setUsu_var_foto(formataArquivoURL('usuario', $result[0]["url"]));
            } else {
                $usuario->setUsu_var_foto(formataArquivoURL('usuario', $_POST["atual"]));
            }
            if ($result["status"]) {
                echo json_encode($usuarioDao->insert($usuario));
            } else {
                echo json_encode($result);
            }
        }
        break;
    case "upd":
        if (GSecurity::verificarPermissaoAjax("USUARIO_UPD")) {
            $result["status"] = true;
            if (isset($_FILES["usu_var_foto"]) && $_FILES["usu_var_foto"] != 'undefined') {
                $result[] = fazerUpload($_FILES["usu_var_foto"], "usuario/", true, false);
                $result["status"] = $result[0]['status'] == 'success';
                $result["msg"] = $result[0]['message'];
                $usuario->setUsu_var_foto(formataArquivoURL('usuario', $result[0]["url"]));
            } else {
                $usuario->setUsu_var_foto(formataArquivoURL('usuario', $_POST["atual"]));
            }
            if ($result["status"]) {
                echo json_encode($usuarioDao->update($usuario));
            } else {
                echo json_encode($result);
            }
        }
        break;
    case "del":
        if (GSecurity::verificarPermissaoAjax("USUARIO_DEL")) {
            echo json_encode($usuarioDao->delete($usuario));
        }
        break;
    case "sel":
        $usuario = $usuarioDao->selectById($usuario);
        echo json_encode($usuario->getArray());
        break;
    case "log":
        if (GSecurity::verificarPermissaoAjax("USUARIO_REP")) {
            echo json_encode($usuarioDao->logarComo($usuario));
        }
        break;
    case "enviar":
        if (GSecurity::verificarPermissaoAjax("USUARIO_ENV_SEN")) {
            $usuario = $usuarioDao->selectById($usuario);
            echo json_encode($usuarioDao->esqueciSenha($usuario));
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