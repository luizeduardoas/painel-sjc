<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("NIVEL");
GF::import(array("nivel"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Estrutura Hierárquica", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Estrutura Hierárquica", true);
$header->addMenu("NIVEL", "Listagem de Estrutura Hierárquica", "Visualize a Estrutura Hierárquica do sistema");
$header->addTheme(Theme::addLib(array("tree")));
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();

$lista = montarArvore();

$html = '';
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Estrutura Hierárquica',
    'id' => 'visualizacao',
    'col' => 4,
    'fa' => 'sitemap'
        ));
$html .= '<div class="col-xs-12"><ul id="tree1"></ul><div class="__clear"></div></div>';
$html .= gerarRodape(array('tipo' => 'box', 'col' => 4));
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#tree1').ace_tree({
            dataSource: load(),
            multiSelect: true,
            cacheItems: true,
            'open-icon': 'ace-icon tree-minus',
            'close-icon': 'ace-icon tree-plus',
            'itemSelect': false,
            'folderSelect': false,
            'selected-icon': 'ace-icon fa fa-check',
            'unselected-icon': 'ace-icon fa fa-times',
            loadingHTML: '<div class="tree-loading"><i class="ace-icon fa fa-refresh fa-spin blue"></i></div>'
        });
    });

    function load() {
        var dataSource1 = function (options, callback) {
            var $data = null
            if (!("text" in options) && !("type" in options)) {
                $data = <?php echo json_encode($lista); ?>;//the root tree
                callback({data: $data});
                return;
            } else if ("type" in options && options.type == "folder") {
                if ("additionalParameters" in options && "children" in options.additionalParameters)
                    $data = options.additionalParameters.children || {};
                else
                    $data = {}
            }
            if ($data != null)
                setTimeout(function () {
                    callback({data: $data});
                }, parseInt(Math.random() * 500) + 200);
        }
        return dataSource1;
    }
</script>
