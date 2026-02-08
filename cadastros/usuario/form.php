<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("USUARIO");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Cadastros >> Usuários", URL_SYS . 'cadastros/usuario/', 1);
$breadcrumb->add("Manutenção", $_SERVER["REQUEST_URI"], 2);

$header = new GHeader("Manutenção de Usuários", true);
$header->addMenu("USUARIO", "Manutenção de Usuários", "Insira, altere e exclua os usuários do sistema");
$header->addTheme(Theme::addLib(array("mask")));
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$usuarioSessao = getUsuarioSessao();
$mysql = new GDbMysql();
if ($usuarioSessao->getPerfil()->getPef_int_codigo() == PERFIL_ADMINISTRADOR) {
    $opt_pef_var_descricao = $mysql->executeCombo("SELECT pef_int_codigo, pef_var_descricao FROM perfil WHERE pef_int_codigo <> 0 ORDER BY pef_var_descricao;");
} else {
    $opt_pef_var_descricao = $mysql->executeCombo("SELECT pef_int_codigo, pef_var_descricao FROM perfil WHERE pef_int_codigo > 2 ORDER BY pef_var_descricao;");
}

$acao = 'ins';
$usu_int_codigo = null;
$usu_var_token = '';
$pef_int_codigo = '-1';
$usu_cha_status = 'A';
$usu_var_nome = '';
$usu_var_identificador = '';
$usu_var_email = '';
$usu_var_foto = URL_UPLOAD . 'usuario/unknown.png';
$usu_var_motivo = '';
$div_motivo = 'display:none;';
$usu_cha_validado = 'S';

global $_id;
$usuario = new Usuario();
$usuario->setUsu_int_codigo(seVazioRetorneNulo($_id));
$usuarioDao = new UsuarioDao();
$usuario = $usuarioDao->selectById($usuario);
if (!is_null($usuario->getUsu_var_nome())) {
    GSecurity::verificarPermissao("USUARIO_UPD");
    $acao = 'upd';
    $usu_int_codigo = $usuario->getUsu_int_codigo();
    $usu_var_token = $usuario->getUsu_var_token();
    $pef_int_codigo = $usuario->getPerfil()->getPef_int_codigo();
    $usu_var_nome = $usuario->getUsu_var_nome();
    $usu_cha_status = $usuario->getUsu_cha_status();
    $usu_var_identificador = $usuario->getUsu_var_identificador();
    $usu_var_email = $usuario->getUsu_var_email();
    $usu_var_foto = $usuario->getUsu_var_foto();
    $usu_var_motivo = $usuario->getUsu_var_motivo();
    $div_motivo = ($usuario->getUsu_cha_status()) ? 'display:none;' : 'display:block;';
    $usu_cha_validado = $usuario->getUsu_cha_validado();
} else {
    GSecurity::verificarPermissao("USUARIO_INS");
}

$form = new GForm();
$html = '';
// <editor-fold desc="formulario">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Manutenção de Usuário',
    'id' => 'formulario',
    'col' => 12,
    'fa' => 'user'
        ));
