<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("TAG");
GF::import(array("tag"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Tags", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Configurações >> Tags", true);
$header->addMenu("TAG", "Listagem de Tags", "Visualize, insira, altere e exclua as Tags do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */


$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Tags',
    'id' => 'lista',
    'botaoNovo' => true,
    'botaoNovoPermissao' => 'TAG_INS',
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array("TAG_UPD", "TAG_DEL"));
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Título", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Url", "largura" => false, "ordem" => true, "visivel" => false, "classe" => "left"),
    array("titulo" => "Valores", "largura" => false, "ordem" => true, "visivel" => false, "classe" => "left"),
    array("titulo" => "Informações", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Permissão", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'configuracoes/tag/load.php', false, $colunas, false, 25, false, true, false);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_novo").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'configuracoes/tag/'; ?>form/";
        });
        jQuery(".formFiltros select").change(function () {
            dt_dados.ajax.reload();
        });
        jQuery("#btn_atualizar").click(function () {
            dt_dados.ajax.reload();
        });
    });

    function __alterar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'configuracoes/tag/'; ?>form/" + codigo + "/";
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'configuracoes/tag/'; ?>view/" + codigo + "/";
    }

    function __excluir(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente excluir essa Tag?", "__calbackExcluir('" + codigo + "');");
    }
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/tag/'; ?>exec.php", {acao: 'del', tag_int_codigo: codigo}, "dt_dados.ajax.reload();", "");
    }
</script>
