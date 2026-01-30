<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");

$header = new GHeader("Escolher Empresa");
$header->addCSS(URL_SYS_TEMA . 'css/login' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/login' . getminify() . '.css'));
$header->addCSS(URL_SYS_TEMA . 'css/cores' . getminify() . '.css?' . filemtime(ROOT_SYS_TEMA . 'css/cores' . getminify() . '.css'));
$header->show(isFrame(true));
/* -------------------------------------------------------------------------- */
global $__param;

$url = isset($__param[1]) ? $__param[1] : URL_SYS . 'home/';

$usuario = getUsuarioSessao();
if (!is_null($usuario) && !is_null(getPerfilSessao())) {
    echo '<script>window.location = "' . $url . '";</script>';
}
$pef_int_codigo = getPerfilSessao();

$mysql = new GDbMysql();
$opt_pef_var_descricao = $mysql->executeCombo("SELECT pef_int_codigo, pef_var_descricao FROM perfil pef WHERE pef_cha_status = 'A' AND EXISTS (SELECT 1 FROM perfil_troca pt RIGHT OUTER JOIN usuario usu ON (pt.pef_int_codigo = usu.pef_int_codigo) WHERE (pt.pef_int_codigo_troca = pef.pef_int_codigo OR usu.pef_int_codigo = pef.pef_int_codigo) AND usu.usu_int_codigo = ?) ORDER BY pef_var_descricao;", array("i", $usuario->getUsu_int_codigo()));

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
            <div id="login-box" class="login-box visible col-lg-12 animated bounceIn">
                <div class="caixa">
                    <?php echo $form->open("form_escolha", "post", "_self", false, false, "UTF-8", "", ' autocomplete="off"'); ?>
                    <div class="row">
                        <div class="col-lg-12"><div class="titulo"><i class="ace-icon fa fa-user-secret icon-animated-vertical"></i> Escolha o Perfil</div></div>
                        <?php echo $form->addInput("hidden", "acao", false, array("value" => "escolha")); ?>

                        <div class="col-lg-12">
                            <label class="block clearfix">
                                <span class="block input-icon input-icon-right">
                                    <?php echo $form->addSelect("pef_int_codigo", $opt_pef_var_descricao, buscarCookie("filtro_perfil", $pef_int_codigo), "Perfil", array("class" => "form-control chosen-select", "validate" => "([~] != -1)|Obrigatório"), array("class" => "required"), false, false); ?>
                                </span>
                            </label>
                        </div>

                        <div class="col-lg-12">
                            <button type="button" id="btn_escolha" class="btn btn-block btn-primary">Escolher</button>
                        </div>
                    </div>
                    <?php echo $form->close(); ?>
                </div><!-- /.caixa -->
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
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery(":input:visible:enabled:not([readonly='readonly']):not('.nav-search-input'):first").focus();

        jQuery("#btn_escolha").click(function () {
            if (jQuery("#form_escolha").gValidate()) {
                setParametroCookie('filtro_perfil', jQuery('#pef_int_codigo').val());
                jQuery.gAjax.exec("<?php echo URL_SYS; ?>inc/exe/usuario.php", jQuery("#form_escolha").serializeArray(), "jQuery.gDisplay.loadStart('HTML'); jQuery('#form_escolha').submit();", "", false);
            }
        });
    });
</script>