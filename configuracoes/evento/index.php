<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("EVENTO");
GF::import(array("evento"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Eventos", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Configurações >> Eventos", true);
$header->addMenu("EVENTO", "Configuração de Eventos", "Visualize todos eventos do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

$html = '';

// <editor-fold desc="Lista">
$filtro = '';
global $__arrayTipoEvento;
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_tipo", array("-1" => "Todos") + $__arrayTipoEvento, buscarCookie("filtro_tipo", "-1"), "Tipo", array("class" => "chosen-select"), false, false, false) . '</div>';
$filtro .= '<div class="filtroInline">' . $form->addDateRange("filtro_periodo", "Período", false, array("value" => buscarCookie("filtro_periodo", date("d/m/Y") . ' - ' . date("d/m/Y")), "class" => "form-control campo", "placeholder" => "Período de Avaliação", "style" => "width: 180px;"), array("'maxDate'" => "'" . buscarDataAmanha() . "'"), array("hoje" => true, "mes" => true)) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Eventos',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'botaoAtualizar' => true
        ));
$colunas = array(
    array("titulo" => "", "largura" => "60px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Data e Hora", "largura" => "140px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Tipo", "largura" => "80px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Título", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Dados", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Usuário", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
);
$filtros = array(
    "filtro_tipo", "filtro_periodo"
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'configuracoes/evento/load.php', $filtros, $colunas, false, 25);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script>
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery('#filtro_periodo').on('apply.daterangepicker', function (ev, picker) {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
        jQuery(".formFiltros select").change(function () {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
        jQuery("#btn_atualizar").click(function () {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
    });

    function salvarFiltros() {
        setParametroCookie("filtro_tipo", jQuery("#filtro_tipo").val());
        setParametroCookie("filtro_periodo", jQuery("#filtro_periodo").val());
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'configuracoes/evento/'; ?>view/" + codigo + "/";
    }

    function __popup(codigo) {
        var parametros = {
            iframe: true,
            width: '90%',
            height: '90%',
            href: "<?php echo URL_SYS . 'configuracoes/evento/view/'; ?>" + codigo + "?iframe=on&close=on"
        };
        jQuery.colorbox(jsonConcat(paramColorboxIframe, parametros));
    }
</script>