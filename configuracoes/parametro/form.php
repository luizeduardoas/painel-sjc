<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PARAMETRO");
GF::import(array("parametro"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Parâmetros", URL_SYS . 'configuracoes/parametro/', 1);
$breadcrumb->add("Manutenção", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Manutenção de Parâmetros", true);
$header->addMenu("PARAMETRO", "Manutenção de Parâmetros", "Insira, altere e exclua os parâmetros de configurações do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$acao = 'ins';
$par_int_codigo = '';
$par_var_chave = '';
$par_var_descricao = '';
$par_txt_valor = '';
$par_dti_atualizacao = '';
$usu_var_nome = '';

global $_id;
$parametro = new Parametro();
$parametro->setPar_int_codigo($_id);
$parametroDao = new ParametroDao();
$parametro = $parametroDao->selectById($parametro);
if (!is_null($parametro->getPar_var_descricao())) {
    GSecurity::verificarPermissao("PARAMETRO_UPD");
    $acao = 'upd';
    $par_int_codigo = $parametro->getPar_int_codigo();
    $par_var_chave = $parametro->getPar_var_chave();
    $par_var_descricao = $parametro->getPar_var_descricao();
    $par_txt_valor = $parametro->getPar_txt_valor();
    $par_dti_atualizacao = $parametro->getPar_dti_atualizacao_format();
    $usu_var_nome = $parametro->getUsuario()->getUsu_var_nome();
} else {
    GSecurity::verificarPermissao("PARAMETRO_INS");
}

$form = new GForm();
$html = '';
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => formataTituloManutencao($acao) . 'Parâmetro',
    'id' => 'formulario',
    'col' => 8,
    'fa' => 'wrench'
        ));
$html .= $form->open("form");
$html .= $form->addInput('hidden', 'acao', false, array('value' => $acao));
$html .= $form->addInput("hidden", "par_int_codigo", false, array("value" => $par_int_codigo));
$html .= '<fieldset>';
$html .= $form->addInput("text", "par_var_chave", "Chave", array("value" => $par_var_chave, "class" => "form-control __upper", "size" => "50", "maxlength" => "50", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "par_var_descricao", "Descrição", array("value" => $par_var_descricao, "class" => "form-control", "size" => "100", "maxlength" => "200", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addTextarea("par_txt_valor", $par_txt_valor, "Valor", array("class" => "form-control", "rows" => "2", "cols" => "50", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</fieldset>';
if ($acao == 'ins')
    $html .= carregarBotoes("N");
else
    $html .= carregarBotoes("I");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 8));
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
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/parametro/'; ?>exec.php", jQuery("#form").serializeArray(), "calbackCancelar();", "");
            }
        });
        jQuery("#btn_insert_novo").click(function () {
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/parametro/'; ?>exec.php", jQuery("#form").serializeArray(), "resetForm('form');", "");
            }
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "calbackCancelar();", "");
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'configuracoes/parametro/'; ?>";
    }
</script>