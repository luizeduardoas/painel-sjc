<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");

$pef_int_codigo = $_POST["pef_int_codigo"];
$filter = new GFilter();
$filter->addParam("i", $pef_int_codigo);

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("pem_var_codigo", "pem_var_descricao", "pem_var_vinculo"), $search);
}

$arrColunas = array("pem_var_codigo", "pem_var_descricao", "pem_var_vinculo");

$col = "pem_var_codigo";
if (isset($_POST["order"][0]["column"])) {
    $col = $arrColunas[$_POST["order"][0]["column"]];
}
$ord = "DESC";
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
    $sql = "SELECT COUNT(*) ";
    $sql .= " FROM permissao p ";
    $sql .= " INNER JOIN perfil_permissao pp ON (p.pem_var_codigo = pp.pem_var_codigo AND pp.pef_int_codigo = ?) ";
    $qtd = $mysql->executeValue($sql . $filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $sql = "SELECT p.pem_var_codigo,p.pem_var_descricao,p.pem_var_vinculo,(SELECT pem_var_descricao FROM permissao v WHERE v.pem_var_codigo = p.pem_var_vinculo) desc_vinculo ";
        $sql .= " FROM permissao p ";
        $sql .= " INNER JOIN perfil_permissao pp ON (p.pem_var_codigo = pp.pem_var_codigo AND pp.pef_int_codigo = ?) ";
        $mysql->execute($sql . $filter->getWhere(false), $filter->getParam());
        $arr = array();
        if ($mysql->numRows() > 0) {
            $i = 0;
            while ($mysql->fetch()) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arr[$i][] = carregarBotoesGrid(array(
                    "view" => "__visualizar('" . $mysql->res["pem_var_codigo"] . "')"
                ));
                // </editor-fold>

                $arr[$i][] = $mysql->res["pem_var_codigo"];
                $arr[$i][] = $mysql->res["pem_var_descricao"];
                if ($mysql->res["pem_var_vinculo"])
                    $arr[$i][] = '<a href="' . URL_SYS . 'seguranca/permissao/view/' . $mysql->res["pem_var_vinculo"] . '">' . $mysql->res["pem_var_vinculo"] . ' - ' . $mysql->res["desc_vinculo"] . '</a>';
                else
                    $arr[$i][] = '-';
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
