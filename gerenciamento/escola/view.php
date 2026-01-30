<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

GSecurity::verificarPermissao("ESCOLA");
GF::import(array("escola"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Escolas", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Escolas", true);
$header->addMenu("ESCOLA", "Visualização de Escolas", "Visualize as informações dessa Escola do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();
$html = '';

global $_id;
$escola = new Escola();
$escola->setEsc_int_codigo($_id);
$escolaDao = new EscolaDao();
$escola = $escolaDao->selectById($escola);
if (is_null($escola->getEsc_var_ip())) {
    echo carregarPagina500();
} else {
    $html .= gerarCabecalho(array(
        'tipo' => 'box',
        'titulo' => 'Visualização de Escolas',
        'id' => 'visualizacao',
        'col' => 6,
        'fa' => 'eye'
    ));
    $html .= $form->open("form");
    $html .= $form->addInput("hidden", "esc_int_codigo", false, array("value" => $escola->getEsc_int_codigo()));
    $arr = array();
    $arr['Código'] = formataDadoVazio($escola->getEsc_int_codigo());
    $arr['Nome'] = formataDadoVazio($escola->getEsc_var_nome());
    $html .= gerarCamposVisualizacao($arr);

    $arrayBotoes = array();
    if (!isFrame()) {
        $arrayBotoes["btn_todos"] = "Ver Todos";
    }
    $arrayBotoes["btn_voltar"] = "Voltar";

    $html .= carregarBotoes($arrayBotoes);
    $html .= $form->close();
    $html .= gerarRodape(array('tipo' => 'box', 'col' => 6));

    echo $html;
}


/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_todos").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'gerenciamento/escola/'; ?>";
        });
        jQuery("#btn_voltar").click(function () {
            if (window.history.length > 1) {
                jQuery.gDisplay.loadStart('HTML');
                window.history.back();
            }
        });
        if (window.history.length < 2) {
            jQuery("#btn_voltar").attr("disabled", "disabled");
        }
        jQuery("#btn_close").click(function () {
            closeColorbox();
        });
    });
</script>
