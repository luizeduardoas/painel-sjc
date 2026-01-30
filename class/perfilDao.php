<?php

require_once(ROOT_SYS_CLASS . "perfil.php");

class PerfilDao {

    private $sql;
    private $sqlCount;
    private $pef_cha_status;

    function __construct() {
        global $__arrayAtivo;
        $this->pef_cha_status = gerarCase("pef_cha_status", $__arrayAtivo, false);
        $this->sql = "SELECT pef_int_codigo,pef_var_descricao,$this->pef_cha_status FROM perfil pef ";
        $this->sqlCount = "SELECT COUNT(pef_int_codigo) FROM perfil pef ";
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

    /** @param Perfil $perfil */
    public function ifExists($perfil) {
        $retorno = false;
        $param = array("i", $perfil->getPef_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE pef.pef_int_codigo = ?;", $param);
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

    /** @param Perfil $perfil */
    public function selectById($perfil, $loadObj = true) {
        $param = array("i", $perfil->getPef_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . "WHERE pef.pef_int_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $perfil = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $perfil;
    }

    /** @param Perfil $perfil */
    public function insert($perfil) {

        $return = array();
        $param = array("ss", $perfil->getPef_var_descricao(), $perfil->getPef_cha_status());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_perfil_ins(?,?);", $param);
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

    /** @param Perfil $perfil */
    public function update($perfil) {

        $return = array();
        $param = array("iss", $perfil->getPef_int_codigo(), $perfil->getPef_var_descricao(), $perfil->getPef_cha_status());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_perfil_upd(?,?,?);", $param);
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

    /** @param Perfil $perfil */
    public function delete($perfil) {

        $return = array();
        $param = array("i", $perfil->getPef_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_perfil_del(?);", $param);
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
    
        /** @param Perfil $perfil */
    public function clonar($perfil) {

        $return = array();
        $param = array("i", $perfil->getPef_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_perfil_clo(?);", $param);
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
        $perfil = new Perfil();
        $perfil->setPef_int_codigo($mysql->res["pef_int_codigo"]);
        $perfil->setPef_var_descricao($mysql->res["pef_var_descricao"]);
        $perfil->setPef_cha_status($mysql->res["pef_cha_status"]);
        $perfil->setPef_cha_status_format($mysql->res["pef_cha_status_format"]);
        return $perfil;
    }

}

?>