<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GF::import(array("relatorio", "filtro"));

$rel_int_codigo = $_POST["rel_int_codigo"];
$relatorio = new Relatorio();
$relatorio->setRel_int_codigo($rel_int_codigo);
$relatorioDao = new RelatorioDao();
$relatorio = $relatorioDao->selectById($relatorio);
if (!is_null($relatorio->getRel_var_titulo())) {

    if (!seNuloOuVazio($relatorio->getRel_var_permissao())) {
        GSecurity::verificarPermissaoAjax($relatorio->getRel_var_permissao());
    }

    $arrReturn = getDadosRelatorio($relatorio, $_POST);
    if ($arrReturn["status"]) {
        if (count($arrReturn["dados"])) {
            $arrDados = $arrReturn["dados"];
            $arrTitulos = $arrReturn["titulos"];
            echo '<table style="margin-bottom: 0;" class="table table-responsive table-bordered table-hover table-striped">';
            echo '<thead>';
            echo '<tr>';
            foreach ($arrTitulos as $titulo) {
                echo '<th>' . $titulo . '</th>';
            }
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            foreach ($arrDados as $dado) {
                echo '<tr>';
                foreach ($dado as $key => $val) {
                    echo '<td>' . $val . '</td>';
                }
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p style="margin-bottom: 0;" class="alert alert-danger center"><i class="ace-icon fa fa-exclamation-circle bigger-150 icon-animated-vertical"></i> Nenhum dado foi encontrado para esses filtros.</p>';
        }
    } else {
        echo '<p style="margin-bottom: 0;" class="alert alert-danger center"><i class="ace-icon fa fa-exclamation-circle bigger-150 icon-animated-vertical"></i> ' . $arrReturn["msg"] . '</p>';
    }
} else {
    echo '<p style="margin-bottom: 0;" class="alert alert-danger center"><i class="ace-icon fa fa-exclamation-circle bigger-150 icon-animated-vertical"></i> Desculpa, mas não encontramos esse relatório.</p>';
}
?>