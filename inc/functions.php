<?php

// <editor-fold defaultstate="collapsed" desc="Sessão">
/**
 * Retorna um Objeto do tipo Usuario que está na sessão
 *
 * @return Usuario
 */
function getUsuarioSessao() {
    $usuario = new Usuario();
    if (isset($_SESSION['usuario'])) {
        $usuario = unserialize($_SESSION['usuario']);
    } else
        $usuario = null;
    return $usuario;
}

/**
 * Grava na sessão o Objeto do tipo Usuário com os dados
 * 
 * @param Usuario $user
 */
function setUsuarioSessao($user) {
    $usuario = new Usuario();
    $usuario->setUsu_var_identificador($user['identificador']);
    $usuarioDao = new UsuarioDao();
    $usuario = $usuarioDao->selectByIdentificador($usuario);
    if (!is_null($usuario->getUsu_var_nome())) {
        unset($_SESSION["usuario"]);
        $_SESSION["usuario"] = serialize($usuario);
        $usuarioDao->carregarPermissoes();
    }
}

/**
 * Altera o perfil do usuário da sessão
 * 
 * @param int $pef_int_codigo
 */
function setPerfilSessao($pef_int_codigo) {
    if (is_null($pef_int_codigo)) {
        unset($_SESSION["pef_int_codigo"]);
    } else {
        $usuario = getUsuarioSessao();
        if (!is_null($usuario)) {
            unset($_SESSION["pef_int_codigo"]);
            unset($_SESSION["usuario"]);
            $perfil = new Perfil();
            $perfil->setPef_int_codigo($pef_int_codigo);
            if (!is_null($pef_int_codigo)) {
                $perfilDao = new PerfilDao();
                $perfil = $perfilDao->selectById($perfil);
                $_SESSION["pef_int_codigo"] = $pef_int_codigo;
            }
            $usuario->setPerfil($perfil);
            $_SESSION["usuario"] = serialize($usuario);
            $usuarioDao = new UsuarioDao();
            $usuarioDao->carregarPermissoes();
        }
    }
}

/**
 * Retorna o código do perfil que está na sessão, se não existir, retorna null
 * 
 * @return int
 */
function getPerfilSessao() {
    return (isset($_SESSION['pef_int_codigo'])) ? $_SESSION['pef_int_codigo'] : null;
}

/**
 * Busca o código do usuário de acordo com a sessão que está aberta;
 * @return int
 */
function getSessao() {
    $mysql = new GDbMysql();
    return $mysql->executeValue("SELECT usu_int_codigo FROM usuario WHERE usu_var_sessao = ?;", array("s", session_id()));
}

/**
 * Busca o código de ssessão de acordo com usuário passado;
 * @return int
 */
function getSessaoByUsuario($usu_int_codigo) {
    $mysql = new GDbMysql();
    return $mysql->executeValue("SELECT usu_var_sessao FROM usuario WHERE usu_int_codigo = ?;", array("i", $usu_int_codigo));
}

/**
 * Grava no banco de dados, o codigo da sessão e a data e hora do ultimo acesso do usuário;
 * 
 * @param int $usu_int_codigo
 */
function setSessao($usu_int_codigo, $sair = false) {
    $mysql = new GDbMysql();
    if (isset($usu_int_codigo)) {
        if ($sair) {
            $mysql->execute("UPDATE usuario SET usu_var_sessao = NULL, usu_dti_ultimo = CURRENT_TIMESTAMP WHERE usu_int_codigo = ?;", array("i", $usu_int_codigo), false);
        } else {
            $mysql->execute("UPDATE usuario SET usu_var_sessao = ?, usu_dti_ultimo = CURRENT_TIMESTAMP WHERE usu_int_codigo = ?;", array("si", session_id(), $usu_int_codigo), false);
        }
        global $__arrayNaoGravarAcesso;
        if (!in_array($_SERVER["REQUEST_URI"], $__arrayNaoGravarAcesso)) {
            $mysql->execute("INSERT INTO acesso (ace_dti_criacao, ace_int_usuario, ace_var_ip, ace_var_sessao, ace_var_server, ace_var_url, ace_txt_request, ace_var_agent, ace_txt_json) VALUES (NOW(),?,?,?,?,?,?,?,?);", array("isssssss", $usu_int_codigo, getClientIP(), session_id(), $_SERVER["SERVER_ADDR"], $_SERVER["REQUEST_URI"], json_encode($_REQUEST), $_SERVER["HTTP_USER_AGENT"], json_encode($_SERVER)), false);
        }
    }
}

/**
 * Buscar o Ip do cliente
 * 
 * @return string
 */
function getClientIP() {
    if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    } else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
        return $_SERVER["REMOTE_ADDR"];
    } else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    return '0.0.0.0';
}

/**
 * Verifica se o usuário logado é Luiz
 * 
 * @return bool
 */
function ehLuiz() {
    $__usuario = getUsuarioSessao();
    if ($__usuario->getUsu_var_email() == 'luiz.eduardo.as@gmail.com') {
        return true;
    }
    return false;
}

/**
 * Busca o código da empresa que está na sessão. Se não existir, retorna zero
 * 
 * @return int
 */
function buscarCodigoEmpresaSessao() {
    $emp_int_codigo = 0;
    $empresaSessao = getEmpresaSessao();
    if (is_object($empresaSessao)) {
        $emp_int_codigo = $empresaSessao->getEmp_int_codigo();
    }
    return $emp_int_codigo;
}

/**
 * Busca o ientificador da empresa que está na sessão. Se não existir, retorna zero
 * 
 * @return int
 */
function buscarIdentificadorEmpresaSessao() {
    $emp_var_identificador = 0;
    $empresaSessao = getEmpresaSessao();
    if (is_object($empresaSessao)) {
        $emp_var_identificador = $empresaSessao->getEmp_var_identificador();
    }
    return $emp_var_identificador;
}

// </editor-fold>
// 
// 
// 
// <editor-fold defaultstate="collapsed" desc="Arquivo">
/**
 * Remove um diretório com todos os arquivos dentro
 *
 * @param String $dir
 */
function remover_pasta($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir")
                    remover_pasta($dir . "/" . $object);
                else
                    unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

/**
 * Dar permissao no diretório para todos os usuários no FTP
 *
 * @param String $dir
 */
function permissao($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir")
                    permissao($dir . "/" . $object);
                else
                    chmod($dir . "/" . $object, 0777);
            }
        }
        reset($objects);
        chmod($dir, 0777);
    }
}

/**
 * Retorna a string vazia ou com a palavra .min para carregar os CSS e JavaScripts minimizados
 * 
 * @return string
 */
function getMinify() {
    return (SYS_MINIFY_TEMA) ? '.min' : '';
}

/**
 * Exclui um arquivo $arq de uma pasta $path, contidas na pasta uploads no servidor
 *
 * @param String $path
 * @param String $arq
 */
function deleteUpload($path, $arq) {
    if (($arq != "unknown.jpg") && ($arq != "unknown.png")) {
        if (file_exists(ROOT_UPLOAD . $path . "/" . $arq)) {
            @unlink(ROOT_UPLOAD . $path . "/" . $arq);
        }
    }
}

/**
 * Exclui um arquivo $arq de uma pasta $path, contidas na pasta uploads no servidor
 * e no S3 da Amazon
 * 
 * @param String $path
 * @param String $arq
 */
function deleteUploadS3($path, $arq) {
    if (($arq != "unknown.jpg") && ($arq != "unknown.png")) {
        require_once(ROOT_SYS_INC . "aws/AwsS3.php");
        $s3 = new AwsS3();
        if ($s3->Existe($path . "/" . $arq)) {
            $s3->Apagar($path . "/" . $arq);
            @unlink(ROOT_UPLOAD . $path . "/" . $arq);
        }
    }
}

/**
 * Retorna o arquivo com a url pronta para exibir, validando se existe o arquivo, senão exibe unknown
 * 
 * @param string $path
 * @param string $arq
 * @param string $tamanho
 * @return string
 */
function setUpload($path, $arq, $tamanho = '') {
    $retorno = '';
    if ((!is_null($arq)) && ($arq != '') && (GF::seExiste(URL_UPLOAD . $path . "/" . $arq))) {
        switch (strtoupper($tamanho)) {
            case "P":
                $retorno = pathinfo($arq, PATHINFO_FILENAME) . '_p.' . pathinfo($arq, PATHINFO_EXTENSION);
                break;
            case "M":
                $retorno = pathinfo($arq, PATHINFO_FILENAME) . '_m.' . pathinfo($arq, PATHINFO_EXTENSION);
                break;
            default:
                $retorno = $arq;
                break;
        }
    } else {
        switch (strtoupper($tamanho)) {
            case "P":
                $retorno = "unknown_p.png";
                break;
            case "M":
                $retorno = "unknown_m.png";
                break;
            default:
                $retorno = "unknown.png";
                break;
        }
    }

    return URL_UPLOAD . $path . "/" . $retorno;
}

/**
 * Formata a url de um arquivo retirando toda url e deixando somente o nome do aruqivo
 * 
 * @param string $path
 * @param string $arq
 * @return string
 */
function formataArquivoURL($path, $arq) {
    return str_replace(URL_UPLOAD . $path . "/", "", $arq);
}

/**
 * Formata uma string de url de um arquivo retirando o endereço e deixando apenas o nome do arquivo
 * 
 * @param string $path
 * @param string $arq
 * @return string
 */
function formataArquivoURLS3($path, $arq) {
    if (strpos($arq, "?")) {
        $arr = explode("?", $arq);
    } else {
        $arr[0] = $arq;
    }
    return str_replace(URL_IMAGENS . $path . "/", "", $arr[0]);
}

/**
 * Formata a string de endereço de um arquivo para adicionar a data de ultima modificação como parâmetro na url,
 * assim o arquivo não fará cache
 * 
 * @param string $tipo
 * @param string $arquivo
 * @return string
 */
function formatarImagemS3($tipo, $arquivo) {
    require_once(ROOT_SYS_INC . "aws/AwsS3.php");
    $s3 = new AwsS3();
    $ret_get = $s3->Buscar($tipo . '/' . formataArquivoURLS3($tipo, $arquivo), 'LastModified');
    return $arquivo . '?' . formatarLastModified($ret_get["LastModified"]);
}

/**
 * Formata uma string com data hora minutos e segundos para uma string somente numérica
 * 
 * @param string $str
 * @return string
 */
function formatarLastModified($str) {
    return substr(formatarSomenteNumeros($str), 0, 14);
}

/**
 * Exclui o arquivo do servidor
 * 
 * @param String $arq
 */
function deleteImg($arq) {
    if (file_exists($arq))
        unlink($arq);
}

/**
 * Girar imagem em graus passado por parâmetro
 * 
 * @param string $folder
 * @param string $filename
 * @param int $graus
 */
function girarImagem($folder, $filename, $graus = 90) {
    criarArquivoTemp($folder, $filename);

    ini_set('memory_limit', '512M');
    GF::carregarLib(array("imagem"));

    $src = ROOT_UPLOAD . 'tmp/' . $filename;

    $img = new imaging();
    $img->set_quality(100);
    $img->set_img($src);
    $img->rotate_img($graus);

    enviarArquivoTemp($folder, $filename);
}

/**
 * Verifica se o arquivo passado existe e dar um include, senão existir dar um include na página ops.php
 * 
 * @param string $arq
 * @param bool $site Se for o mesmo ops para o site
 */
function ifIncludeOnce($arq, $site = false) {
    if (trim($arq) != '') {
        if (file_exists($arq)) {
            include_once($arq);
        } else {
            if ($site)
                require_once(__DIR__ . "/../ops.php");
            else
                require_once(__DIR__ . "/../ops.php");
        }
    } else
        require_once(__DIR__ . "/../ops.php");
}

// </editor-fold>
// 
// 
// 
// <editor-fold defaultstate="collapsed" desc="Banco de dados">
/**
 * Gerar Sql Case de acordo com um array
 *
 * @param String $campo
 * @param Array $array
 * @param Boolean $somenteFormat Default: true
 * @return String
 */
function gerarCase($campo, $array, $somenteFormat = true, $renomear = true) {
    $return = ($somenteFormat) ? "CASE " . $campo : $campo . ",CASE " . $campo;
    foreach ($array as $key => $value) {
        $return .= " WHEN '$key' THEN '$value'";
    }
    $return .= " WHEN '' THEN 'Não Informado'";
    $return .= " WHEN NULL THEN 'Não Informado'";
    $arr = explode(".", $campo);
    if ($renomear) {
        $rename = (count($arr) > 1) ? $arr[1] : $campo;
        $return .= ( $somenteFormat) ? " ELSE NULL END as " . $rename : " ELSE NULL END as " . $rename . "_format";
    } else {
        $return .= ( $somenteFormat) ? " ELSE NULL END " : " ELSE NULL END ";
    }

    return $return;
}

/**
 * Gerar Sql Date_format
 *
 * @param String $campo Ex: usu_dti_criacao
 * @param Boolean $somenteFormat Default: true
 * @param String $format Deafult: '%d/%m/%Y %H:%i'
 * @return string
 */
function gerarDate_format($campo, $somenteFormat = true, $format = '%d/%m/%Y %H:%i') {
    if ($somenteFormat)
        $return = "DATE_FORMAT(" . $campo . ", '" . $format . "') as " . $campo;
    else
        $return = $campo . ",DATE_FORMAT(" . $campo . ", '" . $format . "') as " . $campo . "_format";

    return $return;
}

/**
 * Gerar Sql de Concat para formatar do tipo Money
 *
 * @param String $campo EX: pro_dec_valor
 * @param Boolean $somenteFormat Default: true
 * @return string
 */
function gerarConcatMoney($campo, $somenteFormat = true, $rename = null) {
    $rename = $rename ?? $campo;
    if ($somenteFormat)
        $return = "CONCAT('R$ ',REPLACE(" . $campo . ",'.',',')) as " . $rename;
    else
        $return = $campo . ",CONCAT('R$ ',REPLACE(" . $campo . ",'.',',')) as " . $rename . "_format";

    return $return;
}

/**
 * Gerar Sql de Concat para formatar do tipo Money
 *
 * @param String $campo EX: pro_dec_valor
 * @param Boolean $somenteFormat Default: true
 * @return string
 */
function gerarConcatMoneyFormatado($campo, $somenteFormat = true, $rename = null) {
    $rename = $rename ?? $campo;
    if ($somenteFormat)
        $return = "CONCAT('R$ '," . $campo . ") as " . $rename;
    else
        $return = $campo . ",CONCAT('R$ '," . $campo . ") as " . $rename . "_format";

    return $return;
}

/**
 * Gerar Sql de Concat para formatar do tipo Decimal
 *
 * @param String $campo EX: pro_dec_valor
 * @param Boolean $somenteFormat Default: true
 * @return string
 */
function gerarConcatDecimal($campo, $somenteFormat = true) {
    if ($somenteFormat)
        $return = "REPLACE(" . $campo . ",'.',',') as " . $campo;
    else
        $return = $campo . ",REPLACE(" . $campo . ",'.',',') as " . $campo . "_format";

    return $return;
}

/**
 * Gerar Sql de Concat para formatar do tipo Percentual
 *
 * @param String $campo EX: pro_dec_valor
 * @param Boolean $somenteFormat Default: true
 * @return string
 */
function gerarConcatPercentual($campo, $somenteFormat = true, $rename = null) {
    $rename = $rename ?? $campo;
    if ($somenteFormat)
        $return = "CONCAT(REPLACE(REPLACE(" . $campo . ",'.',','),',00',''), '%') as " . $rename;
    else
        $return = $campo . ",CONCAT(REPLACE(REPLACE(" . $campo . ",'.',','),',00',''), '%') as " . $rename . "_format";

    return $return;
}

/**
 * Gerar Sql de Concat para formatar
 *
 * @param String $campo EX: pro_dec_valor
 * @param String $inicio
 * @param String $fim
 * @param Boolean $somenteFormat Default: true
 * @return string
 */
function gerarConcat($campo, $inicio = '', $fim = '', $somenteFormat = true) {
    if ($somenteFormat)
        $return = "CONCAT('" . $inicio . "'," . $campo . ",'" . $fim . "') as " . $campo;
    else
        $return = $campo . ",CONCAT('" . $inicio . "'," . $campo . ",'" . $fim . "') as " . $campo . "_format";

    return $return;
}

/**
 * Gerar Sql de Concat com campos de uma array
 *
 * @param String $nome EX: extra
 * @param Array $array
 * @param String $separador
 * @return string
 */
function gerarConcatArray($nome, $array, $separador = ';') {
    $return = "CONCAT(";
    $qtd = count($array);
    $i = 0;
    foreach ($array as $campo) {
        $i++;
        if ($i < $qtd) {
            $return .= $campo . ", '" . $separador . "', ";
        } else {
            $return .= $campo;
        }
    }
    $return .= ") as " . $nome;

    return $return;
}

/**
 * Gerar Sql de Replace para formatar
 *
 * @param String $campo EX: pro_dec_valor
 * @param String $procura
 * @param String $substitui
 * @param Boolean $somenteFormat Default: true
 * @return string
 */
function gerarReplace($campo, $procura = '', $substitui = '', $somenteFormat = true) {
    if ($somenteFormat)
        $return = "REPLACE(" . $campo . ",'" . $procura . "','" . $substitui . "') as " . $campo;
    else
        $return = $campo . ",REPLACE(" . $campo . ",'" . $procura . "','" . $substitui . "') as " . $campo . "_format";

    return $return;
}

/**
 * Buscar parametro de acordo com uma chave
 * 
 * @param string $chave Chave do parâmetro buscado
 * @param string $seVazio default null - retorna o valor informado quando não existir o parametro
 * @return string
 */
function buscarParametro($chave, $seVazio = null) {
    $retorno = false;
    $mysql = new GDbMysql();
    $mysql->execute("SELECT par_txt_valor FROM parametro WHERE par_var_chave = ?;", array("s", $chave));
    if ($mysql->fetch()) {
        $retorno = $mysql->res["par_txt_valor"];
    }
    return seNuloOuVazio($retorno) ? $seVazio : $retorno;
}

/**
 * Alterar parametro de acordo com uma chave
 * 
 * @param string $chave Chave do parâmetro buscado
 * @param string $valor Valor a ser alterado
 */
function alterarParametro($chave, $valor) {
    $mysql = new GDbMysql();
    $mysql->execute("SELECT par_txt_valor FROM parametro WHERE par_var_chave = ?;", array("s", $chave));
    if ($mysql->fetch()) {
        $mysql->execute("UPDATE parametro SET par_txt_valor = ?, par_dti_atualizacao = NOW(), usu_int_codigo = ? WHERE par_var_chave = ?;", array("sis", $valor, getUsuarioSessao()->getUsu_int_codigo(), $chave), false);
    }
}

/**
 * Buscar o perfil de um código de usuario
 * 
 * @param int $usu_int_codigo Código do Usuário
 * @return int
 */
function buscarPerfil($usu_int_codigo) {
    $retorno = false;
    $mysql = new GDbMysql();
    $mysql->execute("SELECT pef_int_codigo FROM usuario WHERE usu_int_codigo = ?;", array("i", $usu_int_codigo));
    if ($mysql->fetch()) {
        $retorno = $mysql->res["pef_int_codigo"];
    }
    return $retorno;
}

/**
 * Buscar o nome de um código de usuario
 * 
 * @param int $usu_int_codigo Código do Usuário
 * @return int
 */
function buscarNomeUsuario($usu_int_codigo) {
    $retorno = false;
    $mysql = new GDbMysql();
    $mysql->execute("SELECT usu_var_nome FROM usuario WHERE usu_int_codigo = ?;", array("i", $usu_int_codigo));
    if ($mysql->fetch()) {
        $retorno = $mysql->res["usu_var_nome"];
    }
    return $retorno;
}

/**
 * Retorna se a permissão passada está liberada para o perfil informado
 * 
 * @param string $permissao EX: 'HOME'
 * @param int $perfil Ex: 1
 * @return bool
 */
