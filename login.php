<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");

$header = new GHeader("Login");
$header->addCSS(URL_SYS_TEMA . 'css/login' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/login' . getminify() . '.css'));
$header->addCSS(URL_SYS_TEMA . 'css/cores' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/cores' . getminify() . '.css'));
$theme = new Theme();
$header->addTheme($theme->addLib(array("mask")));
$header->show(isFrame(true));
/* -------------------------------------------------------------------------- */
global $__p2, $__p3, $__param;

$erro = '';
if ($__p2 == 'error') {
    $erro = ($__p3 == 'session') ? 'Sua sessão expirou!</br>Para continuar faça login novamente.' : '';
}

$url = isset($__param[1]) ? $__param[1] : URL_SYS . 'home/';

$usuario = getUsuarioSessao();
if ($usuario)
    echo '<script>window.location = "' . $url . '";</script>';

$reCaptcha = '';
if (buscarParametro("RECAPTCHA", "F") == "V") {
    if (isset($_SESSION["tentativas"]) && $_SESSION["tentativas"] > 2) {
        $reCaptcha = '<div class="col-lg-12" style="margin-bottom: 10px;"><label class="block clearfix"><div class="g-recaptcha" data-sitekey="' . CAPTCHA_SITE . '"></div></label></div>';
    }
}

$form = new GForm();
?>
<div class="fundoImagem"></div>
<div class="filtroCor">
    <div class="container">
        <div class="row" style="max-width: 387px; margin: 50px auto;float: none;">
            <div class="col-lg-12 caixaLogoBranca">
                <a href="<?php echo URL_SYS; ?>" alt="vai para o <?php echo SYS_NOME; ?>" title="vai para o <?php echo SYS_NOME; ?>"><img style="margin:auto" class="animated img-responsive" src="<?php echo URL_SYS_LOGO; ?>" /></a>
            </div>         
        </div>
        <?php if ($erro != '') { ?>
            <div class="row" style="max-width: 387px;margin: auto;float: none;">
                <div class="col-lg-12 alert alert-danger" style="margin-bottom: 30px;text-align: center;">
                    <?php echo $erro; ?>
                </div>
            </div>
        <?php } ?>
        <div class="row" style="max-width: 387px;margin: auto;float: none;">            
            <div id="login-box" class="login-box visible col-lg-12 animated bounceIn">
                <div class="caixa">
                    <?php echo $form->open("form_login", "post", "_self", false, false, "UTF-8", "", ' autocomplete="off"'); ?>
                    <div class="row">
                        <div class="col-lg-12"><div class="titulo"><i class="ace-icon fa fa-key icon-animated-vertical"></i> Identifique-se</div></div>
                        <?php echo $form->addInput("hidden", "acao", false, array("value" => "autenticar")); ?>

                        <div class="col-lg-12">
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <?php echo $form->addInput("text", "log_var_usuario", "Usuário", array("class" => "form-control", "size" => "30", "maxlength" => "50", "validate" => "required", "placeholder" => "Usuário", "readonly" => "readonly", "onfocus" => "if (this.hasAttribute('readonly')) { this.removeAttribute('readonly'); this.blur(); this.focus(); }", "style" => "background: #ffffff!important;"), array("style" => "display: none;"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-user"></i></span>'), true); ?>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-12" style="margin-bottom: 10px;">
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <?php echo $form->addInput("password", "log_var_senha", "Senha", array("class" => "form-control", "size" => "30", "maxlength" => "25", "autocomplete" => "off", "placeholder" => "Senha", "validate" => "required;senha"), array("style" => "display: none;"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-lock"></i></span>'), true); ?>
                                </span>
                            </label>
                        </div>
                        <?php echo $reCaptcha; ?>
                        <div class="col-lg-12">
                            <button type="button" id="btn_login" class="btn btn-block btn-primary">Entrar</button>
                        </div>
                    </div>
                    <?php echo $form->close(); ?>
                </div><!-- /.caixa -->
                <div class="barra">
                    <a href="<?php echo URL_SYS . 'forgot/'; ?>" id="btnEsqueciSenha" class="forgot-password-link">Esqueci minha senha</a>
                </div>
            </div><!-- /.login-box -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.filtroCor -->
<div class="bordaTopo"></div>
<?php
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame(true));
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#log_var_usuario").focus();
//        jQuery("#log_var_usuario").mask("999.999.999-99");

        jQuery("#btn_login").click(function () {
            if (jQuery("#form_login").gValidate()) {
<?php if ($reCaptcha != '') { ?>
                    var response = grecaptcha.getResponse();
                    if (response.length == 0) {
                        jQuery.gDisplay.showError("Se você não é um robô, favor sinalizar essa opção.", "");
                    } else {
<?php } ?>
                    jQuery.ajax({
                        type: "POST",
                        url: "<?php echo URL_SYS; ?>inc/exe/usuario.php",
                        data: jQuery("#form_login").serializeArray(),
                        dataType: 'json',
                        async: true,
                        beforeSend: function () {
                            jQuery.gDisplay.loadStart('HTML');
                        },
                        error: function (jqXHR) {
                            jQuery.gDisplay.loadError('HTML', "Erro ao carregar a página...");
                            jQuery.gDisplay.showError('OPS!<br>Ocorreu um problema mas já estamos resolvendo.<div style="display:none">' + jqXHR.responseText + '</div>');
                        },
                        success: function (json) {
                            jQuery.gDisplay.loadStop('HTML');
                            if (json.status) {
                                jQuery.gDisplay.loadStart('HTML');
                                jQuery('#form_login').submit();
                            } else {
                                if (json.tentativas > 2) {
                                    jQuery.gDisplay.showError(json.msg, "window.location.reload();");
                                } else {
                                    jQuery.gDisplay.showError(json.msg, "");
                                }
                            }
                        }
                    });
<?php if ($reCaptcha != '') { ?>
                    }
<?php } ?>
            }
        });
        pressEnter("#log_var_senha", "jQuery('#btn_login').click();");
    });
</script>