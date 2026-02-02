<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../global.php");

GSecurity::verificarAutenticacaoAjax();

$filtro_nivel = '';
if ($_POST["filtro_nivel"] != "") {
    $filtro_nivel = implode(",", $_POST["filtro_nivel"]);
}
if ($filtro_nivel != '') {
    $mysql = new GDbMysql();
    $query = "SELECT cur_int_codigo, cur_var_nome ";
    $query .= "FROM ava_curso cur ";
    $query .= "WHERE niv_int_codigo IN ($filtro_nivel) ";
    $query .= "ORDER BY cur_var_nome ASC ";
    $opt_cur_int_codigo = $mysql->executeCombo($query);

    $codigosCurso = explode(",", buscarCookie("filtro_curso"));
    if (count($codigosCurso) == 0 || (isset($codigosCurso[0]) && $codigosCurso[0] == '')) {
        $codigosCurso = array_keys($opt_cur_int_codigo);
    }

    $form = new GForm();
    echo $form->addSelectMulti("cur_int_codigo", $opt_cur_int_codigo, $codigosCurso, "Curso:", array("class" => "multiselect"), array("class" => "required"), false, false);
    echo '<div class="space space-8"></div>';
}
?>