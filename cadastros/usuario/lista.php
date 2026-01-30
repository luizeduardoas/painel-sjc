<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

$form = new GForm();

$pef_int_codigo = (isset($_REQUEST["pef_int_codigo"]) && $_REQUEST["pef_int_codigo"] != 'undefined') ? " AND pef_int_codigo = " . $_REQUEST["pef_int_codigo"] . " " : "";
if (isset($_GET["term"])) {
    $json = "";
    $mysql = new GDbMysql();
    $mysql->execute("SELECT usu_int_codigo,usu_var_identificador,usu_var_nome FROM usuario usu WHERE LOWER(usu.usu_var_nome) LIKE LOWER(?) $pef_int_codigo", array("s", "%" . str_replace(' ', '%', $_GET["term"]) . "%"));
    if ($mysql->numRows()) {
        while ($mysql->fetch()) {
            $json .= '{"id":"' . $mysql->res["usu_int_codigo"] . '", "value": "' . $mysql->res["usu_var_nome"] . '", "identificador": "' . $mysql->res["usu_var_identificador"] . '"},';
        }
        echo '[' . substr($json, 0, -1) . ']';
    }
    $mysql->close();
} elseif (isset($_POST["id"])) {
    $mysql = new GDbMysql();
    $mysql->execute("SELECT usu_int_codigo,usu_var_identificador,usu_var_nome FROM usuario usu WHERE usu_var_identificador = ? $pef_int_codigo", array("s", $_POST["id"]));
    if ($mysql->fetch()) {
        echo '{"id":"' . $mysql->res["usu_int_codigo"] . '", "value": "' . $mysql->res["usu_var_nome"] . '", "identificador": "' . $mysql->res["usu_var_identificador"] . '"}';
    }
    $mysql->close();
} else {

    $header = new GHeader("Usuários");
    $header->show(true);
    /* -------------------------------------------------------------------------- */
    $mysql = new GDbMysql();
    $opt_pef_var_descricao = $mysql->executeCombo("SELECT pef_int_codigo, pef_var_descricao FROM perfil WHERE pef_cha_status = 'A' ORDER BY pef_var_descricao;");

    $campo = $_GET["campo"];

    $html = '';
    // <editor-fold desc="Lista">
    $filtro = '';
    $filtro .= $form->addSelect("filtro_perfil", array("-1" => "Todos") + $opt_pef_var_descricao, $_GET["pef_int_codigo"], "Perfil:", array("class" => "chosen-select"), false, false, false);
    $html .= gerarCabecalho(array(
        'tipo' => 'frame',
        'titulo' => 'Listagem de Usuários',
        'id' => 'lista',
        'botaoNovo' => false,
        'botaoAtualizar' => true,
        'export' => false,
        'filtro' => $filtro
    ));
    $colunas = array(
        array("titulo" => "", "largura" => "30px", "ordem" => false, "visivel" => true, "classe" => "center"),
        array("titulo" => "Identificador", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
        array("titulo" => "Nome", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "left"),
        array("titulo" => "Perfil", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "center"),
        array("titulo" => "Status", "largura" => false, "ordem" => true, "visivel" => true, "classe" => "center")
    );
    $filtros = array(
        "filtro_perfil"
    );
    $html .= getTableDataServerSide("dt_dados", URL_SYS . 'cadastros/usuario/loadLista.php', $filtros, $colunas, false);
    $html .= gerarRodape(array('tipo' => 'full'));
    // </editor-fold>
    echo $html;

    /* -------------------------------------------------------------------------- */
    $footer = new GFooter();
    $footer->show(true);

    echo gerarScriptStyleLista($campo);
}
?>