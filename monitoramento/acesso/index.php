<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("ACESSO");
GF::import(array("acesso"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Monitoramento >> Acessos", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Monitoramento >> Acessos", true);
$header->addMenu("ACESSO", "Listagem de Acessos", "Visualize, insira, altere e exclua os Acessos do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$mysql = new GDbMysql();
$opt_usu_int_codigo = $mysql->executeCombo("SELECT DISTINCT(usu_int_codigo), usu_var_nome FROM usuario ORDER BY usu_var_nome;");

$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$filtro = '';
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_usuario", array("-1" => "Todos") + $opt_usu_int_codigo, buscarCookie("filtro_usuario", "-1"), "Usuário", array("class" => "chosen-select"), false, false, false) . '</div>';
$filtro .= '<div class="filtroInline">' . $form->addDateRange("filtro_periodo", "Período", false, array("value" => buscarCookie("filtro_periodo", date("d/m/Y") . ' - ' . date("d/m/Y")), "class" => "form-control campo", "placeholder" => "Período de Avaliação", "style" => "width: 180px;"), array("'maxDate'" => "'" . buscarDataAmanha() . "'"), array("hoje" => true, "mes" => true)) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Acessos',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array());
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Data e Hora", "largura" => "120px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Usuário", "largura" => "80px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "IP", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Sessão", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Server", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "URL", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Request", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Agent", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Json", "largura" => false, "ordem" => true, "visivel" => false, "classe" => "left"),
    array("titulo" => "Lead", "largura" => false, "ordem" => true, "visivel" => false, "classe" => "left"),
);
$filtros = array('filtro_usuario', 'filtro_periodo');
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'monitoramento/acesso/load.php', $filtros, $colunas, false, 25, false, true, true);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>

<script type="text/javascript">
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
        setParametroCookie("filtro_usuario", jQuery("#filtro_usuario").val());
        setParametroCookie("filtro_periodo", jQuery("#filtro_periodo").val());
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'monitoramento/acesso/'; ?>view/" + codigo + "/";
    }
    
    function __popup(codigo) {
        var parametros = {
            iframe: true,
            width: '90%',
            height: '90%',
            href: "<?php echo URL_SYS . 'monitoramento/acesso/view/'; ?>" + codigo + "?iframe=on&close=on"
        };
        jQuery.colorbox(jsonConcat(paramColorboxIframe, parametros));
    }
</script>
