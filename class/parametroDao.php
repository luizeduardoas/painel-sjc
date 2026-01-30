<?php

require_once(ROOT_SYS_CLASS . "parametro.php");
GF::import(array("usuario"));

class ParametroDao {

    private $par_dti_atualizacao;
    private $sql;
    private $sqlCount;

    function __construct() {
        $this->par_dti_atualizacao = gerarDate_format("par_dti_atualizacao", false);
        $this->sql = "SELECT par_int_codigo,par_var_chave,par_var_descricao,par_txt_valor,$this->par_dti_atualizacao,usu_int_codigo FROM parametro par ";
        $this->sqlCount = "SELECT COUNT(par_int_codigo) FROM parametro par ";
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

    /** @param Parametro $parametro */
    public function ifExists($parametro) {
        $retorno = false;
        $param = array("i", $parametro->getPar_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE par.par_int_codigo = ?;", $param);
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

    /** @param Parametro $parametro */
    public function selectById($parametro, $loadObj = true) {
        $param = array("i", $parametro->getPar_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE par.par_int_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $parametro = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $parametro;
    }

    /** @param Parametro $parametro */
    public function selectByChave($parametro, $loadObj = true) {
        $param = array("s", $parametro->getPar_var_chave());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE par.par_var_chave = ? ", $param);
            if ($mysql->fetch()) {
                $parametro = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $parametro;
    }

    /** @param Parametro $parametro */
    public function insert($parametro) {

        $return = array();
        $param = array("sssi", $parametro->getPar_var_chave(), $parametro->getPar_var_descricao(), $parametro->getPar_txt_valor(), $parametro->getUsuario()->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_parametro_ins(?,?,?,?);", $param);
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
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    /** @param Parametro $parametro */
    public function update($parametro) {

        $return = array();
        $param = array("isssi", $parametro->getPar_int_codigo(), $parametro->getPar_var_chave(), $parametro->getPar_var_descricao(), $parametro->getPar_txt_valor(), $parametro->getUsuario()->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_parametro_upd(?,?,?,?,?);", $param);
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

    /** @param Parametro $parametro */
    public function delete($parametro) {

        $return = array();
        $param = array("i", $parametro->getPar_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_parametro_del(?);", $param);
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
        $parametro = new Parametro();
        $parametro->setPar_int_codigo($mysql->res["par_int_codigo"]);
        $parametro->setPar_var_chave($mysql->res["par_var_chave"]);
        $parametro->setPar_var_descricao($mysql->res["par_var_descricao"]);
        $parametro->setPar_txt_valor($mysql->res["par_txt_valor"]);
        $parametro->setPar_dti_atualizacao($mysql->res["par_dti_atualizacao"]);
        $parametro->setPar_dti_atualizacao_format($mysql->res["par_dti_atualizacao_format"]);

        $usuario = new Usuario();
        $usuario->setUsu_int_codigo($mysql->res["usu_int_codigo"]);
        if ($loadObj) {
            $usuarioDao = new UsuarioDao();
            $usuario = $usuarioDao->selectById($usuario);
        }
        $parametro->setUsuario($usuario);

        return $parametro;
    }

}

?>