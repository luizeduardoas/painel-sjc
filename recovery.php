<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");

$header = new GHeader("Criar Nova Senha", false);
$header->addMetas('<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=0.5, maximum-scale=4">');
$header->addScriptInicio(URL_SYS_TEMA_GLOBAL . 'plugins/jquery351/jquery-3.5.1.min.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/jquery351/jquery-3.5.1.min.js'));
$header->addScript(URL_SYS_TEMA_GLOBAL . 'js/functions.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'js/functions.js'));
$header->addScript(URL_SYS_TEMA . 'js/jquery.maskedinput.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.maskedinput.min.js'));
$header->addScript(URL_SYS_TEMA_GLOBAL . 'plugins/maskedinput/jquery.mask.js?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'plugins/maskedinput/jquery.mask.js'));
$header->addCSS('https://fonts.googleapis.com/css?family=Roboto:400,500');
$header->addCSS(URL_SYS_TEMA_GLOBAL . 'css/style.min.css?' . filemtime(ROOT_SYS_TEMA_GLOBAL . 'css/style.min.css'));
$header->addCSS(URL_SYS_TEMA . 'css/login.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/login.min.css'));
$header->addCSS(URL_SYS_TEMA . 'css/cores.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/cores.min.css'));
$header->addCSS(URL_SYS_TEMA . 'css/bootstrap.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap.min.css'));
$header->addCSS(URL_SYS_TEMA . 'font-awesome/4.7.0/css/font-awesome.min.css?' . filemtime(ROOT_SYS_TEMA . 'font-awesome/4.7.0/css/font-awesome.min.css'));
$header->addLib(array("animate", "gDisplay", "gAjax", "gValidate"));
$header->show(true, null);

global $__p2, $__param;

$url = isset($__param[1]) ? $__param[1] : URL_SYS;

$usuario = getUsuarioSessao();
if ($usuario)
    echo '<script>window.location = "' . $url . '";</script>';

$form = new GForm();
?>
<div class="fundoImagem"></div>
<div class="filtroCor">
    <div class="container" style="max-width: 465px;">
        <div class="row">
            <div class="col-lg-12" style="margin-bottom: 30px;text-align: center;">
                <div class="col-lg-12">
                    <a href="<?php echo URL_SYS; ?>" alt="vai para o <?php echo SYS_NOME; ?>" title="vai para o <?php echo SYS_NOME; ?>"><img style="margin:auto" class="animated fadeInDown img-responsive" src="<?php echo URL_SYS_LOGO; ?>" /></a>
                </div>
            </div>
        </div>
        <div class="row" style="max-width: 495px;margin: auto;float: none;">
            <div class="login-box col-lg-12 animated fadeInUp">
                <div class="caixa">
                    <?php echo $form->open("form_alterarSenha", "post", "_self", false, false, "UTF-8"); ?>
                    <div class="row">
                        <div class="col-lg-12"><div class="titulo"><i class="ace-icon fa fa-key icon-animated-vertical"></i> Criar Nova Senha</div></div>
                        <?php
                        echo $form->addInput("hidden", "acao", false, array("value" => "tokSenha"));
                        echo $form->addInput("hidden", "token", false, array("value" => $__p2, "validate" => "required"));
                        ?>
                        <div class="col-lg-12">
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <?php echo $form->addInput("password", "usu_var_senha_new", "Senha", array("class" => "form-control", "size" => "30", "maxlength" => "25", "validate" => "required;conferencia", "placeholder" => "Senha"), array("style" => "display: none;"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-lock"></i></span>'), true); ?>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-12" style="margin-bottom: 10px;">
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <?php echo $form->addInput("password", "usu_var_senha_new_conf", "Repita a senha", array("class" => "form-control", "size" => "30", "maxlength" => "25", "autocomplete" => "off", "placeholder" => "Repita a senha", "validate" => "required;senha"), array("style" => "display: none;"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-retweet"></i></span>'), true); ?>
                                </span>
                            </label>
                        </div>
                        <div class="col-lg-12">
                            <button type="button" id="btn_senha" class="btn btn-block btn-primary">Criar Senha</button>
                        </div>
                    </div>
                    <?php echo $form->close(); ?>
                </div><!-- /.caixa -->
                <div class="barra">
                    <a href="<?php echo URL_SYS; ?>" class="forgot-password-link">Voltar ao Início</a>
                </div>
            </div><!-- /.login-box -->
        </div><!-- /.row -->
    </div><!-- /.container -->
</div><!-- /.filtroCor -->
<div class="bordaTopo"></div>
<?php
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(true);
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#usu_var_senha_new").val('');
        jQuery("#usu_var_senha_new_conf").val('');
        jQuery("#btn_senha").click(function () {
            if (jQuery('#token').val() !== '') {
                if (jQuery("#form_alterarSenha").gValidate())
                    jQuery.gAjax.exec("<?= URL_SYS ?>inc/exe/usuario.php", jQuery("#form_alterarSenha").serializeArray(), "window.location = '<?= URL_SYS; ?>admin/';", "");
            } else
                jQuery.gDisplay.showError('Link para recuperação de senha inválido');
        });
        pressEnter("#usu_var_senha_new_conf", "jQuery('#btn_senha').click();");
    });
</script>