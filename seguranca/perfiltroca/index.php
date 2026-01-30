<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERFILTROCA");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Trocas de Perfis", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Segurança >> Trocas de Perfis", true);
$header->addMenu("PERFILTROCA", "Trocas de Perfis", "Realize trocas de perfis no sistema");
$header->addCSS(URL_SYS_TEMA . 'css/bootstrap-duallistbox.min.css?' . filemtime(ROOT_SYS_TEMA . 'css/bootstrap-duallistbox.min.css'));
$header->addScript(URL_SYS_TEMA . 'js/jquery.bootstrap-duallistbox.min.js?' . filemtime(ROOT_SYS_TEMA . 'js/jquery.bootstrap-duallistbox.min.js'));
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$mysql = new GDbMysql();
$form = new GForm();

$opt_pef_var_descricao = $mysql->executeCombo("SELECT pef_int_codigo, pef_var_descricao FROM perfil WHERE pef_int_codigo <> 0 ORDER BY pef_var_descricao;");

$html = '';
// <editor-fold desc="Formulário">
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Trocas de Perfis',
    'col' => 12,
    'id' => 'formulario',
    'fa' => 'key'
        ));
$html .= $form->openClass("form");
$html .= '<fieldset>';
$html .= '<div class="col-xs-12 no-padding">';
$html .= $form->addSelect("pef_int_codigo", $opt_pef_var_descricao, "-1", "Perfil", array("class" => "chosen-select form-control"));
$html .= '</div>';
$html .= '<div class="space space-8 col-xs-12"></div>';
$html .= '<select multiple="multiple" size="10" name="perfis[]" id="duallist"></select>';
$html .= '</fieldset>';
$html .= carregarBotoes("I");
$html .= $form->close();
$html .= gerarRodape(array('tipo' => 'box', 'col' => 12));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script>
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosen);
        var $list = jQuery('select[name="perfis[]"]').bootstrapDualListbox({
            infoTextFiltered: '<span class="label label-purple label-lg">Filtrado</span>',
            nonSelectedListLabel: 'Disponíveis',
            selectedListLabel: 'Atribuídas',
            preserveSelectionOnMove: 'moved',
            filterTextClear: 'Exibir Todos',
            filterPlaceHolder: 'Filtrar',
            moveAllLabel: 'Mover Todos',
            infoText: 'Exibir Todos: ',
            infoTextEmpty: 'Lista Vazia',
            moveOnSelect: false
        });
        jQuery("#pef_int_codigo").change(function () {
            if (jQuery(this).val() > 0) {
                var perfil = jQuery("#pef_int_codigo").val();
                jQuery.gAjax.load("<?php echo URL_SYS . 'seguranca/perfiltroca/'; ?>load.php", {pef_int_codigo: perfil}, "#duallist", undefined, false);
                $list.bootstrapDualListbox('refresh');
            }
        });
        jQuery("#btn_insert").click(function () {
            if (jQuery("#form").gValidate()) {
                var perfil = jQuery("#pef_int_codigo").val();
                var perfis = jQuery("#duallist").val();
                jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfiltroca/'; ?>exec.php", {pef_int_codigo: perfil, perfis: perfis}, "calbackCancelar();", "");
            }
        });
        jQuery("#btn_cancel").click(function () {
            jQuery.gDisplay.showYN("Deseja realmente cancelar?", "calbackCancelar();", "");
        });
    });
    function calbackCancelar() {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'seguranca/perfiltroca/'; ?>";
    }
</script>