<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../../inc/global.php");

try {
    $mysql = new GDbMysql();
    $arrDados = array();
    $arrTitulos = array("Código", "Perfil", "Identificador", "Nome", "Email", "Status", "Validado", "Cadastro", "Último Acesso");
    global $__arrayBloqueado, $__arrayValidado;
    $usu_dti_criacao = gerarDate_format("usu_dti_criacao");
    $usu_dti_ultimo = gerarDate_format("usu_dti_ultimo");
    $usu_cha_status = gerarCase("usu_cha_status", $__arrayBloqueado);
    $usu_cha_validado = gerarCase("usu_cha_validado", $__arrayValidado);
    $sql = "SELECT usu.usu_int_codigo, pef.pef_int_codigo, pef.pef_var_descricao, usu.usu_var_identificador, usu.usu_var_nome, usu.usu_var_email, $usu_cha_status, $usu_cha_validado, $usu_dti_criacao, $usu_dti_ultimo ";
    $sql .= "FROM usuario usu ";
    $sql .= "INNER JOIN perfil pef ON (usu.pef_int_codigo = pef.pef_int_codigo) ";
    $sql .= "ORDER BY usu.usu_int_codigo ASC ";

    $mysql->execute($sql);
    if ($mysql->numRows() > 0) {
        $i = 0;
        while ($mysql->fetch()) {
            $arrDados[$i][] = $mysql->res["usu_int_codigo"];
            $arrDados[$i][] = $mysql->res["pef_var_descricao"];
            $arrDados[$i][] = $mysql->res["usu_var_identificador"];
            $arrDados[$i][] = $mysql->res["usu_var_nome"];
            $arrDados[$i][] = $mysql->res["usu_var_email"];
            $arrDados[$i][] = $mysql->res["usu_cha_status"];
            $arrDados[$i][] = $mysql->res["usu_cha_validado"];
            $arrDados[$i][] = $mysql->res["usu_dti_criacao"];
            $arrDados[$i][] = formataDadoVazio($mysql->res["usu_dti_ultimo"]);
            $i++;
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}

if (count($arrDados)) {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Type: application/force-download");
    header("Content-Disposition: attachment; filename=usuarios.xls");
    header("Pragma: no-cache");
    echo mb_convert_encoding(arrayToTable($arrTitulos, $arrDados, false), 'utf-16', 'utf-8');
} else {
    echo '<h1>Nenhum dado encontrado para exportação</h1>';
}
?>