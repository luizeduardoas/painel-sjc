<?php

require_once("../../inc/global.php");
GF::import(array("parametro"));

$parametro = new Parametro();
if (isset($_POST["par_int_codigo"]))
    $parametro->setPar_int_codigo($_POST["par_int_codigo"]);
if (isset($_POST["par_var_chave"]))
    $parametro->setPar_var_chave(strtoupper($_POST["par_var_chave"]));
if (isset($_POST["par_var_descricao"]))
    $parametro->setPar_var_descricao($_POST["par_var_descricao"]);
if (isset($_POST["par_txt_valor"]))
    $parametro->setPar_txt_valor($_POST["par_txt_valor"]);
$usuario = new Usuario();
$usuario->setUsu_int_codigo(getUsuarioSessao()->getUsu_int_codigo());
$parametro->setUsuario($usuario);

$parametroDao = new ParametroDao();

switch ($_POST["acao"]) {
    case "ins":
        if (GSecurity::verificarPermissaoAjax("PARAMETRO_INS")) {
            echo json_encode($parametroDao->insert($parametro));
        }
        break;
    case "upd":
        if (GSecurity::verificarPermissaoAjax("PARAMETRO_UPD")) {
            echo json_encode($parametroDao->update($parametro));
        }
        break;
    case "del":
        if (GSecurity::verificarPermissaoAjax("PARAMETRO_DEL")) {
            echo json_encode($parametroDao->delete($parametro));
        }
        break;
    case "sel":
        if (GSecurity::verificarPermissaoAjax("PARAMETRO")) {
            $parametro = $parametroDao->selectById($parametro);
            echo json_encode($parametro->getArray());
        }
        break;
    case "delM":
        if (GSecurity::verificarPermissaoAjax("PARAMETRO_DEL")) {
            $erros = array();
            $codigos = explode(";", $_POST["codigos"]);
            foreach ($codigos as $codigo) {
                if ($codigo != "") {
                    $temp = new Parametro();
                    $temp->setPar_int_codigo($codigo);
                    $return = $parametroDao->delete($temp);
                    if (!$return["status"]) {
                        $erros[$codigo] = $return["msg"];
                    }
                }
            }
            if (count($erros))
                $msg = '{"status": false, "msg":"Não foi possível excluir os seguintes itens:' . gerarTabelaErros($erros) . '"}';
            else
                $msg = '{"status": true, "msg":"Itens excluidos com sucesso."}';
            echo $msg;
        }
        break;
    default:
        echo '{"status": false, "msg":"Ação inválida"}';
        break;
}
?>