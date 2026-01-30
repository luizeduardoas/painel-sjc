<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("USUARIO");
GF::import(array("usuario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Cadastros >> Usuários", URL_SYS . 'cadastros/usuario/', 1);
$breadcrumb->add("Histórico de Acessos", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Histórico de Acessos", true);
$header->addMenu("USUARIO", "Histórico de Acessos", "Visualize o histórico de acessos desse usuário do sistema");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$usuario = new Usuario();
$usuario->setUsu_int_codigo($_id);
$usuarioDao = new UsuarioDao();
$usuario = $usuarioDao->selectById($usuario);
if (is_null($usuario->getUsu_var_nome())) {
    echo carregarPagina500();
} else {
    $html .= '<div class="col-xs-12 col-sm-10 col-sm-offset-1">';
    $html .= '    <h3 class="header smaller lighter green">Histórico de Acessos</h3>';
    $html .= '    <input type="hidden" id="usu_int_codigo" nema="usu_int_codigo" value="' . $usuario->getUsu_int_codigo() . '"/>';
    $html .= '    <div class="col-xs-12">';
    $html .= '      <div class="well well-form no-margin">';
    $html .= '      Usuário: <a class="tooltip-primary" data-toggle="tooltip" title="Visualizar esse Usuário" href="' . URL_SYS . 'cadastros/usuario/view/' . $usuario->getUsu_int_codigo() . '">' . $usuario->getUsu_var_nome() . '</a>';
    $html .= '      </div>';
    $html .= '      <div class="text-center well col-xs-12 formFiltros">';
    $html .= '          <div class="filtroInline text-left" style="margin-top:0;width: 285px;">' . $form->addDateRange("filtro_periodo", "Período:", false, array("value" => buscarCookie("filtro_periodo", date("d/m/Y") . ' - ' . date("d/m/Y")), "class" => "form-control campo", "placeholder" => "Período de Avaliação", "style" => "width: 180px;"), array("'maxDate'" => "'" . buscarDataAmanha() . "'"), array("hoje" => true, "mes" => true)) . '</div>';
    $html .= '          <div class="filtroInline text-left" style="margin-top:0">' . $form->addSwitch("filtro_sintetico", "Sintético:", ((buscarCookie("filtro_sintetico", "1") == '1') ? array("checked" => "checked") : false), false, 'sim-nao') . '</div>';
    $html .= '          <a id="btn_atualizar" class="btn btn-success btn-lg" style="margin-left: 5px; vertical-align:bottom;"><i class="ace-icon fa fa-refresh"></i> Atualizar</a>';
    $html .= '      </div>';
    $html .= '    </div>';
    $html .= '    <div class="space-20"></div>';
    $html .= '    <div class="clearfix"></div>';
    $html .= '    <div id="load" class="col-xs-12"></div>';
    $html .= '</div>';
    $html .= '<div class="clearfix"></div>';

    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes, "", false, "background-color:#fff;border: 0");
    $html .= $form->close();

    echo $html;
}
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
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
        jQuery('#filtro_periodo').on('apply.daterangepicker', function (ev, picker) {
            salvarFiltros();
            atualizar();
        });
        jQuery("#btn_atualizar").click(function () {
            salvarFiltros();
            atualizar();
        });
    });

    function salvarFiltros() {
        jQuery("#filtro_sintetico").val(jQuery("#filtro_sintetico_switch").is(":checked") ? '1' : '0');
        setParametroCookie('filtro_periodo', jQuery('#filtro_periodo').val());
        setParametroCookie('filtro_sintetico', jQuery('#filtro_sintetico').val());
    }

    function atualizar() {
        jQuery("#filtro_sintetico").val(jQuery("#filtro_sintetico_switch").is(":checked") ? '1' : '0');
        jQuery.gAjax.load("<?php echo URL_SYS . 'inc/load/'; ?>acessos.php", {usu_int_codigo: jQuery("#usu_int_codigo").val(), filtro_periodo: jQuery("#filtro_periodo").val(), filtro_sintetico: jQuery("#filtro_sintetico").val()}, "#load");
    }
</script>
