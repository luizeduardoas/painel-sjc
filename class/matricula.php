<?php

class Matricula {

    private $mat_int_codigo;
    /* @var $usuario AvaUsuario */
    private $usuario;
    /* @var $curso AvaCurso */
    private $curso;
    private $mat_dti_criacao;
    private $mat_dti_criacao_format;
    private $mat_dti_inicio;
    private $mat_dti_inicio_format;
    private $mat_dti_termino;
    private $mat_dti_termino_format;

    public function getMat_int_codigo() {
        return $this->mat_int_codigo;
    }

    public function setMat_int_codigo($mat_int_codigo) {
        $this->mat_int_codigo = $mat_int_codigo;
    }

    /** @return AvaUsuario */
    public function getUsuario() {
        return $this->usuario;
    }

    /** @param AvaUsuario $usuario */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /** @return AvaCurso */
    public function getCurso() {
        return $this->curso;
    }

    /** @param AvaCurso $curso */
    public function setCurso($curso) {
        $this->curso = $curso;
    }

    public function getMat_dti_criacao() {
        return $this->mat_dti_criacao;
    }

    public function setMat_dti_criacao($mat_dti_criacao) {
        $this->mat_dti_criacao = $mat_dti_criacao;
    }

    public function getMat_dti_criacao_format() {
        return $this->mat_dti_criacao_format;
    }

    public function setMat_dti_criacao_format($mat_dti_criacao_format) {
        $this->mat_dti_criacao_format = $mat_dti_criacao_format;
    }

    public function getMat_dti_inicio() {
        return $this->mat_dti_inicio;
    }

    public function setMat_dti_inicio($mat_dti_inicio) {
        $this->mat_dti_inicio = $mat_dti_inicio;
    }

    public function getMat_dti_inicio_format() {
        return $this->mat_dti_inicio_format;
    }

    public function setMat_dti_inicio_format($mat_dti_inicio_format) {
        $this->mat_dti_inicio_format = $mat_dti_inicio_format;
    }

    public function getMat_dti_termino() {
        return $this->mat_dti_termino;
    }

    public function setMat_dti_termino($mat_dti_termino) {
        $this->mat_dti_termino = $mat_dti_termino;
    }

    public function getMat_dti_termino_format() {
        return $this->mat_dti_termino_format;
    }

    public function setMat_dti_termino_format($mat_dti_termino_format) {
        $this->mat_dti_termino_format = $mat_dti_termino_format;
    }

    public function getArray() {
        $array = array();
        $array["mat_int_codigo"] = $this->mat_int_codigo;
        $array["usu_int_codigo"] = $this->usuario->getUsu_int_codigo();
        $array["Usuário"] = $this->usuario->getDescricao();
        $array["cur_int_codigo"] = $this->curso->getCur_int_codigo();
        $array["Curso"] = $this->curso->getDescricao();
        $array["mat_dti_criacao"] = $this->mat_dti_criacao;
        $array["mat_dti_criacao_format"] = $this->mat_dti_criacao_format;
        $array["mat_dti_inicio"] = $this->mat_dti_inicio;
        $array["mat_dti_inicio_format"] = $this->mat_dti_inicio_format;
        $array["mat_dti_termino"] = $this->mat_dti_termino;
        $array["mat_dti_termino_format"] = $this->mat_dti_termino_format;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->mat_int_codigo;
        $array["Usuário"] = $this->usuario->getDescricao();
        $array["Curso"] = $this->curso->getDescricao();
        $array["Data e hora de Criação"] = $this->mat_dti_criacao;
        $array["Data e hora de Criação"] = $this->mat_dti_criacao_format;
        $array["Data e hora de Início"] = $this->mat_dti_inicio;
        $array["Data e hora de Início"] = $this->mat_dti_inicio_format;
        $array["Data e hora de Término"] = $this->mat_dti_termino;
        $array["Data e hora de Término"] = $this->mat_dti_termino_format;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->mat_int_codigo . ' - ' . $this->usu_int_codigo : '' . $this->usu_int_codigo;
    }
}
