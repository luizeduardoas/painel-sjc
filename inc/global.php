<?php

ini_set("display_errors", 1);
ini_set("display_startup_erros", 1);
error_reporting(E_ALL);

header('X-Frame-Options: SAMEORIGIN');
header('Content-Type: text/html; charset=utf-8');

if ((strpos($_SERVER['HTTP_HOST'], 'localhost') === false) && (strpos($_SERVER['HTTP_HOST'], 'lueli-dell') === false)) {
    require_once(__DIR__ . '/global_pro.php');
} else {
    require_once(__DIR__ . '/global_dev.php');
}

// <editor-fold desc="Constantes do Sistema">
define('SYS_MODULO', 'sjc');
define('SYS_NOME', 'Painel SJC');
define('SYS_SLOGAN', 'Sistema de Acompanhamento dos estudos do EAD da Prefeitura de São José dos Campos');
define('SYS_KEYWORDS', 'SJC Sistema Acompanhamento estudos EAD Prefeitura São José dos Campos');
define('SYS_VERSAO', '1.0');
define('SYS_TEMA', 'ace');
define('SYS_TEMA_GLOBAL', 'global');
define('SYS_GENESIS', 'min');
define('SYS_SEGURANCA_LIBERADA', 'V');
define('SYS_LOGIN_SESSAO_UNICA', 'ON');
define('SYS_MINIFY_TEMA', false);
define('SYS_LIB_DEFAULT', 'php.js,genesis,jalert,cookie');
define('SYS_LIB_DEFAULT_ERROR', 'php.js,genesis,jalert-site,cookie');
define('SYS_CHARSET', 'utf-8');
define('SYS_COLOR', '#34a853');
define('SYS_TIME_LIMIT', 1200); // 20 minutos
define('SYS_LIMIT_DADOS_CRON', 9999);
// </editor-fold>
// <editor-fold desc="Constantes Contatos">	
define('CONTATO', 'luiz.eduardo.as@gmail.com');
define('SYS_CONTATO', SYS_NOME . '<' . CONTATO . '>');
define('SYS_EMAIL', CONTATO);
define('SYS_NOME_EMAIL', SYS_NOME);
define('SYS_NAORESPONDA', CONTATO);
define('SYS_ASSINATURA', SYS_NOME);
// </editor-fold>
// <editor-fold desc="Constantes URL">
define('URL_ENDERECO', 'ead.sjc.sp.gov.br');
define('URL_ENDERECO_MIN', URL_SYS);
define('URL_GENESIS', URL_SYS . 'genesis/');
define('URL_STATIC', URL_SYS);
define('URL_STATIC_GN', URL_STATIC . 'genesis/' . SYS_GENESIS . '/');
define('URL_SYS_TEMA', URL_SYS . 'themes/' . SYS_TEMA . '/');
define('URL_SYS_TEMA_GLOBAL', URL_SYS . 'themes/' . SYS_TEMA_GLOBAL . '/');
define('URL_SYS_LOGO', URL_SYS_TEMA_GLOBAL . 'images/logo_horizontal.png');
define('URL_SYS_LOGO_BRANCA', URL_SYS_TEMA_GLOBAL . 'images/logo_branca.png');
define('URL_SYS_LOGO_ICONE', URL_SYS_TEMA_GLOBAL . 'images/icone.png');
define('URL_UPLOAD', URL_SYS . 'uploads/');
// </editor-fold>
// <editor-fold desc="Constantes Root">
define('ROOT_SYS_INC', ROOT_SYS . 'inc/');
define('ROOT_SYS_CLASS', ROOT_SYS . 'class/');
define('ROOT_GENESIS', ROOT_SYS . 'genesis/' . SYS_GENESIS . '/');
define('ROOT_SYS_TEMA', ROOT_SYS . 'themes/' . SYS_TEMA . '/');
define('ROOT_SYS_TEMA_GLOBAL', ROOT_SYS . 'themes/' . SYS_TEMA_GLOBAL . '/');
define('ROOT_UPLOAD', ROOT_SYS . 'uploads/');
// </editor-fold>
// <editor-fold desc="configuracoes">
define('MYSQL_WRITE', "ON");
define('SYS_DB_LOG', true);
define('PESO_PDF', '25M');
define('MAX_SIZE', '26214400'); //25Mb
define('MIN_WIDTH', '300');
define('MIN_HEIGHT', '400');
define('MAX_WIDTH', '1500');
define('MAX_HEIGHT', '2500');

define('EXTENSIONS_IMAGENS', 'jpg;jpeg;gif;png');
define('EXTENSIONS_ARQUIVOS', 'pdf;doc;docx;txt;xls;xlsx');
define('EXTENSIONS_COMPRIMIDOS', 'zip;rar');
define('EXTENSIONS_VIDEOS', 'mp4;mpeg;avi');
define('EXTENSIONS', EXTENSIONS_IMAGENS . ';' . EXTENSIONS_ARQUIVOS . ';' . EXTENSIONS_COMPRIMIDOS . ';' . EXTENSIONS_VIDEOS);
define('EXTENSIONS_DOWNLOAD', 'pdf;doc;docx;txt;ppt;pps;zip;rar;xls;xlsx');

define('PERFIL_ADMINISTRADOR', 1);
// </editor-fold>

require_once(ROOT_GENESIS . 'genesis.php');
require_once(ROOT_SYS_INC . 'arrays.php');
require_once(ROOT_SYS_INC . 'functions.php');
require_once(ROOT_SYS_INC . 'functionsEmail.php');

$genesis = new Genesis(SYS_MODULO);

require_once(ROOT_SYS_INC . 'class/header.class.php');
require_once(ROOT_SYS_INC . 'class/footer.class.php');
require_once(ROOT_SYS_CLASS . 'auditoria.php');

while (ob_get_level()) {
    ob_end_clean();
}

$_POST = GF::unstrip_array($_POST);
GF::import(array("usuario"));
GF::salvarLog('Debug', 'URL: ' . $_SERVER["REQUEST_URI"]);
?>