function verificaPermissaoPerfil($permissao, $perfil) {
    $mysql = new GDbMysql();
    $mysql->execute("SELECT pem_var_codigo FROM perfil_permissao WHERE pem_var_codigo = ? AND pef_int_codigo = ?;", array("si", $permissao, $perfil));
    return ($mysql->fetch()) ? true : false;
}

/**
 * Salvar evento com os dados
 * 
 * @param string $tipo E: Erro; S: Sucesso; A: Atenção
 * @param string $titulo Título do evento ocorrido
 * @param string $dados Dados do evento
 */
function salvarEvento($tipo, $titulo, $dados) {
    $eve_int_usuario = (isset($_SESSION['usuario'])) ? getUsuarioSessao()->getUsu_int_codigo() : null;
    $eve_var_identificador = (isset($_SESSION['usuario'])) ? getUsuarioSessao()->getUsu_var_identificador() : null;
    if (strlen($titulo) > 250) {
        $dados = $titulo . ' || ' . $dados;
        if (substr($titulo, 0, 20) == '<div class="__erro">') {
            $posIni = strpos($titulo, '<br>Mensagem: <b><span style="color:red">');
            $posFim = strpos($titulo, '</span>');
            if ($posIni === false) {
                $titulo = $titulo;
            } else {
                $titulo = substr($titulo, ($posIni + 4), $posFim);
            }
        }
    }
    if (MYSQL_WRITE == "ON") {
        try {
            $mysqlEve = new GDbMysql();
            $mysqlEve->execute("INSERT INTO evento (eve_dti_criacao, eve_var_titulo, eve_cha_tipo, eve_txt_dados, eve_int_usuario, eve_var_identificador) VALUES (NOW(),?,?,?,?,?);", array("sssis", substr($titulo, 0, 250), $tipo, stripslashes($dados), $eve_int_usuario, $eve_var_identificador), false);
            return $mysqlEve->insertId();
        } catch (GDbException $e) {
            echo '<pre>';
            var_dump($e->getTraceAsString());
            echo '</pre>';
        }
    } else
        return 0;
}

/**
 * faz uma consulta no banco de dados através dos parâmentros passados, retornando os valores agrupados por virgula.
 * 
 * @param string $group
 * @param string $tabela
 * @param string $chave
 * @param string $valor
 * @return string
 */
function formatarGroupConcat($group, $tabela, $chave, $valor) {
    $retorno = '';
    try {
        $mysql = new GDbMysql();
        $mysql->execute("SELECT GROUP_CONCAT(" . $valor . ") FROM " . $tabela . " WHERE " . $chave . " IN (" . $group . ")");
        if ($mysql->fetch()) {
            $retorno = $mysql->res[0];
        }
        $mysql->close();
    } catch (GDbException $e) {
        echo $e->getError();
    }
    return $retorno;
}

/**
 * Retorna o objeto $mysql com os dados de uma consulta de relatório genérico de acordo com o os filtros do $_POST
 * 
 * @param Relatorio $relatorio
 * @param $_POST $post
 * @return array
 */
function getDadosRelatorio($relatorio, $post) {
    $arrReturn = array("status" => false, "msg" => "Nenhum dado encontrado.");

    GF::import(array("relatorio", "filtro"));

    $filtro = new Filtro();
    $filtroDao = new FiltroDao();
    $arrFiltros = $filtroDao->selectByRelatorio($relatorio);

    $indices = "";
    $param = array();
    $where = "";
    foreach ($arrFiltros as $filtro) {

        $identificador = getPosicaoSplit($filtro->getFil_var_identificador(), '.', 1);
        if (!seNuloOuVazioOuMenosUm($_POST[$identificador])) {
            switch ($filtro->getFil_cha_tipo()) {
                case "DAT":
                case "DTI":
                    $where .= " AND (" . $filtro->getFil_var_identificador() . " = ?) ";
                    $indices .= "s";
                    $param = array_merge($param, array($_POST[$identificador]));
                    break;
                case "TXT":
                    $where .= " AND (" . $filtro->getFil_var_identificador() . " LIKE ?) ";
                    $indices .= "s";
                    $param = array_merge($param, array('%' . $_POST[$identificador] . '%'));
                    break;
                case "DTR":
                    $arrData = explode(" - ", $_POST[$identificador]);
                    $where .= " AND (" . $filtro->getFil_var_identificador() . " BETWEEN ? AND ?) ";
                    $indices .= "ss";
                    $param = array_merge($param, array(GF::formatarData($arrData[0]) . ' 00:00:00', GF::formatarData($arrData[1]) . ' 23:59:59'));
                    break;
                case "SPN":
                    $where .= " AND (" . $filtro->getFil_var_identificador() . " = ?) ";
                    $indices .= "i";
                    $param = array_merge($param, array($_POST[$identificador]));
                    break;
                default:
                    break;
            }
        }
    }
    array_unshift($param, $indices);
//    echo '<pre>';
//    var_dump($where);
//    var_dump($param);
//    echo '</pre>';

    try {
        $mysql = new GDbMysql();
        if (seNuloOuVazio($where)) {
            $mysql->execute($relatorio->getRel_txt_query());
        } else {
            $mysql->execute($relatorio->getRel_txt_query() . $where, $param);
        }
        if ($mysql->numRows()) {

            $arrTitulos = array();
            $arrDados = array();

            $arrProjecoes = explode(";", $relatorio->getRel_txt_projecoes());
            foreach ($arrProjecoes as $projecao) {
                list($k, $v) = explode(":", $projecao);
                $arrTitulos[] = $v;
            }

            while ($mysql->fetch()) {
                $arr = array();
                foreach ($arrProjecoes as $projecao) {
                    list($k, $v) = explode(":", $projecao);
                    $arr[$v] = $mysql->res[$k];
                }
                $arrDados[] = $arr;
            }
            $arrReturn["status"] = true;
            $arrReturn["msg"] = $mysql->numRows() . " Registros encontrados.";
            $arrReturn["titulos"] = $arrTitulos;
            $arrReturn["dados"] = $arrDados;
        }
        $mysql->close();
    } catch (GDbException $e) {
        $arrReturn["status"] = false;
        $arrReturn["msg"] = $e->getError();
    }

    return $arrReturn;
}

/**
 * Gerar uma parte de uma query contendo a formatação de LIKE dos campos passado
 * 
 * @param boolean $logic
 * @param array $arrFields
 * @param array $valueParam
 * @return string
 */
function montarQueryWhereLike($logic, $arrFields, $valueParam) {
    $where = array();
    $arrValues = explode(" ", $valueParam);
    $where[] = $logic . ' ( ';
    $or = false;
    $and = false;
    foreach ($arrFields as $field) {
        if ($or) {
            $where[] = ' OR ';
        }
        $or = true;
        $where[] = ' ( ';
        $and = false;
        foreach ($arrValues as $value) {
            if ($and) {
                $where[] = ' AND ';
            }
            $and = true;
            $where[] = " UPPER(" . $field . ") LIKE UPPER('%" . $value . "%') ";
        }
        $where[] = ' ) ';
    }
    $where[] = ' ) ';
    return implode("", $where);
}

/**
 * Registra na tabela de processamento o que aconteceu com o agendamento
 * 
 * @param int $age_int_codigo
 * @param array $arrDados
 * @param string $usu_var_identificador
 * @return array
 */
function registrarProcessamento($age_int_codigo, $status, $arrDados, $usu_var_identificador) {
    GF::import(array("agendamento", "processamento"));

    $agendamento = new Agendamento();
    $agendamento->setAge_int_codigo($age_int_codigo);

    $processamento = new Processamento();
    $processamento->setAgendamento($agendamento);
    $processamento->setPro_cha_status((($status) ? 'S' : 'E'));
    $processamento->setPro_cha_tipo('M');
    $processamento->setPro_var_url($arrDados["url"]);
    $processamento->setPro_txt_enviado($arrDados["enviado"]);
    $processamento->setPro_txt_recebido($arrDados["recebido"]);
    $processamento->setPro_var_usuario($usu_var_identificador);
    $processamentoDao = new ProcessamentoDao();

    return $processamentoDao->insert($processamento);
}

/**
 * Carrega em um array, toda estrutura de níveis para montar uma arvore
 * 
 * @return array
 */
