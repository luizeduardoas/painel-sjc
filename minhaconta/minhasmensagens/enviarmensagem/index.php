<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");

GSecurity::verificarPermissao("ENVIARMENSAGEM");
GF::import(array("mensagem"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Minhas Mensagens", URL_SYS . 'minhaconta/minhasmensagens/', 1);
$breadcrumb->add("Enviar Mensagem", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Enviar Mensagem", true);
$header->addMenu("MINHASMENSAGENSENVIARMENSAGEM", "Enviar Mensagem", "Envie mensagem para outros usuários do sistema");
$header->addTheme(Theme::addLib(array("multiselect")));
$header->addLib(array("ckeditor"));
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */
$usuario = getUsuarioSessao();

$mysql = new GDbMysql();
$opt_usu_var_nome = $mysql->executeCombo("SELECT usu_int_codigo, usu_var_nome FROM usuario usu WHERE usu.usu_int_codigo <> " . $usuario->getUsu_int_codigo() . "  ORDER BY usu_var_nome;");

$men_var_titulo = '';
$men_txt_texto = '';
$destinatarios = array();

global $_id;
$mensagem = new Mensagem();
$mensagem->setMen_int_codigo($_id);
$mensagemDao = new MensagemDao();
$mensagem = $mensagemDao->selectById($mensagem);
if (!is_null($mensagem->getMen_var_titulo())) {
    $men_var_titulo = 'RES:: ' . $mensagem->getMen_var_titulo();
    $men_txt_texto = '<p></p><p></p><p></p><p><strong>' . $mensagem->getMen_dti_envio_format() . ' - ' . $mensagem->getRemetente()->getUsu_var_nome() . '</strong></p>' . $mensagem->getMen_txt_texto();
    $destinatarios = array($mensagem->getRemetente()->getUsu_int_codigo());
}

$form = new GForm();
$html = '';
// <editor-fold desc="formulario">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Enviar Mensagem',
    'id' => 'formulario',
    'col' => 10,
    'fa' => 'paper-plane'
        ));
$html .= $form->open("form");
$html .= $form->addInput('hidden', 'acao', false, array('value' => "enviar"));
$html .= $form->addInput("hidden", "men_int_remetente", false, array("value" => $usuario->getUsu_int_codigo()));
$html .= $form->addLabel("arr_destinatarios", "Destinatários", array("style" => "display:none;"));
$html .= $form->addInput("hidden", "arr_destinatarios", false, array("validate" => "required"));
$html .= '<fieldset>';

$html .= $form->addSelectMulti("destinatarios", $opt_usu_var_nome, $destinatarios, "Destinatários", array("class" => "form-control multiselect"), array("style" => "display:block;", "class" => "required"), false, false);
$html .= '<div class="space space-8"></div>';

if (GSecurity::verificarPermissao("ENVIARMENSAGEMEMAIL", false)) {
    $html .= $form->addSwitch("enviar_email", "Enviar também por E-mail?", false, false, 'sim-nao');
    $html .= '<div class="space space-8"></div>';
}

$html .= $form->addInput("text", "men_var_titulo", "Assunto", array("value" => $men_var_titulo, "class" => "form-control input", "size" => "80", "maxlength" => "100", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addCKEditor("men_txt_texto", $men_txt_texto, "Texto", array("class" => "form-control ckeditor", "validate" => "required"), array("toolbar" => "'minimo'"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</fieldset>';
$html .= carregarBotoes("EV");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 4));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#form').attr('autocomplete', 'off');
        jQuery(":input:visible:enabled:not([readonly='readonly']):not('.nav-search-input'):first").focus();
        jQuery('.chosen-select').chosen(paramChosen);

        jQuery("#btn_enviar").click(function () {
            jQuery("#arr_destinatarios").val(jQuery("#destinatarios").val());
            jQuery("#enviar_email").val(jQuery("#enviar_email_switch").is(":checked") ? 'S' : 'N');
            if (jQuery("#form").gValidate()) {
                jQuery.gAjax.exec("<?php echo URL_SYS . 'minhaconta/minhasmensagens/'; ?>exec.php", jQuery("#form").serializeArray(), "calbackCancelar();", "");
            }
        });
        jQuery("#btn_voltar").click(function () {
            jQuery.gDisplay.showYN("Essa mensagem não foi enviada e todo seu conteúdo será perdido. Deseja realmente voltar?", "calbackCancelar();", "");
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'minhaconta/minhasmensagens/enviarmensagem/'; ?>" + window.location.search;
    }
</script>
