<?php
global $genesis;
if (is_null($genesis))
    require_once("inc/global.php");

$header = new GHeader("Ativar Conta", false);
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
$script = '';
$url = '';
$count = '10';
if (isset($__param[1])) {
    $url = URL_SYS . 'admin/?url=' . $__param[1];
} else {
    $url = URL_SYS . 'admin/';
}

if (!seNuloOuVazio($__p2)) {
    $usuario = new Usuario();
    $usuario->setUsu_var_token($__p2);
    $usuarioDao = new UsuarioDao();
    $return = $usuarioDao->validateToken($usuario);
} else {
    salvarEvento("E", 'Validação de Token do cadastro do usuário.', 'Token vazio');
    $return["status"] = false;
}
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
        <div class="row">
            <?php if ($return["status"]) { ?>
                <div class="col-lg-12 animated fadeInUp text-center texto-grande">
                    <div class="linha sucess"><strong>Parabéns</strong></div>
                    <div class="linha"><strong>Seu cadastro foi validado com sucesso.</strong></div>
                    <div class="linha">Em <span class="contadorRegressivo" id="contador">5</span> segundos, você será direcionado para página inicial.</div>
                    <?php
                    $script = 'if (!isIframe()) { contadorRegressivo(); } else { jQuery(".linhaMsg").hide(); } ';
                    if (isset($__param[1])) {
                        $url = URL_SYS . 'admin/?url=' . $__param[1];
                    } else {
                        $url = URL_SYS . 'admin/';
                    }
                    ?>
                    <div class="linha">
                        <div class="botaoVoltar" onClick="location.href = '<?php echo $url; ?>'">Ir para página inicial</div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="col-lg-12 animated fadeInUp text-center texto-grande">
                    <div class="linha error"><strong>Endereço para validação de cadastro inválido.</strong></div>
                    <div class="linha error">Tente novamente, em caso de persistência do problema,</div>
                    <div class="linha">favor entrar em <a href="mailto:<?php echo CONTATO; ?>" class="no-hover"><i class="fa fa-envelope-o"></i> contato</a>.</div>
                    <div class="linha">
                        <div class="botaoVoltar" onClick="location.href = '<?php echo URL_SYS; ?>'">Voltar para a página inicial</div>
                    </div>
                </div>
            <?php } ?>
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
    var tempo = $("#contador").text("5");
    tempo = new Number();
    tempo = <?php echo $count; ?>;
    jQuery(document).ready(function () {
<?php echo $script; ?>
    });
    function contadorRegressivo() {
        $("#contador").html(tempo);
        if (tempo > 0) {
            tempo--;
            setTimeout('contadorRegressivo()', 1000);
        } else {
            jQuery.gDisplay.loadStart('HTML');
            window.location = "<?php echo $url; ?>";
        }
    }
</script>