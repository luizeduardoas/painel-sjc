<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("PERFIL");
GF::import(array("perfil"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Segurança >> Perfis", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Segurança >> Perfis", true);
$header->addMenu("PERFIL", "Cadastro de Perfis", "Insira, altere e exclua os perfis de usuários do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */
$form = new GForm();

$html = '';

// <editor-fold desc="Lista">
$filtro = '';
global $__arrayAtivo;
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_status", array("-1" => "Todos") + $__arrayAtivo, buscarCookie("filtro_status", "-1"), "Status", array("class" => "chosen-select"), false, false, false) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Perfis',
    'id' => 'lista',
    'botaoNovo' => true,
    'botaoAtualizar' => true,
    'filtro' => $filtro,
    'botaoNovoPermissao' => 'PERFIL_INS'
        ));
$largura = obterLarguraColunaAcoes(array("PERFIL_UPD", "PERFIL_DEL", "PERFIL_CLO"));
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Descrição", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Status", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "center")
);
$filtros = array(
    "filtro_status"
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'seguranca/perfil/load.php', $filtros, $colunas, false, 25, false, true, false);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show(isFrame());
?>
<script>
    jQuery(document).ready(function () {
        jQuery('.chosen-select').chosen(paramChosenInline);
        jQuery("#btn_novo").click(function () {
            jQuery.gDisplay.loadStart('HTML');
            window.location.href = "<?php echo URL_SYS . 'seguranca/perfil/'; ?>form/";
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
        window.location.href = "<?php echo URL_SYS . 'seguranca/perfil/'; ?>form/" + codigo + "/";
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'seguranca/perfil/'; ?>view/" + codigo + "/";
    }

    function __clonar(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente clonar esse Perfil?", "__calbackClonar('" + codigo + "');");
    }

    function __calbackClonar(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfil/'; ?>exec.php", {acao: 'clo', pef_int_codigo: codigo}, "dt_dados.ajax.reload();", "");
    }

    function __excluir(codigo) {
        jQuery.gDisplay.showYN("Deseja realmente excluir esse Perfil?", "__calbackExcluir('" + codigo + "');");
    }
    function __calbackExcluir(codigo) {
        jQuery.gAjax.exec("<?php echo URL_SYS . 'seguranca/perfil/'; ?>exec.php", {acao: 'del', pef_int_codigo: codigo}, "dt_dados.ajax.reload();", "");
    }
</script>