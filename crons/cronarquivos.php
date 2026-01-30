<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

ini_set('memory_limit', '-1');
set_time_limit(SYS_TIME_LIMIT);

$root = ROOT_LOGS . "crons/arquivos/";
if (!is_dir($root)) {
    mkdir($root, 0777, true);
}
$fp = fopen($root . date("Y-m-d") . ".txt", "a");
fwrite($fp, "\n\n");
fwrite($fp, date("d/m/Y H:i:s") . " ------ INÍCIO CRON -----\n");

salvarEvento('S', 'Cron de limpeza dos arquivos não utilizados iniciado', '');

ob_start();

$form = new GForm();
echo gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Cron de Limpeza de Arquivos',
    'id' => 'cron',
    'col' => 8,
    'fa' => 'cogs'
));
echo $form->open("form");
echo '<fieldset>';
echo '<dl class="dl-horizontal dl-imagens">';
try {
    $totais = '';
    $qtd = limparArquivos($fp, ROOT_UPLOAD . 'usuario/', URL_UPLOAD . 'usuario/', 'usuario', 'usu_var_foto');
    echo '<dt class="text-info">Totais</dt><dd class="text-info">' . $qtd . '</dd>';
    fwrite($fp, date("d/m/Y H:i:s") . ' - Totais: ' . $qtd . "\n");
    $totais .= date("d/m/Y H:i:s") . ' - Totais: ' . $qtd . '<br/>';
} catch (Exception $e) {
    echo 'Erro ao executar cron de Limpeza de arquivos: <br/>' . $e->getMessage();
    fwrite($fp, date("d/m/Y H:i:s") . ' - Erro ao executar cron de Limpeza de arquivos: ' . $e->getMessage() . "\n");
    salvarEvento('E', $e->getError(), '');
}
echo '</dl>';
echo '</fieldset>';
echo carregarBotoes("V");
echo $form->close();
echo gerarRodape(array('tipo' => 'box', 'col' => 8));

salvarEvento('S', 'Cron de limpeza dos arquivos não utilizados finalizado', $totais);

ob_end_flush();

fwrite($fp, date("d/m/Y H:i:s") . " ------ TÉRMINO CRON -----\n");
fclose($fp);

function limparArquivos($fp, $root, $url, $tabela, $campo) {
    fwrite($fp, date("d/m/Y H:i:s") . ' - REMOVENDO ARQUIVOS - INÍCIO ' . "\n");
    echo '<dt class="text-warning">=></dt><dd class="text-warning">Buscando arquivos...</dd>';
    ob_flush();
    flush();
    $arrayImagens = explode(";", EXTENSIONS_IMAGENS);
    $count = 0;
    $mysql = new GDbMysql();
    if ($handle = opendir($root)) {
        while ($arquivo = readdir($handle)) {
            if (!is_dir($arquivo) && $arquivo != 'lixo' && $arquivo != 'unknown.png' && $arquivo != 'unknown.jpg' && $arquivo != 'unknown.pdf' && $arquivo != '.' && $arquivo != '..') {
                $arrCampo = explode(";", $campo);
                if (count($arrCampo) > 1) {
                    $where = "";
                    $indices = "";
                    $arrParam = array();
                    $primeiro = true;
                    foreach ($arrCampo as $coluna) {
                        if ($primeiro) {
                            $where = " WHERE $coluna = ? ";
                        } else {
                            $where .= " OR $coluna = ? ";
                        }
                        $primeiro = false;
                        $indices .= "s";
                        $arrParam[] = $arquivo;
                    }
                    $mysql->execute("SELECT * FROM $tabela $where", array_merge(array($indices), $arrParam));
                } else {
                    $mysql->execute("SELECT * FROM $tabela WHERE $campo = ? ", array("s", $arquivo));
                }
                if (!$mysql->fetch()) {
                    $count++;
                    $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
                    if (in_array($extensao, $arrayImagens)) {
                        echo '<dt>Movendo para lixo</dt><dd><img src="' . $url . 'lixo/' . $arquivo . '" style="max-width:200px;max-height:100px;"/></dd>';
                    } else {
                        echo '<dt>Movendo para lixo</dt><dd><img src="' . $url . 'unknown.png" style="max-width:200px;max-height:100px;"/></dd>';
                    }
                    rename($root . $arquivo, $root . 'lixo/' . $arquivo);
                    flush();
                    if (ob_get_level() > 0)
                        ob_flush();
                }
                $mysql->close();
            }
        }
        closedir($handle);
    }
    fwrite($fp, date("d/m/Y H:i:s") . ' - REMOVENDO ARQUIVOS - TÉRMINO ' . "\n");
    return $count;
}
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_voltar").click(function () {
            if (window.history.length > 1) {
                jQuery.gDisplay.loadStart('HTML');
                window.history.back();
            }
        });
        if (window.history.length < 2) {
            jQuery("#btn_voltar").attr("disabled", "disabled");
        }
    });
</script>