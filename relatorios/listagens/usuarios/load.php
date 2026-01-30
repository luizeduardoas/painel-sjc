<?php

require_once("../../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");


$filter = new GFilter();

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("pef_var_descricao", "usu_var_identificador", "usu_var_nome", "usu_var_email"), $search);
}

$arrColunas = array("usu_int_codigo", "pef_var_descricao", "usu_var_identificador", "usu_var_nome", "usu_var_email", "usu_cha_status", "usu_cha_validado", "usu_dti_criacao", "usu_dti_ultimo");

$col = "usu_int_codigo";
if (isset($_POST["order"][0]["column"])) {
    $col = $arrColunas[$_POST["order"][0]["column"]];
}
$ord = "ASC";
if (isset($_POST["order"][0]["dir"])) {
    $ord = $_POST["order"][0]["dir"];
}
$start = (isset($_POST["start"]) ? $_POST["start"] : 0);
$length = (isset($_POST["length"]) ? $_POST["length"] : 99999999);
$filter->setLimit($start, $length);
$filter->setOrder(array($col => $ord));
//$filter->setGroupBy("pem_var_codigo");

try {
    $arr = array();
    $mysql = new GDbMysql();
    global $__arrayBloqueado, $__arrayValidado;
    $usu_dti_criacao = gerarDate_format("usu_dti_criacao");
    $usu_dti_ultimo = gerarDate_format("usu_dti_ultimo");
    $usu_cha_status = gerarCase("usu_cha_status", $__arrayBloqueado);
    $usu_cha_validado = gerarCase("usu_cha_validado", $__arrayValidado);
    $sqlProjecao = " SELECT usu.usu_int_codigo, pef.pef_int_codigo, pef.pef_var_descricao, usu.usu_var_identificador, usu.usu_var_nome, usu.usu_var_email, $usu_cha_status, $usu_cha_validado, $usu_dti_criacao, $usu_dti_ultimo ";
    $sql = "FROM usuario usu ";
    $sql .= "INNER JOIN perfil pef ON (usu.pef_int_codigo = pef.pef_int_codigo) ";
    $qtd = $mysql->executeValue("SELECT COUNT(*) " . $sql . $filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $mysql->execute($sqlProjecao . $sql . $filter->getWhere(false), $filter->getParam());
        $arr = array();
        if ($mysql->numRows() > 0) {
            $i = 0;
            while ($mysql->fetch()) {
                $arr[$i][] = $mysql->res["usu_int_codigo"];
                $arr[$i][] = $mysql->res["pef_var_descricao"];
                $arr[$i][] = $mysql->res["usu_var_identificador"];
                $arr[$i][] = $mysql->res["usu_var_nome"];
                $arr[$i][] = $mysql->res["usu_var_email"];
                $arr[$i][] = $mysql->res["usu_cha_status"];
                $arr[$i][] = $mysql->res["usu_cha_validado"];
                $arr[$i][] = $mysql->res["usu_dti_criacao"];
                $arr[$i][] = formataDadoVazio($mysql->res["usu_dti_ultimo"]);
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>