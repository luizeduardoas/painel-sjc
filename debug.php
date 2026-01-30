<?php

require_once("inc/global.php");

$header = new GHeader("Debug");
$header->show();

$_SESSION['debug'] = true;

echo '<pre>';
echo 'date("d/m/Y H:i"): ' . date("d/m/Y H:i") . '</br>';
echo 'session_cache_expire(): ' . session_cache_expire() . '</br>';
echo 'ini_get("session.gc_maxlifetime"): ' . ini_get("session.gc_maxlifetime") . '</br>';
echo '</pre>';

$footer = new GFooter();
$footer->show();
?>