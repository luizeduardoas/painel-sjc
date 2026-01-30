<?php

include_once("inc/global.php");

$__array = explode('/', $_GET['p']);
$__param = explode('?', str_replace("url=", "", $_SERVER['REQUEST_URI']));
$__pg = explode('&', str_replace("pg=", "", $_SERVER['REQUEST_URI']));

$__p1 = (count($__array) >= 1) ? $__array[0] : null;
$__p2 = (count($__array) >= 2) ? $__array[1] : null;
$__p3 = (count($__array) >= 3) ? $__array[2] : null;
$__p4 = (count($__array) >= 4) ? $__array[3] : null;
$__p5 = (count($__array) >= 5) ? $__array[4] : null;
$__p6 = (count($__array) >= 6) ? $__array[5] : null;
$__p7 = (count($__array) >= 7) ? $__array[6] : null;

switch (strtolower($__p1)) {
    case '': case 'index': case 'inicio':
//        ifIncludeOnce("index.htm");
//        break;
    case 'home': case 'dashboard': case 'admin':
        ifIncludeOnce("home.php");
        break;
    case 'login': case 'autenticar':
        ifIncludeOnce("login.php");
        break;
    case 'forgot': case 'esqueci':
        ifIncludeOnce("forgot.php");
        break;
    case 'sair':
        ifIncludeOnce("sair.php");
        break;
    case 'escolha': case 'escolher':
        ifIncludeOnce("escolha.php");
        break;
    case 'validate':
        ifIncludeOnce("validate.php");
        break;
    case 'search': case 'buscar': case 'pesquisa': case 'pesquisar': case 'procurar':
        ifIncludeOnce("search.php");
        break;
    case 'recuperar': case 'recuperarSenha': case 'recuperar-senha': case 'recovery':
        ifIncludeOnce("recovery.php");
        break;
    case '404': case '403':
        $__p2 = $__p1;
        ifIncludeOnce("ops.php");
        break;
    case 'ops':
        ifIncludeOnce("ops.php");
        break;
    case 'error':
        $_id = $__p2;
        $_permissao = $__p3;
        ifIncludeOnce("error.php");
        break;
    case 'minhaconta':
        if (is_null($__p3) || $__p3 == '') {
            if (is_dir("minhaconta/" . $__p2) && $__p2 != '')
                ifIncludeOnce("minhaconta/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view': case 'add':
                    $_id = $__p4;
                    ifIncludeOnce("minhaconta/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p3)) {
                        $pla_int_codigo = $__p3;
                        ifIncludeOnce("minhaconta/" . $__p2 . "/index.php");
                    } else {
                        if (is_numeric($__p4) || $__p4 == '') {
                            $_id = $__p4;
                            ifIncludeOnce("minhaconta/" . $__p2 . "/" . $__p3 . "/index.php");
                        } else {
                            switch ($__p4) {
                                case 'form': case 'view': case 'add':
                                    $_id = $__p5;
                                    ifIncludeOnce("minhaconta/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                    break;
                                default :
                                    ifIncludeOnce("ops.php");
                                    break;
                            }
                        }
                    }
                    break;
            }
            break;
        }
        break;
    case 'seguranca':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("seguranca/" . $__p2) && $__p2 != '')
                ifIncludeOnce("seguranca/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view':
                    $_id = $__p4;
                    ifIncludeOnce("seguranca/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p4) || $__p4 == '') {
                        ifIncludeOnce("seguranca/" . $__p2 . "/" . $__p3 . "/index.php");
                    } else {
                        switch ($__p4) {
                            case 'form': case 'view':
                                $_id = $__p5;
                                ifIncludeOnce("seguranca/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                break;
                            default :
                                ifIncludeOnce("ops.php");
                                break;
                        }
                    }
                    break;
            }
        }
        break;
    case 'importacoes':
        if (is_null($__p3) || $__p3 == '') {
            if (is_dir("importacoes/" . $__p2) && $__p2 != '')
                ifIncludeOnce("importacoes/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        }
        break;
    case 'monitoramento':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("monitoramento/" . $__p2) && $__p2 != '')
                ifIncludeOnce("monitoramento/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view':
                    $_id = $__p4;
                    ifIncludeOnce("monitoramento/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p4) || $__p4 == '') {
                        ifIncludeOnce("monitoramento/" . $__p2 . "/" . $__p3 . "/index.php");
                    } else {
                        switch ($__p4) {
                            case 'form': case 'view':
                                $_id = $__p5;
                                ifIncludeOnce("monitoramento/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                break;
                            default :
                                ifIncludeOnce("ops.php");
                                break;
                        }
                    }
                    break;
            }
        }
        break;
    case 'configuracoes':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("configuracoes/" . $__p2) && $__p2 != '')
                ifIncludeOnce("configuracoes/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view':
                    $_id = $__p4;
                    ifIncludeOnce("configuracoes/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p4) || $__p4 == '') {
                        ifIncludeOnce("configuracoes/" . $__p2 . "/" . $__p3 . "/index.php");
                    } else {
                        switch ($__p4) {
                            case 'form': case 'view':
                                $_id = $__p5;
                                ifIncludeOnce("configuracoes/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                break;
                            default :
                                ifIncludeOnce("ops.php");
                                break;
                        }
                    }
                    break;
            }
        }
        break;
    case 'cadastros':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("cadastros/" . $__p2) && $__p2 != '')
                ifIncludeOnce("cadastros/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view': case 'viewLista': case 'formLista': case 'historico': case 'auditoria':
                    $_id = $__p4;
                    ifIncludeOnce("cadastros/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p4) || $__p4 == '') {
                        $_id = $__p4;
                        ifIncludeOnce("cadastros/" . $__p2 . "/" . $__p3 . "/index.php");
                    } else {
                        switch ($__p4) {
                            case 'form': case 'view': case 'add': case 'viewLista':
                                $_id = $__p5;
                                ifIncludeOnce("cadastros/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                break;
                            default :
                                if (is_numeric($__p5) || $__p5 == '') {
                                    $_id = $__p5;
                                    ifIncludeOnce("cadastros/" . $__p2 . "/" . $__p3 . "/" . $__p4 . "/index.php");
                                } else {
                                    switch ($__p5) {
                                        case 'form': case 'view': case 'add':
                                            $_id = $__p6;
                                            ifIncludeOnce("cadastros/" . $__p2 . "/" . $__p3 . "/" . $__p4 . "/" . $__p5 . ".php");
                                            break;
                                        default :
                                            ifIncludeOnce("ops.php");
                                            break;
                                    }
                                }
                                break;
                        }
                    }
                    break;
            }
        }
        break;
    case 'administracao':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("administracao/" . $__p2) && $__p2 != '')
                ifIncludeOnce("administracao/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view': case 'exec':
                    $_id = $__p4;
                    ifIncludeOnce("administracao/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p4) || $__p4 == '') {
                        $_id = $__p4;
                        ifIncludeOnce("administracao/" . $__p2 . "/" . $__p3 . "/index.php");
                    } else {
                        switch ($__p4) {
                            case 'form': case 'view': case 'add':
                                $_id = $__p5;
                                ifIncludeOnce("administracao/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                break;
                            default :
                                ifIncludeOnce("ops.php");
                                break;
                        }
                    }
                    break;
            }
        }
        break;
    case 'comunicacao':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("comunicacao/" . $__p2) && $__p2 != '')
                ifIncludeOnce("comunicacao/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view': case 'exec':
                    $_id = $__p4;
                    ifIncludeOnce("comunicacao/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    ifIncludeOnce("ops.php");
                    break;
            }
        }
        break;
    case 'gerenciamento':
        if (is_numeric($__p3) || $__p3 == '') {
            if (is_dir("gerenciamento/" . $__p2) && $__p2 != '')
                ifIncludeOnce("gerenciamento/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            switch ($__p3) {
                case 'form': case 'view': case 'viewLista': case 'formLista': case 'historico': case 'auditoria': case 'viewEnvio':
                    $_id = $__p4;
                    ifIncludeOnce("gerenciamento/" . $__p2 . "/" . $__p3 . ".php");
                    break;
                default :
                    if (is_numeric($__p4) || $__p4 == '') {
                        $_id = $__p4;
                        ifIncludeOnce("gerenciamento/" . $__p2 . "/" . $__p3 . "/index.php");
                    } else {
                        switch ($__p4) {
                            case 'form': case 'view': case 'add':
                                $_id = $__p5;
                                ifIncludeOnce("gerenciamento/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
                                break;
                            default :
                                ifIncludeOnce("ops.php");
                                break;
                        }
                    }
                    break;
            }
        }
        break;
    case 'graficos':
        if ($__p3 == '') {
            if (is_dir("graficos/" . $__p2) && $__p2 != '')
                ifIncludeOnce("graficos/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            if ($__p3 == 'excel' || $__p3 == 'pdf') {
                ifIncludeOnce("graficos/" . $__p2 . "/" . $__p3 . ".php");
            } else {
                ifIncludeOnce("ops.php");
            }
        }
        break;
    case 'tabelas':
        if ($__p3 == '') {
            if (is_dir("tabelas/" . $__p2) && $__p2 != '')
                ifIncludeOnce("tabelas/" . $__p2 . "/index.php");
            else
                ifIncludeOnce("ops.php");
        } else {
            if ($__p3 == 'excel' || $__p3 == 'pdf') {
                ifIncludeOnce("tabelas/" . $__p2 . "/" . $__p3 . ".php");
            } else {
                ifIncludeOnce("ops.php");
            }
        }
        break;
    case 'relatorios':
        if ($__p4 != '') {
            if (is_dir("relatorios/" . $__p2 . "/" . $__p3 . "/" . $__p4)) {
                ifIncludeOnce("relatorios/" . $__p2 . "/" . $__p3 . "/" . $__p4 . "/index.php");
            } else if ($__p4 == 'excel' || $__p4 == 'pdf') {
                ifIncludeOnce("relatorios/" . $__p2 . "/" . $__p3 . "/" . $__p4 . ".php");
            }
        } else {
            if ($__p3 != '' && is_dir("relatorios/" . $__p2 . "/" . $__p3)) {
                ifIncludeOnce("relatorios/" . $__p2 . "/" . $__p3 . "/index.php");
            } else {
                if ($__p2 != '' && is_dir("relatorios/" . $__p2)) {
                    ifIncludeOnce("relatorios/" . $__p2 . "/index.php");
                } else {
                    $id = $__p2;
                    ifIncludeOnce("relatorios/generico/index.php");
                }
            }
        }
        break;
    case 'crons':
        if (trim($__p2) != '')
            ifIncludeOnce("crons/" . $__p2);
        else
            ifIncludeOnce("ops.php");
        break;
    default:
        ifIncludeOnce("ops.php");
        break;
}
?>