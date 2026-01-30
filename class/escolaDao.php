<?php

require_once(ROOT_SYS_CLASS . "escola.php");

class EscolaDao {

    private $sql;
    private $sqlCount;

    function __construct() {
        $this->sql = "SELECT esc.esc_int_codigo,esc.esc_var_nome FROM escola esc ";
        $this->sqlCount = "SELECT COUNT(esc_int_codigo) FROM escola esc ";
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
                $array[$mysql->res["esc_int_codigo"]] = $this->carregarObjeto($mysql, $loadObj);
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
                $array[$mysql->res["esc_int_codigo"]] = $this->carregarObjeto($mysql, false)->getDescricao();
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

    /** @param Escola $escola */
    public function ifExists($escola) {
        $retorno = false;
        $param = array("i", $escola->getEsc_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE esc.esc_int_codigo = ? ", $param);
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

    /** @param Escola $escola */
    public function selectById($escola, $loadObj = true) {
        $param = array("i", $escola->getEsc_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE esc.esc_int_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $escola = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $escola;
    }

    private function carregarObjeto($mysql, $loadObj = true) {
        $escola = new Escola();
        $escola->setEsc_int_codigo($mysql->res["esc_int_codigo"]);
        $escola->setEsc_var_nome($mysql->res["esc_var_nome"]);

        return $escola;
    }
}
