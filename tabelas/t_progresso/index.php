<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("T_PROGRESSO");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Tabelas >> Progresso", URL_SYS . 'tabelas/t_progresso/', 1);

$header = new GHeader("Tabelas de Progresso", true);
$header->addMenu("T_PROGRESSO", "Tabelas de Progresso", "Visualize os tabelas de Progresso do sistema");
$header->addTheme(Theme::addLib(array("multiselect")));
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$opt_niv_var_nome = carregarComboNiveis();
$codigosNivel = explode(",", buscarCookie("filtro_nivel"));
if (count($codigosNivel) == 0 || (isset($codigosNivel[0]) && $codigosNivel[0] == '')) {
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
global $__arrayTipoProgresso;
$html .= '<div class="col-lg-4 col-xs-12 no-padding-left">';
$html .= $form->addSelect("filtro_tipo", $__arrayTipoProgresso, buscarCookie("filtro_tipo", "-1"), "Tipo de Progresso", array("class" => "form-control selects chosen-select", "validate" => "([~] != -1)|Obrigatório"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
global $__arrayAgrupamentoProgresso;
$html .= '<div class="col-lg-3 col-xs-12 no-padding-left">';
$html .= $form->addSelect("filtro_agrupamento", $__arrayAgrupamentoProgresso, buscarCookie("filtro_agrupamento", "-1"), "Agrupar", array("class" => "form-control selects chosen-select", "validate" => "([~] != -1)|Obrigatório"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-lg-5 col-xs-12 no-padding-left">';
global $__paramDataRangeRelatorio;
$html .= $form->addDateRange("filtro_periodo", "Período:", false, array("value" => buscarCookie("filtro_periodo", date("d/m/Y") . ' - ' . date("d/m/Y")), "class" => "form-control campo", "placeholder" => "Período de tempo", "style" => "width: 180px;"), $__paramDataRangeRelatorio, array("ano" => true, "mes" => true), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-xs-12 no-padding-left">';
$html .= $form->addSelectMulti("filtro_nivel", $opt_niv_var_nome, $codigosNivel, "Estrutura Organizacional:", array("class" => "multiselect"), array("class" => "required"), false, false, false, "loadNivel();");
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-xs-12 no-padding-left" id="div_curso" style="display:none;">';
$html .= '</div>';
global $__arrayOrdemProgresso;
$html .= '<div class="col-lg-4 col-xs-12 no-padding-left">';
$html .= $form->addSelect("ordenacao", $__arrayOrdemProgresso, buscarCookie("ordenacao", "QD"), "Ordenação", array("class" => "form-control selects", "validate" => "([~] != -1)|Obrigatório"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '</fieldset>';
$html .= carregarBotoes("G");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 6));
$html .= gerarCabecalho(array('tipo' => 'box', 'col' => 6, 'fa' => 'table', 'titulo' => 'Tabelas de progresso'));
$html .= '<div id="div_load" class="p-4 divLoadTabela">';
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
            carregarTabela();
        });
        jQuery("#btn_excel").click(function () {
            window.open("<?php echo URL_SYS . 'tabelas/t_progresso/'; ?>excel.php");
        });
        loadNivel();
        jQuery("#filtro_agrupamento").change(function () {
            atualizarOrdenacao();
        });
        atualizarOrdenacao();
    });

    function salvarFiltros() {
        setParametroCookie('filtro_tipo', jQuery('#filtro_tipo').val());
        setParametroCookie('filtro_agrupamento', jQuery('#filtro_agrupamento').val());
        setParametroCookieGeral('filtro_nivel', jQuery('#filtro_nivel').val());
        setParametroCookieGeral('filtro_curso', jQuery('#cur_int_codigo').val());
        setParametroCookie("filtro_periodo", jQuery("#filtro_periodo").val());
        setParametroCookie('ordenacao', jQuery('#ordenacao').val());
    }

    function loadNivel() {
        jQuery.gAjax.load("<?php echo URL_SYS . 'inc/load/'; ?>cursos.php", {filtro_nivel: jQuery("#filtro_nivel").val()}, "#div_curso", undefined, false);
        jQuery("#div_curso").show();
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery('.chosen-container').width("100%");
    }

    function atualizarOrdenacao() {
        var agrupamento = jQuery('#filtro_agrupamento').val();
        var $ordenacao = jQuery('#ordenacao');
        $ordenacao.find('option:not([value="-1"])').hide().prop('disabled', true);
        if (agrupamento === 'E') {
            // Escola: Permite Escola (E) e Quantidade (Q)
            $ordenacao.find('option[value^="E"], option[value^="Q"]').show().prop('disabled', false);
        } else if (agrupamento === 'C') {
            // Curso: Permite Curso (C) e Quantidade (Q)
            $ordenacao.find('option[value^="C"], option[value^="Q"]').show().prop('disabled', false);
        } else if (agrupamento === 'A') {
            // Aluno: Permite Aluno (A)
            $ordenacao.find('option[value^="A"]').show().prop('disabled', false);
        }
        // Validação: Se a opção atualmente selecionada foi desabilitada, 
        if ($ordenacao.find('option:selected').css('display') === 'none') {
            $ordenacao.val("-1");
        }
    }

    function carregarTabela(tipo, codigo) {
        jQuery.gAjax.load("<?php echo URL_SYS . 'tabelas/t_progresso/'; ?>load.php", {filtro_curso: jQuery("#cur_int_codigo").val(), filtro_tipo: jQuery("#filtro_tipo").val(), filtro_agrupamento: jQuery("#filtro_agrupamento").val(), filtro_periodo: jQuery("#filtro_periodo").val(), ordenacao: jQuery("#ordenacao").val(), tipo: tipo, codigo: codigo}, "#div_load");
    }
</script>