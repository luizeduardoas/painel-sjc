<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("MINHASPENDENCIAS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Minha Conta >> Minhas Pendências", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Minha Conta >> Minhas Pendências", true);
$header->addMenu("MINHASPENDENCIAS", "Minhas Pendências", "Visualize todas as suas pendências");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();

$usuario = getUsuarioSessao();
$html = '';
// <editor-fold desc="Lista">
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Minhas Pendências',
    'id' => 'lista',
    'botaoNovo' => false,
    'export' => false
        ));
$html .= '<div id="loadPendencias" class="col-xs-12 caixaItens" style="min-height: 100px;">';
$html .= '</div>';
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        __atualizar();
    });
    function __atualizar() {
        jQuery.gAjax.load("<?php echo URL_SYS . 'minhaconta/minhaspendencias/'; ?>load.php", false, "#loadPendencias");
    }
</script>