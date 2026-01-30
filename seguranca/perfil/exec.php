<?php

require_once("../../inc/global.php");
GF::import(array("perfil"));

$perfil = new Perfil();
if (isset($_POST["pef_int_codigo"]))
    $perfil->setPef_int_codigo($_POST["pef_int_codigo"]);
if (isset($_POST["pef_var_descricao"]))
    $perfil->setPef_var_descricao($_POST["pef_var_descricao"]);
if (isset($_POST["pef_cha_status"]))
    $perfil->setPef_cha_status($_POST["pef_cha_status"]);

$perfilDao = new PerfilDao();

switch ($_POST["acao"]) {
    case "ins":
        if (GSecurity::verificarPermissaoAjax("PERFIL_INS")) {
            echo json_encode($perfilDao->insert($perfil));
        }
        break;
    case "upd":
        if (GSecurity::verificarPermissaoAjax("PERFIL_UPD")) {
            echo json_encode($perfilDao->update($perfil));
        }
        break;
    case "del":
        if (GSecurity::verificarPermissaoAjax("PERFIL_DEL")) {
            echo json_encode($perfilDao->delete($perfil));
        }
        break;
    case "sel":
        $perfil = $perfilDao->selectById($perfil);
        echo json_encode($perfil->getArray());
        break;
    case "clo":
        if (GSecurity::verificarPermissaoAjax("PERFIL_CLO")) {
            echo json_encode($perfilDao->clonar($perfil));
        }
        break;
    case "delM":
        if (GSecurity::verificarPermissaoAjax("PERFIL_DEL")) {
            $erros = array();
            $codigos = explode(";", $_POST["codigos"]);
            foreach ($codigos as $codigo) {
                if ($codigo != "") {
                    $temp = new Perfil();
                    $temp->setPef_int_codigo($codigo);
                    $return = $perfilDao->delete($temp);
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