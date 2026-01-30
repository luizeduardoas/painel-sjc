<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("CRON_ARQUIVOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Monitoramento >> Logs da CRON Limpeza de arquivos não utilizados", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Monitoramento >> Logs da CRON Limpeza de arquivos não utilizados", true);
$header->addMenu("CRON_ARQUIVOS", "Visualização de Logs da CRON Limpeza de arquivos não utilizados", "Visualize as informações de Logs da CRON Limpeza de arquivos não utilizados do sistema");
$header->addTheme(Theme::addLib(array("datepicker")));
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

$html .= '<div class="col-lg-4 col-lg-offset-4 col-sm-6 col-sm-offset-3 col-xs-12">';
$html .= '    <h3 class="header smaller lighter green center">Visualização de Logs da CRON Limpeza de arquivos não utilizados</h3>';
$html .= '    <p>Acompanhe os logs gerados na execução do CRON automatizado para limpeza de arquivos não utilizados na plataforma.</p>';
$html .= '    <div class="col-xs-12">';
$html .= '      <div class="text-center well col-xs-12 formFiltros">';
global $__paramDataLog;
$html .= '          <div class="filtroInline text-left" style="margin-top:0;width:140px;">' . $form->addDatePicker("filtro_data", "Data:", false, array("value" => buscarCookie("filtro_data", date("d/m/Y")), "class" => "form-control"), array("class" => "required"), true, false, $__paramDataLog) . '</div>';
$html .= '          <a id="btn_atualizar" class="btn btn-success btn-sm" style="margin-left: 5px; margin-bottom: 10px; vertical-align:bottom;"><i class="ace-icon fa fa-refresh"></i> Atualizar</a>';
$html .= '      </div>';
$html .= '    </div>';
$html .= '</div>';
$html .= '<div class="clearfix"></div>';
$html .= '<div class="space-20"></div>';
$html .= '<div id="load" class="col-xs-12"></div>';
$html .= '<div class="clearfix"></div>';
$html .= '<div class="space-20"></div>';

$arrayBotoes["btn_voltar"] = "Voltar";

$html .= carregarBotoes($arrayBotoes, "", false, "background-color:#fff;border: 0");
$html .= $form->close();

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
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
        jQuery("#btn_atualizar").click(function () {
            salvarFiltros();
            atualizar();
        });
    });

    function salvarFiltros() {
        setParametroCookieHoje('filtro_data', jQuery('#filtro_data').val());
    }

    function atualizar() {
        jQuery.gAjax.load("<?php echo URL_SYS . 'monitoramento/cron_arquivos/'; ?>load.php", {filtro_data: jQuery("#filtro_data").val()}, "#load");
    }
</script>
