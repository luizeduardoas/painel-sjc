<?php

require_once(ROOT_SYS_CLASS . "usuario.php");
GF::import(array("perfil"));

class UsuarioDao {

    private $usu_cha_status;
    private $usu_dti_criacao;
    private $pef_cha_status;
    private $usu_cha_validado;
    private $usu_dti_ultimo;
    private $sql;
    private $sqlCount;

    function __construct() {
        global $__arrayBloqueado, $__arrayAtivo, $__arrayValidado;
        $this->usu_dti_criacao = gerarDate_format("usu_dti_criacao", false);
        $this->usu_cha_status = gerarCase("usu_cha_status", $__arrayBloqueado, false);
        $this->pef_cha_status = gerarCase("pef_cha_status", $__arrayAtivo, false);
        $this->usu_cha_validado = gerarCase("usu_cha_validado", $__arrayValidado, false);
        $this->usu_dti_ultimo = gerarDate_format("usu_dti_ultimo", false);
        $this->sql = "SELECT usu.usu_int_codigo,usu.pef_int_codigo,usu_var_nome,usu_var_identificador,usu_var_email,usu_var_senha,$this->usu_cha_status,usu_var_motivo,$this->usu_dti_criacao,usu_var_token,pef_var_descricao,$this->pef_cha_status,usu_var_foto,$this->usu_cha_validado,$this->usu_dti_ultimo,IFNULL(usu_var_sessao, 0) as usu_var_sessao FROM usuario usu INNER JOIN perfil pef ON (usu.pef_int_codigo = pef.pef_int_codigo) ";
        $this->sqlCount = "SELECT COUNT(usu_int_codigo) FROM usuario usu ";
    }

