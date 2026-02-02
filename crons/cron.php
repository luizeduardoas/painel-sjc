<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../inc/global.php");

$header = new GHeader("Crons", true);
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

include_once("cronarquivos.php");
include_once("cronnivel.php");
include_once("cronescola.php");
include_once("croncurso.php");
include_once("cronusuario.php");
include_once("cronmatricula.php");
include_once("cronmodulo.php");
include_once("cronacesso.php");
include_once("cronconclusao.php");

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>