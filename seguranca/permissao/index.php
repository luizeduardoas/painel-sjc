<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERMISSAO");
GF::import(array("permissao"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Permissões", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Segurança >> Permissões", true);
$header->addMenu("PERMISSAO", "Cadastro de Permissões", "Insira, altere e exclua as permissões dos perfis no sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$form = new GForm();

$html = '';

// <editor-fold desc="Lista">
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Permissões',
    'id' => 'lista',
    'botaoAtualizar' => true,
    'botaoNovo' => true,
    'botaoNovoPermissao' => 'PERMISSAO_INS'
        ));
$largura = obterLarguraColunaAcoes(array("PERMISSAO_UPD", "PERMISSAO_DEL"));
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Descrição", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Vínculo", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'seguranca/permissao/load.php', false, $colunas, false, 25, false, true, false);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>
echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script>
    jQuery(document).ready(function () {
        jQuery("#btn_novo").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>form/";
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
        window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>form/" + codigo + "/";
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'seguranca/permissao/'; ?>view/" + codigo + "/";
    }

    function __excluir(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente excluir esse Permissão?", "__calbackExcluir('" + codigo + "');");
    }
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/permissao/'; ?>exec.php", {acao: 'del', pem_var_codigo: codigo}, "dt_dados.ajax.reload();", "");
    }
</script>