function montarArvore() {
    GF::import(array("nivel"));

    $nivel = new Nivel();
    $nivelDao = new NivelDao();
    $arr = $nivelDao->select("WHERE niv_int_nivel = 1 ORDER BY niv_var_nome;");
    $lista = array();
    if (count($arr)) {
        foreach ($arr as $nivel) {
            $nivelDao2 = new NivelDao();
            $lista[$nivel->getNiv_var_identificador()] = array("text" => $nivel->getNiv_var_nome(), "type" => $nivelDao2->ifExistsFilho($nivel) ? "folder" : "item");
            if ($nivel->getNiv_cha_visivel() == 'N')
                $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["class"] = 'tree-branch-disabled';
            $arr2 = $nivelDao2->select("WHERE niv_int_nivel = 2 AND niv_var_identificador_pai = ? ORDER BY niv_var_nome;", array("i", $nivel->getNiv_var_identificador()));
            if (count($arr2)) {
                foreach ($arr2 as $nivel2) {
                    $nivelDao3 = new NivelDao();
                    $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel2->getNiv_var_identificador()] = array("text" => $nivel2->getNiv_var_nome(), "type" => $nivelDao3->ifexistsfilho($nivel2) ? "folder" : "item");
                    if ($nivel2->getNiv_cha_visivel() == 'N')
                        $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel2->getNiv_var_identificador()]["additionalParameters"]["class"] = 'tree-branch-disabled';
                    $arr3 = $nivelDao3->select("WHERE niv_int_nivel = 3 AND niv_var_identificador_pai = ? ORDER BY niv_var_nome;", array("i", $nivel2->getNiv_var_identificador()));
                    if (count($arr3)) {
                        foreach ($arr3 as $nivel3) {
                            $nivelDao4 = new NivelDao();
                            $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel2->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel3->getNiv_var_identificador()] = array("text" => $nivel3->getNiv_var_nome(), "type" => $nivelDao4->ifexistsfilho($nivel3) ? "folder" : "item");
                            if ($nivel3->getNiv_cha_visivel() == 'N')
                                $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel2->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel3->getNiv_var_identificador()]["additionalParameters"]["class"] = 'tree-branch-disabled';
                            $arr4 = $nivelDao4->select("WHERE niv_int_nivel = 4 AND niv_var_identificador_pai = ? ORDER BY niv_var_nome;", array("i", $nivel3->getNiv_var_identificador()));
                            if (count($arr4)) {
                                foreach ($arr4 as $nivel4) {
                                    $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel2->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel3->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel4->getNiv_var_identificador()] = array("text" => $nivel4->getNiv_var_nome(), "type" => "item");
                                    if ($nivel4->getNiv_cha_visivel() == 'N')
                                        $lista[$nivel->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel2->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel3->getNiv_var_identificador()]["additionalParameters"]["children"][$nivel4->getNiv_var_identificador()]["additionalParameters"]["class"] = 'tree-branch-disabled';
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    return $lista;
}

/**
 * Retorna um array para usar como combobox de listagem de níveis
 * 
 * @return array
 */
function carregarComboNiveis() {
    $mysql = new GDbMysql();
    //return $mysql->executeCombo("SELECT niv_int_codigo, CONCAT(CASE niv_cha_visivel WHEN 'N' THEN '###' ELSE '' END, niv_var_hierarquia) FROM nivel ORDER BY niv_var_hierarquia, niv_int_nivel;");
    return $mysql->executeCombo("SELECT niv_int_codigo, niv_var_hierarquia FROM nivel ORDER BY niv_var_hierarquia, niv_int_nivel;");
}

/**
 * Retorna a quantidade de cursos existentes
 * 
 * @return int
 */
function locaQtdCursos() {
    $mysql = new GDbMysql();
    return $mysql->executeValue("SELECT COUNT(*) FROM ava_curso;");
}

// </editor-fold>
// 
// 
// 
// <editor-fold defaultstate="collapsed" desc="String">

/**
 * Verifica se a string passada está nulla ou vazia 
 * 
 * @param String $dado
 * @return bool
 */
function seNuloOuVazio($dado) {
    if (is_object($dado)) {
        return false;
    }
    return ((is_null($dado)) || (trim($dado) == ""));
}

/**
 * Verifica se a string passada é vazia ou null
 * 
 * @param string $dado
 * @return string
 */
function seVazioOuNulo($dado) {
    return (is_null($dado) || trim($dado) == "");
}

/**
 * Valida se o valor passado é nulo ou vazio ou zero
 * 
 * @param string $dado
 * @return boolean
 */
function seNuloOuVazioOuZero($dado) {
    if ((is_null($dado)) || (trim($dado) == "") || (trim($dado) == "0"))
        return true;
    else
        return false;
}

/**
 * Valida se o valor passado é nulo ou vazio ou zero ou menos 1
 * 
 * @param string $dado
 * @return boolean
 */
function seNuloOuVazioOuZeroOuMenosUm($dado) {
    if (!isset($dado) || (is_null($dado)) || (trim($dado) == "") || (trim($dado) == "0") || (trim($dado) == "-1"))
        return true;
    else
        return false;
}

/**
 * Valida se o valor passado é nulo ou vazio ou menos 1
 * 
 * @param string $dado
 * @return boolean
 */
function seNuloOuVazioOuMenosUm($dado) {
    if ((is_null($dado)) || (trim($dado) == "") || (trim($dado) == "-1"))
        return true;
    else
        return false;
}

/**
 * Valida se a data passada é vazia
 * 
 * @param string $dado
 * @return boolean
 */
function seDataVazia($dado) {
    if ((is_null($dado)) || (trim($dado) == "") || ($dado == '0000-00-00') || ($dado == '0000-00-00 00:00:00') || ($dado == '0000-00-00 00:00') || ($dado == '00/00/0000') || ($dado == '00/00/0000 00:00:00') || ($dado == '00/00/0000 00:00'))
        return true;
    else
        return false;
}

/**
 * Valida se a data passada é vazia e retorna null caos seja
 * 
 * @param string $dado
 * @return boolean
 */
function seDataVaziaRetorneNulo($dado) {
    if ((is_null($dado)) || (trim($dado) == "") || ($dado == '0000-00-00') || ($dado == '0000-00-00 00:00:00') || ($dado == '0000-00-00 00:00') || ($dado == '00/00/0000') || ($dado == '00/00/0000 00:00:00') || ($dado == '00/00/0000 00:00'))
        return null;
    else
        return $dado;
}

/**
 * Verifica se a string passada está vazia e retorna null, 
 * senão verifica se existe objeto para executar, 
 * senão retorna a própria string
 * 
 * @param String $dado
 * @param Objeto $execute
 * @return string
 */
function seVazioRetorneNulo($dado, $execute = null) {
    $return = $dado;
    if (!isset($dado) || trim($dado) == "")
        $return = null;
    elseif ($execute != null) {
        $return = eval($execute . ';');
    }

    return $return;
}

/**
 * Verifica se a string passada está vazia ou é string NULL e retorna null, 
 * 
 * @param String $dado
 * @return string
 */
function seVazioOuTextoNULLRetorneNulo($dado) {
    $return = $dado;
    if (trim($dado) == "" || maiusculo(trim($dado)) == "NULL")
        $return = null;

    return $return;
}

/**
 * Verifica se a string passada está vazia ou é string NULL e retorna null, 
 * 
 * @param String $dado
 * @return string
 */
function seVazioOuTextoNULLRetorneVazio($dado) {
    $return = $dado;
    if (trim($dado) == "" || maiusculo(trim($dado)) == "NULL")
        $return = "";

    return $return;
}

/**
 * Verifica se a string passada está vazia ou é string NULL e retorna null, 
 * 
 * @param String $dado
 * @return string
 */
function seVazioOuTextoNULLRetorneMenosUm($dado) {
    $return = $dado;
    if (trim($dado) == "" || maiusculo(trim($dado)) == "NULL")
        $return = "-1";

    return $return;
}

/**
 * Verifica se a string passada está vazia e retorna zero, 
 * senão retorna a própria string
 * 
 * @param String $dado
 * @return string
 */
function seVazioRetorneZero($dado) {
    $return = $dado;
    if (strlen(trim($dado)) == 0)
        $return = 0;

    return $return;
}

/**
 * Verifica se a string passada está nulla ou vazia e retorna zero, 
 * senão retorna a própria string
 * 
 * @param String $dado
 * @return string
 */
function seNuloOuVazioRetorneZero($dado) {
    if ((is_null($dado)) || (trim($dado) == ""))
        $dado = 0;

    return $dado;
}

/**
 * Verifica se a string passada está vazia e retorna null, 
 * se a string for '-1' também retorna null, 
 * senão retorna a própria string
 * 
 * @param String $dado
 * @return string
 */
function seVazioOuMenosUmRetorneNulo($dado) {
    $return = $dado;
    if (strlen(trim($dado)) == 0)
        $return = null;
    if ($dado == "-1")
        $return = null;

    return $return;
}

/**
 * Verifica se a string passada esta vazia e retorna "-",
 * senão retorna a própria string
 * 
 * @param String $dado
 * @param String $preencher Default FALSE
 * @return string
 */
function formataDadoVazio($dado, $preencher = false, $center = false) {
    $return = ($preencher) ? $preencher : $dado;
    if (seNuloOuVazio($dado) || $dado === '01/01/1900' || $dado === '00/00/0000' || $dado === '00/00/0000 00:00' || $dado === '00:00' || $dado === '00:00:00' || $dado === 'R$ ' || trim(GF::retirarEspeciais($dado)) === '') {
        $return = $center ? "<center>-</center>" : "-";
    } else {
        if ($dado == '<i class="fa fa-ban"></i>') {
            $return = '<center><i class="fa fa-ban"></i></center>';
        }
    }
    return $return;
}

/**
 * Verifica se a string passada esta vazia e a variável $preencher,
 * senão retorna a própria string
 * 
 * @param string $dado
 * @param string $preencher
 * @return string
 */
function formataNvl($dado, $preencher) {
    $return = $dado;
    if (seNuloOuVazio($dado))
        $return = $preencher;
    return $return;
}

/**
 * Verifica se a string passada esta vazia ou é menos um e a variável $preencher,
 * senão retorna a própria string
 * 
 * @param string $dado
 * @param string $preencher
 * @return string
 */
function formataNvlOuMenosUm($dado, $preencher) {
    $return = $dado;
    if (seNuloOuVazioOuMenosUm($dado))
        $return = $preencher;
    return $return;
}

/**
 * Verifica se a string passada esta vazia e a variável $seVazio,
 * senão retorna a string $senaoVazio
 * 
 * @param string $dado
 * @param string $seVazio
 * @param string $senaoVazio 
 * @return string
 */
function formataNvl2($dado, $seVazio, $senaoVazio) {
    if (seNuloOuVazio($dado))
        $return = $seVazio;
    else
        $return = $senaoVazio;
    return $return;
}

/**
 * Verifica se a chave passada contém no cookie,
 * senão retorna null ou o valor passado no parametro $seVazio
 * 
 * @param String $dado
 * @param String $seVazio Default FALSE
 * @return string
 */
function buscarCookie($key, $seVazio = false) {
    $return = ($seVazio) ? $seVazio : null;
    if (isset($_COOKIE[$key]))
        $return = $_COOKIE[$key];
    return $return;
}

/**
 * retorna somente a data de uma data com hora
 * 
 * @param string $data
 * @return string
 */
function somenteData($data) {
    $return = $data;
    if (trim($data) == "") {
        $return = "-";
    } else {
        $arr = explode(" ", $data);
        if (count($arr) >= 2) {
            $return = $arr[0];
        } else {
            $return = "-";
        }
    }
    return $return;
}

/**
 * retorna somente a hora de uma data com hora
 * 
 * @param string $data
 * @return string
 */
function somenteHora($data) {
    $return = $data;
    if (trim($data) == "") {
        $return = "-";
    } else {
        $arr = explode(" ", $data);
        if (count($arr) == 2) {
            $return = $arr[1];
        } else {
            $return = "-";
        }
    }
    return $return;
}

/**
 * retorna somente a data sem o ano e com hora
 * 
 * @param string $data
 * @return string
 */
function dataSemAno($data) {
    $return = $data;
    if (trim($data) == "") {
        $return = "-";
    } else {
        $arr = explode(" ", $data);
        if (count($arr) == 2) {
            $return = substr($arr[0], 0, 5) . ' às ' . $arr[1];
        } else {
            $return = "-";
        }
    }
    return $return;
}

/**
 * Retorna qual o tipo de browser o usuário está utilizando, se é Desktop ou Mobile
 * 
 * @return string
 */
function verificaTipoBrowser() {
    $iphone = strpos($_SERVER ['HTTP_USER_AGENT'], "iPhone");
    $ipad = strpos($_SERVER['HTTP_USER_AGENT'], "iPad");
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
    $palmpre = strpos($_SERVER ['HTTP_USER_AGENT'], "webOS");
    $berry = strpos($_SERVER['HTTP_USER_AGENT'], "BlackBerry");
    $ipod = strpos($_SERVER['HTTP_USER_AGENT'], "iPod");
    $symbian = strpos($_SERVER['HTTP_USER_AGENT'], "Symbian");
    if ($iphone || $ipad || $android || $palmpre || $ipod || $berry || $symbian == true)
        return 'M';
    else
        return 'D';
}

/**
 * Formata uma sring de CPF para o formato 000.000.000-00
 *
 * @param String $str
 * @return String
 */
function formatarCpf($str) {
    if (strlen($str) == 11) {
        $um = substr($str, 0, 3);
        $dois = substr($str, 3, 3);
        $tres = substr($str, 6, 3);
        $quatro = substr($str, 9, 2);
        return "$um.$dois.$tres-$quatro";
    } else {
        return null;
    }
}

/**
 * Formata uma string de CEP para o formato 00.000-00
 *
 * @param String $str
 * @return String
 */
function formatarCep($str, $erro = null) {
    if (strlen($str) == 8) {
        $um = substr($str, 0, 2);
        $dois = substr($str, 2, 3);
        $tres = substr($str, 5, 3);
        return "$um.$dois-$tres";
    } else {
        return $erro;
    }
}

/**
 * Verifica se o remetente existe, senão preenche com o usuário do sistema
 *
 * @param Usuario $remetente
 * @return Usuario
 */
function verificaRemetente($remetente) {
    if (is_null($remetente->getUsu_int_codigo())) {
        $remetente = new Usuario();
        $remetente->setUsu_var_nome(SYS_NOME);
        $remetente->setUsu_var_email(SYS_EMAIL);
    }
    return $remetente;
}

/**
 * gera uma string formatada para filtro de query usando a clausula IN
 * 
 * @param array $lista
 * @return string
 */
function formatarFiltroIn($lista) {
    $arr = array();
    foreach ($lista as $item) {
        $arr[] = "'" . $item . "'";
    }
    return implode(', ', $arr);
}

/**
 * 
 * 
 * @param string $arquivo
 * @return string
 */
function buscarExtensaoArquivo($arquivo) {
    $extensao = '';
    $arr = explode("/", $arquivo);
    if (count($arr)) {
        $extensao = pathinfo($arr[count($arr) - 1], PATHINFO_EXTENSION);
    } else {
        $extensao = pathinfo($arquivo, PATHINFO_EXTENSION);
    }
    return $extensao;
}

/**
 * Buscar o content-type de acordo com a extensão do arquivo passado
 * 
 * @param string $fileExtension
 * @return string
 */
function buscarContentType($fileExtension) {
    switch (strtolower($fileExtension)) {
        case 'pdf':
            $contentType = 'application/pdf';
            break;
        case 'txt':
            $contentType = 'text/plain';
            break;
        case 'png':
            $contentType = 'image/png';
            break;
        case 'jpg':
        case 'jpeg':
            $contentType = 'image/jpeg';
            break;
        case 'docx':
            $contentType = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            break;
        // Adicione mais tipos conforme necessário
        default:
            $contentType = 'application/octet-stream'; // Tipo genérico para outros arquivos
            break;
    }
    return $contentType;
}

/**
 * Converte um texto UTF-8 para ISO-8859-1
 * 
 * @param string $texto
 * @return string
 */
function cvt($texto) {
    return iconv("UTF-8", "ISO-8859-1", $texto);
}

/**
 * Formata os segundos em dia, hora, minuto e segundo
 * 
 * @param int $seconds
 * @return string
 */
function secondsToTime($seconds) {
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a dia(s), %H:%I:%S');
}

/**
 * converte um tempo em duração mais específica para usuário
 * 
 * @param type $time
 * @return string
 */
function converterDuracao($time) {
    $w = floor($time / 86400 / 7);
    $d = floor($time / 86400 % 7);
    $h = floor($time / 3600 % 24);
    $m = floor($time / 60 % 60);
    $s = floor($time % 60);

    $retorno = '';

    if ($w > 0) {
        if ($w > 1)
            $retorno .= $w . ' semanas, ';
        else
            $retorno .= $w . ' semana, ';
    }
    if ($d > 0) {
        if ($d > 1)
            $retorno .= $d . ' dias, ';
        else
            $retorno .= $d . ' dia, ';
    }
    if ($h > 0) {
        if ($h > 1)
            $retorno .= $h . ' horas, ';
        else
            $retorno .= $h . ' hora, ';
    }
    if ($m > 0) {
        if ($m > 1)
            $retorno .= $m . ' minutos, ';
        else
            $retorno .= $m . ' minuto, ';
    }

    if ($s > 1)
        $retorno .= ' e ' . $s . ' segundos';
    else
        $retorno .= ' e ' . $s . ' segundos';

    return $retorno;
}

/**
 * converte um tempo em duração abreviada para usuário
 * @param type $time
 * @return string
 */
function formatarTempo($time) {
    $w = floor($time / 86400 / 7);
    $d = floor($time / 86400 % 7);
    $h = floor($time / 3600 % 24);
    $m = floor($time / 60 % 60);
    $s = floor($time % 60);

    $retorno = '';

    if ($w > 0) {
        if ($w > 1)
            $retorno .= $w . ' semanas, ';
        else
            $retorno .= $w . ' semana, ';
    }
    if ($d > 0) {
        $retorno .= $d . 'd ';
    }
    if ($h > 0) {
        $retorno .= $h . 'h ';
    }
    if ($m > 0) {
        $retorno .= $m . 'm ';
    }

    return $retorno;
}

/**
 * Transforma o tamanho de uma arquivo de bytes para um mais legível
 * 
 * @param type $size
 * @param type $precision
 * @return type
 */
function formatarBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('b', 'Kb', 'Mb', 'Gb', 'Tb');
    $valor = GF::trocarPonto(round(pow(1024, $base - floor($base)), $precision));
    return $valor . ' ' . $suffixes[floor($base)];
}

/**
 * Calcular o tamanho de um diretório
 * 
 * @param string $dir
 * @return int
 */
function foldersize($dir) {
    $count_size = 0;
    $count = 0;
    $dir_array = scandir($dir);
    foreach ($dir_array as $key => $filename) {
        if ($filename != ".." && $filename != ".") {
            if (is_dir($dir . "/" . $filename)) {
                $new_foldersize = foldersize($dir . "/" . $filename);
                $count_size = $count_size + $new_foldersize;
            } else if (is_file($dir . "/" . $filename)) {
                $count_size = $count_size + filesize($dir . "/" . $filename);
                $count++;
            }
        }
    }

    return $count_size;
}

/**
 * Calcula o valor correspondente a porcentagem de um número
 * Função de porcentagem: Quanto é X% de total?
 * 
 * @param double $porcentagem
 * @param double $total
 * @return double
 */
function porcentagem_xn($porcentagem, $total) {
    return ( $porcentagem / 100 ) * $total;
}

/**
 * Calcula o percentual de um número em relação ao total
 * Função de porcentagem: parcial é X% de total
 * 
 * @param double $parcial
 * @param double $total
 * @return double
 */
function porcentagem_nx($parcial, $total) {
    $resultado = 0;
    if ($total > 0)
        $resultado = ( $parcial * 100 ) / $total;
    return $resultado;
}

/**
 * Calcula o total de um percentual de um número
 * Função de porcentagem: parcial é percentual de X
 * 
 * @param double $parcial
 * @param double $porcentagem
 * @return double
 */
function porcentagem_nnx($parcial, $porcentagem) {
    return ( $parcial / $porcentagem ) * 100;
}

/**
 * Comprime uma imagem com a qualidade desejada
 * 
 * @param string $source
 * @param string $destination
 * @param int $quality
 * @return string
 */
function compress($source, $destination, $quality) {
    $info = getimagesize($source);
    if ($info['mime'] == 'image/jpeg')
        $image = imagecreatefromjpeg($source);
    elseif ($info['mime'] == 'image/gif')
        $image = imagecreatefromgif($source);
    elseif ($info['mime'] == 'image/png')
        $image = imagecreatefrompng($source);
    imagejpeg($image, $destination, $quality);
    return $destination;
}

/**
 * verifica se existe a chave no array e retorna o valor
 * 
 * @param array $array
 * @param string $chave
 * @return string
 */
function seExisteChave($array, $chave) {
    if (@array_key_exists($chave, $array)) {
        return $array[$chave];
    } else {
        return '--';
    }
}

/**
 * Calcula o percentual de desconto baseando-se no valor original e promocional
 * 
 * @param decimal $original
 * @param decimal $promocional
 * @return decimal
 */
function calcularPercentualDesconto($original, $promocional) {
    $ret = 0;
    if ($original > 0) {
        $ret = 100 - round(($promocional / $original) * 100);
    }
    return $ret;
}

/**
 * Retorna o primeiro dia do mês atual
 * 
 * @param bool $brasil
 * @return string
 */
function buscarPrimeiroDiaMes($brasil = true) {
    return ($brasil) ? "01" . "/" . date('m') . "/" . date('Y') : date('Y') . "-" . date('m') . "-" . "01";
}

/**
 * Retorna o último dia do mês atual
 * 
 * @param bool $brasil
 * @return string
 */
function buscarUltimoDiaMes($brasil = true) {
    $mes = date('m');
    $ano = date("Y");
    $ultimo_dia = date("t", mktime(0, 0, 0, $mes, '01', $ano));
    return ($brasil) ? $ultimo_dia . "/" . date('m') . "/" . date('Y') : date('Y') . "-" . date('m') . "-" . $ultimo_dia;
}

/**
 * Retorna a data de amanhã
 * 
 * @return string
 */
function buscarDataAmanha() {
    $date = date('m-d-Y');
    $date1 = str_replace('-', '/', $date);
    return date('d/m/Y', strtotime($date1 . "+1 days"));
}

/**
 * Buscar a data anterior de acordo com um numero de dias 
 * 
 * @param int $dias
 * @return string
 */
function buscarDataAnterior($dias) {
    $date = date('m-d-Y');
    $date1 = str_replace('-', '/', $date);
    return date('d/m/Y', strtotime($date1 . "-" . $dias . " days"));
}

/**
 * Formatar os estados com seus respectivos nomes
 * 
 * @param string $arr Ex: SE,BA,SP,MG,PA
 * @return string
 */
function formatarEstados($arr) {
    global $__arrayEstados;
    $arrEstados = array();
    $arrTemp = explode(",", $arr);
    if (count($arrTemp)) {
        foreach ($arrTemp as $sigla) {
            $arrEstados[] = $__arrayEstados[$sigla];
        }
    }
    return implode(", ", $arrEstados);
}

/**
 * Formata o texto retirando os caracteres de bloco de notas
 * 
 * @param string $valor
 * @return string
 */
function formatarCaracteresTXT($valor) {
    return str_replace(array("\n", "\t", "\r"), '', $valor);
}

/**
 * Formata o texto trocando a quebra de linha por <br>
 * 
 * @param string $valor
 * @return string
 */
function formatarQuebraLinhaTXT($valor) {
    return str_replace(array("\n"), '<br/>', $valor);
}

/**
 * Formata o texto trocando o espaço em html por espaço em branco
 * 
 * @param string $valor
 * @return string
 */
function formatarEspacosTXT($valor) {
    return str_replace('&nbsp;', ' ', $valor);
}

/**
 * Formata se o valor for 0 para a palavra Grátis
 * 
 * @param string $valor
 * @return string
 */
function formatarGratis($valor) {
    if ($valor == "R$ 0,00") {
        return 'Grátis';
    } else {
        return $valor;
    }
}

/**
 * Retorna qual é a extensão do arquivo
 * 
 * @param string $arquivo
 * @return string
 */
function getExtensaoArquivo($arquivo) {
    return pathinfo($arquivo, PATHINFO_EXTENSION);
}

/**
 * Retorna qual é nome do arquivo
 * 
 * @param string $arquivo
 * @return string
 */
function getNomeArquivo($arquivo) {
    return pathinfo($arquivo, PATHINFO_FILENAME);
}

/**
 * Formata a string permitindo somente números
 * 
 * @param string $str
 * @return string
 */
function formatarSomenteNumeros($str) {
    return preg_replace("/[^0-9]/", "", $str);
}

/**
 * Retorna a data passada por extenso
 * 
 * @param string $time
 * @return string
 */
function dataExtenso($time = 'now') {
    $hoje = strtotime($time);
    $i = getdate($hoje); // Consegue informações data/hora
    $data = $i["mday"]; //Representação numérica do dia do mês (1 a 31)
    $dia = $i["wday"]; // representação numérica do dia da semana com 0 (para Domingo) a 6 (para Sabado)
    $mes = $i["mon"]; // Representação numérica de um mês (1 a 12)
    $ano = $i["year"]; // Ano com 4 digitos, lógico, né?
    $data = str_pad($data, 2, "0", STR_PAD_LEFT); // só para colocar um zerinho à esquerda caso seja de 1 à 9, sacou?
    $nomemes = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    return "$data de {$nomemes[$mes]} de $ano";
}

/**
 * Cria um array com números sequenciais do início ao fim
 * 
 * @param int $inicio
 * @param int $fim
 * @return array
 */
function createArrayNumeros($inicio, $fim) {
    $arr = array();
    for ($i = $inicio; $i <= $fim; $i++) {
        $arr[$i] = $i;
    }
    return $arr;
}

/**
 * Monta um array com os dados do aluno * 
 * 
 * @param Aluno $aluno
 */
function montarArrayParametros($aluno) {
    $arr = array();
    $arr["#NOME#"] = $aluno->getAlu_var_nome();

    return $arr;
}

/**
 * Substitui as palavras chaves pelos valores
 * 
 * @param type $txt
 * @param type $parametros
 * @return type
 */
function substituirParametros($txt, $parametros) {
    foreach ($parametros as $key => $value) {
        $txt = str_replace($key, $value, $txt);
    }
    return $txt;
}

/**
 * Retorna se o cpf passado é válido
 * 
 * @param string $cpf (sem formatação)
 * @return boolean
 */
function validaCPF($cpf) {
    if (!seVazioOuNulo($cpf)) {
        $cpf = preg_replace('/[^0-9]/is', '', $cpf);
        if (strlen($cpf) != 11) {
            return false;
        }
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        for ($t = 9; $t < 11; $t++) {
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$c] != $d) {
                return false;
            }
        }
    }
    return true;
}

/**
 * Valida se a string tem somente números e se passado o tamanho, também valida.
 * 
 * @param string $string
 * @param int $tamanho
 * @return bool
 */
function validaSomenteNumeros($string, $tamanho = false) {
    // Verifica se a string tem o tamanho passado
    if ($tamanho && strlen($string) !== $tamanho) {
        return false;
    }

    // Verifica se todos os caracteres são números
    if (!ctype_digit($string)) {
        return false;
    }

    return true;
}

/**
 * Retorna se o CNPJ passado é válido
 * 
 * @param string $cnpj
 * @return bool
 */
function validaCNPJ($cnpj) {
    if (!seVazioOuNulo($cnpj)) {
        $cnpj = preg_replace('/[^0-9]/is', '', $cnpj);
        if (strlen($cnpj) != 14) {
            return false;
        }
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += intval($cnpj[$i]) * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        $digito1 = ($resto < 2) ? 0 : 11 - $resto;
        if (intval($cnpj[12]) != $digito1) {
            return false;
        }
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += intval($cnpj[$i]) * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        $resto = $soma % 11;
        $digito2 = ($resto < 2) ? 0 : 11 - $resto;

        if (intval($cnpj[13]) != $digito2) {
            return false;
        }
    }
    return true;
}

/**
 * Retorna se o email é válido
 * 
 * @param string $email
 * @return boolean
 */
function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Valida se uma data é válida no formato YYYY-MM-DD.
 *
 * @param string $data A string da data a ser validada.
 * @return bool Retorna true se a data for válida, false caso contrário.
 */
function validaData($data) {
    // Verifica se a string corresponde ao formato YYYY-MM-DD usando expressão regular
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
        return false;
    }

    // Separa o ano, mês e dia
    list($ano, $mes, $dia) = explode('-', $data);

    // Usa a função checkdate para verificar se a data é válida
    return checkdate((int) $mes, (int) $dia, (int) $ano);
}

/**
 * Valida se o ano informado é um ano válido (inteiro positivo).
 *
 * @param mixed $ano O valor a ser verificado como ano.
 * @return bool Retorna true se o ano for válido, false caso contrário.
 */
function validaAno($ano) {
    // Verifica se o valor é um número inteiro
    if (!is_numeric($ano) || !is_int(intval($ano))) {
        return false;
    }

    // Converte para inteiro para garantir a comparação
    $ano = intval($ano);

    // Verifica se o ano é positivo (maior que zero)
    if ($ano <= 0) {
        return false;
    }

    return true;
}

/**
 * Valida um CEP (Código de Endereçamento Postal) brasileiro.
 *
 * @param string $cep O CEP a ser validado.
 * @return bool Retorna true se o CEP for válido, false caso contrário.
 */
function validaCEP($cep) {
    // Remove caracteres não numéricos
    $cepLimpo = preg_replace('/[^0-9]/', '', $cep);

    // Verifica se o CEP possui o formato correto (8 dígitos)
    if (strlen($cepLimpo) !== 8) {
        return false;
    }
    return true;
}

/**
 * Calcula a diferença de um tempo para o atual
 * 
 * @return date
 */
function getTempoRestante($dti) {
    $previsao = new DateTime($dti);
    $atual = new DateTime();
    $interval = $atual->diff($previsao);
    if ($atual->getTimestamp() > $previsao->getTimestamp()) {
        return '00:00:00';
    } else {
        return $interval->format('%H:%I:%S');
    }
}

/**
 * retira do array os valores vazios
 * 
 * @param array $arr
 * @return array
 */
function retirarElementosVazios($arr) {
    $new = array();
    foreach ($arr as $value) {
        if (!seNuloOuVazio($value))
            $new[] = $value;
    }
    return $new;
}

/**
 * Busca a identificação do servidor
 * 
 * @global array $__arrayServer
 * @return string
 */
function getServer() {
    global $__arrayServer;
    if (isset($__arrayServer[$_SERVER["SERVER_ADDR"]])) {
        return $__arrayServer[$_SERVER["SERVER_ADDR"]];
    } else {
        return $_SERVER["SERVER_ADDR"];
    }
}

/**
 * Busca o valor de parte uma de string separada pelo delimitador
 * 
 * @param string $string
 * @param char $delimitador
 * @param int $posicao
 * @return string
 */
function getPosicaoSplit($string, $delimitador = ';', $posicao = 0) {
    $arr = explode($delimitador, $string);
    if (is_array($arr)) {
        if (isset($arr[$posicao]))
            return $arr[$posicao];
        else
            return null;
    } else {
        return null;
    }
}

/**
 * Formata os dados para excel
 * 
 * @param string $str
 */
function filterData(&$str) {
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"'))
        $str = '"' . str_replace('"', '""', $str) . '"';
}

/**
 * Verifica se a string passada é um HTML ou não
 * 
 * @param string $string
 * @return boolean
 */
function isHTML($string) {
    return $string != strip_tags($string) ? true : false;
}

