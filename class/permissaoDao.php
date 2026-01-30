<?php

require_once(ROOT_SYS_CLASS . "permissao.php");

class PermissaoDao {

    private $sql;
    private $sqlCount;

    function __construct() {
        $this->sql = "SELECT pem_var_codigo,pem_var_descricao,pem_var_vinculo FROM permissao pem ";
        $this->sqlCount = "SELECT COUNT(pem_var_codigo) FROM permissao pem ";
    }

    public function select($where = false, $param = false, $loadObj = true) {
        $array = array();
        try {
            $mysql2 = new GDbMysql();
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

    /** @param Permissao $permissao */
    public function selectById($permissao, $loadObj = true) {
        $param = array("s", $permissao->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE pem_var_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $permissao = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $permissao;
    }

    /** @param Permissao $permissao */
    public function insert($permissao) {

        $return = array();
        $param = array("sss", $permissao->getPem_var_codigo(), $permissao->getPem_var_descricao(), $permissao->getVinculo()->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_permissao_ins(?,?,?);", $param);
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

    /** @param Permissao $permissao */
    public function update($permissao) {

        $return = array();
        $param = array("sss", $permissao->getPem_var_codigo(), $permissao->getPem_var_descricao(), $permissao->getVinculo()->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_permissao_upd(?,?,?);", $param);
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

    /** @param Permissao $permissao */
    public function delete($permissao) {

        $return = array();
        $param = array("s", $permissao->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_permissao_del(?);", $param);
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
        $permissao = new Permissao();
        $permissao->setPem_var_codigo($mysql->res["pem_var_codigo"]);
        $permissao->setPem_var_descricao($mysql->res["pem_var_descricao"]);

        $vinculo = new Permissao();
        $vinculo->setPem_var_codigo($mysql->res["pem_var_vinculo"]);
        $mysql2 = new GDbMysql();
        $mysql2->execute("SELECT pem_var_codigo,pem_var_descricao FROM permissao WHERE pem_var_codigo = ?", array("s", $mysql->res["pem_var_vinculo"]));
        if ($mysql2->fetch()) {
            $vinculo->setPem_var_codigo($mysql2->res["pem_var_codigo"]);
            $vinculo->setPem_var_descricao($mysql2->res["pem_var_descricao"]);
        }
        $permissao->setVinculo($vinculo);

        return $permissao;
    }

}

?>