<?php

require_once("inc/global.php");
echo '<pre>';
var_dump(date("d/m/Y H:i:s"));
var_dump(extension_loaded('openssl') ? 'SSL loaded' : 'SSL not loaded');
echo '</pre>';


//$mysqlMoodle = new GDbMysqlMoodle();
//$mysqlMoodle->execute("SELECT * FROM mdl_user");
//while ($mysqlMoodle->fetch()) {
//    echo '<pre>';
//    var_dump($mysqlMoodle->res["username"]);
//    echo '</pre>';
//}
//echo '<pre>';
//var_dump(matricularAlunoOfertaAtiva(1));
//echo '</pre>';
//echo '<pre>';
//var_dump(salvarEvento('S', '$titulo', '$dados'));
//echo '</pre>';
//
//$email = new GEmail();
//$email->setMensagem("Testando");
//$email->setAssunto("Tetando envio de mensagem");
//$email->setDestinatario("Luiz Eduardo<luiz.eduardo.as@gmail.com>");
//$returnEmail = $email->enviar();
//if ($returnEmail["status"]) {
//    $return["msg"] = "Email enviado com sucesso.";
//} else {
//    $return["status"] = false;
//    $return["msg"] = $returnEmail["msg"];
//}
//echo '<pre>';
//var_dump($return);
//echo '</pre>';
//$arrApi['tipo'] = 'POST';
//$arrApi['url'] = buscarParametro('API_AVA_URL', API_AVA_URL) . 'token';
//$arrApi['dados'] = array("api_var_usuario" => buscarParametro('API_AVA_USUARIO', API_AVA_USUARIO), "api_var_senha" => buscarParametro('API_AVA_SENHA', API_AVA_SENHA));
//$retToken = chamarApi($arrApi);
//echo '<pre>';
//var_dump($retToken);
//echo '</pre>';
//
//$arrToken = json_decode($retToken["dados"]["recebido"], true);
//
//$arrApi['tipo'] = 'POST';
//$arrApi['token'] = $arrToken["token"];
//$arrApi['url'] = buscarParametro('API_AVA_URL', API_AVA_URL) . 'dados';
//$parametros = json_encode(array("empresa" => "Responsável financeiro não informado", "cursos" => "'UNIABAD','UNIABADINTCADSUP'"));
//$arrApi['dados'] = array("tipo" => "ALUNOS", "parametros" => $parametros);
//$ret = chamarApi($arrApi);
//echo '<pre>';
//var_dump($ret);
//echo '</pre>';
//require_once(ROOT_SYS_INC . "aws/AwsS3.php");
//
//$s3 = new AwsS3();
//echo '<pre>';
//var_dump($s3->TornarPublico());
//echo '</pre>';
//$arquivo = 'documentos/01049115570/625ca638db8c7.png';
//
//$ret = $s3->Existe($arquivo);
//echo '<pre>';
//var_dump($ret);
//echo '</pre>';
//$ret_env = $s3->Enviar($arquivo, ROOT_UPLOAD . $arquivo);
//echo '<pre>';
//var_dump($ret_env);
//echo '</pre>';
//echo '<pre>';
//echo '<img src="' . $s3->BuscarURL($arquivo) . '"/>';
//echo '</pre>';
//echo '<pre>';
//limparImagens(ROOT_UPLOAD . 'redacao/', URL_UPLOAD . 'redacao/', 'redacao', 'red_var_arquivo');
//limparImagens(ROOT_UPLOAD . 'tema/', URL_UPLOAD . 'tema/', 'tema', 'tem_var_imagem');
//limparImagens(ROOT_UPLOAD . 'usuario/', URL_UPLOAD . 'usuario/', 'usuario', 'usu_var_foto');
//echo '</pre>';
//
//function limparImagens($root, $url, $tabela, $campo) {
//    $mysql = new GDbMysql();
//    if ($handle = opendir($root)) {
//        while ($arquivo = readdir($handle)) {
//            if (!is_dir($arquivo) && $arquivo != 'lixo' && $arquivo != 'unknown.png' && $arquivo != 'unknown.jpg' && $arquivo != '.' && $arquivo != '..') {
//                $mysql->execute("SELECT * FROM $tabela WHERE $campo = ? ", array("s", str_replace("_p", "", str_replace("_m", "", str_replace("_g", "", $arquivo)))));
//                if (!$mysql->fetch()) {
//                    var_dump("Movendo para lixo: " . $arquivo);
//                    echo '<img src="' . $url . 'lixo/' . $arquivo . '"/>';
//                    var_dump(rename($root . $arquivo, $root . 'lixo/' . $arquivo));
//                }
//                $mysql->close();
//            }
//        }
//        closedir($handle);
//    }
//}
?>