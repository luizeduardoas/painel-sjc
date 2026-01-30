<?php
global $genesis;
if (is_null($genesis))
    require_once("../inc/global.php");

$header = new GHeader("OPS", false);
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

global $__p2, $__param, $__ops;
$script = '';
$url = '';
$count = '5';

$__p2 = (is_null($__p2) ? $__ops : $__p2);
$__p2 = (is_null($__p2) ? "" : $__p2);
?>
<div class="fundoImagem"></div>
<div class="filtroCor">
    <div class="container">
        <div class="row" style="max-width: 387px; margin: 50px auto;float: none;">
            <div class="col-xs-12 text-center">
                <a href="http://<?php echo URL_ENDERECO; ?>"><img alt="<?php echo SYS_NOME; ?>" title="<?php echo SYS_NOME; ?>" class="logosTopo animated fadeInDown" src="<?php echo URL_SYS_LOGO; ?>" /></a>
            </div>            
        </div>
        <div class="row">
            <div class="col-lg-12 animated fadeInUp text-center texto-grande">
                <?php
                switch (substr($__p2, 0, 3)) {
                    case '999':
                        echo '<div class="linha error"><strong>Não foi possível autenticar essa sessão.</strong></div>';
                        echo '<div class="linha">Tente novamente, em caso de persistência do problema,</div>';
                        echo '<div class="linha">favor entrar em <a href="mailto:' . CONTATO . '" target="_blanc" class="no-hover"><i class="fa fa-envelope-o"></i> contato</a>.</div>';
                        break;
                    case '403':
                        echo '<div class="linha error"><strong>Você não tem permissão para essa funcionalidade.</strong></div>';
                        echo '<div class="linha">Em caso de necessidade desse acesso,</div>';
                        echo '<div class="linha">favor entrar em <a href="mailto:' . CONTATO . '" target="_blanc" class="no-hover"><i class="fa fa-envelope-o"></i> contato</a>.</div>';
                        break;
                    case '500':
                        echo '<div class="linha error"><strong>Sistema indisponível no momento.</strong></div>';
                        echo '<div class="linha">Tente novamente, em caso de persistência do problema,</div>';
                        echo '<div class="linha">favor entrar em <a href="mailto:' . CONTATO . '" class="no-hover"><i class="fa fa-envelope-o"></i> contato</a>.</div>';
                        break;
                    case '001':
                        echo '<div class="linha error"><strong>Sua sessão expirou.</strong></div>';
                        echo '<div class="linha linhaMsg">Em <span class="contadorRegressivo" id="contador">5</span> segundos, você será direcionado para página de autenticação.</div>';
                        $script = 'if (!isIframe()) { contadorRegressivo(); } else { jQuery(".linhaMsg").hide(); } ';
                        if (isset($__param[1])) {
                            $url = URL_SYS . 'admin/?url=' . $__param[1];
                        } else {
                            $url = URL_SYS . 'admin/';
                        }
                        echo '<div class="linha linhaMsg">';
                        echo '<div class="botaoVoltar" onClick="location.href = \'' . $url . '\'">Ir para página de autenticação</div>';
                        echo '</div>';
                        break;
                    default:
                        echo '<div class="linha error"><strong>Essa página não foi encontrada.</strong></div>';
                        echo '<div class="linha">Talvez ela tinha sido desativada ou o seu caminho esteja incorreto.</div>';
                        echo '<div class="linha linhaMsg">';
                        echo '<div class="botaoVoltar" onClick="location.href=\'' . URL_SYS . '\'"><i class="fa fa-undo"></i> Voltar ao Início</div>';
                        echo '</div>';
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<div class="bordaTopo"></div>
<?php
/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(true);
?>
<style>
    body {
        min-height: 100vh;
        /*        overflow: hidden;*/
        width: 100%;
    }
</style>
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