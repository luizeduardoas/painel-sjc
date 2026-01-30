<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../global.php");

$json = '';
$existe = false;
switch ($_GET["tipo"]) {
    case 'acesso':
        GF::import(array("acesso"));
        $acesso = new Acesso();
        $acesso->setAce_int_codigo($_GET["id"]);
        $acessoDao = new AcessoDao();
        $acesso = $acessoDao->selectById($acesso);
        if (!is_null($acesso->getAce_var_ip())) {
            $json = $acesso->getAce_txt_json();
            $existe = true;
        }
        break;
    case 'api_log':
        GF::import(array("apiLog"));
        $apiLog = new ApiLog();
        $apiLog->setLog_int_codigo($_GET["id"]);
        $apiLogDao = new ApiLogDao();
        $apiLog = $apiLogDao->selectById($apiLog);
        if (!is_null($apiLog->getLog_var_rota())) {
            $json = $apiLog->getLog_txt_response();
            $existe = true;
        }
        break;
    case 'api_registro_old':
        GF::import(array("apiRegistro"));
        $apiRegistro = new ApiRegistro();
        $apiRegistro->setReg_int_codigo($_GET["id"]);
        $apiRegistroDao = new ApiRegistroDao();
        $apiRegistro = $apiRegistroDao->selectById($apiRegistro);
        if (!is_null($apiRegistro->getReg_dti_criacao())) {
            $json = $apiRegistro->getReg_txt_old();
            $existe = true;
        }
        break;
    case 'api_registro_new':
        GF::import(array("apiRegistro"));
        $apiRegistro = new ApiRegistro();
        $apiRegistro->setReg_int_codigo($_GET["id"]);
        $apiRegistroDao = new ApiRegistroDao();
        $apiRegistro = $apiRegistroDao->selectById($apiRegistro);
        if (!is_null($apiRegistro->getReg_dti_criacao())) {
            $json = $apiRegistro->getReg_txt_new();
            $existe = true;
        }
        break;

    default:
        break;
}
if ($existe) {
    header('Content-Type: application/json');
    echo $json;
} else {
    $header = new GHeader("", true);
    $header->show(true);
    echo carregarAlerta("E", "Não foi possível localizar esse Json");
    $footer = new GFooter();
    $footer->show(true);
}
?>