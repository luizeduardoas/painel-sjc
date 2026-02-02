<?php

require_once(ROOT_SYS_CLASS . "avaCurso.php");
GF::import(array("nivel"));

class AvaCursoDao {

    private $sql;
    private $sqlCount;
    private $cur_cha_visivel;

    function __construct() {
        global $__arraySimNao;
        $this->cur_cha_visivel = gerarCase("cur_cha_visivel", $__arraySimNao, false);
        $this->sql = "SELECT cur.cur_int_codigo,cur.cur_var_nome,cur.cur_int_courseid,cur.niv_int_codigo,$this->cur_cha_visivel  FROM ava_curso cur ";
        $this->sqlCount = "SELECT COUNT(cur_int_codigo) FROM ava_curso cur ";
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
                $array[$mysql->res["cur_int_codigo"]] = $this->carregarObjeto($mysql, $loadObj);
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
                $array[$mysql->res["cur_int_codigo"]] = $this->carregarObjeto($mysql, false)->getDescricao();
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

    /** @param Curso $curso */
    public function ifExists($curso) {
        $retorno = false;
        $param = array("i", $curso->getCur_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE cur.cur_int_codigo = ? ", $param);
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

    /** @param Curso $curso */
    public function selectById($curso, $loadObj = true) {
        $param = array("i", $curso->getCur_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE cur.cur_int_codigo = ? ", $param);
            if ($mysql->fetch()) {
                $curso = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $curso;
    }

    private function carregarObjeto($mysql, $loadObj = true) {
        $curso = new AvaCurso();
        $curso->setCur_int_codigo($mysql->res["cur_int_codigo"]);
        $curso->setCur_var_nome($mysql->res["cur_var_nome"]);
        $curso->setCur_int_courseid($mysql->res["cur_int_courseid"]);

        $nivel = new Nivel();
        $nivel->setNiv_int_codigo($mysql->res["niv_int_codigo"]);
        if ($loadObj) {
            $nivelDao = new NivelDao();
            $nivel = $nivelDao->selectById($nivel);
        }
        $curso->setNivel($nivel);

        $curso->setCur_cha_visivel($mysql->res["cur_cha_visivel"]);
        $curso->setCur_cha_visivel_format($mysql->res["cur_cha_visivel_format"]);

        return $curso;
    }
}
