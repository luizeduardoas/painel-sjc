<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("ESCOLHEREMPRESA");

global $__param;
$url = isset($__param[1]) ? $__param[1] : URL_SYS . 'home/';

if (isset($_SESSION["empresa"]))
    unset($_SESSION["empresa"]);
$usuario = getUsuarioSessao();
if ($usuario)
    header("location: " . $url);
?>