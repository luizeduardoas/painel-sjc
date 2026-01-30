<?php

require_once("../../inc/global.php");

$mysql = new GDbMysql();
$sql = "SELECT p.pef_int_codigo,p.pef_var_descricao,(SELECT COUNT(*) FROM perfil_troca pt WHERE p.pef_int_codigo = pt.pef_int_codigo_troca AND pt.pef_int_codigo = ?) as perfis ";
$sql .= " FROM perfil p ";
$sql .= " WHERE p.pef_int_codigo <> 0 AND p.pef_int_codigo <> ? ";
$mysql->execute($sql, array("ii", $_POST["pef_int_codigo"], $_POST["pef_int_codigo"]));
if ($mysql->numRows() > 0) {
    while ($mysql->fetch()) {
        echo '<option ' . (($mysql->res["perfis"] > 0) ? 'selected="selected"' : '') . ' value="' . $mysql->res["pef_int_codigo"] . '">' . $mysql->res["pef_var_descricao"] . '</option>';
    }
}