/**
 * Convert em char uma variável booleana
 * 
 * @param boolean $bool
 * @return char
 */
function convertBooleanToChar($bool) {
    return ($bool) ? 'S' : 'N';
}

/**
 * Valida se o user agent é aceitável
 * 
 * @param array $userAgent
 * @return array
 */
function validarUserAgent($userAgent) {
    $arrSistemasOperacionais = explode(";", buscarParametro("SISTEMAS_OPERACIONAIS_ACEITOS"));
    $retorno = array('S', 'Dentro dos parâmetros necessários');
    if ($userAgent["device"]["is_mobile_device"]) {
        $retorno = array('N', 'Dispositivo Mobile');
    } else if ($userAgent["crawler"]["is_crawler"]) {
        $retorno = array('N', 'Crawler');
    } else if (((int) $userAgent["browser"]["version"]) < (((int) $userAgent["browser"]["version_major"]) - 2)) {
        $retorno = array('N', 'Browser: "' . $userAgent["browser"]["name"] . '" desatualizado. Versão Utilizada: "' . $userAgent["browser"]["version"] . '" Versão Atual: "' . $userAgent["browser"]["version_major"] . '"');
    } else if (!in_array($userAgent["os"]["code"], $arrSistemasOperacionais)) {
        $retorno = array('N', 'Sistema Operacional: "' . $userAgent["os"]["name"] . '" inexistente na lista de permitidos.');
    }
    return $retorno;
}

/**
 * Converte o texto em maiúscula
 * 
 * @param string $txt
 * @return string
 */
function maiusculo($txt) {
    return mb_convert_case($txt, MB_CASE_UPPER, 'UTF-8');
}

/**
 * Converte o texto em minúscula
 * 
 * @param string $txt
 * @return string
 */
function minusculo($txt) {
    return mb_convert_case($txt, MB_CASE_LOWER, 'UTF-8');
}

/**
 * Escrever log no arquivo e na tela durante a execução do CRON
 * 
 * @param resourse $fp
 * @param string $titulo
 * @param string $mensagem
 * @param bool $status
 */
function escrever($fp, $titulo, $mensagem, $status = null) {
    if (is_null($status)) {
        echo '<dt style="white-space: normal">' . $titulo . '</dt><dd>' . $mensagem . '</dd>';
        fwrite($fp, date("d/m/Y H:i:s") . ' - ' . $titulo . ' - ' . $mensagem . "\n");
    } else {
        switch ($status) {
            case 'S':
                echo '<dt class="green" style="white-space: normal">' . $titulo . '</dt><dd class="green">' . $mensagem . '</dd>';
                fwrite($fp, date("d/m/Y H:i:s") . ' - SUCESSO: ' . $titulo . ' - ' . $mensagem . "\n");
                break;
            case 'E':
                echo '<dt class="red" style="white-space: normal">' . $titulo . '</dt><dd class="red">' . $mensagem . '</dd>';
                fwrite($fp, date("d/m/Y H:i:s") . ' - ERRO: ' . $titulo . ' - ' . $mensagem . "\n");
                break;
            case 'A':
                echo '<dt class="blue" style="white-space: normal">' . $titulo . '</dt><dd class="blue">' . $mensagem . '</dd>';
                fwrite($fp, date("d/m/Y H:i:s") . ' - ATENÇÃO: ' . $titulo . ' - ' . $mensagem . "\n");
                break;
        }
    }
}

/**
 * Buscar o tamanho da coluna de ações da tabela de acordo com as permissões passadas
 * 
 * @param array $arrPermissoes array com as permissões que geram icones de acões
 * @return int
 */
function obterLarguraColunaAcoes($arrPermissoes) {
    $largura = 25;
    $qtd = 1;
    foreach ($arrPermissoes as $permissao) {
        $qtd += (GSecurity::verificarPermissao($permissao, false)) ? 1 : 0;
    }
    return ($qtd * $largura) + (($qtd > 2) ? 8 : 10);
}

/**
 * Calcular a idade com base na data de nascimento
 * 
 * @param date $dataNascimento
 * @return string
 */
function calcularIdade($dataNascimento) {
    $retorno = '';

    if (seNuloOuVazio($dataNascimento) || $dataNascimento === '1900-01-01' || $dataNascimento === '0000-00-00' || $dataNascimento === '0000-00-00 00:00' || $dataNascimento === '00:00' || $dataNascimento === '00:00:00') {
        $retorno = '-';
    } else {
        $date = new DateTime($dataNascimento);
        $interval = $date->diff(new DateTime(date('Y-m-d')));
        if ($interval->format('%Y') > 1) {
            $retorno = $interval->format('%Y anos');
        } else {
            if ($interval->format('%Y') == 1) {
                $retorno = $interval->format('1 ano');
            }
            if ($interval->format('%Y') > 0) {
                $retorno .= ' e ';
            }
            if ($interval->format('%m') > 1) {
                $retorno .= $interval->format('%m meses');
            } elseif ($interval->format('%m') == 1) {
                $retorno .= $interval->format('1 mês');
            }
        }
    }
    return $retorno;
}

/**
 * Valida se o valor passado é zero
 * 
 * @param string $dado
 * @return boolean
 */
function seZero($dado) {
    if (isset($dado) && !is_object($dado) && (trim($dado) == "0"))
        return true;
    else
        return false;
}

function gerarSenhaComplexa(int $tamanho = 8) {
    $letrasMaiusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $letrasMinusculas = 'abcdefghijklmnopqrstuvwxyz';
    $numeros = '0123456789';
    $caracteresEspeciais = '!@#$%&_+=-;:<>?';

    $todosCaracteres = $letrasMaiusculas . $letrasMinusculas . $numeros . $caracteresEspeciais;
    $tamanhoMaximo = strlen($todosCaracteres) - 1;
    $senha = '';

    for ($i = 0; $i < $tamanho; $i++) {
        $senha .= $todosCaracteres[random_int(0, $tamanhoMaximo)];
    }

    // Garante que pelo menos um caractere de cada tipo esteja presente
    $hasLetraMaiuscula = false;
    $hasLetraMinuscula = false;
    $hasNumero = false;
    $hasCaractereEspecial = false;

    for ($i = 0; $i < $tamanho; $i++) {
        if (strpos($letrasMaiusculas, $senha[$i]) !== false) {
            $hasLetraMaiuscula = true;
        } elseif (strpos($letrasMinusculas, $senha[$i]) !== false) {
            $hasLetraMinuscula = true;
        } elseif (strpos($numeros, $senha[$i]) !== false) {
            $hasNumero = true;
        } elseif (strpos($caracteresEspeciais, $senha[$i]) !== false) {
            $hasCaractereEspecial = true;
        }
    }

    // Se algum tipo estiver faltando, regenera a senha (uma abordagem simples)
    if (!$hasLetraMaiuscula || !$hasLetraMinuscula || !$hasNumero || !$hasCaractereEspecial) {
        return gerarSenhaComplexa($tamanho); // Chama a função novamente para tentar gerar outra senha
    }

    return str_shuffle($senha); // Embaralha a senha para aumentar a aleatoriedade
}

/**
 * Criptografar a senha do usuário no moodle
 * 
 * @param string $password
 * @return string
 */
function criptografarPasswordMoodle($password) {
    $rounds = 10000;
    $randombytes = random_bytes(16);
    $salt = substr(strtr(base64_encode($randombytes), '+', '.'), 0, 16);

    // Now construct the password string with the salt and number of rounds.
    // The password string is in the format $algorithm$rounds$salt$hash. ($6 is the SHA512 algorithm).
    $generatedhash = crypt($password, implode('$', [
        '',
        '6',
        "rounds={$rounds}",
        $salt,
        '',
    ]));
    return $generatedhash;
}

/**
 * Retorna parte do nome de acordo com o parãmetro $opcao
 * 
 * @param string $nomeCompleto
 * @param string $opcao Default: 'firstname'
 * @return string
 */
function obterNome($nomeCompleto, $opcao = 'firstname') {
    $nomes = explode(' ', trim($nomeCompleto));

    if (empty($nomes)) {
        return null;
    }

    if (strtolower($opcao) === 'firstname') {
        return $nomes[0];
    } elseif (strtolower($opcao) === 'lastname') {
        if (count($nomes) > 1) {
            return implode(' ', array_slice($nomes, 1));
        } else {
            return null; // Retorna vazio se só houver um nome e a opção for lastname
        }
    } else {
        return 'Opção inválida. Use "firstname" ou "lastname".';
    }
}

// </editor-fold>
// 
// 
// 
// <editor-fold defaultstate="collapsed" desc="HTML">
/**
 * Retorna um código HTML com os botões equivalentes aos parametros passado:
 * I -> Inserir e Cancelar
 * P -> Inserir, Cancelar, Salvar e Novo e Salvar e Copiar
 * N -> Inserir, Cancelar e Inserir e Novo
 * A -> Alterar e Cancelar
 * E -> Editar e Cancelar
 * S -> Salvar
 * G -> Gerar
 * Q -> Pesquisar
 * C -> Confirmar
 * W -> Alterar e Ver Todos
 * T -> Ver Todos
 * Default -> Inserir e Cancelar
 * 
 * @param String $tipo Por padrão o tipo é "I"
 * @param String $class Por padrão a class é "buttom"
 * @return string
 */
