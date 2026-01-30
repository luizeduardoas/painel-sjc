<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../global.php");

GSecurity::verificarAutenticacaoAjax();

$header = new GHeader("", true);
$header->show(isFrame(true));

if (isset($_GET["key"])) {

    require_once(ROOT_SYS_INC . "aws/AwsS3.php");

    $s3 = new AwsS3();
    $url = $s3->BuscarURL($_GET['key']);
    $arr = explode("/", $_GET['key']);
    $extensao = strtolower(getExtensaoArquivo($arr[count($arr) - 1]));
    $arrImagens = explode(";", EXTENSIONS_IMAGENS);
    if (in_array($extensao, $arrImagens)) {
        echo '<img src="' . $url . '" class="img-responsive" style="height:100%;margin:auto" />';
    } else if ($extensao == 'pdf') {
        echo '<iframe src="' . $url . '" width="100%" height="100%" style="border: none;"></iframe>';
    } else {
        $botaoBaixar = '<a class="btn btn-primary" href="' . $url . '" target="_blank"><i class="fa fa-download"></i> Baixar</a>';
        echo '<br/><br/><br/>' . carregarMensagem("A", 'A visualização deste tipo de arquivo está indisponível<br/><br/><br/>' . $botaoBaixar, 6, false);
    }
} else {
    echo carregarMensagem("E", "Arquivo não encontrado", 10, false);
}

$footer = new GFooter();
$footer->show(isFrame(true));
?>
<style>
    .__corpoFrame {
        height: 99vh;
    }
</style>