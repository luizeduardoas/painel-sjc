<?php

require_once(ROOT_SYS_CLASS . "matricula.php");

GF::import(array("avaUsuario", "avaCurso", "escola"));

class MatriculaDao {

    private $mat_dti_criacao;
    private $mat_dti_inicio;
    private $mat_dti_termino;
    private $sql;
    private $sqlCount;

    function __construct() {

        $this->mat_dti_criacao = gerarDate_format("mat_dti_criacao", false);
        $this->mat_dti_inicio = gerarDate_format("mat_dti_inicio", false, "%d/%m/%Y");
        $this->mat_dti_termino = gerarDate_format("mat_dti_termino", false, "%d/%m/%Y");
        $join = " INNER JOIN ava_usuario usu ON (usu.usu_int_codigo = mat.usu_int_codigo) INNER JOIN escola esc ON (esc.esc_int_codigo = usu.esc_int_codigo) ";
        $this->sql = "SELECT mat.mat_int_codigo,mat.usu_int_codigo,mat.cur_int_codigo,$this->mat_dti_criacao,$this->mat_dti_inicio,$this->mat_dti_termino,usu.esc_int_codigo,usu.usu_int_userid,usu.usu_var_cpf,usu.usu_var_matricula,usu.usu_var_nome,usu.usu_var_cargo,usu.usu_var_funcao,usu.usu_var_email,esc.esc_var_nome FROM ava_matricula mat " . $join;
        $this->sqlCount = "SELECT COUNT(mat_int_codigo) FROM ava_matricula mat " . $join;
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
                $array[$mysql->res["mat_int_codigo"]] = $this->carregarObjeto($mysql, $loadObj);
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
                $array[$mysql->res["mat_int_codigo"]] = $this->carregarObjeto($mysql, false)->getDescricao();
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

    /** @param Matricula $matricula */
    public function ifExists($matricula) {
        $retorno = false;
        $param = array("i", $matricula->getMat_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE mat.mat_int_codigo = ?  ", $param);
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

    /** @param Matricula $matricula */
    public function selectById($matricula, $loadObj = true) {
        $param = array("i", $matricula->getMat_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE mat.mat_int_codigo = ?  ", $param);
            if ($mysql->fetch()) {
                $matricula = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $matricula;
    }

    /** @param AvaUsuario $avausuario */
    public function selectByUsuario($avausuario, $loadObj = true) {
        $array = array();
        $param = array("i", $avausuario->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE mat.usu_int_codigo = ? ", $param);
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

    /** @param AvaCurso $avacurso */
    public function selectByCurso($avacurso, $loadObj = true) {
        $array = array();
        $param = array("i", $avacurso->getCur_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE mat.cur_int_codigo = ? ", $param);
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

    /** @param Escola $escola */
    public function selectByEscola($escola, $loadObj = true) {
        $array = array();
        $param = array("i", $escola->getEsc_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE esc.esc_int_codigo = ? ", $param);
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
        $matricula = new Matricula();
        $matricula->setMat_int_codigo($mysql->res["mat_int_codigo"]);

        $usuario = new AvaUsuario();
        $usuario->setUsu_int_codigo($mysql->res["usu_int_codigo"]);
        $escola = new Escola();
        $escola->setEsc_int_codigo($mysql->res["esc_int_codigo"]);
        $escola->setEsc_var_nome($mysql->res["esc_var_nome"]);
        $usuario->setEscola($escola);
        $usuario->setUsu_int_userid($mysql->res["usu_int_userid"]);
        $usuario->setUsu_var_cpf($mysql->res["usu_var_cpf"]);
        $usuario->setUsu_var_matricula($mysql->res["usu_var_matricula"]);
        $usuario->setUsu_var_nome($mysql->res["usu_var_nome"]);
        $usuario->setUsu_var_cargo($mysql->res["usu_var_cargo"]);
        $usuario->setUsu_var_funcao($mysql->res["usu_var_funcao"]);
        $usuario->setUsu_var_email($mysql->res["usu_var_email"]);
        $matricula->setUsuario($usuario);

        $curso = new AvaCurso();
        $curso->setCur_int_codigo($mysql->res["cur_int_codigo"]);
        if ($loadObj) {
            $cursoDao = new AvaCursoDao();
            $curso = $cursoDao->selectById($curso);
        }
        $matricula->setCurso($curso);

        $matricula->setMat_dti_criacao($mysql->res["mat_dti_criacao"]);
        $matricula->setMat_dti_criacao_format($mysql->res["mat_dti_criacao_format"]);
        $matricula->setMat_dti_inicio($mysql->res["mat_dti_inicio"]);
        $matricula->setMat_dti_inicio_format($mysql->res["mat_dti_inicio_format"]);
        $matricula->setMat_dti_termino($mysql->res["mat_dti_termino"]);
        $matricula->setMat_dti_termino_format($mysql->res["mat_dti_termino_format"]);

        return $matricula;
    }
}