$html .= '<form id="form" name="form" action="" class="formConta" enctype="multipart/form-data" lang="pt" method="post" autocomplete="off">';
$html .= $form->addInput('hidden', 'acao', false, array('value' => $acao));
$html .= $form->addInput("hidden", "usu_int_codigo", false, array("value" => $usu_int_codigo));
$html .= $form->addInput("hidden", "usu_var_token", false, array("value" => $usu_var_token));
$html .= $form->addInput('hidden', 'atual', false, array('value' => $usu_var_foto));
$html .= '<fieldset>';
$html .= '<div style="float:left; width:calc(100% - 200px);">';
$html .= '<div class="col-sm-6 no-padding-left">';
$html .= $form->addSelect("pef_int_codigo", $opt_pef_var_descricao, $pef_int_codigo, "Perfil", array("class" => "chosen-select form-control", "validate" => "([~] != -1)|Obrigatório"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "usu_var_nome", "Nome", array("value" => $usu_var_nome, "class" => "form-control", "size" => "25", "maxlength" => "50", "validate" => "required"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "usu_var_email", "E-mail", array("value" => $usu_var_email, "class" => "form-control __lower", "size" => "40", "maxlength" => "100", "validate" => "required;email", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';

$html .= '<div class="col-sm-6 no-padding-left">';
$html .= $form->addInput("text", "usu_var_identificador", "Identificador", array("value" => $usu_var_identificador, "class" => "form-control __lower", "size" => "20", "maxlength" => "25", "autocomplete" => "off", "onKeypress" => "return validarUsuario(event)", "validate" => "required;usuario", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
if ($acao == 'upd')
    $html .= $form->addInput("password", "usu_var_senha", "Senha", array("class" => "form-control", "size" => "17", "maxlength" => "20", "autocomplete" => "off", "validate" => "conferencia", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"));
else
    $html .= $form->addInput("password", "usu_var_senha", "Senha", array("class" => "form-control", "size" => "17", "maxlength" => "20", "autocomplete" => "off", "validate" => "required;conferencia", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
if ($acao == 'upd')
    $html .= $form->addInput("password", "usu_var_senha_conf", "Senha Confirmação", array("class" => "form-control", "size" => "17", "maxlength" => "20", "autocomplete" => "off", "validate" => "senha", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"));
else
    $html .= $form->addInput("password", "usu_var_senha_conf", "Senha Confirmação", array("class" => "form-control", "size" => "17", "maxlength" => "20", "autocomplete" => "off", "validate" => "required;senha", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"), array("class" => "required"));
$html .= '<div class="space space-8"></div>';
$html .= '<div class="col-md-4 no-padding-left">';
$html .= $form->addSwitch("usu_cha_status", "Status", (($usu_cha_status == 'A') ? array("checked" => "checked") : false));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '<div class="col-md-4 no-padding">';
$html .= $form->addSwitch("usu_cha_validado", "Validado", (($usu_cha_validado == 'S') ? array("checked" => "checked") : false), false, 'sim-nao');
$html .= '<div class="space space-8"></div>';
$html .= '</div>';
$html .= '</div>';
$html .= '<div class="col-sm-12 no-padding" id="div_motivo" style="' . $div_motivo . '">';
$html .= $form->addInput("text", "usu_var_motivo", "Motivo do bloqueio", array("value" => $usu_var_motivo, "class" => "form-control", "size" => "50", "maxlength" => "250"));
$html .= '<div class="space space-8"></div>';
$html .= '</div>';

$html .= '</div>';

$html .= '<div style="width: 200px;float:left;">';
$html .= '<div class="previewFoto"><img id="foto" class="img-responsive" src="' . $usu_var_foto . '"/></div>';
$html .= '<div class="text-center mt-2">';
$html .= '<label for="usu_var_foto" class="btn btn-warning"><i class="fa fa-photo"></i> Escolher Foto</label>';
$html .= '<input type="file" name="usu_var_foto" id="usu_var_foto" style="display: none;" />';
$html .= '</div>';
$html .= '</div>';

$html .= '</fieldset>';
if ($acao == 'ins')
    $html .= carregarBotoes("N");
else
    $html .= carregarBotoes("I");
$html .= '</form>';
$html .= gerarRodape(array('tipo' => 'box', 'col' => 12));
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

        var upload = document.getElementById("usu_var_foto");
        upload.addEventListener("change", function (e) {
            var size = upload.files[0].size;
            if (size > <?php echo MAX_SIZE; ?>) {
                jQuery("#usu_var_foto").val('');
                upload.value = "";
                jQuery('#foto').attr('src', jQuery("#atual").val());
                jQuery.gDisplay.showError("Infelizmente não é possivel enviar a foto que escolheu, pois a mesma é muito grande.<br/>Favor escolher uma foto com tamanho de no máximo <b><?php echo formatarBytes(MAX_SIZE); ?></b>.", "");
            }
            e.preventDefault();
        });

        jQuery("#usu_var_foto").change(function () {
            readURL(this);
        });

        jQuery("#btn_insert").click(function () {
            jQuery("#usu_cha_status").val(jQuery("#usu_cha_status_switch").is(":checked") ? 'A' : 'I');
            jQuery("#usu_cha_validado").val(jQuery("#usu_cha_validado_switch").is(":checked") ? 'S' : 'N');
            if (jQuery("#form").gValidate()) {
                var data = new FormData();
                var form_data = jQuery('#form').serializeArray();
                jQuery.each(form_data, function (key, input) {
                    data.append(input.name, input.value);
                });
                data.append('usu_var_foto', jQuery('input[name="usu_var_foto"]')[0].files[0]);
                jQuery.gAjax.execData('<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php', data, "calbackCancelar();", "");
            }
        });
        jQuery("#btn_insert_novo").click(function () {
            jQuery("#usu_cha_status").val(jQuery("#usu_cha_status_switch").is(":checked") ? 'A' : 'I');
            jQuery("#usu_cha_validado").val(jQuery("#usu_cha_validado_switch").is(":checked") ? 'S' : 'N');
            if (jQuery("#form").gValidate()) {
                var data = new FormData();
                var form_data = jQuery('#form').serializeArray();
                jQuery.each(form_data, function (key, input) {
                    data.append(input.name, input.value);
                });
                data.append('usu_var_foto', jQuery('input[name="usu_var_foto"]')[0].files[0]);
                jQuery.gAjax.execData("<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php", data, "jQuery.gDisplay.loadStart('HTML');window.location.reload();", "");
            }
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "calbackCancelar();", "");
        });

        jQuery("#usu_var_email").blur(function () {
            var email = jQuery("#usu_var_email").val();
            if ((email.length > 5) && (verifyEmail(email))) {
                jQuery("#usu_var_identificador").focus();
                jQuery.gAjax.exec('<?php echo URL_SYS; ?>inc/exe/usuario.php', {acao: 'verificaExisteEmail', usu_int_codigo: jQuery("#usu_int_codigo").val(), usu_var_email: jQuery("#usu_var_email").val()}, "", 'jQuery("#usu_var_email").focus();', false, true, '#usu_var_email');
            } else {
                if (email.length > 0) {
                    jQuery.gDisplay.showError("Esse e-mail está inválido, favor o preencher corretamente.", 'jQuery("#usu_var_email").focus();');
                }
            }
        });

        jQuery("#usu_var_identificador").blur(function () {
            var erro = 'jQuery("#usu_var_identificador").focus();';
            jQuery.gAjax.exec('<?php echo URL_SYS . 'cadastros/usuario/'; ?>exec.php', {acao: 'validarIdentificador', usu_int_codigo: jQuery("#usu_int_codigo").val(), usu_var_identificador: jQuery("#usu_var_identificador").val()}, "", erro, false, true, '#usu_var_identificador');
        });

        jQuery("#usu_cha_status_switch").change(function () {
            if (jQuery(this).is(":checked")) {
                jQuery("#div_motivo").hide();
            } else {
                jQuery("#div_motivo").show();
            }
        });
    });

    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'cadastros/usuario/'; ?>" + window.location.search;
    }

    function verifyEmail(email) {
        var er = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
        if (er.test(email)) {
            return true;
        } else {
            return false;
        }
    }

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                jQuery('#foto').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>