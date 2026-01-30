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
$header->addMenu("ESCOLA", "Listagem de Escolas", "Visualize as Escolas do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$filtro = '';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Escolas',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array());
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'gerenciamento/escola/load.php', false, $colunas, false, 25, false, true, true);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery("#btn_atualizar").click(function () {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
    });

    function salvarFiltros() {
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'gerenciamento/escola/'; ?>view/" + codigo + "/";
    }
</script>
