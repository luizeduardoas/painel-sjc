<?php

Class GSecurity {

    /**
     * Verifica Permissão para funcionalidade
     * @param String $permissao
     * @param bool $redirecionar
     * @return bool
     */
    public static function verificarPermissao($permissao, $redirecionar = true) {
        if ($redirecionar) {
            GSecurity::verificarAutenticacao();
        }
        if (SYS_SEGURANCA_LIBERADA == 'V') {
            $__usuario = getUsuarioSessao();
            if (isset($_SESSION["usuario"]) && $__usuario->getUsu_var_email() == 'luiz.eduardo.as@gmail.com') {
                return true;
            }
        }
        $url = (strlen($_SERVER["REQUEST_URI"]) > 1) ? '?url=' . $_SERVER["REQUEST_URI"] : '';
        if ((!isset($_SESSION["usuario"])) || (!isset($_SESSION["arr_permissao"])) || (!in_array($permissao, $_SESSION["arr_permissao"]))) {
            if ($redirecionar) {
                echo '<script>self.location = "' . URL_SYS . 'error/0/' . $permissao . '/' . $url . '";</script>';
            }
            return false;
        } else {
            if ((isset($_SESSION["usuario"])) && !isset($_SESSION["pef_int_codigo"])) {
                echo '<script>self.location = "' . URL_SYS . 'escolher/' . $url . '";</script>';
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * Verifica Permissão para funcionalidade e retorna um array para resposta AJAX
     * @param String $permissao
     * @return bool
     */
    public static function verificarPermissaoAjax($permissao) {
        GSecurity::verificarAutenticacaoAjax(true);
        if (SYS_SEGURANCA_LIBERADA == 'V') {
            $__usuario = getUsuarioSessao();
            if (isset($_SESSION["usuario"]) && $__usuario->getUsu_var_email() == 'luiz.eduardo.as@gmail.com') {
                return true;
            }
        }
        if ((!isset($_SESSION["usuario"])) || (!isset($_SESSION["arr_permissao"])) || (!in_array($permissao, $_SESSION["arr_permissao"]))) {
            echo '{"status": false, "msg": "Você não tem acesso a essa funcionalidade!<br/>Solicite ao administrador do sistema sua permissão para a funcionalidade \'' . $permissao . '\'"}';
            return false;
        } else {
            return true;
        }
    }

    /**
     * Verifica se usuário logado, senão redireciona para autenticar
     */
    public static function verificarAutenticacao() {
        $usuario = new Usuario();
        if (isset($_SESSION["usuario"]))
            $usuario = unserialize($_SESSION['usuario']);
        $url = (strlen($_SERVER["REQUEST_URI"]) > 1) ? '?url=' . $_SERVER["REQUEST_URI"] : '';
        if ((isset($_SESSION["usuario"]) && !is_null($_SESSION["usuario"])) && (SYS_LOGIN_SESSAO_UNICA == "ON")) {
            $usu_var_sessao = getSessaoByUsuario($usuario->getUsu_int_codigo());
            if (isset($usu_var_sessao)) {
                if ($usuario->getUsu_int_codigo() != 2) {
                    if (hash_equals(session_id(), $usu_var_sessao) === false) {
                        if (session_status() === PHP_SESSION_ACTIVE) {
                            setSessao($usuario->getUsu_int_codigo(), true);
                            session_unset();
                            session_destroy();
                        }
                    }
                }
            } else {
                session_unset();
                session_destroy();
            }
        }
        if (!isset($_SESSION["usuario"]) || is_null($_SESSION["usuario"])) {
            if (isFrame())
                echo '<script>parent.location = "' . URL_SYS . 'autenticar/error/session/' . $url . '";</script>';
            else
                echo '<script>self.location = "' . URL_SYS . 'autenticar/error/session/' . $url . '";</script>';
        } else {
            setSessao($usuario->getUsu_int_codigo());
            if (is_null(getPerfilSessao())) {
                echo '<script>self.location = "' . URL_SYS . 'escolha/' . $url . '";</script>';
            }
        }
    }

    /**
     * Verifica se usuário logado, senão exibe mensagem de erro
     */
    public static function verificarAutenticacaoAjax($json = false) {
        $usuario = new Usuario();
        if (isset($_SESSION["usuario"]))
            $usuario = unserialize($_SESSION['usuario']);
        if (isset($_SESSION["usuario"]) && (!is_null($_SESSION["usuario"])) && (SYS_LOGIN_SESSAO_UNICA == "ON")) {
            $usu_var_sessao = getSessaoByUsuario($usuario->getUsu_int_codigo());
            if (isset($usu_var_sessao)) {
                if ($usuario->getUsu_int_codigo() != 2) {
                    if (hash_equals(session_id(), $usu_var_sessao) === false) {
                        if (session_status() === PHP_SESSION_ACTIVE) {
                            setSessao($usuario->getUsu_int_codigo(), true);
                            session_unset();
                            session_destroy();
                        }
                    }
                }
            } else {
                session_unset();
                session_destroy();
            }
        }
        if (!isset($_SESSION["usuario"]) || is_null($_SESSION["usuario"])) {
            if ($json)
                echo '{"status": false, "msg":"<script>jQuery.alerts._hide();var str = window.location.href; var url = str.replace(\"https://' . $_SERVER['HTTP_HOST'] . '\", \"\"); window.location = \"' . URL_SYS . 'autenticar/error/session/?url=\"+url;</script>"}';
            else
                echo '<script>var str = parent.window.location.href; var url = str.replace("https://' . $_SERVER['HTTP_HOST'] . '", ""); parent.window.location = "' . URL_SYS . 'autenticar/error/session/?url="+url;</script>';
            exit();
        } else {
            setSessao($usuario->getUsu_int_codigo());
        }
    }

    /**
     * Verifica se o usuário logado tem o perfil passado
     * 
     * @param string $perfil
     * @param bool $redirecionar
     * @return boolean
     */
    public static function verificarPerfil($perfil, $redirecionar = true) {
        $__usuario = getUsuarioSessao();
        $url = (strlen($_SERVER["REQUEST_URI"]) > 1) ? '?url=' . $_SERVER["REQUEST_URI"] : '';
        if (!isset($_SESSION["usuario"]) || (is_null($_SESSION["usuario"])) || (!array_key_exists("arr_permissao", $_SESSION))) {
            if ($redirecionar) {
                echo '<script>self.location = "' . URL_SYS . 'autenticar/error/session/' . $url . '";</script>';
            }
            return false;
        } else {
            if (!isset($_SESSION["pef_int_codigo"])) {
                echo '<script>self.location = "' . URL_SYS . 'escolher/' . $url . '";</script>';
                return false;
            } else {
                if ($__usuario->getPerfil()->getPef_int_codigo() == $perfil) {
                    return true;
                } else {
                    if ($redirecionar) {
                        echo '<script>self.location = "' . URL_SYS . 'ops/003/' . $url . '";</script>';
                    }
                    return false;
                }
            }
        }
    }

    /**
     * Verifica se o usuário logado tem o perfil passado
     * 
     * @param array $arrPerfis
     * @param bool $redirecionar
     * @return boolean
     */
    public static function verificarPerfis($arrPerfis, $redirecionar = true) {
        $__usuario = getUsuarioSessao();
        $url = (strlen($_SERVER["REQUEST_URI"]) > 1) ? '?url=' . $_SERVER["REQUEST_URI"] : '';
        if (!isset($_SESSION["usuario"]) || (is_null($_SESSION["usuario"])) || (!array_key_exists("arr_permissao", $_SESSION))) {
            if ($redirecionar) {
                echo '<script>self.location = "' . URL_SYS . 'autenticar/' . $url . '";</script>';
            }
            return false;
        } else {
            if (!isset($_SESSION["pef_int_codigo"])) {
                echo '<script>self.location = "' . URL_SYS . 'escolher/' . $url . '";</script>';
                return false;
            } else {
                if (in_array($__usuario->getPerfil()->getPef_int_codigo(), $arrPerfis)) {
                    return true;
                } else {
                    if ($redirecionar) {
                        echo '<script>self.location = "' . URL_SYS . 'ops/003/' . $url . '";</script>';
                    }
                    return false;
                }
            }
        }
    }
}

?>