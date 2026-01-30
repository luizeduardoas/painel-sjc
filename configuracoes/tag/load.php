<?php

require_once("../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("tag"));

$filter = new GFilter();

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("tag_var_titulo", "tag_var_url", "tag_txt_valores", "tag_var_informacoes", "pem_var_codigo"), $search);
}

$arrColunas = array("", "tag_int_codigo", "tag_var_titulo", "tag_var_url", "tag_txt_valores", "tag_var_informacoes", "pem_var_codigo");

$col = "tag_var_titulo";
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
//$filter->setGroupBy("tag_int_codigo");

try {
    $arr = array();
    $tagDao = new TagDao();
    $qtd = $tagDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $tagDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $tag Tag */
            foreach ($arrDados as $tag) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arrayBotoes = array();
                $arrayBotoes["view"] = "__visualizar('" . $tag->getTag_int_codigo() . "')";
                if (GSecurity::verificarPermissao("TAG_UPD", false)) {
                    $arrayBotoes["update"] = "__alterar('" . $tag->getTag_int_codigo() . "')";
                }
                if (GSecurity::verificarPermissao("TAG_DEL", false)) {
                    $arrayBotoes["delete"] = "__excluir('" . $tag->getTag_int_codigo() . "')";
                }
                $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                // </editor-fold>

                $arr[$i][] = $tag->getTag_int_codigo();
                $arr[$i][] = $tag->getTag_var_titulo();
                $arr[$i][] = $tag->getTag_var_url();
                $arr[$i][] = $tag->getTag_txt_valores();
                $arr[$i][] = $tag->getTag_var_informacoes();
                $arr[$i][] = $tag->getPem_var_codigo();
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>
