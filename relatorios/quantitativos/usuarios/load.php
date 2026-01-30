<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");

$ini = strtotime('now');

$filtro_tipo = $_POST["filtro_tipo"];

include_once(__DIR__ . "/../../querys/usuarios.php");

echo gerarTabela($arrTitulos, $arrDados, $arrFooter, $arrFormats, false, $arrStyleTitulos);

echo '<div class="well well-sm text-center" style="margin: 0;">Tempo de carregamento: ' . (strtotime('now') - $ini) . 's</div>';
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        scrollTop();
    });
</script>