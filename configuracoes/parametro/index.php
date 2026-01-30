<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PARAMETRO");
GF::import(array("parametro"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Configurações >> Parâmetros", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Configurações >> Parâmetros", true);
$header->addMenu("PARAMETRO", "Configuração de Parâmetros", "Insira, altere e exclua os parâmetros de configurações do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

$html = '';

// <editor-fold desc="Lista">
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Parâmetros',
    'id' => 'lista',
    'botaoNovo' => true,
    'botaoNovoPermissao' => 'PARAMETRO_INS',
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array("PARAMETRO_UPD", "PARAMETRO_DEL"));
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Chave", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Descrição", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Valor", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Atualização", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Usuário", "largura" => false, "ordem" => false, "visivel" => true, "classe" => "left")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'configuracoes/parametro/load.php', false, $colunas, false, 25);
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
            window.location.href = "<?php echo URL_SYS . 'configuracoes/parametro/'; ?>form/";
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
        window.location.href = "<?php echo URL_SYS . 'configuracoes/parametro/'; ?>form/" + codigo + "/";
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'configuracoes/parametro/'; ?>view/" + codigo + "/";
    }

    function __excluir(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente excluir esse Parâmetro?", "__calbackExcluir('" + codigo + "');");
    }
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'configuracoes/parametro/'; ?>exec.php", {acao: 'del', par_int_codigo: codigo}, "dt_dados.ajax.reload();", "");
    }
</script>