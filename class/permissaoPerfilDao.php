<?php

require_once(ROOT_SYS_CLASS . "permissaoPerfil.php");
GF::import(array("perfil", "permissao"));

class PermissaoPerfilDao {

    private $sql;
    private $sqlCount;

    function __construct() {
        $this->sql = "SELECT pem_var_codigo,pef_int_codigo FROM perfil_permissao pp ";
        $this->sqlCount = "SELECT COUNT(pem_var_codigo) FROM perfil_permissao pp ";
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

    /** @param PermissaoPerfil $permissaoPerfil */
    public function ifExists($permissaoPerfil) {
        $param = array("is", $permissaoPerfil->getPerfil()->getPef_int_codigo(), $permissaoPerfil->getPermissao()->getPem_var_codigo());
        $retorno = false;
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE pp.pef_int_codigo = ? AND pp.pem_var_codigo = ? ", $param);
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

    /** @param PermissaoPerfil $permissaoPerfil */
    public function selectById($permissaoPerfil, $loadObj = true) {
        $param = array("is", $permissaoPerfil->getPerfil()->getPef_int_codigo(), $permissaoPerfil->getPermissao()->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE pp.pef_int_codigo = ? AND pp.pem_var_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $permissaoPerfil = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $permissaoPerfil;
    }

    /** @param PermissaoPerfil $permissaoPerfil */
    public function insert($permissaoPerfil) {

        $return = array();
        $param = array("is", $permissaoPerfil->getPerfil()->getPef_int_codigo(), $permissaoPerfil->getPermissao()->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_perfil_permissao_ins(?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            if ($return["status"]) {
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param PermissaoPerfil $permissaoPerfil */
    public function delete($permissaoPerfil) {

        $return = array();
        $param = array("is", $permissaoPerfil->getPerfil()->getPef_int_codigo(), $permissaoPerfil->getPermissao()->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_perfil_permissao_del(?,?);", $param);
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
        $permissaoPerfil = new PermissaoPerfil();

        $perfil = new Perfil();
        $perfil->setPef_int_codigo($mysql->res["pef_int_codigo"]);
        if ($loadObj) {
            $perfilDao = new PerfilDao();
            $perfil = $perfilDao->selectById($perfil);
        }

        $permissao = new Permissao();
        $permissao->setPem_var_codigo($mysql->res["pem_var_codigo"]);
        if ($loadObj) {
            $permissaoDao = new PermissaoDao();
            $permissao = $permissaoDao->selectById($permissao);
        }

        $permissaoPerfil->setPerfil($perfil);
        $permissaoPerfil->setPermissao($permissao);

        return $permissaoPerfil;
    }

}

?>