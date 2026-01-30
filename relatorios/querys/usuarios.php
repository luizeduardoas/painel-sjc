<?php

global $genesis;
if (is_null($genesis))
    require_once(__DIR__ . "/../../inc/global.php");

$arr = array();
try {
    $mysql = new GDbMysql();
    $arrTipoDistincts = false;
    switch ($filtro_tipo) {
        case "perfil":

            $arrTitulos = array("Perfil", "Quantidade");
            $arrTipoDistincts = $mysql->executeArray("SELECT pef_var_descricao FROM perfil ORDER BY pef_var_descricao");
            $sql = "SELECT pef.pef_var_descricao, COUNT(*) as qtd ";
            $sql .= "FROM usuario usu ";
            $sql .= "INNER JOIN perfil pef ON (usu.pef_int_codigo = pef.pef_int_codigo) ";
            $sql .= "GROUP BY pef.pef_var_descricao ";

            break;
        case "status":

            $arrTitulos = array("Status", "Quantidade");
            global $__arrayBloqueado;
            $arrTipoDistincts = array_values($__arrayAtivo);
            $usu_cha_status = gerarCase("usu_cha_status", $__arrayBloqueado);
            $sql = "SELECT $usu_cha_status, COUNT(*) as qtd ";
            $sql .= "FROM usuario usu ";
            $sql .= "GROUP BY usu.usu_cha_status ";

            break;
    }
    $mysql->execute($sql);
    $i = 0;
    $total = 0;
    $arrDistincts = array();
    while ($mysql->fetch()) {
        $arrDistincts[] = formataDadoVazio($mysql->res[0]);
        $arr[$i][] = formataDadoVazio($mysql->res[0]);
        $arr[$i][] = $mysql->res[1];
        $arrLinha[] = '';
        $i++;
    }

    if ($arrTipoDistincts) {
        foreach ($arrTipoDistincts as $val) {
            if (!in_array($val, $arrDistincts)) {
                $arr[$i][] = $val;
                $arr[$i][] = "0";
                $arrLinha[] = '';
                $i++;
            }
        }
    }

    $arrDados = $arr;
    $arrFooter = array();
    $arrStyleTitulos = array("text-align: center;font-weight: bold", "text-align: center;font-style: bold");
    $arrFormats = array("text-align: center;font-weight: bold", "text-align: center;font-style: italic");
    $arrTitulosColspan = array();
} catch (GDbException $e) {
    echo $e->getError();
    $arrTitulos = array();
    $arrDados = array();
    $arrFooter = array();
    $arrStyleTitulos = array();
    $arrFormats = array();
    $arrTitulosColspan = array();
}
?>
