<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("MEUSDADOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Meus Dados", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Minha Conta >> Meus Dados", true);
$header->addMenu("MEUSDADOS", "Meus Dados", "Atualize seus dados");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$usuario = getUsuarioSessao();

$form = new GForm();
$html = '';
// <editor-fold desc="PAGE CONTENT">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Meus Dados',
    'id' => 'formulario',
    'col' => 8,
    'fa' => 'user'
        ));
$html .= '<form id="form" name="form" action="" class="formConta" enctype="multipart/form-data" lang="pt" method="post" autocomplete="off">';
$html .= $form->addInput('hidden', 'acao', false, array('value' => 'meusDados'));
$html .= $form->addInput('hidden', 'atual', false, array('value' => $usuario->getUsu_var_foto(false)));
$html .= $form->addInput('hidden', 'usu_var_identificador', false, array('value' => $usuario->getUsu_var_identificador()));
$html .= '<fieldset>';
$html .= '<div class="col-md-6 col-sm-8 col-xs-12 no-padding-left">';
$html .= $form->addInput("text", "usu_var_nome", "Nome", array("value" => $usuario->getUsu_var_nome(), "class" => "form-control", "size" => "25", "maxlength" => "50", "validate" => "required"), array("class" => "required"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-user"></i></span>'), true);
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "usu_var_email", "E-mail", array("value" => $usuario->getUsu_var_email(), "class" => "form-control __lower", "size" => "35", "maxlength" => "100", "validate" => "required;email"), array("class" => "required"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-envelope"></i></span>'), true);
$html .= '<div class="space space-8"></div>';
$html .= $form->addInput("text", "usu_var_identificador", "Identificador", array("value" => $usuario->getUsu_var_identificador(), "class" => "form-control __lower", "size" => "20", "disabled" => "disabled", "maxlength" => "25", "autocomplete" => "off", "placeholder" => "Identificador"), array("class" => "required"), array("A" => '<span class="input-group-addon"><i class="ace-icon fa fa-lock"></i></span>'), true);
$html .= '</div>';
$html .= '<div class="col-md-6 col-sm-4 col-xs-12 no-padding-right">';
//$paramCroppic = array("uploadUrl" => URL_SYS . 'cadastros/usuario/upload.php', "cropUrl" => URL_SYS . 'cadastros/usuario/crop.php');
//$html .= $form->addCroppic(array("id" => 'usu_var_foto', "titulo" => 'Foto', "imagem" => $usuario->getUsu_var_foto(), "width" => '200px', "height" => '200px', "paramCroppic" => $paramCroppic));
$html .= '<div class="previewFoto"><img id="foto" class="img-responsive" src="' . $usuario->getUsu_var_foto() . '"/></div>';
$html .= '<div class="text-center"><label for="usu_var_foto" class="btn btn-warning"><i class="fa fa-photo"></i> Escolher Foto</label>';
$html .= '<input type="file" name="usu_var_foto" id="usu_var_foto" style="display: none;" />';
$html .= '</div>';

$html .= '</div>';
$html .= '</fieldset>';
$html .= carregarBotoes("I");
$html .= '</form>';
$html .= gerarRodape(array('tipo' => 'box', 'col' => 8));
// </editor-fold>

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script>
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
            if (jQuery("#form").gValidate()) {
                var data = new FormData();
                var form_data = jQuery('#form').serializeArray();
                jQuery.each(form_data, function (key, input) {
                    data.append(input.name, input.value);
                });
                data.append('usu_var_foto', jQuery('input[name="usu_var_foto"]')[0].files[0]);
                jQuery.gAjax.execData('<?php echo URL_SYS . 'minhaconta/meusdados/'; ?>exec.php', data, "jQuery.gDisplay.loadStart('HTML');window.location.reload();", "");
            }
        });

//        jQuery("#btn_insert").click(function () {
//            if (jQuery("#form").gValidate())
//                jQuery.gAjax.exec('<?php //echo URL_SYS . 'minhaconta/meusdados/';            ?>exec.php', jQuery("#form").serializeArray(), "jQuery.gDisplay.loadStart('HTML');window.location = '<?php //echo $url;            ?>';", "");
//        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "jQuery.gDisplay.loadStart('HTML');window.location = '<?php echo URL_SYS; ?>home/';", "");
        });

        jQuery("#usu_var_identificador").blur(function () {
            jQuery.gAjax.exec('<?php echo URL_SYS . 'minhaconta/meusdados/'; ?>exec.php', {acao: 'validarIdentificador', usu_var_identificador: jQuery("#usu_var_identificador").val()}, "", 'jQuery("#usu_var_identificador").focus();', false, true, '#usu_var_identificador');
        });

    });

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