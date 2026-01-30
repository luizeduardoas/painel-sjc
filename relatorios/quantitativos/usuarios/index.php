<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");
GSecurity::verificarPermissao("QUANTITATIVOSUSUARIOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Relatórios >> Quantitativos", URL_SYS . 'relatorios/quantitativos/', 1);
$breadcrumb->add("Usuários", URL_SYS . 'relatorios/quantitativos/usuarios/', 2);

$header = new GHeader("Relatório de Quantitativo de Usuários", true);
$header->addMenu("QUANTITATIVOSUSUARIOS", "Relatório de Quantitativo de Usuários", "Visualize os Quantitativo das Usuários do sistema");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();

$html = '';
$filtro = '';
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_tipo", array("perfil" => "por Perfil", "status" => "por Status"), buscarCookie("filtro_tipo", "perfil"), "Tipo:", array("class" => "chosen-select"), false, false, false) . '</div>';
// <editor-fold desc="Lista">
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Relatório de Quantitativo de Usuários',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'export' => true,
    'botaoAtualizar' => true,
    'botaoExcel' => true
        ));
$html .= '<div id="dt_dados" class="dataTables_wrapper"></div>';
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery(".formFiltros select").change(function () {
            salvarFiltros();
            __atualizar();
        });
        jQuery("#btn_atualizar").click(function () {
            salvarFiltros();
            __atualizar();
        });
        jQuery("#btn_excel").click(function () {
            window.open("<?php echo URL_SYS . 'relatorios/quantitativos/usuarios/'; ?>excel.php?filtro_tipo=" + jQuery('#filtro_tipo').val());
        });
        __atualizar();
    });

    function salvarFiltros() {
        setParametroCookie("filtro_tipo", jQuery("#filtro_tipo").val());
    }

    function __atualizar() {
        jQuery.gAjax.load("<?php echo URL_SYS . 'relatorios/quantitativos/usuarios/load.php'; ?>", {"filtro_tipo": jQuery("#filtro_tipo").val()}, "#dt_dados");
    }
</script>