<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

$nomeDoArquivo = ROOT_LOGS . "crons/usuarios/" . gf::formatarData($_POST["filtro_data"]) . ".txt";

if (file_exists($nomeDoArquivo)) {
    $conteudo = file_get_contents($nomeDoArquivo);
    echo '<pre>'; // A tag <pre> preserva a formatação do texto
    echo $conteudo;
    echo '</pre>';
} else {
    echo carregarMensagem("A", "Nenhum registro de log foi encontrado nessa data", 6, false);
}
?>