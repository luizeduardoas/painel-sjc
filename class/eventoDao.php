<?php

require_once(ROOT_SYS_CLASS . "evento.php");

class EventoDao {

    private $eve_cha_tipo;
    private $eve_dti_criacao;
    private $sql;
    private $sqlCount;

    function __construct() {
        global $__arrayTipoEvento;
        $this->eve_cha_tipo = gerarCase("eve_cha_tipo", $__arrayTipoEvento, false);
        $this->eve_dti_criacao = gerarDate_format("eve_dti_criacao", false, '%d/%m/%Y %T');
        $this->sql = "SELECT eve_int_codigo,eve_var_titulo,$this->eve_cha_tipo,$this->eve_dti_criacao,eve_txt_dados,eve_int_usuario,eve_var_identificador FROM evento eve ";
        $this->sqlCount = "SELECT COUNT(eve_int_codigo) FROM evento eve ";
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

    /** @param Evento $evento */
    public function ifExists($evento) {
        $retorno = false;
        $param = array("i", $evento->getEve_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE eve.eve_int_codigo = ?;", $param);
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

    /** @param Evento $evento */
    public function selectById($evento, $loadObj = true) {
        $param = array("i", $evento->getEve_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE eve.eve_int_codigo = ?;", $param);
            if ($mysql->fetch()) {
                $evento = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $evento;
    }

    /** @param Evento $evento */
    public function insert($evento) {

        $return = array();
        $param = array("sii", $evento->getEve_var_titulo(), $evento->getEve_cha_tipo(), $evento->getUsuario()->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_evento_ins(?,?,?);", $param);
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

    /** @param Evento $evento */
    public function update($evento) {

        $return = array();
        $param = array("isii", $evento->getEve_int_codigo(), $evento->getEve_var_titulo(), $evento->getEve_cha_tipo(), $evento->getUsuario()->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_evento_upd(?,?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["affectedRows"] = $mysql->res[2];
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

    /** @param Evento $evento */
    public function delete($evento) {

        $return = array();
        $param = array("i", $evento->getEve_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_evento_del(?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["affectedRows"] = $mysql->res[2];
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

    private function carregarObjeto($mysql, $loadObj = true) {
        $evento = new Evento();
        $evento->setEve_int_codigo($mysql->res["eve_int_codigo"]);
        $evento->setEve_var_titulo($mysql->res["eve_var_titulo"]);
        $evento->setEve_cha_tipo($mysql->res["eve_cha_tipo"]);
        $evento->setEve_cha_tipo_format($mysql->res["eve_cha_tipo_format"]);
        $evento->setEve_txt_dados($mysql->res["eve_txt_dados"]);
        $evento->setEve_dti_criacao($mysql->res["eve_dti_criacao"]);
        $evento->setEve_dti_criacao_format($mysql->res["eve_dti_criacao_format"]);

        $usuario = new Usuario();
        $usuario->setUsu_int_codigo($mysql->res["eve_int_usuario"]);
        if ($loadObj) {
            $usuarioDao = new UsuarioDao();
            $usuario = $usuarioDao->selectById($usuario);
        }
        $evento->setUsuario($usuario);

        return $evento;
    }

}

?>