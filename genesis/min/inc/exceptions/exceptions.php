<?php

require_once (ROOT_GENESIS . 'inc/exceptions/dbException.class.php');
require_once (ROOT_GENESIS . 'inc/exceptions/appException.class.php');

set_error_handler('myErrorHandler');
//register_shutdown_function('myShutdownFunction');

if (SERVIDOR == 'D' || SERVIDOR == 'H') {
    ini_set('display_errors', 'On');
}

function myErrorHandler($errno, $errstr, $errfile, $errline) {
    switch ($errno) {
        case E_USER_WARNING:
        case E_WARNING:
            if (SERVIDOR == 'D' || SERVIDOR == 'H')
                echo $errno, ' - ', $errstr, ' - ', $errfile, ' - ', $errline, ' - ', date('Y-m-d h:i:s'), '<br>';
            GF::salvarLog('Warning', $errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
        //    salvarEvento('A', $errno, $errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
            break;
        case E_ERROR:
        case E_USER_ERROR:
            if (SERVIDOR == 'D' || SERVIDOR == 'H')
                echo $errno, ' - ', $errstr, ' - ', $errfile, ' - ', $errline, ' - ', date('Y-m-d h:i:s'), '<br>';
            GF::salvarLog('Error', $errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
        //    salvarEvento('E', $errno, $errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
            break;
        default:
            GF::salvarLog('Notice', $errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
          //  salvarEvento('A', $errno, $errno . ' - ' . $errstr . ' - ' . $errfile . ' - ' . $errline);
            break;
    }
//
//    if ($errno == 2)
//        throw new AppException($errstr);
    return true;
}

//function myShutdownFunction() {
//    $error = error_get_last();
//    myErrorHandler($error['type'], $error['message'], $error['file'], $error['line']);
//}
?>
