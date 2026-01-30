<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERMISSAO");
GF::import(array("permissao"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Permissões", URL_SYS . 'seguranca/permissao/', 1);
$breadcrumb->add("Manutenção", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Manutenção de Permissões", true);
$header->addMenu("PERMISSAO", "Manutenção de Permissões", "Insira, altere e exclua as permissões dos perfis no sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$mysql = new GDbMysql();
$opt_pem_var_descricao = $mysql->executeCombo("SELECT pem_var_codigo, CONCAT(pem_var_codigo, ' - ', pem_var_descricao) FROM permissao ORDER BY pem_var_descricao;");

$acao = 'ins';
$pem_var_codigo = '';
$pem_var_descricao = '';
$pem_var_vinculo = '-1';

global $_id;
$permissao = new Permissao();
$permissao->setPem_var_codigo($_id);
$permissaoDao = new PermissaoDao();
$permissao = $permissaoDao->selectById($permissao);
if (!is_null($permissao->getPem_var_descricao())) {
    $acao = 'upd';
    $pem_var_codigo = $permissao->getPem_var_codigo();
    $pem_var_descricao = $permissao->getPem_var_descricao();
    $pem_var_vinculo = $permissao->getVinculo()->getPem_var_codigo();
}

$form = new GForm();
$html = '';
// <editor-fold desc="formulario">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => formataTituloManutencao($acao) . 'Permissão',
    'id' => 'formulario',
    'col' => 6,
    'fa' => 'key'
        ));
$html .= $form->open("form");
$html .= $form->addInput('hidden', 'acao', false, array('value' => $acao));
$html .= '<fieldset>';
if ($acao == 'ins')
    $html .= $form->addInput("text", "pem_var_codigo", "Código", array("value" => $pem_var_codigo, "class" => "form-control __upper", "size" => "20", "maxlength" => "50", "validate" => "required"), array("class" => "required"));
else
    $html .= $form->addInput("text", "pem_var_codigo", "Código", array("value" => $pem_var_codigo, "class" => "form-control __upper", "size" => "20", "maxlength" => "50", "validate" => "required", "disabled" => "disabled"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "pem_var_descricao", "Descrição", array("value" => $pem_var_descricao, "class" => "form-control", "size" => "25", "maxlength" => "100", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addSelect("pem_var_vinculo", $opt_pem_var_descricao, $pem_var_vinculo, "Vínculo", array("class" => "form-control chosen-select"));
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
        jQuery(":input:visible:enabled:not([readonly='readonly']):not('.nav-search-input'):first").focus();
        jQuery('.chosen-select').chosen(paramChosen);

        jQuery("#btn_insert").click(function () {
            jQuery("#pem_var_codigo").removeAttr("disabled");
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/permissao/'; ?>exec.php", jQuery("#form").serializeArray(), "calbackCancelar();", "");
            }
        });
        jQuery("#btn_insert_novo").click(function () {
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/permissao/'; ?>exec.php", jQuery("#form").serializeArray(), "window.location.reload();", "");
            }
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "calbackCancelar();", "");
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>";
    }
</script>