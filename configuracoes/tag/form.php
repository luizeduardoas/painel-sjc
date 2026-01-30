<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("TAG");
GF::import(array("tag"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Tags", URL_SYS . 'configuracoes/tag/', 1);
$breadcrumb->add("Manutenção", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Manutenção de Tags", true);
$header->addMenu("TAG", "Manutenção de Tags", "Insira, altere e exclua as tags do sistema");
$header->addTheme(Theme::addLib(array("tag")));
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */


$acao = 'ins';
$tag_int_codigo = '';
$tag_var_titulo = '';
$tag_var_url = '';
$tag_txt_valores = '';
$tag_var_informacoes = '';
$pem_var_codigo = '';

global $_id;
$tag = new Tag();
$tag->setTag_int_codigo($_id);
$tagDao = new TagDao();
$tag = $tagDao->selectById($tag);
if (!is_null($tag->getTag_var_url())) {
    GSecurity::verificarPermissao("TAG_UPD");
    $acao = 'upd';
    $tag_int_codigo = $tag->getTag_int_codigo();
    $tag_var_titulo = $tag->getTag_var_titulo();
    $tag_var_url = $tag->getTag_var_url();
    $tag_txt_valores = $tag->getTag_txt_valores();
    $tag_var_informacoes = $tag->getTag_var_informacoes();
    $pem_var_codigo = $tag->getPem_var_codigo();
} else {
    GSecurity::verificarPermissao("TAG_INS");
}

$form = new GForm();
$html = '';
// <editor-fold desc="formulario">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => formataTituloManutencao($acao) . 'Tag',
    'id' => 'formulario',
    'col' => 6,
    'fa' => 'edit'
        ));
$html .= $form->open("form");
$html .= $form->addInput('hidden', 'acao', false, array('value' => $acao));
$html .= '<fieldset>';
$html .= $form->addInput("hidden", "tag_int_codigo", false, array("value" => $tag_int_codigo));
$html .= $form->addInput("text", "tag_var_titulo", "Título", array("value" => $tag_var_titulo, "class" => "form-control input", "size" => "80", "maxlength" => "100", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "tag_var_url", "Url", array("value" => $tag_var_url, "class" => "form-control input", "size" => "80", "maxlength" => "200", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addTextarea("tag_txt_valores", $tag_txt_valores, "Valores", array("class" => "form-control textarea", "placeholder" => "Digite e pressione enter...", "validate" => "required"), array("class" => "required", "style" => "display: block"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addTextarea("tag_var_informacoes", $tag_var_informacoes, "Informações", array("class" => "form-control textarea", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "pem_var_codigo", "Permissão", array("value" => $pem_var_codigo, "class" => "form-control input __upper", "size" => "40", "maxlength" => "40", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';

$html .= '</fieldset>';
if ($acao == 'ins')
    $html .= carregarBotoes("N");
else
    $html .= carregarBotoes("I");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 6));
// </editor-fold>
echo $html;


/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#form').attr('autocomplete', 'off');
        jQuery(":input:visible:enabled:not([readonly='readonly']):not('.nav-search-input'):first").focus();
        jQuery('.chosen-select').chosen(paramChosen);

        jQuery("#tag_txt_valores").tag({placeholder: jQuery("#tag_txt_valores").attr('placeholder')});

        jQuery("#btn_insert").click(function () {
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/tag/'; ?>exec.php", jQuery("#form").serializeArray(), "calbackCancelar();", "");
            }
        });
        jQuery("#btn_insert_novo").click(function () {
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/tag/'; ?>exec.php", jQuery("#form").serializeArray(), "window.location.reload();", "");
            }
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "calbackCancelar();", "");
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        if (QueryString.return == undefined) {
            window.location.href = "<?php echo URL_SYS . 'configuracoes/tag/'; ?>";
        } else {
            window.location.href = "<?php echo URL_SYS; ?>" + QueryString.return;
        }
    }
</script>
