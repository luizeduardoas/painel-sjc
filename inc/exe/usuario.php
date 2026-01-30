<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

require_once("../global.php");
GF::import(array("usuario"));

$usuario = new Usuario();
$usuario->setUsu_var_senha($_POST["usu_var_senha"] ?? null);
$usuario->setUsu_var_senha_new($_POST["usu_var_senha_new"] ?? null);
$usuarioDao = new UsuarioDao();

$login = new Usuario();
$login->setUsu_var_identificador($_POST["log_var_usuario"] ?? null);
$login->setUsu_var_email($_POST["log_var_usuario"] ?? null);
$login->setUsu_var_senha($_POST["log_var_senha"] ?? null);
$loginDao = new UsuarioDao();

switch ($_POST["acao"]) {
    case "autenticar":
        GF::salvarLog('Debug', 'Login: ' . $login->getUsu_var_identificador());
        echo json_encode($loginDao->autenticar($login));
        break;
    case "verificaExisteIdentficador":
        echo json_encode($usuarioDao->verificaExisteIdentficador($_POST["usu_var_identificador"]));
        break;
    case "verificaExisteEmail":
        echo json_encode($usuarioDao->verificaExisteEmail($_POST["usu_var_email"], $_POST["usu_int_codigo"]));
        break;
    case "alterarSenha":
        echo json_encode($usuarioDao->alterarSenha($usuario));
        break;
    case "esqueciSenha":
        $login = new Usuario();
        $login->setUsu_var_email($_POST["log_var_email_recuperacao"]);
        echo json_encode($usuarioDao->esqueciSenha($login));
        break;
    case "tokSenha":
        $usuario->setUsu_var_token($_POST["token"]);
        echo json_encode($usuarioDao->validateTokenSenha($usuario));
        break;
    case "create":
        $perfil = new Perfil();
        $perfil->setPef_int_codigo($_POST["pef_int_codigo"]);

        $usuario = new Usuario();
        $usuario->setUsu_var_nome($_POST["usu_var_nome"]);
        $usuario->setPerfil($perfil);
        $usuario->setUsu_var_email($_POST["usu_var_email"]);
        $usuario->setUsu_var_identificador($_POST["usu_var_identificador"]);
        $usuario->setUsu_var_senha($_POST["usu_var_senha"]);
        $usuario->setUsu_var_senha_new($_POST["usu_var_senha_new"]);
        $usuario->setUsu_cha_status('A');
        $usuario->setUsu_var_motivo(null);
        $usuario->setUsu_var_token(null);
        $usuario->setUsu_cha_validado(formataNvl($_POST["usu_cha_validado"], 'N'));

        $return = $usuarioDao->cadastrarse($usuario);
        echo json_encode($return);
        break;
    case "escolha":
        setPerfilSessao($_POST["pef_int_codigo"]);
        echo '{"status": true, "msg":"Perfil escolhido com sucesso."}';
        break;
    default:
        echo '{"status": false, "msg":"Ação inválida"}';
        break;
}
?>