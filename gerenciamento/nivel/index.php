<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("NIVEL");
GF::import(array("nivel"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Estrutura Organizacional", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Estrutura Organizacional", true);
$header->addMenu("NIVEL", "Listagem de Estrutura Organizacional", "Visualize a Estrutura Organizacional do sistema");
$header->addTheme(Theme::addLib(array("tree")));
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();

$lista = montarArvore();

$html = '';
$html .= gerarCabecalho(array(
    'tipo' => 'box',
    'titulo' => 'Estrutura Organizacional',
    'id' => 'visualizacao',
    'col' => 4,
    'fa' => 'sitemap'
        ));

$html .= '<div class="col-xs-12">';
$html .= '    <div class="row text-center" style="margin: 10px;">';
$html .= '        <div class="col-xs-12">';
$html .= '            <button type="button" class="btn btn-sm btn-info" id="btn-expandir-tudo">';
$html .= '                <i class="ace-icon fa fa-expand bigger-110"></i> Expandir Tudo';
$html .= '            </button>';
$html .= '            <button type="button" class="btn btn-sm btn-danger" id="btn-recolher-tudo">';
$html .= '                <i class="ace-icon fa fa-compress bigger-110"></i> Recolher Tudo';
$html .= '            </button>';
$html .= '        </div>';
$html .= '    </div>';
$html .= '    <ul id="tree1"></ul>';
$html .= '    <div class="__clear"></div>';
$html .= '</div>';

$html .= gerarRodape(array('tipo' => 'box', 'col' => 4));
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<style>
    li.tree-branch-disabled,
    li.tree-item.tree-branch-disabled {
        background-color: #f9f9f9 !important;
    }
    .tree-branch-disabled .tree-label {
        text-decoration: line-through;
        color: #999 !important;
    }
</style>
<script type="text/javascript">
    jQuery(document).ready(function () {
        var $minhaArvore = jQuery('#tree1');
        $minhaArvore.ace_tree({
            dataSource: load(),
            multiSelect: true,
            cacheItems: true,
            'open-icon': 'ace-icon tree-minus',
            'close-icon': 'ace-icon tree-plus',
            'itemSelect': false,
            'folderSelect': false,
            'selected-icon': 'ace-icon fa fa-arrow-right blue',
            'unselected-icon': 'ace-icon fa fa-arrow-right grey',
            loadingHTML: '<div class="tree-loading"><i class="ace-icon fa fa-refresh fa-spin blue"></i></div>'
        });
        // Função para Expandir Tudo
        jQuery('#btn-expandir-tudo').on('click', function () {
            $minhaArvore.tree('discloseAll');
        });
        // Função para Recolher Tudo
        jQuery('#btn-recolher-tudo').on('click', function () {
            $minhaArvore.find('.tree-minus').each(function () {
                jQuery(this).closest('.tree-branch-header').click();
            });
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
                    // --- O SEGREDO ESTÁ AQUI ---
                    // Após o callback, o Ace Tree renderiza os itens. 
                    // Vamos varrer o que foi criado e aplicar a classe se ela existir no dado original.
                    $.each($data, function (index, item) {
                        if (item.additionalParameters && item.additionalParameters.class) {
                            // Procuramos o elemento que acabamos de inserir pelo texto (é o jeito mais seguro aqui)
                            var $elementoCriado = $('#tree1').find('.tree-label').filter(function () {
                                return $(this).text() === item.text;
                            }).closest('li');

                            $elementoCriado.addClass(item.additionalParameters.class);
                        }
                    });
                    // ---------------------------
                }, parseInt(Math.random() * 500) + 200);
        }
        return dataSource1;
    }
</script>
