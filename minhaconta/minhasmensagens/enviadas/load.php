<?php

require_once("../../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("mensagem", "destinatario"));

$usuario = getUsuarioSessao();

$filter = new GFilter();
$filter->addClause('AND men_int_remetente = ' . $usuario->getUsu_int_codigo() . ' ');


$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("men_var_titulo"), $search);
}

$arrColunas = array("", "men_int_codigo", "men_dti_envio", "usuario", "men_var_titulo");

$col = "men_dti_envio";
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
//$filter->setGroupBy("men_int_codigo");

try {
    $arr = array();
    $mensagemDao = new MensagemDao();
    $qtd = $mensagemDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $mensagemDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            /* @var $mensagem Mensagem */
            foreach ($arrDados as $mensagem) {
                // <editor-fold defaultstate="collapsed" desc="Botões">
                $arr[$i][] = carregarBotoesGrid(array(
                    "view" => "__visualizar(" . $mensagem->getMen_int_codigo() . ")"
                ));
                // </editor-fold>

                $arr[$i][] = $mensagem->getMen_int_codigo();
                $arr[$i][] = $mensagem->getMen_dti_envio_format();
                $arr[$i][] = formatarGroupConcat($mensagem->getDestinatarios(), "usuario", "usu_int_codigo", "CONCAT(' ', usu_var_nome)");
                $arr[$i][] = $mensagem->getMen_var_titulo();
                $i++;
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>