<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("AVAUSUARIO");
GF::import(array("avaUsuario"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Usuários", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Usuários", true);
$header->addMenu("AVAUSUARIO", "Listagem de Usuários", "Visualize os Usuários do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$mysql = new GDbMysql();
$opt_esc_var_nome = $mysql->executeCombo("SELECT n.esc_int_codigo, esc_var_nome FROM escola n WHERE EXISTS (SELECT 1 FROM ava_usuario c WHERE c.esc_int_codigo = n.esc_int_codigo) ORDER BY esc_var_nome, n.esc_int_codigo;");

$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$filtro = '';
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_escola", array("-1" => "Todos") + $opt_esc_var_nome, buscarCookie("filtro_escola", "-1"), "Escolas", array("class" => "chosen-select"), false, false, false) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Usuários',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array());
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Escola", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Identificador", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "CPF", "largura" => "120px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Matrícula", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Cargo", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Função", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Email", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "left")
);
$filtros = array("filtro_escola");
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'gerenciamento/avausuario/load.php', $filtros, $colunas, false, 25, false, true, true);
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
        jQuery(".formFiltros select").change(function () {
            salvarFiltros();
            dt_dados.ajax.reload();
        });
    });

    function salvarFiltros() {
        setParametroCookie('filtro_escola', jQuery('#filtro_escola').val());
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'gerenciamento/avausuario/'; ?>view/" + codigo + "/";
    }
</script>
