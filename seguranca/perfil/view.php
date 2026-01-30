<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERFIL");
GF::import(array("perfil"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Perfis", URL_SYS . 'seguranca/perfil/', 1);
$breadcrumb->add("Visualização", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualização de Perfil", true);
$header->addMenu("PERFIL", "Visualização de Perfil", "Visualize todas informações desse perfil do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

global $_id;
$perfil = new Perfil();
$perfil->setPef_int_codigo($_id);
$perfilDao = new PerfilDao();
$perfil = $perfilDao->selectById($perfil);
if (is_null($perfil->getPef_var_descricao())) {
    echo carregarPagina500();
} else {
    $html = gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Perfil',
        'id' => 'visualizacao',
        'col' => 6,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "pef_int_codigo", false, array("value" => $perfil->getPef_int_codigo()));
    $html .= gerarCamposVisualizacao(array(
        'Descrição' => $perfil->getPef_var_descricao(),
        'Status' => '<span class="label label-sm label-' . labelStatus($perfil->getPef_cha_status()) . ' label-white middle">' . $perfil->getPef_cha_status_format() . '</span>'
    ));
    $arrayBotoes = array();
    $arrayBotoes["btn_todos"] = "Ver Todos";
    if ($perfil->getPef_int_codigo() > 0 && GSecurity::verificarPermissao("PERFIL_CLO", false)) {
        $arrayBotoes["btn_clonar"] = "Clonar";
    }
    if ($perfil->getPef_int_codigo() > 0 && GSecurity::verificarPermissao("PERFIL_UPD", false)) {
        $arrayBotoes["btn_alterar"] = "Alterar";
    }
    if ($perfil->getPef_int_codigo() > 0 && GSecurity::verificarPermissao("PERFIL_DEL", false)) {
        $arrayBotoes["btn_excluir"] = "Excluir";
    }
    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes);
    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 6));

    $html .= '<div class="col-xs-12"><div class="hr hr-18 dotted hr-double"></div></div>';

    $html .= gerarCabecalho(array(
        'tipo' => 'full',
        'titulo' => 'Permissões do Perfil',
        'id' => 'lista',
        'botaoNovo' => false
    ));
    $colunas = array(
        array("titulo" => "", "largura" => "30px", "ordem" => false, "visivel" => true, "classe" => "center"),
        array("titulo" => "Código", "largura" => false, "ordem" => true, "visivel" => false, "classe" => "left"),
        array("titulo" => "Descrição", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
        array("titulo" => "Vínculo", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
    );
    $filtros = array(
        "pef_int_codigo"
    );
    $html .= getTableDataServerSide("dt_dados", URL_SYS . 'seguranca/perfil/loadPermissoes.php', $filtros, $colunas, false, 25, false, true, false);
    $html .= gerarRodape(array('tipo' => 'full'));
    echo $html;
}
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_alterar").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'seguranca/perfil/'; ?>form/" + jQuery("#pef_int_codigo").val() + "/";
        });
        jQuery("#btn_clonar").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente clonar esse Perfil?", "__calbackClonar('" + jQuery("#pef_int_codigo").val() + "');");
        });
        jQuery("#btn_excluir").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente excluir esse Perfil?", "__calbackExcluir('" + jQuery("#pef_int_codigo").val() + "');");
        });
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'seguranca/perfil/'; ?>";
        });
        jQuery("#btn_voltar").click(function () {
            if (window.history.length > 1) {
                jQuery.gDisplay.loadStart('HTML');
                window.history.back();
            }
        });
        if (window.history.length < 2) {
            jQuery("#btn_voltar").attr("disabled", "disabled");
        }
    });
    function __calbackClonar(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfil/'; ?>exec.php", {acao: 'clo', pef_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'seguranca/perfil/'; ?>';", "");
    }
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfil/'; ?>exec.php", {acao: 'del', pef_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'seguranca/perfil/'; ?>';", "");
    }
    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>view/" + codigo + "/";
    }
</script>