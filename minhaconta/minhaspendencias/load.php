<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

include_once(ROOT_SYS . "pendencias.php");

if ($arrPendencias && count($arrPendencias)) {
    foreach ($arrPendencias as $pendencia) {
        echo '<div class="alert alert-warning"><i class="ace-icon fa fa-' . $pendencia["icone"] . ' bigger-130"></i>&nbsp;' . $pendencia["motivo"] . '<br/>';
        if (!isset($pendencia["frame"]) || seNuloOuVazio($pendencia["frame"])) {
            echo '<i class="ace-icon fa fa-angle-right bigger-110"></i> <a href="' . $pendencia["link"] . '">' . $pendencia["solucao"] . '</a>';
        } else {
            echo '<i class="ace-icon fa fa-angle-right bigger-110"></i> ' . $pendencia["solucao"];
        }
        echo '</div>';
    }
} else {
    echo '<p class="alert alert-success center"><i class="ace-icon fa fa-check bigger-150 icon-animated-vertical"></i> Parabéns! você não possui nenhuma pendência no momento.</p>';
}
?>