<?php

require_once(ROOT_SYS_CLASS . "nivel.php");

class NivelDao {

    private $sql;
    private $sqlCount;
    private $niv_cha_visivel;

    function __construct() {
        global $__arraySimNao;
        $this->niv_cha_visivel = gerarCase("niv_cha_visivel", $__arraySimNao, false);
        $this->sql = "SELECT niv.niv_int_codigo,niv_var_identificador,niv.niv_var_nome,niv.niv_int_nivel,niv.niv_var_identificador_pai,niv.niv_var_hierarquia,$this->niv_cha_visivel FROM nivel niv ";
        $this->sqlCount = "SELECT COUNT(niv_int_codigo) FROM nivel niv ";
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
                $array[$mysql->res["niv_int_codigo"]] = $this->carregarObjeto($mysql, $loadObj);
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
                $array[$mysql->res["niv_int_codigo"]] = $this->carregarObjeto($mysql, false)->getDescricao();
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

    /** @param Nivel $nivel */
    public function ifExists($nivel) {
        $retorno = false;
        $param = array("i", $nivel->getNiv_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE niv.niv_int_codigo = ? ", $param);
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

    /** @param Nivel $nivel */
    public function ifExistsFilho($nivel) {
        $retorno = false;
        $param = array("s", $nivel->getNiv_var_identificador());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE niv.niv_var_identificador_pai = ? ", $param);
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

    /** @param Nivel $nivel */
    public function selectById($nivel, $loadObj = true) {
        $param = array("i", $nivel->getNiv_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE niv.niv_int_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $nivel = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $nivel;
    }

    /** @param Nivel $nivel */
    public function selectByIdentificador($nivel, $loadObj = true) {
        $param = array("s", $nivel->getNiv_var_identificador());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE niv.niv_var_identificador = ? ", $param);
            if ($mysql->fetch()) {
                $nivel = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $nivel;
    }

    private function carregarObjeto($mysql, $loadObj = true) {
        $nivel = new Nivel();
        $nivel->setNiv_int_codigo($mysql->res["niv_int_codigo"]);
        $nivel->setNiv_var_identificador($mysql->res["niv_var_identificador"]);
        $nivel->setNiv_var_nome($mysql->res["niv_var_nome"]);
        $nivel->setNiv_int_nivel($mysql->res["niv_int_nivel"]);
        $nivel->setNiv_var_identificador_pai($mysql->res["niv_var_identificador_pai"]);
        $nivel->setNiv_var_hierarquia($mysql->res["niv_var_hierarquia"]);
        $nivel->setNiv_cha_visivel($mysql->res["niv_cha_visivel"]);
        $nivel->setNiv_cha_visivel_format($mysql->res["niv_cha_visivel_format"]);

        return $nivel;
    }
}
