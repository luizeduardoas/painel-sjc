<?php

require_once("../../inc/global.php");
GF::import(array("permissao"));

$vinculo = new Permissao();
if (isset($_POST["pem_var_vinculo"]))
    $vinculo->setPem_var_codigo(($_POST["pem_var_vinculo"] == "-1") ? NULL : $_POST["pem_var_vinculo"]);

$permissao = new Permissao();
if (isset($_POST["pem_var_codigo"]))
    $permissao->setPem_var_codigo($_POST["pem_var_codigo"]);
if (isset($_POST["pem_var_descricao"]))
    $permissao->setPem_var_descricao($_POST["pem_var_descricao"]);
$permissao->setVinculo($vinculo);

$permissaoDao = new PermissaoDao();

switch ($_POST["acao"]) {
    case "ins":
        if (GSecurity::verificarPermissaoAjax("PERMISSAO_INS")) {
            echo json_encode($permissaoDao->insert($permissao));
        }
        break;
    case "upd":
        if (GSecurity::verificarPermissaoAjax("PERMISSAO_UPD")) {
            echo json_encode($permissaoDao->update($permissao));
        }
        break;
    case "del":
        if (GSecurity::verificarPermissaoAjax("PERMISSAO_DEL")) {
            echo json_encode($permissaoDao->delete($permissao));
        }
        break;
    case "sel":
        $permissao = $permissaoDao->selectById($permissao);
        echo json_encode($permissao->getArray());
        break;
    case "delM":
        if (GSecurity::verificarPermissaoAjax("PERMISSAO_DEL")) {
            $erros = array();
            $codigos = explode(";", $_POST["codigos"]);
            foreach ($codigos as $codigo) {
                if ($codigo != "") {
                    $temp = new Permissao();
                    $temp->setPem_var_codigo($codigo);
                    $return = $permissaoDao->delete($temp);
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