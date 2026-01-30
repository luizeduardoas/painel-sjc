<?php

require_once(ROOT_SYS_CLASS . "tag.php");

class TagDao {

    private $sql;
    private $sqlCount;

    function __construct() {
        $this->sql = "SELECT tag.tag_int_codigo,tag.tag_var_titulo,tag.tag_var_url,tag.tag_txt_valores,tag.tag_var_informacoes,tag.pem_var_codigo FROM tag tag ";
        $this->sqlCount = "SELECT COUNT(tag_int_codigo) FROM tag tag ";
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

    /** @param Tag $tag */
    public function ifExists($tag) {
        $retorno = false;
        $param = array("i", $tag->getTag_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE tag.tag_int_codigo = ?  ", $param);
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

    /** @param Tag $tag */
    public function selectById($tag, $loadObj = true) {
        $param = array("i", $tag->getTag_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE tag.tag_int_codigo = ?  ", $param);
            if ($mysql->fetch()) {
                $tag = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $tag;
    }

    /** @param Tag $tag */
    public function insert($tag) {

        $return = array();
        $param = array("sssss", $tag->getTag_var_titulo(), $tag->getTag_var_url(), $tag->getTag_txt_valores(), $tag->getTag_var_informacoes(), $tag->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_tag_ins(?,?,?,?,?);", $param);
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

    /** @param Tag $tag */
    public function update($tag) {

        $return = array();
        $param = array("isssss", $tag->getTag_int_codigo(), $tag->getTag_var_titulo(), $tag->getTag_var_url(), $tag->getTag_txt_valores(), $tag->getTag_var_informacoes(), $tag->getPem_var_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_tag_upd(?,?,?,?,?,?);", $param);
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

    /** @param Tag $tag */
    public function delete($tag) {

        $return = array();
        $param = array("i", $tag->getTag_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_tag_del(?);", $param);
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
        $tag = new Tag();
        $tag->setTag_int_codigo($mysql->res["tag_int_codigo"]);
        $tag->setTag_var_titulo($mysql->res["tag_var_titulo"]);
        $tag->setTag_var_url($mysql->res["tag_var_url"]);
        $tag->setTag_txt_valores($mysql->res["tag_txt_valores"]);
        $tag->setPem_var_codigo($mysql->res["pem_var_codigo"]);
        $tag->setTag_var_informacoes($mysql->res["tag_var_informacoes"]);

        return $tag;
    }

}
