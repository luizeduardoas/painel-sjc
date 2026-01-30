<?php

require_once("../../inc/global.php");
GF::import(array("tag"));

$tag = new Tag();
if (isset($_POST["tag_int_codigo"]))
    $tag->setTag_int_codigo($_POST["tag_int_codigo"]);
if (isset($_POST["tag_var_titulo"]))
    $tag->setTag_var_titulo($_POST["tag_var_titulo"]);
if (isset($_POST["tag_var_url"]))
    $tag->setTag_var_url($_POST["tag_var_url"]);
if (isset($_POST["tag_txt_valores"]))
    $tag->setTag_txt_valores($_POST["tag_txt_valores"]);
if (isset($_POST["tag_var_informacoes"]))
    $tag->setTag_var_informacoes($_POST["tag_var_informacoes"]);
if (isset($_POST["pem_var_codigo"]))
    $tag->setPem_var_codigo($_POST["pem_var_codigo"]);

$tagDao = new TagDao();

switch ($_POST["acao"]) {
    case "ins":
        echo json_encode($tagDao->insert($tag));
        break;
    case "upd":
        echo json_encode($tagDao->update($tag));
        break;
    case "del":
        echo json_encode($tagDao->delete($tag));
        break;
    default:
        echo '{"status": false, "msg":"Ação inválida"}';
        break;
}
?>
