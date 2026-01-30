<?php

require_once("../../inc/global.php");
GF::import(array("mensagem", "destinatario"));

$remetente = new Usuario();
$remetente->setUsu_int_codigo($_POST["men_int_remetente"]);

$mensagem = new Mensagem();
$mensagem->setMen_int_codigo($_POST["men_int_codigo"]);
$mensagem->setMen_var_titulo($_POST["men_var_titulo"]);
$mensagem->setMen_txt_texto($_POST["men_txt_texto"]);
$mensagem->setRemetente($remetente);
$mensagem->setDestinatarios($_POST["arr_destinatarios"]);

$mensagemDao = new MensagemDao();

switch ($_POST["acao"]) {
    case "enviar":
        $usuarioDao = new UsuarioDao();
        $return = $mensagemDao->enviar($mensagem);
        if ($return["status"]) {
            $return["msg"] = 'Mensagem enviada aos destinatários do sistema!';
            $enviar_email = $_POST["enviar_email"];
            if ($enviar_email == 'S') {
                $arrErros = array();
                $arrDestinatarios = explode(",", $mensagem->getDestinatarios());
                foreach ($arrDestinatarios as $destinatario) {
                    $usuario = new Usuario();
                    $usuario->setUsu_int_codigo($destinatario);
                    $usuario = $usuarioDao->selectById($usuario);
                    $email = new GEmail();
                    $msg = gerarEmail($mensagem->getMen_txt_texto(), $mensagem->getMen_var_titulo());
                    $email->setMensagem($msg);
                    $email->setAssunto($mensagem->getMen_var_titulo());
                    $email->setDestinatario($usuario->getUsu_var_nome() . "<" . $usuario->getUsu_var_email() . ">");
                    $returnEmail = $email->enviar();
                    if (!$returnEmail["status"]) {
                        $arrErros[] = $usuario->getUsu_var_nome() . ' (' . $usuario->getUsu_var_email() . ') - ' . $returnEmail["msg"];
                    }
                }
                if (count($arrErros) > 0) {
                    if (count($arrErros) > 1) {
                        $return["msg"] .= '<br/>Porém não foi possível enviar cópias para os seguintes destinatários:';
                    } else {
                        $return["msg"] .= '<br/>Porém não foi possível enviar cópia para o seguinte destinatário:';
                    }
                    $return["msg"] .= '<ul class="listaErros">';
                    foreach ($arrErros as $erro) {
                        $return["msg"] .= '<li><i class="ace-icon fa fa-times bigger-110 red"></i> ' . $erro . '</li>';
                    }
                    $return["msg"] .= '</ul>';
                } else {
                    if (count($arrDestinatarios) > 1) {
                        $return["msg"] .= '<br/>Também foram enviadas cópias por email!';
                    } else {
                        $return["msg"] .= '<br/>Também foi enviada cópia por email!';
                    }
                }
            }
        }
        echo json_encode($return);
        break;
    case "del":
        $mensagem = new Mensagem();
        $mensagem->setMen_int_codigo($_POST["men_int_codigo"]);
        $destinatario = new Destinatario();
        $destinatario->setDes_int_destinatario($_POST["usu_int_codigo"]);
        $destinatarioDao = new DestinatarioDao();
        $destinatario = $destinatarioDao->selectByMensagemDestinatario($mensagem, $destinatario);
        $destinatario->setDes_cha_status(2);
        $return = $destinatarioDao->update($destinatario);
        if ($return["status"]) {
            $return["msg"] = 'Mensagem excluída com sucesso!';
        }
        echo json_encode($return);
        break;
    case "naolida":
        $mensagem = new Mensagem();
        $mensagem->setMen_int_codigo($_POST["men_int_codigo"]);
        $destinatario = new Destinatario();
        $destinatario->setDes_int_destinatario($_POST["usu_int_codigo"]);
        $destinatarioDao = new DestinatarioDao();
        $destinatario = $destinatarioDao->selectByMensagemDestinatario($mensagem, $destinatario);
        $destinatario->setDes_cha_status(0);
        $return = $destinatarioDao->update($destinatario);
        if ($return["status"]) {
            $return["msg"] = 'Mensagem marcada como não lida com sucesso!';
        }
        echo json_encode($return);
        break;
    default:
        echo '{"status": false, "msg":"Ação inválida"}';
        break;
}
?>
