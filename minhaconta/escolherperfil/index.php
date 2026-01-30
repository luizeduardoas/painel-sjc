<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("ESCOLHERPERFIL");

global $__param;
$url = isset($__param[1]) ? $__param[1] : URL_SYS . 'home/';

setPerfilSessao(null);
$usuario = getUsuarioSessao();
if (!is_null($usuario))
    header("location: " . $url);
?>