<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
//    ini_set('session.save_handler', 'redis');
//    ini_set('session.save_path', 'tcp://seu-redis-endpoint:6379');
    session_cache_expire(60);
    session_start();
}

// <editor-fold desc="Servidor">
define('SERVER', 'TESTE');
define('SERVIDOR', 'D');
define('URL_SYS', 'http://localhost/sjc/painel/');
define('URL_SYS_AVA', 'https://ead.sjc.sp.gov.br/');
define('ROOT_SYS', 'C:/xampp/htdocs/sjc/painel/');
define('ROOT_VAR', 'C:/xampp/htdocs/');
define('ROOT_LOGS', 'C:/xampp/htdocs/logs/');
//</editor-fold>
// <editor-fold desc="Constantes Banco de Dados">
define('MYSQL_HOST', 'lueli-server');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '12345678');
define('MYSQL_BASE', 'sjc');
define('MYSQL_CHARSET', 'utf8mb4');
// MOODLE
define('MYSQL_MOODLE_HOST', 'lueli-server');
define('MYSQL_MOODLE_USER', 'root');
define('MYSQL_MOODLE_PASS', '12345678');
define('MYSQL_MOODLE_BASE', 'moodle');
define('MYSQL_MOODLE_CHARSET', 'utf8mb4');
// </editor-fold>
// <editor-fold desc="Constantes de SMTP"> 
define('SYS_MAIL_SMTP', 'F');
define('SYS_CONTATO_SMTP', 'sjc@outlook.com');
define('SYS_USUARIO_SMTP', 'sjc@outlook.com');
define('SYS_SENHA_SMTP', '');
define('SYS_HOST_SMTP', 'smtp-mail.outlook.com');
define('SYS_PORTA_SMTP', '587');
define('SYS_AUTENTICACAO_SMTP', 'tls');
define('SYS_SANDBOX_SMTP', 'V');
// </editor-fold>