<?php

require_once(ROOT_SYS_CLASS . "acesso.php");

class AcessoDao {

    private $ace_dti_criacao;
    private $sql;
    private $sqlCount;

    function __construct() {

        $this->ace_dti_criacao = gerarDate_format("ace_dti_criacao", false);
        $this->sql = "SELECT ace.ace_int_codigo,$this->ace_dti_criacao,ace.ace_int_usuario,usu.usu_var_nome,ace.ace_var_ip,ace.ace_var_sessao,ace.ace_var_server,ace.ace_var_url,ace.ace_txt_request,ace.ace_var_agent,ace.ace_txt_json,ace.ace_int_lead FROM acesso ace INNER JOIN usuario usu ON (usu.usu_int_codigo = ace.ace_int_usuario) ";
        $this->sqlCount = "SELECT COUNT(ace_int_codigo) FROM acesso ace INNER JOIN usuario usu ON (usu.usu_int_codigo = ace.ace_int_usuario) ";
    }

    public function selectKeys($where = false, $param = false, $loadObj = true) {
        $array = array();
        try {
            $mysql = new GDbMysql();
            if ($param)
                $mysql->execute($this->sql . $where, $param);
            else
                $mysql->execute($this->sql . $where);
            while ($mysql->fetch()) {
                $array[$mysql->res["ace_int_codigo"]] = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $array;
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

    public function selectCombo($where = false, $param = false) {
        $array = array();
        try {
            $mysql = new GDbMysql();
            if ($param)
                $mysql->execute($this->sql . $where, $param);
            else
                $mysql->execute($this->sql . $where);
            while ($mysql->fetch()) {
                $array[$mysql->res["ace_int_codigo"]] = $this->carregarObjeto($mysql, false)->getDescricao();
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

    /** @param Acesso $acesso */
    public function ifExists($acesso) {
        $retorno = false;
        $param = array("i", $acesso->getAce_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE ace.ace_int_codigo = ?  ", $param);
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

    /** @param Acesso $acesso */
    public function selectById($acesso, $loadObj = true) {
        $param = array("i", $acesso->getAce_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE ace.ace_int_codigo = ?  ", $param);
            if ($mysql->fetch()) {
                $acesso = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $acesso;
    }

    /** @param Acesso $acesso */
    public function insert($acesso) {

        $return = array();
        $param = array("sisssssssi", $acesso->getAce_dti_criacao(), $acesso->getAce_int_usuario(), $acesso->getAce_var_ip(), $acesso->getAce_var_sessao(), $acesso->getAce_var_server(), $acesso->getAce_var_url(), $acesso->getAce_txt_request(), $acesso->getAce_var_agent(), $acesso->getAce_txt_json(), $acesso->getAce_int_lead());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_acesso_ins(?,?,?,?,?,?,?,?,?,?);", $param);
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
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Acesso $acesso */
    public function update($acesso) {

        $return = array();
        $param = array("isisssssssi", $acesso->getAce_int_codigo(), $acesso->getAce_dti_criacao(), $acesso->getAce_int_usuario(), $acesso->getAce_var_ip(), $acesso->getAce_var_sessao(), $acesso->getAce_var_server(), $acesso->getAce_var_url(), $acesso->getAce_txt_request(), $acesso->getAce_var_agent(), $acesso->getAce_txt_json(), $acesso->getAce_int_lead());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_acesso_upd(?,?,?,?,?,?,?,?,?,?,?);", $param);
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
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Acesso $acesso */
    public function delete($acesso) {

        $return = array();
        $param = array("i", $acesso->getAce_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_acesso_del(?);", $param);
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
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    private function carregarObjeto($mysql, $loadObj = true) {
        $acesso = new Acesso();
        $acesso->setAce_int_codigo($mysql->res["ace_int_codigo"]);
        $acesso->setAce_dti_criacao($mysql->res["ace_dti_criacao"]);
        $acesso->setAce_dti_criacao_format($mysql->res["ace_dti_criacao_format"]);
        $acesso->setAce_int_usuario($mysql->res["ace_int_usuario"]);
        $acesso->setAce_int_usuario_nome($mysql->res["usu_var_nome"]);
        $acesso->setAce_var_ip($mysql->res["ace_var_ip"]);
        $acesso->setAce_var_sessao($mysql->res["ace_var_sessao"]);
        $acesso->setAce_var_server($mysql->res["ace_var_server"]);
        $acesso->setAce_var_url($mysql->res["ace_var_url"]);
        $acesso->setAce_txt_request($mysql->res["ace_txt_request"]);
        $acesso->setAce_var_agent($mysql->res["ace_var_agent"]);
        $acesso->setAce_txt_json($mysql->res["ace_txt_json"]);
        $acesso->setAce_int_lead($mysql->res["ace_int_lead"]);

        return $acesso;
    }
}
