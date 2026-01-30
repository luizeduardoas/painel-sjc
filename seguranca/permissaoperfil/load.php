<?php

require_once("../../inc/global.php");

$mysql = new GDbMysql();
$sql = "SELECT p.pem_var_codigo,p.pem_var_descricao,p.pem_var_vinculo,(SELECT COUNT(*) FROM perfil_permissao pp WHERE p.pem_var_codigo = pp.pem_var_codigo AND pp.pef_int_codigo = ?) as permissao ";
$sql .= " FROM permissao p ";
$mysql->execute($sql, array("i", $_POST["pef_int_codigo"]));
if ($mysql->numRows() > 0) {
    while ($mysql->fetch()) {
        echo '<option ' . (($mysql->res["permissao"] > 0) ? 'selected="selected"' : '') . ' value="' . $mysql->res["pem_var_codigo"] . '">' . $mysql->res["pem_var_codigo"] . ' - ' . $mysql->res["pem_var_descricao"] . ' (' . $mysql->res["pem_var_vinculo"] . ')</option>';
    }
}
?>