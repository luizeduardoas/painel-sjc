<?php

require_once("../../../inc/global.php");
require_once(ROOT_GENESIS . "inc/filter.class.php");
GF::import(array("mensagem", "destinatario"));

$usuario = getUsuarioSessao();

$filter = new GFilter();
$filter->addClause('AND EXISTS (SELECT 1 FROM destinatario d WHERE men.men_int_codigo = d.men_int_codigo AND d.des_int_destinatario = ' . $usuario->getUsu_int_codigo() . ')');

$search = $_POST["search"]["value"];
if (!empty($search)) {
    $filter->addLike("AND", array("men_var_titulo"), $search);
}

$arrColunas = array("", "men_int_codigo", "remetente", "men_dti_envio", "men_var_titulo");

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
//$filter->setGroupBy("des_int_codigo");

try {
    $arr = array();
    $mensagemDao = new MensagemDao();
    $qtd = $mensagemDao->selectCount($filter->getWhere(true), $filter->getParam());
    if ($qtd > 0) {
        $arrDados = $mensagemDao->select($filter->getWhere(false), $filter->getParam());
        if (count($arrDados) > 0) {
            $i = 0;
            $destinatarioDao = new DestinatarioDao();
            /* @var $mensagem Mensagem */
            foreach ($arrDados as $mensagem) {
                /* @var $destinatario Destinatario */
                $destinatario = new Destinatario();
                $destinatario->setDes_int_destinatario($usuario->getUsu_int_codigo());
                $destinatario = $destinatarioDao->selectByMensagemDestinatario($mensagem, $destinatario);
                if ($destinatario->getDes_cha_status() < 2) {
                    // <editor-fold defaultstate="collapsed" desc="Botões">
                    $arrayBotoes = array(
                        "view" => "__visualizar(" . $mensagem->getMen_int_codigo() . ")"
                    );
                    if ($destinatario->getDes_cha_status() == 1) {
                        $arrayBotoes = array_merge($arrayBotoes, array("delete" => "__excluir(" . $mensagem->getMen_int_codigo() . ", " . $usuario->getUsu_int_codigo() . ")"));
                        $arrayBotoes = array_merge($arrayBotoes, array("naolida" => "__naolida(" . $mensagem->getMen_int_codigo() . ", " . $usuario->getUsu_int_codigo() . ")"));
                    }
                    $arr[$i][] = carregarBotoesGrid($arrayBotoes);
                    // </editor-fold>
                    $arr[$i][] = $mensagem->getMen_int_codigo();
                    $arr[$i][] = $mensagem->getMen_dti_envio_format();
                    $arr[$i][] = $mensagem->getRemetente()->getDescricao();
                    $arr[$i][] = $mensagem->getMen_var_titulo();
                    $arr[$i][] = '<span class="label label-sm label-' . labelStatus($destinatario->getDes_cha_status()) . ' label-white middle">' . $destinatario->getDes_cha_status_format() . '</span>';
                    $i++;
                }
            }
        }
    }
} catch (GDbException $e) {
    echo $e->getError();
}
echo '{"data":' . json_encode($arr) . ', "draw": ' . $_POST["draw"] . ', "recordsFiltered": ' . $qtd . ', "recordsTotal": ' . $qtd . '}';
?>