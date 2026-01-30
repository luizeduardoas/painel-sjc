<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("ALTERARSENHA");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Alterar Senha", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Minha Conta >> Alterar Senha", true);
$header->addMenu("ALTERARSENHA", "Alterar Senha", "Altere sua senha para uma mais confortável");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';
// <editor-fold desc="PAGE CONTENT">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Alterar Senha',
    'id' => 'formulario',
    'col' => 4,
    'fa' => 'key'
        ));
$html .= $form->open("form_senha");
$html .= $form->addInput('hidden', 'acao', false, array('value' => 'alterarSenha'));
$html .= '<fieldset>';
$html .= $form->addInput("password", "usu_var_senha", "Senha Atual", array("size" => "20", "maxlength" => "20", "class" => "form-control", "autocomplete" => "off", "validate" => "required;senha"), array("class" => "required"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-key"></i></span>'), true);
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("password", "usu_var_senha_new", "Senha Nova", array("value" => "", "class" => "form-control", "size" => "20", "maxlength" => "20", "autocomplete" => "off", "validate" => "required;conferencia"), array("class" => "required"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-key"></i></span>'), true);
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("password", "usu_var_senha_new_conf", "Repita a senha", array("value" => "", "class" => "form-control", "size" => "20", "maxlength" => "20", "autocomplete" => "off", "validate" => "required;senha"), array("class" => "required"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-key"></i></span>'), true);
$html .= '</fieldset>';
$html .= carregarBotoes("E");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 4));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script>
    jQuery(document).ready(function () {
        jQuery('#form_senha').attr('autocomplete', 'off');
        jQuery(":input:visible:enabled:not([readonly='readonly']):not('.nav-search-input'):first").focus();
        jQuery("#btn_salvar").click(function () {
            if (jQuery("#form_senha").gValidate())
                jQuery.gAjax.exec('<?php echo URL_SYS; ?>inc/exe/usuario.php', jQuery("#form_senha").serializeArray(), "jQuery.gDisplay.loadStart('HTML');jQuery('#form_senha').submit();", "");
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "jQuery.gDisplay.loadStart('HTML');window.location = '<?php echo URL_SYS; ?>home/';", "");
        });
        pressEnter("#usu_var_senha_new_conf", "jQuery('#btn_salvar').click();");
    });
</script>