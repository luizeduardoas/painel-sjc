<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");

$header = new GHeader("Esqueceu sua senha?");
$header->addCSS(URL_SYS_TEMA . 'css/login' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/login' . getminify() . '.css'));
$header->addCSS(URL_SYS_TEMA . 'css/cores' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/cores' . getminify() . '.css'));
$header->show(isFrame(true));
/* -------------------------------------------------------------------------- */
global $__p1, $__p2, $__p3, $__param, $__pg;

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
        <div class="row" style="max-width: 387px;margin: auto;float: none;">            
            <div id="forgot-box" class="forgot-box col-lg-12">
                <div class="caixa">
                    <?php echo $form->open("form_recupera"); ?>
                    <div class="row">
                        <div class="col-lg-12"><div class="titulo"><i class="ace-icon fa fa-key icon-animated-vertical"></i> Esqueci minha senha</div></div>
                        <?php echo $form->addInput("hidden", "acao", false, array("value" => "esqueciSenha")); ?>
                        <div class="col-lg-12" style="margin-bottom: 10px;">
                            <label>Informe seu e-mail para recuperar a senha</label>
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <?php echo $form->addInput("text", "log_var_email_recuperacao", "E-mail", array("size" => "25", "maxlength" => "100", "class" => "form-control", "placeholder" => "E-mail", "validate" => "required;email"), array("style" => "display: none;"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-envelope"></i></span>'), true); ?>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-12" style="margin-bottom: 10px;">
                            <button type="button" id="esqueci_senha" class="btn btn-block btn-primary"> Enviar</button>
                        </div>
                    </div>
                    <?php echo $form->close(); ?>
                </div><!-- /.caixa -->
                <div class="barra">
                    <a href="<?php echo URL_SYS . 'login/'; ?>" id="btnEsqueciParaLogin" class="back-to-login-link">Ir para login</a>
                </div>
            </div><!-- /.forgot-box -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.filtroCor -->
<div class="bordaTopo"></div>
<?php
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame(true));
?>
<!--<style>
    body {
        min-height: 100vh;
        /*overflow: hidden;*/
        width: 100%;
    }
</style>-->
<script>
    jQuery(document).ready(function () {
        jQuery("#log_var_email_recuperacao").focus();

        jQuery("#esqueci_senha").click(function () {
            if (jQuery("#form_recupera").gValidate())
                jQuery.gAjax.exec("<?php echo URL_SYS; ?>inc/exe/usuario.php", jQuery("#form_recupera").serializeArray(), "jQuery.gDisplay.loadStart('HTML'); window.location.reload(true);", "");
        });
        pressEnter("#log_var_email_recuperacao", "jQuery('#esqueci_senha').click();");
    });
</script>