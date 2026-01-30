<?php

require_once("../global.php");

//ini_set("allow_url_fopen", true);
$cep = $_GET['cep'];
$xml = simplexml_load_file("http://cep.republicavirtual.com.br/web_cep.php?cep=" . $cep);

echo '{"resultado" : "' . $xml->resultado . '","resultado_txt" : "' . $xml->resultado_txt . '","uf" : "' . $xml->uf . '","cidade" : "' . $xml->cidade . '","bairro" : "' . $xml->bairro . '","tipo_logradouro" : "' . $xml->tipo_logradouro . '","logradouro" : "' . $xml->logradouro . '"}';

//ini_set("allow_url_fopen", false);
?>