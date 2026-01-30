<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERMISSAO");
GF::import(array("permissao"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Permissões", URL_SYS . 'seguranca/permissao/', 1);
$breadcrumb->add("Visualização", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualização de Permissão", true);
$header->addMenu("PERMISSAO", "Visualização de Permissão", "Visualize todas informações dessa permissão do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

global $_id;
$permissao = new Permissao();
$permissao->setPem_var_codigo($_id);
$permissaoDao = new PermissaoDao();
$permissao = $permissaoDao->selectById($permissao);
if (is_null($permissao->getPem_var_descricao())) {
    echo carregarPagina500();
} else {
    $html = gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Permissão',
        'id' => 'visualizacao',
        'col' => 6,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "pem_var_codigo", false, array("value" => $permissao->getPem_var_codigo()));
    $html .= gerarCamposVisualizacao(array(
        'Código' => $permissao->getPem_var_codigo(),
        'Descrição' => $permissao->getPem_var_descricao(),
        'Vínculo' => (($permissao->getVinculo()->getPem_var_codigo()) ? '<a href="' . URL_SYS . 'seguranca/permissao/view/' . $permissao->getVinculo()->getPem_var_codigo() . '">' . $permissao->getVinculo()->getPem_var_codigo() . ' - ' . $permissao->getVinculo()->getPem_var_descricao() . '</a>' : '-')
    ));
    $arrayBotoes = array();
    $arrayBotoes["btn_todos"] = "Ver Todos";
    if (GSecurity::verificarPermissao("PERMISSAO_UPD", false)) {
        $arrayBotoes["btn_alterar"] = "Alterar";
    }
    if (GSecurity::verificarPermissao("PERMISSAO_DEL", false)) {
        $arrayBotoes["btn_excluir"] = "Excluir";
    }
    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes);
    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 6));
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
            window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>form/" + jQuery("#pem_var_codigo").val() + "/";
        });
        jQuery("#btn_excluir").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente excluir essa Permissão?", "__calbackExcluir('" + jQuery("#pem_var_codigo").val() + "');");
        });
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>";
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
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/permissao/'; ?>exec.php", {acao: 'del', pem_var_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'seguranca/permissao/'; ?>';", "");
    }
</script>