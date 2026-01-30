<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("TAG");
GF::import(array("tag"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Tags", URL_SYS . 'configuracoes/tag/', 1);
$breadcrumb->add("Visualização", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Visualização de Tag", true);
$header->addMenu("TAG", "Visualização de Tag", "Visualize as informações dessa Tag do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$tag = new Tag();
$tag->setTag_int_codigo($_id);
$tagDao = new TagDao();
$tag = $tagDao->selectById($tag);
if (is_null($tag->getTag_var_url())) {
    echo carregarPagina500();
} else {
    $html .= gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Tag',
        'id' => 'visualizacao',
        'col' => 8,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "tag_int_codigo", false, array("value" => $tag->getTag_int_codigo()));
    $html .= gerarCamposVisualizacao(array(
        'Código' => $tag->getTag_int_codigo(),
        'Título' => $tag->getTag_var_titulo(),
        'Url' => $tag->getTag_var_url(),
        'Valores' => $tag->getTag_txt_valores(),
        'Informações' => $tag->getTag_var_informacoes(),
        'Permissão' => $tag->getPem_var_codigo()
    ));

    $arrayBotoes = array();
    if (!isFrame()) {
        if (GSecurity::verificarPermissao("TAG_UPD", false)) {
            $arrayBotoes["btn_alterar"] = "Alterar";
        }
        $arrayBotoes["btn_todos"] = "Ver Todas";
        if (GSecurity::verificarPermissao("TAG_DEL", false)) {
            $arrayBotoes["btn_excluir"] = "Excluir";
        }
    } else {
        $arrayBotoes["btn_popup"] = "Abrir em Outra janela";
    }
    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes);

    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 8));

    echo $html;
}


/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_popup").click(function () {
            parent.window.open("<?php echo URL_SYS . 'configuracoes/tag/view/'; ?>" + jQuery("#tag_int_codigo").val(), "_blank");
        });
        jQuery("#btn_alterar").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'configuracoes/tag/'; ?>form/" + jQuery("#tag_int_codigo").val() + "/?return=configuracoes/tag/view/" + jQuery("#tag_int_codigo").val();
        });
        jQuery("#btn_excluir").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente excluir essa tag?", "__calbackExcluir('" + jQuery("#tag_int_codigo").val() + "');");
        });
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'configuracoes/tag/'; ?>";
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
        jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/tag/'; ?>exec.php", {acao: 'del', tag_int_codigo: codigo}, "jQuery.gDisplay.loadStart('HTML'); window.location.href = '<?php echo URL_SYS . 'configuracoes/tag/'; ?>';", "");
    }
</script>
