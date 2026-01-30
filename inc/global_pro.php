<?php

if (!(strpos($_SERVER['HTTP_HOST'], 'ead') === 0)) {
    header('Location: https://ead.sjc.sp.gov.br' . $_SERVER['REQUEST_URI']);
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.save_handler', 'redis');
    ini_set('session.save_path', 'tcp://moodle.6pdrjy.ng.0001.use1.cache.amazonaws.com:6379');
    session_cache_expire(60);
    session_start();
}

// <editor-fold desc="Servidor">
define('SERVER', 'PRODUCAO');
define('SERVIDOR', (isset($_SESSION['debug']) ? 'D' : 'P'));
define('URL_SYS', 'https://ead.sjc.sp.gov.br/painel/');
define('URL_SYS_AVA', 'https://ead.sjc.sp.gov.br/');
define('ROOT_SYS', '/var/www/html/painel/');
define('ROOT_VAR', '/var/www/');
define('ROOT_LOGS', '/var/www/log/');
//</editor-fold>
// <editor-fold desc="Constantes Banco de Dados">
define('MYSQL_HOST', 'moodle.cfxjdounnest.us-east-1.rds.amazonaws.com');
define('MYSQL_USER', 'admin');
define('MYSQL_PASS', '5ql53rv3r');
define('MYSQL_BASE', 'sjc');
define('MYSQL_CHARSET', 'utf8mb4');
// MOODLE
define('MYSQL_MOODLE_HOST', 'moodle.cfxjdounnest.us-east-1.rds.amazonaws.com');
define('MYSQL_MOODLE_USER', 'admin');
define('MYSQL_MOODLE_PASS', '5ql53rv3r');
define('MYSQL_MOODLE_BASE', 'moodle');
define('MYSQL_MOODLE_CHARSET', 'utf8mb4');
// </editor-fold>
// <editor-fold desc="Constantes de SMTP"> 
define('SYS_MAIL_SMTP', 'F');
define('SYS_CONTATO_SMTP', '');
define('SYS_USUARIO_SMTP', '');
define('SYS_SENHA_SMTP', '');
define('SYS_HOST_SMTP', 'email-smtp.us-east-1.amazonaws.com');
define('SYS_PORTA_SMTP', '587');
define('SYS_AUTENTICACAO_SMTP', 'tls');
define('SYS_SANDBOX_SMTP', 'F');
// </editor-fold>