function carregarBotoes($tipo = "I", $classPadrao = "", $rel = false, $styleDiv = '', $tamanho = 'sm') {
    // <editor-fold defaultstate="collapsed" desc="Configurações">
    $arrConf = array(
        "btn_next" => array("class" => "success", "ico" => "step-forward"),
        "btn_confirmar" => array("class" => "success", "ico" => "check"),
        "btn_iniciar" => array("class" => "success", "ico" => "play"),
        "btn_gerar" => array("class" => "success", "ico" => "cog"),
        "btn_adicionar" => array("class" => "success", "ico" => "plus"),
        "btn_insert" => array("class" => "success", "ico" => "floppy-o"),
        "btn_salvar" => array("class" => "success", "ico" => "floppy-o"),
        "btn_sim" => array("class" => "success", "ico" => "check"),
        "btn_finalizar" => array("class" => "success", "ico" => "flag"),
        "btn_principal" => array("class" => "success", "ico" => "check"),
        "btn_inserir_novo" => array("class" => "success", "ico" => "floppy-o"),
        "btn_upload" => array("class" => "success", "ico" => "floppy-o"),
        "btn_imprimir" => array("class" => "success", "ico" => "print"),
        "btn_alterar" => array("class" => "success", "ico" => "pencil"),
        //
        "btn_cancel" => array("class" => "danger", "ico" => "times"),
        "btn_cancelar" => array("class" => "danger", "ico" => "ban"),
        "btn_alterar2" => array("class" => "danger", "ico" => "floppy-o"),
        "btn_nao" => array("class" => "danger", "ico" => "times"),
        "btn_excluir" => array("class" => "danger", "ico" => "trash-o"),
        "btn_duplicar" => array("class" => "danger", "ico" => "clone"),
        //
        "btn_foto" => array("class" => "warning", "ico" => "camera-retro"),
        "btn_responder" => array("class" => "warning", "ico" => "mail-reply"),
        "btn_objetivo" => array("class" => "warning", "ico" => "bullseye"),
        "btn_disciplinas" => array("class" => "warning", "ico" => "newspaper-o"),
        "btn_usuarios" => array("class" => "warning", "ico" => "users"),
        "btn_documento" => array("class" => "warning", "ico" => "file-text-o"),
        //
        "btn_insert_copia" => array("class" => "primary", "ico" => "floppy-o"),
        "btn_download" => array("class" => "primary", "ico" => "download"),
        "btn_whatsapp" => array("class" => "primary", "ico" => "whatsapp"),
        "btn_insert_copiar" => array("class" => "primary", "ico" => "floppy-o"),
        "btn_executar" => array("class" => "primary", "ico" => "check"),
        //
        "btn_todos" => array("class" => "info", "ico" => "eye"),
        "btn_insert_novo" => array("class" => "info", "ico" => "floppy-o"),
        "btn_enviarsenha" => array("class" => "info", "ico" => "key"),
        "btn_grupos" => array("class" => "info", "ico" => "users"),
        "btn_atualizar" => array("class" => "info", "ico" => "refresh"),
        "btn_abrir" => array("class" => "info", "ico" => "arrow-down"),
        "btn_fechar" => array("class" => "info", "ico" => "arrow-up"),
        "btn_preencher" => array("class" => "info", "ico" => "pencil-square"),
        "btn_visualizar" => array("class" => "info", "ico" => "eye"),
        "btn_close" => array("class" => "info", "ico" => "times"),
        //
        "btn_ver" => array("class" => "purple", "ico" => "eye"),
        "btn_historico" => array("class" => "purple", "ico" => "clock-o"),
        "btn_auditoria" => array("class" => "purple", "ico" => "clock-o"),
        "btn_eixo" => array("class" => "purple", "ico" => "map-signs"),
        "btn_grade" => array("class" => "purple", "ico" => "table"),
        "btn_clonar" => array("class" => "purple", "ico" => "clone"),
        "btn_acoes" => array("class" => "purple", "ico" => "graduation-cap"),
        //        
        "btn_voltar" => array("class" => "grey", "ico" => "arrow-left"),
        "btn_pergunta" => array("class" => "grey", "ico" => "question-circle"),
        "btn_sair" => array("class" => "grey", "ico" => "times"),
        //
        "btn_logar" => array("class" => "pink", "ico" => "magic"),
        "btn_matriculas" => array("class" => "pink", "ico" => "graduation-cap"),
        "btn_processamentos" => array("class" => "pink", "ico" => "cogs"),
        "btn_enviar" => array("class" => "pink", "ico" => "paper-plane")
    );
    // </editor-fold>

    if (is_array($tipo)) {
        $param = $tipo;
    } else {
        switch ($tipo) {
            case "I":
                $param = array("btn_insert" => "Salvar", "btn_cancel" => "Cancelar");
                break;
            case "N":
                $param = array("btn_insert" => "Salvar", "btn_cancel" => "Cancelar", "btn_insert_novo" => "Salvar e Novo");
                break;
            case "O":
                $param = array("btn_insert" => "Salvar", "btn_cancel" => "Cancelar", "btn_insert_novo" => "Salvar e Novo", "btn_insert_copiar" => "Salvar e Copiar");
                break;
            case "E":
                $param = array("btn_salvar" => "Salvar", "btn_cancel" => "Cancelar");
                break;
            case "EV":
                $param = array("btn_enviar" => "Enviar", "btn_voltar" => "Voltar");
                break;
            case "SV":
                $param = array("btn_salvar" => "Salvar", "btn_voltar" => "Voltar");
                break;
            case "S":
                $param = array("btn_salvar" => "Salvar");
                break;
            case "SF":
                $param = array("btn_salvar" => "Salvar", "btn_cancel" => "Fechar");
                break;
            case "F":
                $param = array("btn_cancel" => "Fechar");
                break;
            case "DF":
                $param = array("btn_download" => "Baixar", "btn_cancel" => "Fechar");
                break;
            case "G":
                $param = array("btn_gerar" => "Gerar");
                break;
            case "P":
                $param = array("btn_preencher" => "Preencher");
                break;
            case "W":
                $param = array("btn_visualizar" => "Visualizar");
                break;
            case "X":
                $param = array("btn_executar" => "Executar", "btn_cancel" => "Cancelar");
                break;
            case "TV":
                $param = array("btn_todos" => "Ver Todos", "btn_voltar" => "Voltar");
                break;
            case "V":
                $param = array("btn_voltar" => "Voltar");
                break;
            case "R":
                $param = array("btn_atualizar" => "Atualizar", "btn_abrir" => "Abrir Todos", "btn_fechar" => "Fechar Todos");
                break;
            default:
                $param = array("btn_salvar" => "Salvar", "btn_cancel" => "Cancelar");
                break;
        }
    }
    $html = '';
    $html .= '<div class="form-actions center divBotoes" style="' . $styleDiv . '">';
    foreach ($param as $key => $value) {
        if (array_key_exists($key, $arrConf)) {
            $arrParam = $arrConf[$key];
            $class = $arrParam["class"];
            $ico = $arrParam["ico"];
        }
        $html .= '<button type="button" data-toggle="tooltip" data-placement="top" alt="' . $value . '" title="' . $value . '" rel="' . $rel . '" id="' . $key . '" class="' . $key . ' btn btn-' . $tamanho . ' btn-' . $class . ' tooltip-' . $class . '"><i class="ace-icon fa fa-' . $ico . ' bigger-110"></i>' . $value . '</button> ';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Retorna um código HTML com scripts para os formulários
 * @param string $javascript
 * @return string
 */
function carregarScriptForm($javascript) {
    $script = '';
    $script .= '<script>';
    $script .= 'jQuery(document).ready(function () {';
    $script .= $javascript;
    $script .= '});';
    $script .= '</script>';
    return $script;
}

/**
 * Retorna um código HTML com scripts do botão voltar
 * @return string
 */
function carregarScriptFormVoltar() {
    $script = '';
    $script .= '<script>';
    $script .= 'jQuery(document).ready(function () {';
    $script .= '    jQuery("#btn_voltar").click(function(){ ';
    $script .= '        jQuery.gDisplay.loadStart("HTML");';
    $script .= '	window.history.back();';
    $script .= '    });';
    $script .= '});';
    $script .= '</script>';
    return $script;
}

/**
 * Gera html para cabeçalho
 * 
 * @param array 'tipo' => Tipo de cabeçalho {'box', 'full'}
 *              'titulo' => Título do formulário Default: ''
 *              'id' => Id da div principal Default: 'formulario'
 *              'col' => Tamanho da coluna principal Default: 4
 *              'style' => Estilos de css para a caixa do formulário Default: 'display:none;'
 *              'fa' => Ícone FA que irá aparecer no título Default: 'pencil-square-o'
 * @return string
 */
function gerarCabecalho($param) {
    $tipo = 'box';
    $titulo = '';
    $id = 'formulario';
    $col = 4;
    $classFull = 'col-xs-12';
    $style = '';
    $width = '100%';
    $fa = 'pencil-square-o';
    $tituloBotaoNovo = 'Inserir';
    $tituloBotaoExcel = 'Exportar Excel';
    $tituloBotaoOrdenar = 'Ordenar';
    $tituloBotaoPdf = 'Exportar PDF';
    $iconeBotaoNovo = 'plus';
    $iconeBotaoExcel = 'file-excel-o';
    $iconeBotaoOrdenar = 'sort';
    $iconeBotaoPdf = 'file-pdf-o';

    $tituloBotaoLoteManut = 'Manutenção em Lote';
    $tituloBotaoLoteIns = 'Inserir em Lote';
    $tituloBotaoLoteDel = 'Excluir em Lote';
    $tituloBotaoLoteConf = 'Confirmar em Lote';
    $tituloBotaoLoteTodos = 'Selecionar Todos';
    $iconeBotaoLoteManut = 'check-square-o';
    $iconeBotaoLoteIns = 'plus-square';
    $iconeBotaoLoteDel = 'minus-square';
    $iconeBotaoLoteConf = 'check-square-o';
    $iconeBotaoLoteTodos = 'check-square-o';

    $botaoNovoPermissao = '';
    $botaoExcelPermissao = '';
    $botaoPdfPermissao = '';
    $botaoLoteManutPermissao = '';
    $botaoLoteInsPermissao = '';
    $botaoLoteDelPermissao = '';
    $botaoLoteConfPermissao = '';
    $botaoLoteTodosPermissao = '';

    $export = true;
    $botaoNovo = true;
    $botaoExcel = false;
    $botaoOrdenar = false;
    $botaoPdf = false;
    $botaoLoteManut = false;
    $botaoLoteIns = false;
    $botaoLoteDel = false;
    $botaoLoteConf = false;
    $botaoLoteTodos = false;
    $botaoAtualizar = false;
    $filtro = false;
    $visao = false;
    extract($param);
//$secundaria = ((12 - $col) / 2);
    $html = '';
    if ($tipo == 'box') {
        $html .= '<div class="col-xs-12" id="' . $id . '" style="' . $style . '">';
        $html .= '<div class="row">';
        switch ($col) {
            case 12:
                $html .= '<div class="col-lg-12 col-md-12 col-xs-12 col-sm-12">';
                break;
            case 10:
                $html .= '<div class="col-lg-1 col-md-0 col-sm-0 col-xs-0"></div>';
                $html .= '<div class="col-lg-10 col-md-12 col-sm-12 col-xs-12">';
                break;
            case 8:
                $html .= '<div class="col-lg-2 col-md-1 col-sm-0 col-xs-0"></div>';
                $html .= '<div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">';
                break;
            case 6:
                $html .= '<div class="col-lg-3 col-md-2 col-sm-1 col-xs-0"></div>';
                $html .= '<div class="col-lg-6 col-md-8 col-sm-10 col-xs-12">';
                break;
            case 4:
                $html .= '<div class="col-lg-4 col-md-3 col-sm-2 col-xs-1"></div>';
                $html .= '<div class="col-lg-4 col-md-6 col-sm-8 col-xs-10">';
                break;
            case 2:
                $html .= '<div class="col-lg-5 col-md-4 col-sm-3 col-xs-2"></div>';
                $html .= '<div class="col-lg-2 col-md-4 col-sm-6 col-xs-8">';
                break;
        }
        $html .= '<div class="widget-box">';
        if (!is_null($titulo)) {
            $html .= '<div class="widget-header widget-header-flat">';
            $html .= '<h4 class="widget-title"><i class="ace-icon fa fa-' . $fa . '"></i>' . $titulo . '</h4>';
            $html .= '</div>';
        }
        $html .= '<div class="widget-body">';
        $html .= '<div class="widget-main no-padding">';
    } elseif ($tipo == 'full') {
        $html .= '<div class="' . $classFull . '" id="' . $id . '" style="' . $style . '">';
//if ($botaoNovo || $filtro || $export) {
        if ($filtro || $export) {
            $html .= '<div class="clearfix headList">';
            $html .= '<div class="formFiltros">';
//            if ($botaoNovo) {
//                $html .= '<button id="btn_novo" class="btn btn-sm btn-success"><i class="ace-icon fa fa-' . $iconeBotaoNovo . ' align-top bigger-125"></i>' . $tituloBotaoNovo . '</button>';
//            }
            if ($filtro) {
                $html .= $filtro;
            }
            $html .= '</div>';
            $html .= '<div class="formVisao">';
            if ($visao) {
                $html .= $visao;
            }
            $html .= '</div>';
            if ($export) {
                $html .= '<div class="pull-right tableTools-container"></div>';
            }
            $html .= '</div>';
        }
        $btnNovo = '';
        if (($botaoNovo) && ($botaoNovoPermissao == '' || GSecurity::verificarPermissao($botaoNovoPermissao, false))) {
            $btnNovo = '<button id="btn_novo" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoNovo . '" title="' . $tituloBotaoNovo . '"><i class="ace-icon fa fa-' . $iconeBotaoNovo . ' align-top bigger-125"></i> ' . $tituloBotaoNovo . '</button> ';
        }
        $btnExcel = '';
        if (($botaoExcel) && ($botaoExcelPermissao == '' || GSecurity::verificarPermissao($botaoExcelPermissao, false))) {
            $btnExcel = '<button id="btn_excel" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoExcel . '" title="' . $tituloBotaoExcel . '"><i class="ace-icon fa fa-' . $iconeBotaoExcel . ' align-top bigger-125"></i> ' . $tituloBotaoExcel . '</button> ';
        }
        $btnOrdenar = '';
        if ($botaoOrdenar) {
            $btnOrdenar = '<button id="btn_ordenar" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoOrdenar . '" title="' . $tituloBotaoOrdenar . '"><i class="ace-icon fa fa-' . $iconeBotaoOrdenar . ' align-top bigger-125"></i> ' . $tituloBotaoOrdenar . '</button> ';
        }
        $btnPdf = '';
        if (($botaoPdf) && ($botaoPdfPermissao == '' || GSecurity::verificarPermissao($botaoPdfPermissao, false))) {
            $btnPdf = '<button id="btn_pdf" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoPdf . '" title="' . $tituloBotaoPdf . '"><i class="ace-icon fa fa-' . $iconeBotaoPdf . ' align-top bigger-125"></i> ' . $tituloBotaoPdf . '</button> ';
        }
        $btnLote = '';
        if (($botaoLoteManut) && ($botaoLoteManutPermissao == '' || GSecurity::verificarPermissao($botaoLoteManutPermissao, false))) {
            $btnLote .= '<button id="btn_lote_manut" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoLoteManut . '" title="' . $tituloBotaoLoteManut . '"><i class="ace-icon fa fa-' . $iconeBotaoLoteManut . ' align-top bigger-125"></i> ' . $tituloBotaoLoteManut . '</button> ';
        }
        if (($botaoLoteIns) && ($botaoLoteInsPermissao == '' || GSecurity::verificarPermissao($botaoLoteInsPermissao, false))) {
            $btnLote .= '<button id="btn_lote_ins" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoLoteIns . '" title="' . $tituloBotaoLoteIns . '"><i class="ace-icon fa fa-' . $iconeBotaoLoteIns . ' align-top bigger-125"></i> ' . $tituloBotaoLoteIns . '</button> ';
        }
        if (($botaoLoteDel) && ($botaoLoteDelPermissao == '' || GSecurity::verificarPermissao($botaoLoteDelPermissao, false))) {
            $btnLote .= '<button id="btn_lote_del" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoLoteDel . '" title="' . $tituloBotaoLoteDel . '"><i class="ace-icon fa fa-' . $iconeBotaoLoteDel . ' align-top bigger-125"></i> ' . $tituloBotaoLoteDel . '</button> ';
        }
        if (($botaoLoteConf) && ($botaoLoteConfPermissao == '' || GSecurity::verificarPermissao($botaoLoteConfPermissao, false))) {
            $btnLote .= '<button id="btn_lote_conf" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoLoteConf . '" title="' . $tituloBotaoLoteConf . '"><i class="ace-icon fa fa-' . $iconeBotaoLoteConf . ' align-top bigger-125"></i> ' . $tituloBotaoLoteConf . '</button> ';
        }
        if (($botaoLoteTodos) && ($botaoLoteTodosPermissao == '' || GSecurity::verificarPermissao($botaoLoteTodosPermissao, false))) {
            $btnLote .= '<button id="btn_sel_todos" class="btn btn-sm botaoCabecalhoLeft btn-yellow" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoLoteTodos . '" title="' . $tituloBotaoLoteTodos . '"><i class="ace-icon fa fa-' . $iconeBotaoLoteTodos . ' align-top bigger-125"></i> ' . $tituloBotaoLoteTodos . '</button> ';
        }
        $btnAtualizar = '';
        if ($botaoAtualizar) {
            $btnAtualizar = '<button id="btn_atualizar" class="btn btn-sm btn-info botaoCabecalho" data-toggle="tooltip" data-placement="top" alt="Atualizar" title="Atualizar"><i class="ace-icon fa fa-refresh align-top bigger-125"></i> Atualizar</button> ';
        }
        $html .= '<div class="table-header">' . $btnNovo . $btnExcel . $btnPdf . $btnOrdenar . $btnLote . '<span class="tituloCaixaItens">' . $titulo . '</span>' . $btnAtualizar . '</div>';
    } elseif ($tipo == 'frame') {
        $html .= '<div class="row">';
        $html .= '<div class="col-xs-12" id="' . $id . '" style="' . $style . '">';
        if ($botaoNovo || $botaoExcel || $filtro || $export) {
            $html .= '<div class="clearfix headList">';
            $html .= '<div class="formFiltros">';
            if ($botaoNovo) {
                $html .= '<button id="btn_novo" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="bottom" alt="' . $tituloBotaoNovo . '" title="' . $tituloBotaoNovo . '"><i class="ace-icon fa fa-' . $iconeBotaoNovo . ' align-top bigger-125"></i>' . $tituloBotaoNovo . '</button>';
            }
            if ($botaoExcel) {
                $html .= '<button id="btn_excel" class="btn btn-sm btn-success" data-toggle="tooltip" data-placement="top" alt="' . $tituloBotaoExcel . '" title="' . $tituloBotaoExcel . '"><i class="ace-icon fa fa-' . $iconeBotaoExcel . ' align-top bigger-125"></i>' . $tituloBotaoExcel . '</button>';
            }
            if ($filtro) {
                $html .= $filtro;
            }
            $html .= '</div>';
            if ($export) {
                $html .= '<div class="pull-right tableTools-container"></div>';
            }
            $html .= '</div>';
        }
        $html .= '<div class="table-header">' . $titulo . '</div>';
    } elseif ($tipo == 'fixo') {
        $html .= '<div class="col-xs-12" id="' . $id . '" style="' . $style . '">';
        $html .= '<div class="row">';
        $html .= '<div style="width: ' . $width . '; margin:auto;">';
        $html .= '<div class="widget-box">';
        if (!is_null($titulo)) {
            $html .= '<div class="widget-header widget-header-flat">';
            $html .= '<h4 class="widget-title"><i class="ace-icon fa fa-' . $fa . '"></i>' . $titulo . '</h4>';
            $html .= '</div>';
        }
        $html .= '<div class="widget-body">';
        $html .= '<div class="widget-main no-padding">';
    }

    return $html;
}

/**
 * Gera html para rodape
 *
 * @param array 'col' => Tamanho da coluna principal
 * @return string
 */
function gerarRodape($param) {
    $tipo = 'box';
    $col = 4;
    $voltar = false;
    extract($param);
//$secundaria = ((12 - $col) / 2);
    $html = '';
    if ($tipo == 'box') {
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        switch ($col) {
            case 10:
                $html .= '<div class="col-lg-1 col-md-0 col-sm-0 col-xs-0"></div>';
                break;
            case 8:
                $html .= '<div class="col-lg-2 col-md-1 col-sm-0 col-xs-0"></div>';
                break;
            case 6:
                $html .= '<div class="col-lg-3 col-md-2 col-sm-1 col-xs-0"></div>';
                break;
            case 4:
                $html .= '<div class="col-lg-4 col-md-3 col-sm-2 col-xs-1"></div>';
                break;
            case 2:
                $html .= '<div class="col-lg-5 col-md-4 col-sm-3 col-xs-2"></div>';
                break;
        }
        $html .= '</div>';
        $html .= '</div><!-- col-xs-12 -->';
    } elseif ($tipo == 'full') {
        if ($voltar) {
            $html .= '<div class="center divBotoes">';
            $html .= '<button type="button" data-toggle="tooltip" data-placement="top" alt="Voltar" title="Voltar" onclick="history.back()" class="btn btn-sm btn-info tooltip-info"><i class="ace-icon fa fa-undo bigger-110"></i>Voltar</button> ';
            $html .= '</div>';
        }
        $html .= '</div>';
        $html .= '</div><!-- col-xs-12 -->';
    } elseif ($tipo == 'frame') {
        $html .= '</div>';
        $html .= '</div><!-- col-xs-12 -->';
        $html .= '</div>'; //row
    } elseif ($tipo == 'fixo') {
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div><!-- col-xs-12 -->';
    }
    $html .= '<script>$(\'[data-toggle="tooltip"]\').tooltip();</script>';
    return $html;
}

/**
 * Gera um html para exibição dos campos em página de visualização
 * 
 * @param type $array
 * @return string
 */
function gerarCamposVisualizacao($array) {
    $html = '';
    $html .= '<div class="profile-user-info profile-user-info-striped" style="width:100%">';
    foreach ($array as $titulo => $valor) {
        $html .= '<div class="profile-info-row">';
        $html .= '<div class="profile-info-name">' . $titulo . '</div>';
        $html .= '<div class="profile-info-value">' . $valor . '</div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Gerar tabela com dados
 * 
 * @param string $id id da tabela
 * @param string $url url de carregamento dos dados 
 * @param string $filtros html com campos de filtros da tabela
 * @param array $colunas array das colunas da tabela
 * @param array $acoes Botões de açcões da tabela
 * @param int $countRows quantidade de registros por página
 * @param bool $checklist exibir checkbox para seleção de itens
 * @param bool $paginacao exibir paginação
 * @param bool $exportacao exibir botoes para exportações
 * @param bool $pesquisa exibir o campo de pesquisa
 * @return string
 */
function getTableData($id, $url, $filtros, $colunas = array(), $acoes = false, $countRows = 25, $checklist = false, $paginacao = true, $exportacao = true, $pesquisa = true) {
    $html = '';
    if (count($colunas) > 0) {
        $html .= '<table id="' . $id . '" class="table table-striped table-bordered table-hover table-responsive">';
        $html .= '<thead>';
        $html .= '<tr>';
        foreach ($colunas as $coluna) {
            $html .= '<th>' . $coluna["titulo"] . '</th>';
            $widths[] = isset($coluna["largura"]) ? $coluna["largura"] : false;
            $orders[] = isset($coluna["ordem"]) ? $coluna["ordem"] : true;
            $visibles[] = isset($coluna["visivel"]) ? $coluna["visivel"] : true;
            $class[] = isset($coluna["classe"]) ? $coluna["classe"] : 'left';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '</table>';
        $html .= getScriptsTable($id, $url, 'false', $filtros, $acoes, $visibles, $class, $orders, $widths, $countRows, $checklist, $paginacao, $exportacao, $pesquisa);
    }
    return $html;
}

/**
 * Gerar tabela com dados
 * 
 * @param string $id id da tabela
 * @param string $url url de carregamento dos dados 
 * @param string $filtros html com campos de filtros da tabela
 * @param array $colunas array das colunas da tabela
 * @param array $acoes Botões de açcões da tabela
 * @param int $countRows quantidade de registros por página
 * @param bool $checklist exibir checkbox para seleção de itens
 * @param bool $paginacao exibir paginação
 * @param bool $exportacao exibir botoes para exportações
 * @return string
 */
function getTableDataServerSide($id, $url, $filtros, $colunas = array(), $acoes = false, $countRows = 25, $checklist = false, $paginacao = true, $exportacao = true, $pesquisa = true) {
    $html = '';
    if (count($colunas) > 0) {
        $html .= '<table id="' . $id . '" class="table table-striped table-bordered table-hover table-responsive">';
        $html .= '<thead>';
        $html .= '<tr>';
        foreach ($colunas as $coluna) {
            $html .= '<th>' . $coluna["titulo"] . '</th>';
            $widths[] = isset($coluna["largura"]) ? $coluna["largura"] : false;
            $orders[] = isset($coluna["ordem"]) ? $coluna["ordem"] : true;
            $visibles[] = isset($coluna["visivel"]) ? $coluna["visivel"] : true;
            $class[] = 'nowrap ' . (isset($coluna["classe"]) ? $coluna["classe"] : 'left');
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '</table>';
        $html .= getScriptsTable($id, $url, 'true', $filtros, $acoes, $visibles, $class, $orders, $widths, $countRows, $checklist, $paginacao, $exportacao, $pesquisa);
    }
    return $html;
}

/**
 * carrega um html com os botões de ações da tabela
 * 
 * @param array $param
 * @return string
 */
function carregarBotoesGrid($param) {
    $html = '';
    $li = '';
    $html .= '<div class="action-buttons">'; //hidden-sm hidden-xs
    foreach ($param as $key => $value) {
        switch ($key) {
            // <editor-fold defaultstate="collapsed" desc="Green">
            case "update":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Alterar"><i class="ace-icon fa fa-pencil bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Alterar"><span class="green"><i class="ace-icon fa fa-pencil bigger-120"></i></span></a></li>';
                break;
            case "active":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ativar"><i class="ace-icon fa fa-check bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Ativar"><span class="green"><i class="ace-icon fa fa-check bigger-120"></i></span></a></li>';
                break;
            case "aceitar":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Aceitar"><i class="ace-icon fa fa-check bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Aceitar"><span class="green"><i class="ace-icon fa fa-check bigger-120"></i></span></a></li>';
                break;
            case "finalizar":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Finalizar Correção"><i class="ace-icon fa fa-flag-checkered bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Finalizar Correção"><span class="green"><i class="ace-icon fa fa-flag-checkered bigger-120"></i></span></a></li>';
                break;
            case "ativar":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ativar"><i class="ace-icon fa fa-thumbs-up bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Ativar"><span class="green"><i class="ace-icon fa fa-thumbs-up bigger-120"></i></span></a></li>';
                break;
            case "selecionar":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Selecionar"><i class="ace-icon fa fa-eye-slash bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Selecionar"><span class="green"><i class="ace-icon fa fa-eye-slash bigger-120"></i></span></a></li>';
                break;
            case "naoLida":
                $html .= '<a class="green tooltip-success __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Marcar como Não Lida"><i class="ace-icon fa fa-eye-slash bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-success __pointer" data-toggle="tooltip" title="Marcar como Não Lida"><span class="green"><i class="ace-icon fa fa-eye-slash bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="Blue-dark">
            case "arquivo":
                $html .= '<a class="blue-dark tooltip-info __pointer" href="' . $value . '" data-toggle="tooltip" data-placement="top" title="Visualizar Arquivo" target="_blanc"><i class="ace-icon fa fa-external-link-square bigger-130"></i></a>';
                $li .= '<li><a href="' . $value . '" class="tooltip-info __pointer" data-toggle="tooltip" title="Visualizar Arquivo" target="_blanc"><span class="blue-dark"><i class="ace-icon fa fa-external-link-square bigger-120"></i></span></a></li>';
                break;
            case "inscricoes":
                $html .= '<a class="blue-dark tooltip-info __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Inscrições" target="_blanc"><i class="ace-icon fa fa-users bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-info __pointer" data-toggle="tooltip" title="Inscrições" target="_blanc"><span class="blue-dark"><i class="ace-icon fa fa-users bigger-120"></i></span></a></li>';
                break;
            case "view":
                $html .= '<a class="blue-dark tooltip-info __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ver"><i class="ace-icon fa fa-search-plus bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-info __pointer" data-toggle="tooltip" title="Ver"><span class="blue-dark"><i class="ace-icon fa fa-search-plus bigger-120"></i></span></a></li>';
                break;
            case "detail":
                $html .= '<a class="blue-dark tooltip-info __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ver Detalhes"><i class="ace-icon fa fa-eye bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-info __pointer" data-toggle="tooltip" title="Ver Detalhes"><span class="blue-dark"><i class="ace-icon fa fa-eye bigger-120"></i></span></a></li>';
                break;
            case "grupos":
                $html .= '<a class="blue-dark tooltip-info __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Grupos"><i class="ace-icon fa fa-users bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-info __pointer" data-toggle="tooltip" title="Grupos"><span class="blue-dark"><i class="ace-icon fa fa-users bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="Red">
            case "delete":
                $html .= '<a class="red tooltip-error __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Excluir"><i class="ace-icon fa fa-trash bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-error __pointer" data-toggle="tooltip" title="Excluir"><span class="red"><i class="ace-icon fa fa-trash bigger-120"></i></span></a></li>';
                break;
            case "cancelar":
                $html .= '<a class="red tooltip-error __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Cancelar"><i class="ace-icon fa fa-ban bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-error __pointer" data-toggle="tooltip" title="Cancelar"><span class="red"><i class="ace-icon fa fa-ban bigger-120"></i></span></a></li>';
                break;
            case "duplicar":
                $html .= '<a class="red tooltip-error __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Clonar"><i class="ace-icon fa fa-clone bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-error __pointer" data-toggle="tooltip" title="Clonar"><span class="red"><i class="ace-icon fa fa-clone bigger-120"></i></span></a></li>';
                break;
            case "inativar":
                $html .= '<a class="red tooltip-error __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Inativar"><i class="ace-icon fa fa-thumbs-down bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-error __pointer" data-toggle="tooltip" title="Inativar"><span class="red"><i class="ace-icon fa fa-thumbs-down bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="Orange">
            case "objetivo":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Objetivos"><i class="ace-icon fa fa-bullseye bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Objetivos"><span class="orange"><i class="ace-icon fa fa-bullseye bigger-120"></i></span></a></li>';
                break;
            case "responder":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Responder"><i class="ace-icon fa fa-edit bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Responder"><span class="orange"><i class="ace-icon fa fa-edit bigger-120"></i></span></a></li>';
                break;
            case "logarcomo":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Representar"><i class="ace-icon fa fa-magic bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Representar"><span class="orange"><i class="ace-icon fa fa-magic bigger-120"></i></span></a></li>';
                break;
            case "lida":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Marcar como Lida"><i class="ace-icon fa fa-eye bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Marcar como Lida"><span class="orange"><i class="ace-icon fa fa-eye bigger-120"></i></span></a></li>';
                break;
            case "disciplina":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Disciplinas"><i class="ace-icon fa fa-newspaper-o bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Disciplinas"><span class="orange"><i class="ace-icon fa fa-newspaper-o bigger-120"></i></span></a></li>';
                break;
            case "usuarios":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Usuários"><i class="ace-icon fa fa-users bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Usuários"><span class="orange"><i class="ace-icon fa fa-users bigger-120"></i></span></a></li>';
                break;
            case "documento":
                $html .= '<a class="orange tooltip-warning __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Documentos"><i class="ace-icon fa fa-file-text-o bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-warning __pointer" data-toggle="tooltip" title="Documentos"><span class="orange"><i class="ace-icon fa fa-file-text-o bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="Pink">
            case "naolida":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Marcar como Não Lida"><i class="ace-icon fa fa-envelope-open bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Marcar como Não Lida"><span class="pink"><i class="ace-icon fa fa-envelope-open bigger-120"></i></span></a></li>';
                break;
            case "usuario":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Usuário"><i class="ace-icon fa fa-user bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Usuário"><span class="pink"><i class="ace-icon fa fa-user bigger-120"></i></span></a></li>';
                break;
            case "enviarsenha":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Enviar Nova Senha"><i class="ace-icon fa fa-key bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Enviar Nova Senha"><span class="pink"><i class="ace-icon fa fa-key bigger-120"></i></span></a></li>';
                break;
            case "enviaremail":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Enviar Email"><i class="ace-icon fa fa-paper-plane bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Enviar Email"><span class="pink"><i class="ace-icon fa fa-paper-plane bigger-120"></i></span></a></li>';
                break;
            case "testar":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Testar Modelo"><i class="ace-icon fa fa-cogs bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Testar Modelo"><span class="pink"><i class="ace-icon fa fa-cogs bigger-120"></i></span></a></li>';
                break;
            case "filtros":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Filtros"><i class="ace-icon fa fa-filter bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Filtros"><span class="pink"><i class="ace-icon fa fa-filter bigger-120"></i></span></a></li>';
                break;
            case "matriculas":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Matrículas"><i class="ace-icon fa fa-graduation-cap bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Matrículas"><span class="pink"><i class="ace-icon fa fa-graduation-cap bigger-120"></i></span></a></li>';
                break;
            case "processamentos":
                $html .= '<a class="pink tooltip-pink __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Processamentos"><i class="ace-icon fa fa-cogs bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-pink __pointer" data-toggle="tooltip" title="Processamentos"><span class="pink"><i class="ace-icon fa fa-cogs bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="Grey">
            case "voltar":
                $html .= '<a class="grey tooltip-grey __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Voltar"><i class="ace-icon fa fa-backward bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-grey __pointer" data-toggle="tooltip" title="Voltar"><span class="grey"><i class="ace-icon fa fa-backward bigger-120"></i></span></a></li>';
                break;
            case "popup":
                $html .= '<a class="grey tooltip-grey __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Visualizar"><i class="ace-icon fa fa-arrows-alt bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-grey __pointer" data-toggle="tooltip" title="Visualizar"><span class="grey"><i class="ace-icon fa fa-arrows-alt bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            // <editor-fold defaultstate="collapsed" desc="Purple">
            case "grade":
                $html .= '<a class="purple tooltip-purple __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Grades"><i class="ace-icon fa fa-table bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-purple __pointer" data-toggle="tooltip" title="Grades"><span class="purple"><i class="ace-icon fa fa-table bigger-120"></i></span></a></li>';
                break;
            case "clonar":
                $html .= '<a class="purple tooltip-purple __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Clonar"><i class="ace-icon fa fa-clone bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-purple __pointer" data-toggle="tooltip" title="Clonar"><span class="purple"><i class="ace-icon fa fa-clone bigger-120"></i></span></a></li>';
                break;
            case "historico":
                $html .= '<a class="purple tooltip-purple __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ver Histórico de Acessos"><i class="ace-icon fa fa-history bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-purple __pointer" data-toggle="tooltip" title="Ver Histórico de Acessos"><span class="purple"><i class="ace-icon fa fa-history bigger-120"></i></span></a></li>';
                break;
            case "auditoria":
                $html .= '<a class="purple tooltip-purple __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ver Auditoria dos dados"><i class="ace-icon fa fa-history bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-purple __pointer" data-toggle="tooltip" title="Ver Auditoria dos dados"><span class="purple"><i class="ace-icon fa fa-history bigger-120"></i></span></a></li>';
                break;
            case "matricular":
                $html .= '<a class="purple tooltip-purple __pointer" onclick="' . $value . '" data-toggle="tooltip" data-placement="top" title="Ações nas Matrícular dos Alunos por Oferta"><i class="ace-icon fa fa-graduation-cap bigger-130"></i></a>';
                $li .= '<li><a onclick="' . $value . '" class="tooltip-purple __pointer" data-toggle="tooltip" title="Ações nas Matrícular dos Alunos por Oferta"><span class="purple"><i class="ace-icon fa fa-graduation-cap bigger-120"></i></span></a></li>';
                break;
            // </editor-fold>
            default:
                if (ehLuiz()) {
                    echo '<pre>';
                    var_dump("Botão: " . $key . " Não configurado");
                    echo '</pre>';
                }
                break;
        }
    }
    $html .= '</div>';
    return $html;
}

/**
 * Gera o script de exibição de uma tabela com dados, filtro, paginação etc...
 * 
 * @param string $id
 * @param string $url
 * @param array $filters
 * @param array $param
 * @param array $visibles
 * @param array $class
 * @param array $orders
 * @param array $widths
 * @param int $countRows
 * @param bool $checklist
 * @param bool $paginacao
 * @param bool $exportacao
 * @return string
 */
function getScriptsTable($id, $url = "load.php", $serverSide = 'false', $filters = false, $param = false, $visibles = array(false, true, false), $class = array('center', 'left', 'center'), $orders = array(false, true, false), $widths = array(false, false, "80px"), $countRows = 25, $checklist = false, $paginacao = true, $exportacao = true, $pesquisa = true, $total_footer = false) {
    $orderColumns = '';
    foreach ($orders as $value) {
        $orderColumns .= '{"bSortable": ' . ($value ? "true" : "false") . '},';
    }
    $orderColumns = substr($orderColumns, 0, -1);

    $visibleColumns = '';
    $i = 0;
    foreach ($visibles as $value) {
        $visibleColumns .= '{ "visible" : ' . ($value ? "true" : "false") . ', ' . (($widths[$i] === false) ? '' : '"width" : "' . $widths[$i] . '", ') . '"targets": [' . $i . '], ' . (($class[$i]) ? '"className" : "' . $class[$i] . ' valign-middle", ' : '') . '"targets": [' . $i . '] },';
        $i++;
    }
    $visibleColumns = substr($visibleColumns, 0, -1);

    $data = '';
    if ($filters) {
        $data .= ', "data": ';
        $data .= 'function (d) {';
        foreach ($filters as $filter) {
            $data .= 'd.' . $filter . '=$("#' . $filter . '").val();';
        }
        $data .= '}';
    }

    $paging = ($paginacao) ? '"paging": true,' : '"paging": false,';
    $countRows = ($countRows === false) ? 25 : $countRows;
    $bPaginate = (!$paginacao) ? 'bPaginate: false, "bInfo": false, ' : '';
    $infoFiltered = '';
    if ($paginacao) {
        $infoFiltered = '(Filtrando de _MAX_ registros total)';
    }

    $style = '';
    $script = '';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/jquery.dataTables.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/jquery.dataTables.bootstrap.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/dataTables.buttons.min.js"></script>';
//    $script .= '<script src="' . URL_SYS_TEMA . 'js/dataTables.fixedColumns.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/buttons.flash.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/buttons.html5.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/buttons.print.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/buttons.colVis.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/moment.min.js"></script>';
    $script .= '<script src="' . URL_SYS_TEMA . 'js/datetime-moment.js"></script>';
    if ($checklist)
        $script .= '<script src="' . URL_SYS_TEMA . 'js/dataTables.select.min.js"></script>';
    $script .= '<script type="text/javascript">';
    $script .= 'var ' . $id . ' = null; ';
    $script .= 'jQuery(function ($) {';
    $script .= '    ' . $id . ' = $("#' . $id . '").DataTable({' . $paging . ' responsive: true, fixedColumns: { left: 1 }, /*scrollX: true, scrollCollapse: true,*/ "searching": ' . ($pesquisa ? 'true' : 'false') . ', "serverSide": ' . $serverSide . ', "processing": ' . $serverSide . ', "ajax": {
        "url": "' . $url . '", "type": \'POST\' ' . $data . '},  "columnDefs": [ ' . (($param) ? '{"targets": -1, "render": function (data,type,row){ return \'' . carregarBotoesGrid($param) . '\';} }, ' : '') . $visibleColumns . '], bAutoWidth: false, "aoColumns": [' . $orderColumns . '], "aaSorting": [], "pageLength": ' . $countRows . ', ' . $bPaginate . ' ';
    if ($checklist)
        $script .= ' select: {style: "multi"}, ';
    $script .= '    language: { processing: "<i class=\'ace-icon fa fa-spinner fa-spin orange bigger-200\'></i> Carregando...", search: "Procurar:", lengthMenu: "Exibir _MENU_ registros",info: "Mostrando de <b>_START_</b> &agrave; <b>_END_</b> de <b>_TOTAL_</b> registros",infoEmpty: "Mostrando 0 &agrave; 0 de 0 registros", infoFiltered: "' . $infoFiltered . '",  infoPostFix: "", loadingRecords: "<i class=\'ace-icon fa fa-spinner fa-spin orange bigger-200\'></i> Carregando...", zeroRecords: "<b style=\"color: #d42e2e;text-align: center; display: block;font-weight: initial;\">Nenhum registro foi encontrado</b>", emptyTable: "<b class=\"nenhumDado\">Nenhum registro foi encontrado</b>", paginate: { first: "Primeiro", previous: "Anterior", next: "Próximo",last: "Último"},aria: {sortAscending:": Ordenar de forma crescente",sortDescending: ": Ordenar de forma decrescente"}, select: {rows: "  <i><b>%d</b> linha(s) selecionada(s)</i>"}}});';
    if ($exportacao) {
        $script .= '    $.fn.dataTable.Buttons.defaults.dom.container.className = "dt-buttons btn-overlap btn-group btn-overlap";';
        $script .= '    new $.fn.dataTable.Buttons(' . $id . ', {';
        $script .= '            buttons: [{"extend": "colvis", "text": "<i class=\'fa fa-low-vision bigger-110 blue-dark\'></i> <span class=\'hidden\'>Exibir/Ocultar colunas</span>", "className": "btn btn-white btn-primary btn-bold",columns: \':not(:first)\'},'; //:not(:last)
        $script .= '                      {"extend": "copy", "text": "<i class=\'fa fa-copy bigger-110 pink\'></i> <span class=\'hidden\'>Copiar para área de transferência</span>","className": "btn btn-white btn-primary btn-bold"},';
        $script .= '                      {"extend": "csv", "text": "<i class=\'fa fa-file-excel-o bigger-110 green\'></i> <span class=\'hidden\'>Exportar para CSV</span>", "className": "btn btn-white btn-primary btn-bold"}';
        $script .= '                      ]});';
        $script .= '    ' . $id . '.buttons().container().appendTo($(".tableTools-container"));';
//style the message box
        $script .= '    var defaultCopyAction = ' . $id . '.button(1).action();';
        $script .= '    ' . $id . '.button(1).action(function (e, dt, button, config) { defaultCopyAction(e, dt, button, config); $(".dt-button-info").addClass("gritter-item-wrapper gritter-info gritter-center white");});';
        $script .= '    var defaultColvisAction = ' . $id . '.button(0).action();';
        $script .= '    ' . $id . '.button(0).action(function (e, dt, button, config) { defaultColvisAction(e, dt, button, config); if ($(".dt-button-collection > .dropdown-menu").length == 0) { $(".dt-button-collection").wrapInner(\'<ul class="dropdown-menu dropdown-light dropdown-caret dropdown-caret" />\').find("a").attr("href", "#").wrap("<li />")}';
        $script .= '    $(".dt-button-collection").appendTo(".tableTools-container .dt-buttons")});';
        $script .= '    setTimeout(function () {$($(".tableTools-container")).find("a.dt-button").each(function () { var div = $(this).find(" > div").first(); if (div.length == 1) div.tooltip({container: "body", title: div.parent().text()}); else $(this).tooltip({container: "body", title: $(this).text()});});}, 500);';
        $script .= '    ' . $id . '.on("select", function (e, dt, type, index) { if (type === "row") { $(' . $id . '.row(index).node()).find("input:checkbox").prop("checked", true);}});';
        $script .= '    ' . $id . '.on("deselect", function (e, dt, type, index) { if (type === "row") { $(' . $id . '.row(index).node()).find("input:checkbox").prop("checked", false);}});';
    } else {
        // $style = '<style> .headList { display: none;} </style>';
    }
    if ($checklist) {
//    table checkboxes
        $script .= '    $("th input[type=checkbox], td input[type=checkbox]").prop("checked", false);';
//    select/deselect all rows according to table header checkbox
        $script .= '    $("#' . $id . ' > thead > tr > th input[type=checkbox], #' . $id . '_wrapper input[type=checkbox]").eq(0).on("click", function () { var th_checked = this.checked;';
        $script .= '      $("#' . $id . '").find("tbody > tr").each(function () { var row = this; if (th_checked) ' . $id . '.row(row).select(); else ' . $id . '.row(row).deselect();});';
        $script .= '    });';
//    select/deselect a row when the checkbox is checked/unchecked
        $script .= '    $("#' . $id . '").on("click", "td input[type=checkbox]", function () { var row = $(this).closest("tr").get(0); if (this.checked) ' . $id . '.row(row).deselect(); else ' . $id . '.row(row).select(); });';
        $script .= '    $(document).on("click", "#' . $id . ' .dropdown-toggle", function (e) { e.stopImmediatePropagation(); e.stopPropagation(); e.preventDefault(); });';
//    add tooltip for small view action buttons in dropdown menu
        $script .= '    $(\'[data-toggle="tooltip"]\').tooltip({placement: tooltip_placement});';
        $script .= '    $(".show-details-btn").on("click", function (e) { e.preventDefault(); $(this).closest("tr").next().toggleClass("open"); $(this).find(ace.vars[".icon"]).toggleClass("fa-angle-double-down").toggleClass("fa-angle-double-up");});';
    }
    $script .= '	' . $id . '.on("draw.dt", function(){ $(\'[data-toggle="tooltip"]\').tooltip() });';
    $script .= '	$("#' . $id . '").parent().addClass("table-responsive").attr("id", "' . $id . '_geral");';
//    $script .= '	$("#' . $id . '").parent().addClass("table-responsive").css("overflow-x", "inherit");';
//    $script .= '	$("#' . $id . '").parent().parent().addClass("table-responsive").css("overflow-x", "auto");';
    $script .= '	$("#' . $id . '_wrapper").find(".dataTables_length").parent().removeClass("col-sm-6").addClass("col-xs-6");';
    $script .= '	$("#' . $id . '_wrapper").find(".dataTables_filter").parent().removeClass("col-sm-6").addClass("col-xs-6");';
    if ($total_footer) {
        $script .= '	$("#' . $id . '_wrapper").find(".row:nth-child(3)").find(".col-sm-5").remove();';
        $script .= '	$("#' . $id . '_wrapper").find(".row:nth-child(3)").find(".col-sm-7").removeClass("col-sm-7").addClass("col-xs-12").attr("id", "' . $id . '_footer");';
    }

    $script .= '	' . $id . '.on("processing.dt", function (e, settings, processing) { ';
    $script .= '       if (processing) { ';
    $script .= '            jQuery.gDisplay.loadStart("#' . $id . '"); ';
    $script .= '       } else { ';
    $script .= '            jQuery.gDisplay.loadStop("#' . $id . '"); ';
    $script .= '       } ';
    $script .= '       $.fn.dataTable.moment( "DD/MM/YYYY HH:mm:ss" ); ';
    $script .= '       $("div.dataTables_filter input").unbind(); ';
    $script .= '       $("div.dataTables_filter input").keypress(function( event ) { ';
    $script .= '            if ( event.which == 13 ) { ';
    $script .= '                ' . $id . '.search($("div.dataTables_filter input").val()).draw(); ';
    $script .= '            } ';
    $script .= '        }); ';
    $script .= '   });';

    $script .= '});';
    $script .= '</script>';
    return $script . $style;
}

/**
 * Gera o label específico para cata tipo de status
 * 
 * @param string $status
 * @return string
 */
function labelStatus($status) {
    switch ($status) {
        case '0':
        case 'I':
        case 'E':
        case 'N':
            return 'danger';
            break;
        case '1':
        case 'A':
        case 'S':
        case 'D':
        case 'R':
        case 'P':
            return 'success';
            break;
        case '2':
        case 'U':
        case 'L':
            return 'warning';
            break;
        case '-':
        case 'M':
            return 'info';
            break;
        case '*':
            return 'inverse';
            break;
        default:
            return 'info';
            break;
    }
}

/**
 * Gera o label específico para cada status do evento
 * 
 * @param string $status
 * @return string
 */
function labelStatusEvento($status) {
    switch ($status) {
        case 'E':
            return 'danger';
            break;
        case 'S':
            return 'success';
            break;
        case 'A':
            return 'warning';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Gera o label específico para cada tipo de processamento
 * 
 * @param string $tipo
 * @return string
 */
function labelTipoProcessamento($tipo) {
    switch ($tipo) {
        case 'N':
            return 'pink';
            break;
        case 'M':
            return 'info';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Gera o label específico para cada status do agendamento
 * 
 * @param string $status
 * @return string
 */
function labelStatusAgendamento($status) {
    switch ($status) {
        case 'E':
            return 'danger';
            break;
        case 'P':
            return 'success';
            break;
        case 'A':
            return 'info';
            break;
        case 'I':
            return 'warning';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Gera o label específico para cada sexo
 * 
 * @param string $sexo
 * @return string
 */
function labelSexo($sexo) {
    switch ($sexo) {
        case 'F':
            return 'pink';
            break;
        case 'M':
            return 'info';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Gera o label específico para cada tipo de curso
 * 
 * @param string $tipo
 * @return string
 */
function labelTipoCurso($tipo) {
    switch ($tipo) {
        case 'G':
            return 'info';
            break;
        case 'P':
            return 'warning';
            break;
        case 'T':
            return 'pink';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Gera o label específico para cada modalidade de curso
 * 
 * @param string $modalidade
 * @return string
 */
function labelModalidade($modalidade) {
    switch ($modalidade) {
        case 'E':
            return 'info';
            break;
        case 'P':
            return 'warning';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Gera o label específico para cata tipo de status
 * 
 * @param string $status
 * @return string
 */
function labelStatusMatricula($status) {
    switch ($status) {
        case 'C':
            return 'danger';
            break;
        case 'M':
            return 'success';
            break;
        case 'F':
            return 'inverse';
            break;
        default:
            return 'info';
            break;
    }
}

/**
 * Gera o label específico para cada tipo de etapa
 * 
 * @param string $tipo
 * @return string
 */
function labelTipoEtapa($tipo) {
    switch ($tipo) {
        case 'U':
            return 'pink';
            break;
        case 'M':
            return 'success';
            break;
        case 'E':
            return 'purple';
            break;
        case 'X':
            return 'inverse';
            break;
        case 'C':
            return 'danger';
            break;
        default:
            return 'info';
            break;
    }
}

/**
 * Gera o label específico para cata tipo de status de email
 * 
 * @param string $status
 * @return string
 */
function labelStatusEmail($status) {
    switch ($status) {
        case 'R':
            return 'danger';
            break;
        case 'E':
            return 'success';
            break;
        case 'L':
            return 'info';
            break;
        case 'P':
            return 'warning';
            break;
        default:
            return 'inverse';
            break;
    }
}

/**
 * Carrega um html com informações de uma página de erro 500
 * 
 * @return string
 */
function carregarPagina500() {
    $html = '';
    $html .= '	<div class="col-xs-12">';
    $html .= '      <div class="error-container">';
    $html .= '		<div class="well">';
    $html .= '              <h1 class="grey lighter smaller"><span class="blue-dark bigger-125"><i class="ace-icon fa fa-random"></i>500</span> Aconteceu algo de errado</h1>';
    $html .= '              <hr />';
    $html .= '              <h3 class="lighter smaller">Já estamos trabalhando <i class="ace-icon fa fa-wrench icon-animated-wrench bigger-125"></i> para resolver esse inconveniente</h3>';
    $html .= '              <div class="space"></div>';
    $html .= '              <div>';
    $html .= '                  <h4 class="lighter smaller">Enquanto isso, pesquise aqui o que deseja encontrar:</h4>';
    $html .= '			<div>';
    $html .= '                      <form class="form-search">';
    $html .= '                          <span class="input-icon align-middle"><i class="ace-icon fa fa-search"></i><input type="text" class="search-query" placeholder="pesquise aqui..." /></span>';
    $html .= '                          <button class="btn btn-sm" type="button">buscar</button>';
    $html .= '                      </form>';
    $html .= '                      <div class="space"></div>';
    $html .= '			</div>';
    $html .= '              </div>';
    $html .= '              <hr />';
    $html .= '              <div class="space"></div>';
    $html .= '              <div class="center">';
    $html .= '                  <a href="javascript:history.back()" class="btn btn-grey"><i class="ace-icon fa fa-arrow-left"></i>Voltar a página anterior</a>';
    $html .= '                  <a href="' . URL_SYS . 'home/" class="btn btn-primary"><i class="ace-icon fa fa-home"></i>Início</a>';
    $html .= '              </div>';
    $html .= '		</div>';
    $html .= '      </div>';
    $html .= '	</div><!-- /.col -->';
    return $html;
}

/**
 * Função que faz o upload arquivo para o servidor com a opção de 
 * cortar ou não a imagem. Também é possível limitar o tamanho do arquivo
 * 
 * @param $_FILE $file
 * @param string $folder
 * @param bool $imagem
 * @param bool $croppic
 * @param int $min_width
 * @param int $min_height
 * @param boolean $resize
 * @return string
 */
function fazerUpload($file, $folder, $imagem = false, $croppic = false, $min_width = MIN_WIDTH, $min_height = MIN_HEIGHT, $resize = false) {
    $allowedExts = explode(";", EXTENSIONS);
    try {
        if ($file["size"] < MAX_SIZE) {
            $temp = explode(".", $file["name"]);
            $extension = strtolower(end($temp));
            if (!is_writable(ROOT_UPLOAD . $folder)) {
                $response["status"] = 'error';
                $response["message"] = 'Sem permissão no diretório';
                return $response;
            }
            if ((!$imagem) || ($imagem && in_array($extension, $allowedExts))) {
                if ($file["error"] > 0) {
                    $response["status"] = 'error';
                    $response["message"] = 'ERROR Return Code: ' . $file["error"];
                } else {
                    $filename = $file["tmp_name"];
                    if ($croppic) {
                        $filenameNew = "tmp_" . uniqid() . "." . $extension;
                    } else {
                        $filenameNew = uniqid() . "." . $extension;
                    }
                    if ($imagem) {
                        list($width, $height) = getimagesize($filename);
                    } else {
                        $width = 99999;
                        $height = 99999;
                    }
                    if ($resize) {
                        $filenameCompress = uniqid() . "_c." . $extension;
                    }

                    if ($width >= $min_width && $height >= $min_height) {

                        $imageRoot = ROOT_UPLOAD . $folder;

                        move_uploaded_file($filename, $imageRoot . $filenameNew);

                        if ($resize) {
                            GF::carregarLib(array("imagem"));

                            $src = $imageRoot . $filenameNew;
                            $ext = pathinfo($src, PATHINFO_EXTENSION);
                            $nome = pathinfo($src, PATHINFO_FILENAME);

                            $img = new imaging();
                            $img->set_img($src);
                            $img->set_quality(80);
                            $img->set_size(1000, 1000, true);
                            $img->save_img($imageRoot . $nome . '_g.' . $ext, 0, 0, $width, $height);
                            $img->set_quality(80);
                            $img->set_size(600, 600, true);
                            $img->save_img($imageRoot . $nome . '_m.' . $ext, 0, 0, $width, $height);
                            $img->set_quality(80);
                            $img->set_size(300, 300, true);
                            $img->save_img($imageRoot . $nome . '_p.' . $ext, 0, 0, $width, $height);
                            $img->clear_cache();

                            compress($src, $imageRoot . $filenameCompress, 80);
                            deleteImg($imageRoot . $filenameNew);
                        }

                        $response["status"] = 'success';
                        $response["url"] = URL_UPLOAD . $folder . $filenameNew;
                        $response["width"] = $width;
                        $response["height"] = $height;
                        $response["message"] = 'OK';
                    } else {
                        $response["status"] = 'error';
                        $response["message"] = 'Essa imagem não possui o tamanho mínimo necessário que é de Largura: ' . $min_width . 'px e Altura: ' . $min_height . 'px.';
                    }
                }
            } else {
                $response["status"] = 'error';
                $response["message"] = 'Favor escolher entre as opções ' . implode(", ", $allowedExts) . ' de imagem!';
            }
        } else {
            $response["status"] = 'error';
            $response["message"] = 'Arquivo excede o tamanho máximo de ' . formatarBytes(MAX_SIZE);
        }
    } catch (Exception $e) {
        $response["status"] = 'error';
        $response["message"] = 'Algo deu errado, provavelmente o arquivo é grande para upload. Verifique upload_max_filesize, post_max_size e memory_limit em você php.ini';
    }
    if ($response["status"] == 'success') {
        salvarEvento('S', 'Arquivo enviado para o servidor com sucesso.', $response["message"]);
    } else {
        salvarEvento('E', 'Erro ao enviar arquivo para o servidor', $response["message"]);
    }
    return $response;
}

/**
 * Função que faz o upload arquivo para o servidor S3 na Amazon com a opção de 
 * cortar ou não a imagem. Também é possível limitar o tamanho do arquivo
 * 
 * @param $_FILE $file
 * @param string $folder
 * @param bool $imagem
 * @param bool $croppic
 * @param int $min_width
 * @param int $min_height
 * @return string
 */
function fazerUploadS3($file, $folder, $imagem = false, $croppic = false, $min_width = MIN_WIDTH, $min_height = MIN_HEIGHT) {
    $allowedExts = explode(";", EXTENSIONS);
    try {
        if ($file["size"] < MAX_SIZE) {
            $temp = explode(".", $file["name"]);
            $extension = strtolower(end($temp));
            if (!is_writable(ROOT_UPLOAD . $folder)) {
                $response["status"] = 'error';
                $response["message"] = 'Sem permissão no diretório';
                return $response;
            }
            if ((!$imagem) || ($imagem && in_array($extension, $allowedExts))) {
                if ($file["error"] > 0) {
                    $response["status"] = 'error';
                    $response["message"] = 'ERROR Return Code: ' . $file["error"];
                } else {
                    $filename = $file["tmp_name"];
                    if ($croppic) {
                        $filenameNew = "tmp_" . uniqid() . "." . $extension;
                    } else {
                        $filenameNew = uniqid() . "." . $extension;
                    }
                    if ($imagem) {
                        list($width, $height) = getimagesize($filename);
                    } else {
                        $width = 99999;
                        $height = 99999;
                    }
                    if ($width >= $min_width && $height >= $min_height) {

                        move_uploaded_file($filename, ROOT_UPLOAD . $folder . $filenameNew);

                        // <editor-fold desc="Enviar para S3 e depois apagar do disco local">
                        require_once(ROOT_SYS_INC . "aws/AwsS3.php");

                        $s3 = new AwsS3();
                        $ret_env = $s3->Enviar($folder . $filenameNew, ROOT_UPLOAD . $folder . $filenameNew);
                        if ($ret_env["status"]) {
                            // TO-DO: Excluir a imagem do diretório local, pois já está na Amazon
                            //unlink(ROOT_UPLOAD . $folder . $filenameNew);

                            $response["status"] = 'success';
                            $response["url"] = $ret_env["url"];
                            $response["width"] = $width;
                            $response["height"] = $height;
                        } else {
                            $response["status"] = 'error';
                            $response["message"] = 'Desculpa, mas não foi possível enviar esse arquivo, tente novamente e se o problema persistir, favor avisar ao administrador do sistema.';
                            salvarEvento('E', 'Erro ao enviar arquivo para S3', $ret_env["msg"]);
                        }
                        // </editor-fold>
                    } else {
                        $response["status"] = 'error';
                        $response["message"] = 'Essa imagem não possui o tamanho mínimo necessário que é de Largura: ' . $min_width . 'px e Altura: ' . $min_height . 'px.';
                    }
                }
            } else {
                $response["status"] = 'error';
                $response["message"] = 'Favor escolher entre as opções ' . implode(", ", $allowedExts) . ' de imagem!';
            }
        } else {
            $response["status"] = 'error';
            $response["message"] = 'Arquivo excede o tamanho máximo de ' . formatarBytes(MAX_SIZE);
        }
    } catch (Exception $e) {
        $response["status"] = 'error';
        $response["message"] = 'Algo deu errado, provavelmente o arquivo é grande para upload. Verifique upload_max_filesize, post_max_size e memory_limit em você php.ini. ' . $e->getMessage();
    }
    return $response;
}

/**
 * Função que corta a imagem de acordo com um $_POST
 * 
 * @param $_POST $post
 * @param string $imageRoot
 * @param string $imageUrl
 * @return string
 */
function cortarImagem($post, $imageRoot = ROOT_UPLOAD, $imageUrl = URL_UPLOAD) {
    $imgUrl = $post['imgUrl'];
    $imgInitW = $post['imgInitW'];
    $imgInitH = $post['imgInitH'];
    $imgW = $post['imgW'];
    $imgH = $post['imgH'];
    $imgY1 = $post['imgY1'];
    $imgX1 = $post['imgX1'];
    $cropW = $post['cropW'];
    $cropH = $post['cropH'];

    $pathinfo = pathinfo($imgUrl);
    $arrArq = explode(".", $imgUrl);
    $extension = $arrArq[count($arrArq) - 1];
    $arquivo_old = $pathinfo['filename'];
    $filename = str_replace("tmp_", "", $arquivo_old);
    $output_filenameRoot = $imageRoot . $filename;
    $output_filenameUrl = $imageUrl . $filename;

    $what = getimagesize($imgUrl);
    switch (strtolower($what['mime'])) {
        case 'image/png':
            $source_image = imagecreatefrompng($imgUrl);
            break;
        case 'image/jpeg':
            $source_image = imagecreatefromjpeg($imgUrl);
            break;
        case 'image/gif':
            $source_image = imagecreatefromgif($imgUrl);
            break;
        default:
            $response["status"] = 'error';
            $response["message"] = 'Tipo de imagem inválida';
            return $response;
            break;
    }
    if (!is_writable(dirname($output_filenameRoot))) {
        $response["status"] = 'error';
        $response["message"] = 'Sem permissão no diretório';
        return $response;
    } else {
        $cropped = imagecreatetruecolor($imgW, $imgH);
        imagealphablending($cropped, false);
        imagesavealpha($cropped, true);
        imagealphablending($source_image, true);
        ImageCopyResampled($cropped, $source_image, 0, 0, 0, 0, $imgW, $imgH, $imgInitW, $imgInitH);

        $final_image = imagecreatetruecolor($cropW, $cropH);
        imagealphablending($final_image, false);
        imagesavealpha($final_image, true);
        imagealphablending($cropped, true);
        ImageCopyResampled($final_image, $cropped, 0, 0, $imgX1, $imgY1, $cropW, $cropH, $cropW, $cropH);

        imagepng($final_image, $output_filenameRoot . '.' . $extension);
//imagejpeg($final_image, $output_filenameRoot . $type, 100);
        @unlink($imageRoot . $arquivo_old . '.' . $extension);

        $response["status"] = 'success';
        $response["url"] = $output_filenameUrl . '.' . $extension;
    }
    return $response;
}

/**
 * Gera um HTML com informações de uma mensagem específica
 * 
 * @param string $tipo
 * @param string $mensagem
 * @param int $col Default: 10
 * @param boolean $voltar Default: true
 * @return string
 */
function carregarMensagem($tipo, $mensagem, $col = 10, $voltar = true) {
    $retorno = '';
    switch ($tipo) {
        case 'E':
            $icone = 'fa-exclamation-circle';
            $class = 'alert-danger';
            $titulo = 'OPS!';
            break;
        case 'A':
            $icone = 'fa-exclamation-triangle ';
            $class = 'alert-warning';
            $titulo = 'Aviso!';
            break;
        case 'S':
            $icone = 'fa-check';
            $class = 'alert-success';
            $titulo = 'Sucesso!';
            break;
        case 'I':
            $icone = 'fa-info-circle';
            $class = 'alert-info';
            $titulo = 'Informação!';
            break;
        default:
            $icone = 'fa-times';
            $class = 'alert-info';
            $titulo = 'Informação!';
            break;
    }

    $retorno .= '<div class="col-sm-' . $col . '" style="margin: auto; float: none;">';
    $retorno .= '<div class="alert alert-block ' . $class . '" style="margin-bottom:0">';
    $retorno .= '<p><strong class="bigger-150"><i class="ace-icon fa ' . $icone . '"></i> ' . $titulo . '</strong></p>';
    $retorno .= '<p>' . $mensagem . '</p>';
    if ($voltar) {
        $retorno .= '<p><button onClick="history.go(-1);return true;" class="btn btn-sm btn-info"><i class="ace-icon fa fa-arrow-left bigger-110"></i> Voltar</button></p>';
    }
    $retorno .= '</div>';
    $retorno .= '</div>';
    return $retorno;
}

/**
 * Gera um HTML com informações de um alert
 * 
 * @param string $tipo
 * @param string $mensagem
 * @param int $col
 * @return string
 */
function carregarAlerta($tipo, $mensagem, $col = 10) {
    $alert = '';
    switch ($tipo) {
        case 'E':
            $icone = 'fa-exclamation-circle';
            $class = 'alert-danger';
            $titulo = 'Atenção!';
            break;
        case 'A':
            $icone = 'fa-exclamation-triangle ';
            $class = 'alert-warning';
            $titulo = 'Aviso!';
            break;
        case 'S':
            $icone = 'fa-check';
            $class = 'alert-success';
            $titulo = 'Sucesso!';
            break;
        case 'I':
            $icone = 'fa-info-circle';
            $class = 'alert-info';
            $titulo = 'Informação!';
            break;
        default:
            $icone = 'fa-times';
            $class = 'alert-info';
            $titulo = 'Informação!';
            break;
    }

    $alert .= '<div class="col-sm-' . $col . ' alert ' . $class . ' center" style="margin:10px auto; float:none;">';
    $alert .= ' <button type="button" class="close" data-dismiss="alert"><i class="ace-icon fa fa-times"></i></button>';
    $alert .= ' <strong><i class="ace-icon fa ' . $icone . '"></i> ' . $titulo . ' </strong>' . $mensagem . '<br>';
    $alert .= '</div>';
    return $alert;
}

/**
 * Gerar script para página de lista carregado por uma lov
 * 
 * @param string $campo
 */
function gerarScriptStyleLista($campo) {
    $script = '';
    $script .= '<script type="text/javascript">';
    $script .= 'jQuery(document).ready(function () {';
    $script .= '    jQuery(".formFiltros select").change(function () {';
    $script .= '        dt_dados.ajax.reload();';
    $script .= '    });';
    $script .= '});';
    $script .= 'function __selecionar(codigo, identificador, valor, extra) {';
    $script .= '    jQuery.gDisplay.loadStart("HTML");';
    $script .= '    parent.jQuery("#' . $campo . '").val(codigo);';
    $script .= '    parent.jQuery("#txt_cod_' . $campo . '").val(identificador);';
    $script .= '    parent.jQuery("#txt_val_' . $campo . '").val(valor);';
    $script .= '    parent.jQuery("#txt_cod_' . $campo . '").removeClass("erro_lov");';
    $script .= '    parent.jQuery("#hid_extra_' . $campo . '").val(extra);';
    $script .= '    closeColorbox();';
    $script .= '    return false;';
    $script .= '}';
    $script .= '</script>';
    echo $script;
}

/**
 * Gerar script para página de lista carregado por uma lov
 * 
 * @param string $campo
 */
function gerarScriptStyleListaMult($campo) {
    $script = '';
    $script .= '<script type="text/javascript">';
    $script .= 'jQuery(document).ready(function () {';
    $script .= '    jQuery(".formFiltros select").change(function () {';
    $script .= '        dt_dados.ajax.reload();';
    $script .= '    });';
    $script .= '    jQuery("#checkTodos").click(function () { ';
    $script .= '        jQuery(".checkLista").prop("checked", jQuery(this).prop("checked"));';
    $script .= '    });';
    $script .= '});';
    $script .= 'function __confirmar(funcao) {';
    $script .= '    jQuery("input[type=search]").val("");';
    $script .= '    dt_dados.search("").draw();';
    $script .= '    var codigo = "";';
    $script .= '    jQuery("#dt_dados .checkLista").each(function(){ ';
    $script .= '        if (this.checked) { ';
    $script .= '             codigo += jQuery(this).val() + ",";';
    $script .= '        }';
    $script .= '    });';
    $script .= '    codigo = codigo.substr(0, (codigo.length - 1)); console.log(codigo);';
    $script .= '    jQuery.gDisplay.loadStart("HTML");';
    $script .= '    parent.jQuery("#' . $campo . '").val(codigo);';
    $script .= '    eval(funcao);';
    $script .= '    closeColorbox();';
    $script .= '    return false;';
    $script .= '}';
    $script .= '</script>';
    echo $script;
}

/**
 * Carregar html para paginação com links
 * 
 * @param int $count
 * @param int $page
 * @param int $rp
 * @return string
 */
function carregarPaginacao($count, $page = 1, $rp = 10) {
    $retorno = '';

    $retorno .= '<ul class="pagination pull-right" style="margin:0;">';
    $pages = $count / $rp;
    $pages = ceil($pages);
    $pages = ($pages == 0) ? 1 : $pages;
    if ($page == 1) {
        $retorno .= '     <li class="disabled"><a>Anterior</a></li>';
    } else {
        $retorno .= '     <li><a class="__pointer" onclick="__atualizar(' . ($page - 1) . ');">Anterior</a></li>';
    }
    for ($i = 1; $i <= $pages; $i++) {
        $class = '';
        if ($page == $i) {
            $class = 'active';
        }
        $retorno .= '      <li class="' . $class . '"><a class="__pointer" onclick="__atualizar(' . $i . ');">' . $i . '</a></li>';
    }
    if ($page == $pages) {
        $retorno .= '      <li class="disabled"><a>Próximo</a></li>';
    } else {
        $retorno .= '      <li><a class="__pointer" onclick="__atualizar(' . ($page + 1) . ');">Próximo</a></li>';
    }
    $retorno .= '</ul>';

    return $retorno;
}

/**
 * Carregar caixa de título para o site com opção para exibir um alerta ao lado
 * 
 * @param string $imagem
 * @param string $titulo
 * @param string $info
 * @return string
 */
function carregarTitulo($imagem, $titulo, $info = false) {
    $html = '';
    $html .= '<div class="row" id="div_' . $titulo . '">';
    $html .= '  <div class="' . (($info) ? 'col-lg-5 col-md-12' : 'col-lg-12') . '">';
    $html .= '      <div class="caixa_titulo">';
    $html .= '          <div class="icone hidden-xs hidden-sm" style="background-image:url(' . $imagem . ');"></div>';
    $html .= '          <div class="texto">' . $titulo . '</div>';
    $html .= '      </div>';
    $html .= '  </div>';
    if ($info) {
        $html .= '<div class="col-lg-7 col-md-12" style="margin-bottom:30px !important;">';
        $html .= '  <div class="alert alert-warning alert-dismissible caixa_alerta" role="alert">';
        $html .= '      <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>';
        $html .= '      <i class="fa fa-info-circle" aria-hidden="true"></i>' . $info;
        $html .= '  </div>';
        $html .= '</div>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Carregar caixa de título para o site com opção para exibir a quantidade de rregistros da lista
 * 
 * @param string $imagem
 * @param string $titulo
 * @param string $lista
 * @return string
 */
function carregarTituloLista($imagem, $titulo, $lista) {
    $html = '';
    $html .= '<div class="row" id="div_' . $titulo . '">';
    $html .= '  <div class="col-lg-5 col-md-6">';
    $html .= '      <div class="caixa_titulo">';
    $html .= '          <div class="icone hidden-xs hidden-sm" style="background-image:url(' . $imagem . ');"></div>';
    $html .= '          <div class="texto">' . $titulo . '</div>';
    $html .= '      </div>';
    $html .= '  </div>';
    $html .= $lista;
    $html .= '</div>';
    return $html;
}

/**
 * Carregar script para gráfico de pizza 
 * 
 * @global type $__arrayCores
 * @param string $tipo
 * @return string
 */
function carregarGraficoPizza($tipo) {
    $mysql = new GDbMysql();
    $grafico = 'var data = [];';
    if (($tipo == 'usuarios')) {
        global $__arrayCores;
        $arrUsuarios = $mysql->executeCombo("SELECT usu_var_cargo, COUNT(*) FROM ava_usuario GROUP BY usu_var_cargo ORDER BY COUNT(*) DESC;");
        $grafico = 'var data = [';

        $normalizado = [];
        foreach ($arrUsuarios as $cargo => $qtd) {
            $cargo = strtoupper($cargo);

            // Regras de Normalização
            if (strpos($cargo, 'PROFESSOR') !== false) {
                $label = "PROFESSOR";
            } elseif (strpos($cargo, 'CUIDADOR') !== false) {
                $label = "CUIDADOR INFANTIL";
            } elseif (strpos($cargo, 'AGENTE EDUCADOR') !== false) {
                $label = "AGENTE EDUCADOR";
            } elseif (strpos($cargo, 'COORDENADOR') !== false) {
                $label = "COORDENADOR";
            } elseif (strpos($cargo, 'DIRETOR') !== false) {
                $label = "DIRETORIA";
            } else {
                // Pega a primeira palavra se não cair nas regras acima
                $partes = explode(' ', $cargo);
                $label = $partes[0];
            }

            if (!isset($normalizado[$label]))
                $normalizado[$label] = 0;
            $normalizado[$label] += $qtd;
        }

        // Ordena do maior para o menor
        arsort($normalizado);
        // Separa os 5 primeiros e agrupa o restante
        $top5 = array_slice($normalizado, 0, 5, true);
        $outros_soma = array_sum(array_slice($normalizado, 5));

        if ($outros_soma > 0) {
            $top5["OUTROS"] = $outros_soma;
        }

        $i = 0;
        foreach ($top5 as $label => $valor) {
            $grafico .= ' {label: "' . $label . '", data: ' . $valor . ', color: "' . $__arrayCores[$i] . '"},';
            $i++;
        }
        $grafico .= '];';
    }
    return $grafico;
}

/**
 * Trata uma lista de operações executadas com sucesso e/ou com erros, 
 * retornando um objeto compatível com a exibição do alert personalizado
 * 
 * @param array $arrLista
 * @param string $msgSucPlu
 * @param string $msgSucSin
 * @param string $msgErrPlu
 * @param string $msgErrSin
 * @param string $ops
 * @param boolean $botaoOcultarExibir Default true
 * @return string
 */
function tratarMensagemLote($arrLista, $msgSucPlu, $msgSucSin, $msgErrPlu, $msgErrSin, $ops, $botaoOcultarExibir = true, $msgSucessoGeral = '', $msgSucessoTotal = '') {
    $msg = '';
    $btn = ($botaoOcultarExibir) ? '<a onclick="verListaSucessosErros();" class="btnOcultarExibir __hover">Exbir/Ocultar Detalhes</a><br/>' : '';
    if (isset($arrLista[true]) && is_array($arrLista[true]) && count($arrLista[true])) {
        if ($msgSucessoGeral != '') {
            $msg = $msgSucessoGeral . '<br/>';
        }
        if (count($arrLista[true]) > 1)
            $msg .= count($arrLista[true]) . ' ' . $msgSucPlu . '<br/>';
        else
            $msg .= count($arrLista[true]) . ' ' . $msgSucSin . '<br/>';
        $msg .= $btn;
        $msg .= '<ul class="listaSucessosErros listaSucessos" style="' . ($botaoOcultarExibir ? 'display: none;' : '') . '">';
        foreach ($arrLista[true] as $sucesso) {
            $msg .= '<li><i class="ace-icon fa fa-check bigger-110 blue-dark"></i> ' . $sucesso . '</li>';
        }
        $msg .= '</ul>';
        if (isset($arrLista[false]) && is_array($arrLista[false]) && count($arrLista[false])) {
            $msg .= '<br/>';
            if (count($arrLista[false]) > 1)
                $msg .= count($arrLista[false]) . ' ' . $msgErrPlu . '<br/>';
            else
                $msg .= count($arrLista[false]) . ' ' . $msgErrSin . '<br/>';
            $msg .= $btn;
            $msg .= '<ul class="listaSucessosErros listaErros" style="' . ($botaoOcultarExibir ? 'display: none;' : '') . '">';
            foreach ($arrLista[false] as $erro) {
                $msg .= '<li><i class="ace-icon fa fa-times bigger-110 red"></i> ' . $erro . '</li>';
            }
            $msg .= '</ul>';
        } else {
            if ($msgSucessoTotal != '') {
                $msg = $msgSucessoTotal . '<br/>';
            }
        }
        $return["status"] = true;
        $return["msg"] = $msg;
    } else {
        $return["status"] = false;
        if (isset($arrLista[false]) && is_array($arrLista[false]) && count($arrLista[false])) {
            if (count($arrLista[false]) > 1)
                $msg = count($arrLista[false]) . ' ' . $msgErrPlu . '<br/>';
            else
                $msg = count($arrLista[false]) . ' ' . $msgErrSin . '<br/>';
            $msg .= $btn;
            $msg .= '<ul class="listaSucessosErros listaErros" style="' . ($botaoOcultarExibir ? 'display: none;' : '') . '">';
            foreach ($arrLista[false] as $erro) {
                $msg .= '<li><i class="ace-icon fa fa-times bigger-110 red"></i> ' . $erro . '</li>';
            }
            $msg .= '</ul>';
            $return["msg"] = $msg;
        } else {
            $return["msg"] = "Ops, " . $ops;
        }
    }
    return $return;
}

/** Exibe uma legenda em HTML para informar as respostas corretas e erradas da correçao da prova
 * 
 */
function loadLegenda() {
    $html = '';
    $html .= '<div class="legenda">';
    $html .= '  <h5><i class="ace-icon fa fa-list"></i> Legenda</h5>';
    $html .= '  <ul class="listaLegenda">';
    $html .= '      <li class="alternativaCorreta acertou">Resposta Correta</li>';
    $html .= '      <li class="alternativaCorreta">Alternativa Correta</li>';
    $html .= '      <li class="alternativaCorreta errou">Resposta Errada</li>';
    $html .= '      <li class="alternativaCorreta anulada">Questão Anulada</li>';
    $html .= '  </ul>';
    $html .= '</div>';
    return $html;
}

/**
 * Gerar HTML com legenda dos dados do array passado
 * 
 * @param array $arr
 * @return string
 */
function carregarLegenda($arr) {
    $html = '';
    $html .= '<div class="col-xs-12 col-md-6 col-lg-4 col-xl-3 tabelaLegenda">';
    $html .= '<div class="row">';
    $html .= '<table class="table table-responsive table-striped">';
    $html .= '<thead><tr><th colspan="2">Legenda</th></tr></thead>';
    $html .= '<tbody>';
    foreach ($arr as $key => $value) {
        $html .= '<tr><th style="white-space: nowrap;" width="1%">' . $key . '</th><td width="99%">' . $value . '</td></tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}

/**
 * Gerar html minificado
 * 
 * @param string $code
 * @return string
 */
function minifierHtml($code) {
    $search = array(
// Remove whitespaces after tags 
        '/\>[^\S ]+/s',
// Remove whitespaces before tags 
        '/[^\S ]+\</s',
// Remove multiple whitespace sequences 
        '/(\s)+/s',
// Removes comments 
        '/<!--(.|\s)*?-->/'
    );
    $replace = array('>', '<', '\\1');
    $code = preg_replace($search, $replace, $code);
    return $code;
}

/**
 * Gerar o html de uma tabela com os dados e títulos passados
 * 
 * @param array $arrTitulos
 * @param array $arrDados
 * @param array $arrFooter Default: array()
 * @param array $arrFormats Default: array()
 * @param string $tituloCentral Default: false
 * @param array $arrStyleTitulos Default: false
 * @param string $styleTituloCentral Default: false
 * @return string
 */
function gerarTabela($arrTitulos, $arrDados, $arrFooter = array(), $arrFormats = array(), $tituloCentral = false, $arrStyleTitulos = false, $styleTituloCentral = false, $link = null) {
    $html = '';
    $html .= '<table style="margin-bottom: 0;" class="table table-responsive table-bordered table-hover table-striped">';
    $html .= '<thead>';
    if ($tituloCentral) {
        if ($styleTituloCentral) {
            $html .= '<tr><th colspan="' . count($arrTitulos) . '" class="widget-header widget-header-flat" style="' . $styleTituloCentral . '">' . $tituloCentral . '</th></tr>';
        } else {
            $html .= '<tr><th colspan="' . count($arrTitulos) . '" style="text-align: center">' . $tituloCentral . '</th></tr>';
        }
    }
    $html .= '<tr>';
    $i = 0;
    foreach ($arrTitulos as $titulo) {
        if ($arrStyleTitulos) {
            $html .= '<th style="' . $arrStyleTitulos[$i] . '">' . $titulo . '</th>';
        } else {
            $html .= '<th style="text-align: center">' . $titulo . '</th>';
        }
        $i++;
    }
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    foreach ($arrDados as $dado) {
        $html .= '<tr>';
        $i = 0;
        foreach ($dado as $key => $val) {
            $html .= '<td ' . (isset($arrFormats[$i]) ? 'style="' . $arrFormats[$i] . '" ' : '') . '>' . $val . '</td>';
            $i++;
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    if (count($arrFooter)) {
        $html .= '<tfoot>';
        foreach ($arrFooter as $footer) {
            $html .= '<tr>';
            $j = 0;
            foreach ($footer as $key => $val) {
                $html .= '<th ' . (isset($arrFormats[$j]) ? 'style="' . $arrFormats[$j] . '" ' : '') . '>' . $val . '</th>';
                $j++;
            }
            $html .= '</tr>';
        }
        $html .= '</tfoot>';
    }
    $html .= '</table>';
    if (!seNuloOuVazio($link)) {
        $html .= $link;
    }
    return $html;
}

/**
 * Gerar o html de uma tabela vertial com os dados passados
 * 
 * @param array $arrDados
 * @return string
 */
function gerarTabelaAuditoria($arrTitulos, $arrDados, $aud_int_codigo) {
    $html = '';
    $html .= '<table style="background-color: #fff;" class="table table-responsive table-bordered table-striped tabela-auditoria">';
    $html .= '<thead>';
    $html .= '<tr>';
    foreach ($arrTitulos as $titulo) {
        $html .= '<th style="text-align: center">' . $titulo . '</th>';
    }
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    $i = 0;
    $var = ' var lista = [];';
    foreach ($arrDados as $titulo => $dados) {
        $i++;
        $dado_ant = '';
        $html .= '<tr class="linhas_auditoria" rel="linha_' . $aud_int_codigo . '_' . $i . '">';
        $html .= '<th>' . $titulo . '</th>';
        foreach ($dados as $dado) {
            if ($dado_ant != '' && $dado_ant != $dado) {
                $var .= ' lista.push("linha_' . $aud_int_codigo . '_' . $i . '");';
            }
            $html .= '<td>' . $dado . '</td>';
            $dado_ant = $dado;
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    $html .= '</table>';
    $html .= '<script type="text/javascript">';
    $html .= '  jQuery(document).ready(function () {';
    $html .= $var;
    $html .= '      jQuery(".linhas_auditoria").each(function () { ';
    $html .= '          if (lista.includes(jQuery(this).attr("rel"))) { ';
    $html .= '              jQuery(this).addClass("dados_diferentes"); ';
    $html .= '          }';
    $html .= '      });';
    $html .= '  });';
    $html .= '</script>';
    return $html;
}

/**
 * Gerar o html de uma tabela com os dados e títulos passados
 * 
 * @param array $arrTitulos
 * @param array $arrDados
 * @param array $arrFooter
 * @return string
 */
function arrayToTableHtml($arrTitulos, $arrDados, $arrFooter = array()) {
    $html = '';
    $html .= '<table border="1" width="100%">';
    $html .= '<thead>';
    $html .= '<tr>';
    foreach ($arrTitulos as $titulo) {
        $html .= '<th>' . GF::converter($titulo, FALSE) . '</th>';
    }
    $html .= '</tr>';
    $html .= '</thead>';
    $html .= '<tbody>';
    foreach ($arrDados as $dado) {
        $html .= '<tr>';
        foreach ($dado as $key => $val) {
            if (isHTML($val) || is_numeric($val))
                $html .= '<td>' . $val . '</td>';
            else
                $html .= '<td>' . mb_convert_encoding($val, 'utf-16', 'utf-8') . '</td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody>';
    if (count($arrFooter)) {
        $html .= '<tfoot>';
        foreach ($arrFooter as $footer) {
            $html .= '<tr>';
            foreach ($footer as $key => $val) {
                if (isHTML($val) || is_numeric($val))
                    $html .= '<td>' . $val . '</td>';
                else
                    $html .= '<td>' . mb_convert_encoding($val, 'utf-16', 'utf-8') . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tfoot>';
    }
    $html .= '</table>';
    return $html;
}

/**
 * Gerar em excel os dados de uma tabela incluíndo os títulos
 * 
 * @param array $arrTitulos
 * @param array $arrDados
 * @param array $arrFooter
 * @return string
 */
function arrayToTable($arrTitulos, $arrDados, $arrFooter) {
    $excelData = implode("\t", array_values($arrTitulos)) . "\n";
    foreach ($arrDados as $dados) {
        array_walk($dados, 'filterData');
        $excelData .= implode("\t", array_values($dados)) . "\n";
    }
    if (is_array($arrFooter)) {
        foreach ($arrFooter as $dados) {
            array_walk($dados, 'filterData');
            $excelData .= implode("\t", array_values($dados)) . "\n";
        }
    }
    return $excelData;
}

/**
 * Gerar javascript com variáveis para gerar um gráfico de linha
 * 
 * @param array $arrTitulos
 * @param array $arrDados
 * @return string
 */
function gerarDadosGrafico($arrTitulos, $arrDados) {
    $grafico = 'var coluna = []; var linha = []; ';
    $temDados = false;
    foreach ($arrDados as $dado) {
        foreach ($dado as $key => $val) {
            switch ($key) {
                case "QTD":
                    $temDados = true;
                    $grafico .= 'linha.push(' . $val . '); ';
                    break;
                case "DATA":
                case "CURSO":
                    $temDados = true;
                    $grafico .= 'coluna.push("' . $val . '"); ';
                    break;
                default:
                    break;
            }
        }
    }
    return ($temDados) ? $grafico : null;
}

/**
 * Verifica se a página atual é um iframe
 * 
 * @return boolean
 */
function isFrame($default = false) {
    global $__param;
    if (isset($__param[1])) {
        parse_str($__param[1], $arrParam);
        if ($arrParam["iframe"] ?? null == 'on') {
            return true;
        } else {
            return $default;
        }
    } else {
        return $default;
    }
}

/**
 * 
 * @global array $__param
 * @return string
 */
function getIframe() {
    global $__param;
    if (isset($__param[1])) {
        parse_str($__param[1], $arrParam);
        if ($arrParam["iframe"] ?? null == 'on') {
            return '?iframe=on';
        } else {
            return '';
        }
    } else {
        return '';
    }
}

/**
 * 
 * @global array $__param
 * @return string
 */
function getClose() {
    global $__param;
    if (isset($__param[1])) {
        parse_str($__param[1], $arrParam);
        if ($arrParam["close"] ?? null == 'on') {
            return '?close=on';
        } else {
            return '';
        }
    } else {
        return '';
    }
}

/**
 * Gera html com o campo select (combobox) baseado em um array de dados
 * 
 * @param array $arr
 * @param string $class
 * @return string
 */
function montarSelect($arr, $class) {
    $html = '';
    $html .= '<select class="' . $class . '">';
    if (count($arr)) {
        foreach ($arr as $key => $val) {
            $html .= '<option value="' . $key . '">' . $val . '</option>';
        }
    }
    $html .= '</select>';

    return $html;
}

/**
 * Destaca a palavra procurada dentro de um texto
 * 
 * @param string $texto
 * @param string $buscar
 * @return string
 */
function destacarPalavra($texto, $buscar) {
    return str_replace(maiusculo($buscar), '<mark>' . maiusculo($buscar) . '</mark>', maiusculo($texto));
}

/**
 * Carrega um Mapa do google com base na localização passada por array
 * 
 * @param array $locations
 * @return string
 */
function carregarMapa($locations = array()) {
    $id = uniqid();
    $html = '<div id="map-canvas_' . $id . '" class="divIframe" style="width: 100%;min-height: 200px;"></div>';
    $html .= '<script type="text/javascript">';
    $html .= '  var map_' . $id . ';';
    $minLat = 0;
    $minLon = 0;
    foreach ($locations as $local) {
        $minLat = ($minLat > $local["lat"]) ? $local["lat"] : $minLat;
        $minLon = ($minLon > $local["lon"]) ? $local["lon"] : $minLon;
    }
    $html .= '  var centerPos_' . $id . ' = new google.maps.LatLng(' . $minLat . ',' . $minLon . ');';
    $html .= '  var zoomLevel_' . $id . ' = 16;';
    $html .= '  function initialize() { ';
    $html .= '      var mapOptions_' . $id . ' = {  center: centerPos_' . $id . ', zoom: zoomLevel_' . $id . ' };';
    $html .= '      map_' . $id . ' = new google.maps.Map( document.getElementById("map-canvas_' . $id . '"), mapOptions_' . $id . ' );';
    $html .= '      var locations = [';
    foreach ($locations as $local) {
        $html .= '      [\'' . $local["descricao"] . '\', ' . $local["lat"] . ',' . $local["lon"] . ']';
    }
    $html .= '      ];';

    $html .= '      for (i = 0; i < locations.length; i++) { ';
    $html .= '          marker = new google.maps.Marker({ position: new google.maps.LatLng(locations[i][1], locations[i][2]), title: locations[i][0], map: map_' . $id . ' }); ';
    $html .= '      }';
    $html .= '  }';
    $html .= '  google.maps.event.addDomListener(window, \'load\', initialize);';
    $html .= '</script>';

    return $html;
}

/**
 * Formata o título da caixa de formulário
 * 
 * @param string $acao
 * @return string
 */
function formataTituloManutencao($acao) {
    switch ($acao) {
        case 'ins':
            return 'Inserindo ';
            break;
        case 'upd':
            return 'Alterando ';
            break;
        default:
            return '';
            break;
    }
}

// </editor-fold>
?>