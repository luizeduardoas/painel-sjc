<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");
GSecurity::verificarPermissao("MATRICULA");
GF::import(array("matricula"));

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS, 0);
$breadcrumb->add("Gerenciamento >> Matrículas", $_SERVER["REQUEST_URI"], 1);

$header = new GHeader("Gerenciamento >> Matrículas", true);
$header->addMenu("MATRICULA", "Listagem de Matrículas", "Visualize as Matrículas do sistema");
$header->show(isFrame(), $breadcrumb);
/* -------------------------------------------------------------------------- */

$mysql = new GDbMysql();
$opt_cur_var_nome = $mysql->executeCombo("SELECT cur_int_codigo, cur_var_nome FROM ava_curso ORDER BY cur_var_nome, cur_int_codigo;");

$form = new GForm();

$html = '';
// <editor-fold desc="Lista">
$filtro = '';
$filtro .= '<div class="filtroInline">' . $form->addSelect("filtro_curso", array("-1" => "Todos") + $opt_cur_var_nome, buscarCookie("filtro_curso", "-1"), "Cursos", array("class" => "chosen-select"), false, false, false) . '</div>';
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Listagem de Matrículas',
    'id' => 'lista',
    'botaoNovo' => false,
    'filtro' => $filtro,
    'botaoAtualizar' => true
        ));
$largura = obterLarguraColunaAcoes(array());
$colunas = array(
    array("titulo" => "", "largura" => $largura . "px", "ordem" => false, "visivel" => true, "classe" => "center"),
    array("titulo" => "Código", "largura" => "80px", "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "Curso", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Escola", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Identificador", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "CPF", "largura" => "120px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Matrícula", "largura" => "100px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Cargo", "largura" => "100px", "ordem" => true, "visivel" => false, "classe" => "left"),
    array("titulo" => "Função", "largura" => "100px", "ordem" => true, "visivel" => false, "classe" => "left"),
    array("titulo" => "Email", "largura" => "100px", "ordem" => true, "visivel" => false, "classe" => "left"),
    array("titulo" => "DH. de Criação", "largura" => false, "ordem" => true, "visivel" => false, "classe" => "center"),
    array("titulo" => "DH. de Início", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "DH. de Término", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "center"),
);
$filtros = array("filtro_curso");
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'gerenciamento/matricula/load.php', $filtros, $colunas, false, 25, false, true, true);
$html .= carregarLegenda(array("DH." => "Data e Hora"), 'col-12 col-md-4 col-lg-3 col-xl-2');
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
        setParametroCookie('filtro_curso', jQuery('#filtro_curso').val());
    }

    function __visualizar(codigo) {
        jQuery.gDisplay.loadStart('HTML');
        window.location.href = "<?php echo URL_SYS . 'gerenciamento/matricula/'; ?>view/" + codigo + "/";
    }
</script>
