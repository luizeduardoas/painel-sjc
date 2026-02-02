<?php

require_once(ROOT_SYS_CLASS . "avaUsuario.php");

GF::import(array("escola"));

class AvaUsuarioDao {

    private $sql;
    private $sqlCount;

    function __construct() {

        $this->sql = "SELECT usu.usu_int_codigo,usu.esc_int_codigo,usu.usu_int_userid,usu.usu_var_cpf,usu.usu_var_matricula,usu.usu_var_nome,usu.usu_var_cargo,usu.usu_var_funcao,usu.usu_var_email FROM ava_usuario usu ";
        $this->sqlCount = "SELECT COUNT(usu_int_codigo) FROM ava_usuario usu ";
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
                $array[$mysql->res["usu_int_codigo"]] = $this->carregarObjeto($mysql, $loadObj);
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
                $array[$mysql->res["usu_int_codigo"]] = $this->carregarObjeto($mysql, false)->getDescricao();
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

    /** @param AvaUsuario $usuario */
    public function ifExists($usuario) {
        $retorno = false;
        $param = array("i", $usuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE usu.usu_int_codigo = ?  ", $param);
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

    /** @param AvaUsuario $usuario */
    public function selectById($usuario, $loadObj = true) {
        $param = array("i", $usuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE usu.usu_int_codigo = ?  ", $param);
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

    /** @param Escola $escola */
    public function selectByEscola($escola, $loadObj = true) {
        $array = array();
        $param = array("i", $escola->getEsc_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE usu.esc_int_codigo = ? ", $param);
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

    private function carregarObjeto($mysql, $loadObj = true) {
        $usuario = new AvaUsuario();
        $usuario->setUsu_int_codigo($mysql->res["usu_int_codigo"]);

        $escola = new Escola();
        $escola->setEsc_int_codigo($mysql->res["esc_int_codigo"]);
        if ($loadObj) {
            $escolaDao = new EscolaDao();
            $escola = $escolaDao->selectById($escola);
        }
        $usuario->setEscola($escola);

        $usuario->setUsu_int_userid($mysql->res["usu_int_userid"]);
        $usuario->setUsu_var_cpf($mysql->res["usu_var_cpf"]);
        $usuario->setUsu_var_matricula($mysql->res["usu_var_matricula"]);
        $usuario->setUsu_var_nome($mysql->res["usu_var_nome"]);
        $usuario->setUsu_var_cargo($mysql->res["usu_var_cargo"]);
        $usuario->setUsu_var_funcao($mysql->res["usu_var_funcao"]);
        $usuario->setUsu_var_email($mysql->res["usu_var_email"]);

        return $usuario;
    }
}