    public function select($where = false, $param = false, $loadObj = true) {
        $array = array();
        try {
            $mysql = new GDbMysql();
            if ($param)
                $mysql->execute($this->sql . $where, $param);
            else
                $mysql->execute($this->sql . $where);
            while ($mysql->fetch()) {
                $array[] = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $array;
    }

    public function selectCount($where = false, $param = false) {
        $qtd = 0;
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . $where, $param);
            if ($mysql->fetch())
                $qtd = $mysql->res[0];
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $qtd;
    }

    /** @param Usuario $usuario */
    public function selectById($usuario, $loadObj = true) {
        $param = array("i", $usuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE usu.usu_int_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $usuario = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $usuario;
    }

    /** @param Usuario $usuario */
    public function selectByIdentificador($usuario, $loadObj = true) {
        $param = array("s", $usuario->getUsu_var_identificador());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE usu.usu_var_identificador = ? ", $param);
            if ($mysql->fetch()) {
                $usuario = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $usuario;
    }

    /** @param Perfil $perfil */
    public function selectByPerfil($perfil, $loadObj = true) {
        $array = array();
        $param = array("i", $perfil->getPef_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE usu.pef_int_codigo = ? ", $param);
            while ($mysql->fetch()) {
                $array[] = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $array;
    }

    /** @param Usuario $usuario */
    public function selectByEmail($usuario, $loadObj = true) {
        $param = array("s", $usuario->getUsu_var_email());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE usu.usu_var_email = ? ", $param);
            if ($mysql->fetch()) {
                $usuario = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $usuario;
    }

    /** @param Usuario $usuario */
    public function insert($usuario) {

        $return = array();
        $param = array("isssssssss", $usuario->getPerfil()->getPef_int_codigo(), $usuario->getUsu_var_nome(), $usuario->getUsu_var_identificador(), $usuario->getUsu_var_email(), md5($usuario->getUsu_var_senha()), $usuario->getUsu_cha_status(), $usuario->getUsu_var_motivo(), $usuario->getUsu_var_token(), formataArquivoURL('usuario', $usuario->getUsu_var_foto(false)), $usuario->getUsu_cha_validado());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_ins(?,?,?,?,?,?,?,?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["insertId"] = $mysql->res[2];
            if ($return["status"]) {
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function update($usuario) {
        $senha = (is_null($usuario->getUsu_var_senha()) || $usuario->getUsu_var_senha() == "") ? null : md5($usuario->getUsu_var_senha());
        $return = array();
        $param = array("iisssssssss", $usuario->getUsu_int_codigo(), $usuario->getPerfil()->getPef_int_codigo(), $usuario->getUsu_var_nome(), $usuario->getUsu_var_identificador(), $usuario->getUsu_var_email(), $senha, $usuario->getUsu_cha_status(), $usuario->getUsu_var_motivo(), $usuario->getUsu_var_token(), formataArquivoURL('usuario', $usuario->getUsu_var_foto(false)), $usuario->getUsu_cha_validado());
        try {
            $usuario_old = $this->selectById($usuario);
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_upd(?,?,?,?,?,?,?,?,?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["affectedRows"] = $mysql->res[2];
            if ($return["status"]) {
                if ($usuario->getUsu_var_foto(false) != $usuario_old->getUsu_var_foto(false)) {
                    deleteUpload('usuario', formataArquivoURL('usuario', $usuario_old->getUsu_var_foto(false)));
                }
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function delete($usuario) {

        $return = array();
        $param = array("i", $usuario->getUsu_int_codigo());
        try {
            $usuario_old = $this->selectById($usuario);
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_del(?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["affectedRows"] = $mysql->res[2];
            if ($return["status"]) {
                deleteUpload('usuario', formataArquivoURL('usuario', $usuario_old->getUsu_var_foto(false)));
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (Exception $e) {
            $return["status"] = false;
            $return["msg"] = $e->getMessage();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function validarIdentificador($usuario) {

        $return = array();
        $param = array("is", $usuario->getUsu_int_codigo(), $usuario->getUsu_var_identificador());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_validar(?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $mysql->close();
        } catch (Exception $e) {
            $return["status"] = false;
            $return["msg"] = $e->getMessage();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function atualizar($usuario) {
        $return = array();
        $param = array("iisssssssss", $usuario->getUsu_int_codigo(), $usuario->getPerfil()->getPef_int_codigo(), $usuario->getUsu_var_nome(), $usuario->getUsu_var_identificador(), $usuario->getUsu_var_email(), null, $usuario->getUsu_cha_status(), $usuario->getUsu_var_motivo(), $usuario->getUsu_var_token(), formataArquivoURL('usuario', $usuario->getUsu_var_foto(false)), $usuario->getUsu_cha_validado());
        try {
            $usuario_old = $this->selectById($usuario);
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_upd(?,?,?,?,?,?,?,?,?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["affectedRows"] = $mysql->res[2];
            if ($return["status"]) {
                if ($usuario->getUsu_var_foto() != $usuario_old->getUsu_var_foto()) {
                    deleteUpload('usuario', formataArquivoURL('usuario', $usuario_old->getUsu_var_foto(false)));
                }
                $usuario = $this->selectById($usuario);
                unset($_SESSION["usuario"]);
                unset($_SESSION["pef_int_codigo"]);
                $_SESSION["usuario"] = serialize($usuario);
                $this->carregarPermissoes();
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function autenticar($usuario) {
        $return = array();
        $param = array("ss", $usuario->getUsu_var_identificador(), md5($usuario->getUsu_var_senha()));
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_autenticar(?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            if ($return["status"]) {
                $usuario->setUsu_int_codigo($mysql->res[2]);
                $usuario = $this->selectById($usuario);
                setSessao($usuario->getUsu_int_codigo());
                unset($_SESSION["usuario"]);
                unset($_SESSION["pef_int_codigo"]);
                $_SESSION["usuario"] = serialize($usuario);
                $this->carregarPermissoes();
                salvarEvento('S', 'Autenticação realizada com Sucesso', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $return["msg"]);
            } else {
                unset($_SESSION["usuario"]);
                salvarEvento('A', 'Autenticação não realizada', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $return["msg"]);
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            unset($_SESSION["usuario"]);
            unset($_SESSION["pef_int_codigo"]);
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function logarComo($usuario) {
        $return = array();
        $return["status"] = true;
        $return["msg"] = "Usuario não encontrado";
        $param = array("i", $usuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("SELECT COUNT(usu_int_codigo) FROM usuario WHERE usu_int_codigo = ?;", $param);
            $mysql->fetch();
            if ($mysql->res[0] == 1) {
                $return["status"] = true;
                $return["msg"] = "Logar como outro usuário com sucesso";
                $usuario = $this->selectById($usuario);
                setSessao($usuario->getUsu_int_codigo());
                salvarEvento('S', 'Logar como outro usuário com sucesso', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $return["msg"]);
                unset($_SESSION["usuario"]);
                unset($_SESSION["pef_int_codigo"]);
                $_SESSION["usuario"] = serialize($usuario);
                $this->carregarPermissoes();
            } else {
                $return["status"] = false;
                $return["msg"] = "Usuário não encontrado";
                salvarEvento('E', 'Logar como outro usuário não realizado', 'Usuário: ' . $usuario->getUsu_int_codigo() . ' - Mensagem: ' . $return["msg"]);
                unset($_SESSION["usuario"]);
                unset($_SESSION["pef_int_codigo"]);
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            unset($_SESSION["usuario"]);
            unset($_SESSION["pef_int_codigo"]);
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    public function carregarPermissoes($pef_int_codigo = null) {
        unset($_SESSION["arr_permissao"]);
        $usuario = getUsuarioSessao();
        if (!is_null($_SESSION["usuario"])) {
            $param = array("i", seNuloOuVazio($pef_int_codigo) ? $usuario->getPerfil()->getPef_int_codigo() : $pef_int_codigo);
            try {
                $mysql = new GDbMysql();
                $mysql->execute("SELECT pem_var_codigo FROM perfil_permissao WHERE pef_int_codigo = ?;", $param);
                while ($mysql->fetch()) {
                    $_SESSION["arr_permissao"][] = $mysql->res[0];
                }
                $mysql->close();
                $_SESSION["pef_int_codigo"] = seNuloOuVazio($pef_int_codigo) ? $usuario->getPerfil()->getPef_int_codigo() : $pef_int_codigo;
            } catch (GDbException $e) {
                unset($_SESSION["arr_permissao"]);
                unset($_SESSION["pef_int_codigo"]);
                salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
            }
        }
    }

    /** @param Usuario $usuario */
    public function alterarSenha($usuario) {

        $return = array();
        $usuarioSessao = getUsuarioSessao();
        $param = array("iss", $usuarioSessao->getUsu_int_codigo(), md5($usuario->getUsu_var_senha()), md5($usuario->getUsu_var_senha_new()));
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_alterar_senha(?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            if ($return["status"]) {
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', 'Erro ao tentar alterar a senha', 'Usuário: ' . $usuarioSessao->getUsu_var_identificador() . ' - Mensagem: ' . $return["msg"]);
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function esqueciSenha($usuario, $enviarEmail = true) {
        $token = uniqid();
        $return = array();
        $usuario = $this->selectByEmail($usuario);
        $param = array("ss", $usuario->getUsu_var_email(), $token);
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_esqueci(?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["token"] = $token;
            if ($return["status"] && $enviarEmail) {
                $email = new GEmail();
                $mensagem = gerarEmailEsqueciSenha($usuario, $token);
                $email->setMensagem($mensagem);
                $email->setAssunto("Recuperar sua Senha");
                $email->setDestinatario($usuario->getUsu_var_nome() . "<" . $usuario->getUsu_var_email() . ">");
                $returnEmail = $email->enviar();
                if ($returnEmail["status"]) {
                    $return["msg"] = "Você solicitou a recuperação de sua senha de acesso do " . SYS_NOME . ", será necessário criar uma nova senha e confirmar essa solicitação.<br/>Para isso, acesse seu e-mail e clique no link de alteração de senha.<br><br><strong>OBS.: Caso não receba o email, verifique se está na caixa de spam ou lixo eletrônico.</strong>";
                    $tipo = 'S';
                } else {
                    $return["status"] = false;
                    $return["msg"] = "Não foi possível enviar um email com o link da alteração de senha, favor entrar em contato através do endereço: <a href=\"mailto:" . CONTATO . "\" target=\"_blanc\">" . CONTATO . "</a>.";
                    $tipo = 'E';
                }
                salvarEvento($tipo, 'Envio de email pelo esqueci minha senha', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $returnEmail["msg"]);
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function ifExists($usuario) {
        $retorno = false;
        $param = array("i", $usuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE usu.usu_int_codigo = ?;", $param);
            $mysql->fetch();
            $retorno = ($mysql->res[0] > 0) ? true : false;
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            $retorno = false;
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $retorno;
    }

    /** @param Usuario $usuario */
    public function validateToken($usuario) {

        $return = array();
        $param = array("s", $usuario->getUsu_var_token());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_validar_token(?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["usu_int_codigo"] = $mysql->res[2];
            $return["autenticar"] = $mysql->res[3];
            if ($return["status"]) {
                if ($return["autenticar"]) {
                    $usuario->setUsu_int_codigo($mysql->res[2]);
                    $usuario = $this->selectById($usuario);
                    setSessao($usuario->getUsu_int_codigo());
                    unset($_SESSION["usuario"]);
                    $_SESSION["usuario"] = serialize($usuario);
                    $this->carregarPermissoes();
                }
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $tipo = ($return["status"]) ? 'S' : 'E';
            salvarEvento($tipo, 'Validação de Token do cadastro do usuário.', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $return["msg"]);
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function validateTokenSenha($usuario) {

        $return = array();
        $param = array("ss", $usuario->getUsu_var_token(), md5($usuario->getUsu_var_senha_new()));
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_validar_token_esqueci(?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["usu_int_codigo"] = $mysql->res[2];
            $return["autenticar"] = $mysql->res[3];
            if ($return["status"]) {
                if ($return["autenticar"]) {
                    $usuario->setUsu_int_codigo($mysql->res[2]);
                    $usuario = $this->selectById($usuario);
                    setSessao($usuario->getUsu_int_codigo());
                    unset($_SESSION["usuario"]);
                    $_SESSION["usuario"] = serialize($usuario);
                    $this->carregarPermissoes();
                }
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $tipo = ($return["status"]) ? 'S' : 'E';
            salvarEvento($tipo, 'Validação de Token da senha do usuário.', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $return["msg"]);
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    public function gerarNovaSenha($usuario) {
        $token = uniqid();
        $arrSenha = explode('.', uniqid(rand(), true));
        $senha = $arrSenha[1];
        $return = array();
        $param = array("ssi", md5($senha), $token, $usuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("UPDATE usuario SET usu_var_senha = ?, usu_var_token = ? WHERE usu_int_codigo = ?;", $param, false);
            $usuario = $this->selectById($usuario);
            $email = new GEmail();
            $mensagem = gerarEmailCadastro($usuario, $usuario->getUsu_var_token(), $senha);
            $email->setMensagem($mensagem);
            $email->setAssunto("Bem-vindo ao " . SYS_NOME);
            $email->setDestinatario($usuario->getUsu_var_nome() . "<" . $usuario->getUsu_var_email() . ">");
            $returnEmail = $email->enviar();
            if ($returnEmail["status"]) {
                $return["status"] = true;
                $return["msg"] = "Email com link de ativação enviado com sucesso.";
            } else {
                $return["status"] = false;
                $return["msg"] = "Não foi possível enviar um email com o link de ativação.";
            }
            $tipo = ($returnEmail["status"]) ? 'S' : 'E';
            salvarEvento($tipo, 'Geração de nova senha para usuário.', 'Usuário: ' . $usuario->getUsu_var_identificador() . ' - Mensagem: ' . $returnEmail["msg"]);
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    public function verificaExisteIdentficador($usu_var_identificador) {
        $return = array();
        $param = array("s", $usu_var_identificador);
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE usu_var_identificador = ?;", $param);
            $mysql->fetch();
            if ($mysql->res[0] > 0) {
                $return["status"] = false;
                $return["msg"] = "Usuário existente, favor informar outro.";
            } else {
                $return["status"] = true;
                $return["msg"] = "Não existe usuário com esse identificador.";
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    public function verificaExisteEmail($usu_var_email, $usu_int_codigo = null) {
        $return = array();
        try {
            $mysql = new GDbMysql();
            if ($usu_int_codigo != null) {
                $param = array("si", $usu_var_email, $usu_int_codigo);
                $mysql->execute($this->sqlCount . " WHERE usu_var_email = ? AND usu_int_codigo <> ?;", $param);
            } else {
                $param = array("s", $usu_var_email);
                $mysql->execute($this->sqlCount . " WHERE usu_var_email = ?;", $param);
            }
            $mysql->fetch();
            if ($mysql->res[0] > 0) {
                $return["status"] = false;
                $return["msg"] = "E-mail existente, favor informar outro.";
            } else {
                $return["status"] = true;
                $return["msg"] = "Não existe usuário com esse e-mail.";
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Usuario $usuario */
    public function cadastrarse($usuario) {
        if (buscarParametro('EMAIL_ATIVACAO', 'F') == 'V') {
            $token = uniqid();
            $validado = 0;
        } else {
            $token = $usuario->getUsu_var_token();
            $validado = 2;
        }

        $return = array();
        $param = array("isssssssss", $usuario->getPerfil()->getPef_int_codigo(), $usuario->getUsu_var_nome(), $usuario->getUsu_var_identificador(), $usuario->getUsu_var_email(), md5($usuario->getUsu_var_senha()), $usuario->getUsu_cha_status(), $usuario->getUsu_var_motivo(), $token, formataArquivoURL('usuario', $usuario->getUsu_var_foto(false)), $validado);
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_usuario_ins(?,?,?,?,?,?,?,?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["insertId"] = $mysql->res[2];
            $return["autenticar"] = $mysql->res[3];
            if ($return["status"]) {
                $return["msg"] = 'Parabéns, seu cadastro no ' . SYS_NOME . ' foi realizado com sucesso!';
                $usuario->setUsu_int_codigo($mysql->res[2]);
                $usuario = $this->selectById($usuario);
                if (buscarParametro('EMAIL_ATIVACAO', 'F') == 'V') {
                    $email = new GEmail();
                    $mensagem = gerarEmailCadastro($usuario, $token);
                    $email->setMensagem($mensagem);
                    $email->setAssunto('Bem-vindo(a) ao ' . SYS_NOME);
                    $email->setDestinatario($usuario->getUsu_var_nome() . "<" . $usuario->getUsu_var_email() . ">");
                    $returnEmail = $email->enviar();
                    $return["msg"] .= ($returnEmail["status"]) ? '<br/>Para ter acesso ao sistema, precisamos primeiro ativar sua conta, acesse seu email e clique no link de ativação enviado pelo sistema.<br><br><strong>OBS.: Caso não receba o email na caixa de entrada, verifique se está na caixa de spam ou lixo eletrônico.</strong>' : '<br/>Mas não foi possível enviar um email com o link de ativação. Favor entrar em contato com o nosso suporte através do email <a href="mailto:' . CONTATO . '">' . CONTATO . '</a>.';
                    $tipo = ($returnEmail["status"]) ? 'S' : 'E';
                    salvarEvento($tipo, 'Envio de email ao cadastrar-se', $returnEmail["msg"]);
                } else {
                    if ($return["autenticar"]) {
                        setSessao($usuario->getUsu_int_codigo());
                        unset($_SESSION[SYS_MODULO]["usuario"]);
                        $_SESSION[SYS_MODULO]["usuario"] = serialize($usuario);
                        $this->carregarPermissoes();
                    }
                    salvarEvento('S', 'Usuário cadastrado e ativo', 'O usuário: ' . $usuario->getUsu_var_identificador() . ' foi cadastrado e já teve seu acesso liberado sem necessidade de ativação por e-mail');
                }
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    private function carregarObjeto($mysql, $loadObj = true) {
        $usuario = new Usuario();
        $usuario->setUsu_int_codigo($mysql->res["usu_int_codigo"]);

        $perfil = new Perfil();
        $perfil->setPef_int_codigo($mysql->res["pef_int_codigo"]);
        $perfil->setPef_var_descricao($mysql->res["pef_var_descricao"]);
        $perfil->setPef_cha_status($mysql->res["pef_cha_status"]);
        $perfil->setPef_cha_status_format($mysql->res["pef_cha_status_format"]);

        $usuario->setPerfil($perfil);
        $usuario->setUsu_var_nome($mysql->res["usu_var_nome"]);
        $usuario->setUsu_var_identificador($mysql->res["usu_var_identificador"]);
        $usuario->setUsu_var_email($mysql->res["usu_var_email"]);
        $usuario->setUsu_var_senha($mysql->res["usu_var_senha"]);
        $usuario->setUsu_cha_status($mysql->res["usu_cha_status"]);
        $usuario->setUsu_cha_status_format($mysql->res["usu_cha_status_format"]);
        $usuario->setUsu_var_motivo($mysql->res["usu_var_motivo"]);
        $usuario->setUsu_dti_criacao($mysql->res["usu_dti_criacao"]);
        $usuario->setUsu_dti_criacao_format($mysql->res["usu_dti_criacao_format"]);
        $usuario->setUsu_var_token($mysql->res["usu_var_token"]);
        $usuario->setUsu_var_foto($mysql->res["usu_var_foto"]);
        $usuario->setUsu_cha_validado($mysql->res["usu_cha_validado"]);
        $usuario->setUsu_cha_validado_format($mysql->res["usu_cha_validado_format"]);
        $usuario->setUsu_dti_ultimo($mysql->res["usu_dti_ultimo"]);
        $usuario->setUsu_dti_ultimo_format($mysql->res["usu_dti_ultimo_format"]);
        $usuario->setUsu_var_sessao($mysql->res["usu_var_sessao"]);
        return $usuario;
    }

}

?>