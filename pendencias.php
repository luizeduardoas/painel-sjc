<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "inc/global.php");

$mysql = new GDbMysql();
$arrPendencias = array();
$arrItemPendencias = array();

$usuario = getUsuarioSessao();

// <editor-fold desc="Usuário sem foto no perfil">
if ($usuario->getUsu_var_foto() == URL_UPLOAD . 'usuario/unknown.png') {
    $arrItemPendencias['qtd'] = 1;
    $arrItemPendencias['titulo'] = 'Foto do perfil';
    $arrItemPendencias['motivo'] = 'Cadastre sua foto para que todos te conheçam.';
    $arrItemPendencias['link'] = URL_SYS . 'minhaconta/meusdados/';
    $arrItemPendencias['solucao'] = 'Acesse no menu Minha Conta a opção Meus Dados, adicione sua foto de perfil e salve.';
    $arrItemPendencias['icone'] = 'exclamation-triangle blue';
    $arrPendencias[] = $arrItemPendencias;
}
$mysql->close();
// </editor-fold>

?>