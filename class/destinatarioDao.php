<?php

require_once(ROOT_SYS_CLASS . "destinatario.php");

GF::import(array("mensagem"));

class DestinatarioDao {

    private $des_cha_status;
    private $sql;
    private $sqlCount;

    function __construct() {
        global $__arrayStatusDestinatario;
        $this->des_cha_status = gerarCase("des_cha_status", $__arrayStatusDestinatario, false);
        $this->sql = "SELECT des.des_int_codigo,des.men_int_codigo,des.des_int_destinatario,$this->des_cha_status FROM destinatario des ";
        $this->sqlCount = "SELECT COUNT(des_int_codigo) FROM destinatario des ";
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

    /** @param Destinatario $destinatario */
    public function ifExists($destinatario) {
        $retorno = false;
        $param = array("i", $destinatario->getDes_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE des.des_int_codigo = ?  ", $param);
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

    /** @param Destinatario $destinatario */
    public function selectById($destinatario, $loadObj = true) {
        $param = array("i", $destinatario->getDes_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE des.des_int_codigo = ?  ", $param);
            if ($mysql->fetch()) {
                $destinatario = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $destinatario;
    }

    /** @param Destinatario $destinatario */
    public function selectByDestinatario($destinatario, $loadObj = true) {
        $array = array();
        $param = array("i", $destinatario->getDes_int_destinatario());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE des.des_int_destinatario = ? ", $param);
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

    /** @param Mensagem $mensagem */
    public function selectByMensagem($mensagem, $loadObj = true) {
        $array = array();
        $param = array("i", $mensagem->getMen_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE des.men_int_codigo = ? ", $param);
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

    /** @param Mensagem $mensagem */
    public function selectByMensagemDestinatario($mensagem, $destinatario, $loadObj = true) {
        $param = array("ii", $mensagem->getMen_int_codigo(), $destinatario->getDes_int_destinatario());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE des.men_int_codigo = ? AND des.des_int_destinatario = ? ", $param);
            while ($mysql->fetch()) {
                $destinatario = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $destinatario;
    }

    /** @param Destinatario $destinatario */
    public function insert($destinatario) {

        $return = array();
        $param = array("iis", $destinatario->getMensagem()->getMen_int_codigo(), $destinatario->getDes_int_destinatario(), $destinatario->getDes_cha_status());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_destinatario_ins(?,?,?);", $param);
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

    /** @param Destinatario $destinatario */
    public function update($destinatario) {

        $return = array();
        $param = array("iiis", $destinatario->getDes_int_codigo(), $destinatario->getMensagem()->getMen_int_codigo(), $destinatario->getDes_int_destinatario(), $destinatario->getDes_cha_status());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_destinatario_upd(?,?,?,?);", $param);
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

    /** @param Destinatario $destinatario */
    public function delete($destinatario) {

        $return = array();
        $param = array("i", $destinatario->getDes_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_destinatario_del(?);", $param);
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
        $destinatario = new Destinatario();
        $destinatario->setDes_int_codigo($mysql->res["des_int_codigo"]);

        $mensagem = new Mensagem();
        $mensagem->setMen_int_codigo($mysql->res["men_int_codigo"]);
        if ($loadObj) {
            $mensagemDao = new MensagemDao();
            $mensagem = $mensagemDao->selectById($mensagem);
        }
        $destinatario->setMensagem($mensagem);

        $destinatario->setDes_int_destinatario($mysql->res["des_int_destinatario"]);
        $destinatario->setDes_cha_status($mysql->res["des_cha_status"]);
        $destinatario->setDes_cha_status_format($mysql->res["des_cha_status_format"]);

        return $destinatario;
    }

}
