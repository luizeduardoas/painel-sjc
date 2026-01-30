<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("CURSO");
GF::import(array("avaCurso"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Cursos", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Cursos", true);
$header->addMenu("CURSO", "Listagem de Cursos", "Visualize as Cursos do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$mysql = new GDbMysql();
$opt_niv_var_nome = $mysql->executeCombo("SELECT niv_int_codigo, niv_var_nome FROM nivel ORDER BY niv_var_nome;");

$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$filtro = '';
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_nivel", array("-1" => "Todos") + $opt_niv_var_nome, buscarCookie("filtro_nivel", "-1"), "Nível", array("class" => "chosen-select"), false, false, false) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Cursos',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array());
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Identificador", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Nível", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left")
);
$filtros = array("filtro_nivel");
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'gerenciamento/curso/load.php', $filtros, $colunas, false, 25, false, true, true);
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
        setParametroCookie('filtro_nivel', jQuery('#filtro_nivel').val());
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'gerenciamento/curso/'; ?>view/" + codigo + "/";
    }
</script>
