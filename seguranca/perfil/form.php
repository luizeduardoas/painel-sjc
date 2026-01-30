<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERFIL");
GF::import(array("perfil"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Perfis", URL_SYS . 'seguranca/perfil/', 1);
$breadcrumb->add("Manutenção", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Manutenção de Perfis", true);
$header->addMenu("PERFIL", "Manutenção de Perfis", "Insira, altere e exclua os perfis de usuários do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$acao = 'ins';
$pef_int_codigo = '';
$pef_cha_status = 'A';
$pef_var_descricao = '';

global $_id;
$perfil = new Perfil();
$perfil->setPef_int_codigo(seVazioRetorneNulo($_id));
$perfilDao = new PerfilDao();
$perfil = $perfilDao->selectById($perfil);
if (!is_null($perfil->getPef_var_descricao())) {
    $acao = 'upd';
    $pef_int_codigo = $perfil->getPef_int_codigo();
    $pef_var_descricao = $perfil->getPef_var_descricao();
    $pef_cha_status = $perfil->getPef_cha_status();
}

$form = new GForm();
$html = '';
// <editor-fold desc="formulario">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => formataTituloManutencao($acao) . 'Perfil',
    'id' => 'formulario',
    'col' => 4,
    'fa' => 'key'
        ));
$html .= $form->open("form");
$html .= $form->addInput('hidden', 'acao', false, array('value' => $acao));
$html .= $form->addInput("hidden", "pef_int_codigo", false, array("value" => $pef_int_codigo));
$html .= '<fieldset>';
$html .= $form->addInput("text", "pef_var_descricao", "Descrição", array("value" => $pef_var_descricao, "class" => "form-control", "size" => "25", "maxlength" => "50", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addSwitch("pef_cha_status", "Status", (($pef_cha_status == 'A') ? array("checked" => "checked") : false), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</fieldset>';
if ($acao == 'ins')
    $html .= carregarBotoes("N");
else
    $html .= carregarBotoes("I");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 4));
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

        jQuery("#btn_insert").click(function () {
            jQuery("#pef_cha_status").val(jQuery("#pef_cha_status_switch").is(":checked") ? 'A' : 'I');
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfil/'; ?>exec.php", jQuery("#form").serializeArray(), "calbackCancelar();", "");
            }
        });
        jQuery("#btn_insert_novo").click(function () {
            jQuery("#pef_cha_status").val(jQuery("#pef_cha_status_switch").is(":checked") ? 'A' : 'I');
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfil/'; ?>exec.php", jQuery("#form").serializeArray(), "resetForm('form');", "");
            }
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "calbackCancelar();", "");
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'seguranca/perfil/'; ?>";
    }
</script>