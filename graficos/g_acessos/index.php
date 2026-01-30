<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("G_ACESSOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Gráficos >> Acessos", URL_SYS . 'graficos/g_acessos/', 1);

$header = new GHeader("Gráficos de acessos", true);
$header->addMenu("G_ACESSOS", "Gráficos de acessos", "Visualize os gráficos de acessos do sistema");
$header->addTheme(Theme::addLib(array("multiselect", "apexchart")));
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$mysql = new GDbMysql();
$opt_niv_var_nome = $mysql->executeCombo("SELECT niv_int_codigo, niv_var_hierarquia FROM nivel ORDER BY niv_var_hierarquia, niv_int_nivel;");

$codigosNivel = explode(",", buscarCookie("filtro_nivel"));
if (count($codigosNivel) == 0) {
    $codigosNivel = array_keys($opt_niv_var_nome);
}

$form = new GForm();
$html = '';
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Filtros',
    'id' => 'filtros',
    'col' => 6,
    'fa' => 'filter',
    'botaoNovo' => false,
    'export' => false,
    'botaoAtualizar' => false,
    'botaoExcel' => false
        ));
$html .= $form->open("form");
$html .= '<fieldset>';
global $__arrayTipoAcesso;
$html .= '<div class="col-lg-6 col-xs-12 no-padding-left">';
$html .= $form->addSelect("filtro_tipo", $__arrayTipoAcesso, buscarCookie("filtro_tipo", "-1"), "Tipo de Acesso", array("class" => "form-control selects chosen-select", "validate" => "([~] != -1)|Obrigatório"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-lg-6 col-xs-12 no-padding-left">';
$html .= $form->addDateRange("filtro_periodo", "Período:", false, array("value" => buscarCookie("filtro_periodo", date("d/m/Y") . ' - ' . date("d/m/Y")), "class" => "form-control campo", "placeholder" => "Período de tempo", "style" => "width: 180px;"), array("'maxDate'" => "'" . date("d/m/Y") . "'"), array("ano" => true, "mes" => true), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-xs-12 no-padding-left">';
$html .= $form->addSelectMulti("filtro_nivel", $opt_niv_var_nome, $codigosNivel, "Estrutura Organizacional:", array("class" => "multiselect"), array("class" => "required"), false, false, false, "loadNivel();");
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-xs-12 no-padding-left" id="div_curso" style="display:none;">';
$html .= '</div>';
$html .= '</fieldset>';
$html .= carregarBotoes("G");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 6));
$html .= gerarCabecalho(array('tipo' => 'box', 'col' => 6, 'fa' => 'bar-chart', 'titulo' => 'Gráficos de acessos'));
$html .= '<div id="div_load" class="p-4">';
$html .= carregarMensagem("A", "Selecione os filtros desejados e clique em Gerar.", 12, false);
$html .= '</div>';
$html .= gerarRodape(array('tipo' => 'box', 'col' => 6));
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery("#btn_gerar").click(function () {
            salvarFiltros();
            carregarGrafico();
        });
        jQuery("#btn_excel").click(function () {
            window.open("<?php echo URL_SYS . 'graficos/g_acessos/'; ?>excel.php");
        });
        loadNivel();
    });

    function salvarFiltros() {
        setParametroCookie('filtro_tipo', jQuery('#filtro_tipo').val());
        setParametroCookie('filtro_nivel', jQuery('#filtro_nivel').val());
        setParametroCookieGeral('filtro_curso', jQuery('#cur_int_codigo').val());
        setParametroCookie("filtro_periodo", jQuery("#filtro_periodo").val());
    }

    function loadNivel() {
        jQuery.gAjax.load("<?php echo URL_SYS . 'inc/load/'; ?>cursos.php", {filtro_nivel: jQuery("#filtro_nivel").val()}, "#div_curso", undefined, false);
        jQuery("#div_curso").show();
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery('.chosen-container').width("100%");
    }

    function carregarGrafico() {
        jQuery.gAjax.load("<?php echo URL_SYS . 'graficos/g_acessos/'; ?>load.php", {filtro_curso: jQuery("#cur_int_codigo").val(), filtro_tipo: jQuery("#filtro_tipo").val(), filtro_periodo: jQuery("#filtro_periodo").val()}, "#div_load");
    }
</script>