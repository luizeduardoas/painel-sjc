<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: X-Requested-With');

global $genesis;
if (is_null($genesis))
    require_once("../global.php");

GF::import(array("emailForm"));

if (!seNuloOuVazio($_POST["formulario"]) &&  !seNuloOuVazio($_POST["nome"]) && !seNuloOuVazio($_POST["email"]) && !seNuloOuVazio($_POST["telefone"]) && !seNuloOuVazio($_POST["assunto"]) && !seNuloOuVazio($_POST["mensagem"])) {

    $mensagem = gerarEmailFormulario($_POST["formulario"], $_POST["nome"], $_POST["email"], $_POST["telefone"], $_POST["assunto"], $_POST["mensagem"]);

    $emailForm = new EmailForm();
    $emailForm->setEma_var_nome($_POST["nome"]);
    $emailForm->setEma_var_remetente($_POST["email"]);
    $emailForm->setEma_var_contatos($_POST["telefone"]);
    $emailForm->setEma_var_assunto($_POST["assunto"]);
    $emailForm->setEma_txt_mensagem($_POST["mensagem"]);
    $emailForm->setEma_cha_status('N');
    $emailFormDao = new EmailFormDao();
    $emailFormDao->insert($emailForm);

    $email = new GEmail();
    $email->setMensagem($mensagem);
    $email->setAssunto($_POST["assunto"]);
    $returnEmail = $email->enviar();
    if ($returnEmail["status"]) {
        $return["status"] = true;
        $return["msg"] = "Sua mensagem foi enviada com sucesso.<br/>Aguarde nossa equipe entrar em contato.";
    } else {
        $return["status"] = false;
        $return["msg"] = "Não foi possível enviar sua mensagem.<br/>" . $returnEmail["msg"];
    }
} else {
    $return["status"] = false;
    $return["msg"] = "Nenhum dado foi enviado.";
}
echo json_encode($return);
?>