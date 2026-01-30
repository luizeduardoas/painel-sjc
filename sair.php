<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/inc/global.php");

global $__param;
$url = isset($__param[1]) ? (substr($__param[1], 0, 1) == '/') ? URL_SYS . substr($__param[1], 1) : URL_SYS . $__param[1] : URL_SYS;

$usuario = new Usuario();
$usuario = unserialize($_SESSION['usuario'] ?? null);
if ((!is_null($_SESSION["usuario"] ?? null)) && (MYSQL_WRITE == "ON")) {
    setSessao($usuario->getUsu_int_codigo(), true);
}

session_unset();
session_destroy();
header("location: " . $url);
?>