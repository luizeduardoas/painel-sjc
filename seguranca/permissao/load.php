<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("permissao"));

$filter = new GFilter();
$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("pem_var_codigo", "pem_var_descricao", "pem_var_vinculo"), $search);
}

$arrColunas = array("", "pem_var_codigo", "pem_var_descricao", "pem_var_vinculo");

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
//$filter->setGroupBy("pem_int_codigo");

try {
    $arr = array();
    $permissaoDao = new PermissaoDao();
    $qtd = $permissaoDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $permissaoDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $permissao Permissao */
            foreach ($arrDados as $permissao) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $permissao->getPem_var_codigo() . "')";
                if (GSecurity::verificarPermissao("PERMISSAO_UPD", false)) {
                    $arrayBotoes["update"] = "__alterar('" . $permissao->getPem_var_codigo() . "')";
                }
                if (GSecurity::verificarPermissao("PERMISSAO_DEL", false)) {
                    $arrayBotoes["delete"] = "__excluir('" . $permissao->getPem_var_codigo() . "')";
                }
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>

                $arr[$i][] = $permissao->getPem_var_codigo();
                $arr[$i][] = $permissao->getPem_var_descricao();
                if ($permissao->getVinculo()->getPem_var_codigo())
                    $arr[$i][] = '<a href="' . URL_SYS . 'seguranca/permissao/view/' . $permissao->getVinculo()->getPem_var_codigo() . '">' . $permissao->getVinculo()->getPem_var_codigo() . ' - ' . $permissao->getVinculo()->getPem_var_descricao() . '</a>';
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
