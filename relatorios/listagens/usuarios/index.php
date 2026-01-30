<?php
global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");
GSecurity::verificarPermissao("LISTAGENSUSUARIOS");

$breadcrumb = new Breadcrumb();
$breadcrumb->add("Início", URL_SYS . 'home/', 0);
$breadcrumb->add("Relatórios >> Listagens", URL_SYS . 'relatorios/listagens/', 1);
$breadcrumb->add("Usuários", URL_SYS . 'relatorios/listagens/usuarios/', 2);

$header = new GHeader("Relatório de Listagens de Usuários", true);
$header->addMenu("LISTAGENSUSUARIOS", "Relatório de Listagens de Usuários", "Visualize as informações dos Usuários do sistema");
$header->show(false, $breadcrumb);
/* -------------------------------------------------------------------------- */
$html = '';

// <editor-fold desc="Lista">
$html .= gerarCabecalho(array(
    'tipo' => 'full',
    'titulo' => 'Relatório de Listagens de Usuários',
    'id' => 'lista',
    'botaoNovo' => false,
    'export' => true,
    'botaoAtualizar' => true,
    'botaoExcel' => true
        ));
$colunas = array(
    array("titulo" => "Código", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Perfil", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Identificador", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Email", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
    array("titulo" => "Status", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Validado", "largura" => "60px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Cadastro", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "center"),
    array("titulo" => "Último Acesso", "largura" => "130px", "ordem" => true, "visivel" => true, "classe" => "center")
);
$html .= getTableDataServerSide("dt_dados", URL_SYS . 'relatorios/listagens/usuarios/load.php', false, $colunas, false, 100, false, true, true);
$html .= gerarRodape(array('tipo' => 'full'));
// </editor-fold>

echo $html;

/* -------------------------------------------------------------------------- */
$footer = new GFooter();
$footer->show();
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#btn_atualizar").click(function () {
            dt_dados.ajax.reload();
        });
        jQuery("#btn_excel").click(function () {
            window.open("<?php echo URL_SYS . 'relatorios/listagens/usuarios/'; ?>excel.php");
        });
    });
</script>