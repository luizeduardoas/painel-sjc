<?php

class Nivel {

    private $niv_int_codigo;
    private $niv_var_identificador;
    private $niv_var_nome;
    private $niv_int_nivel;
    private $niv_var_identificador_pai;
    private $niv_var_hierarquia;

    public function getNiv_int_codigo() {
        return $this->niv_int_codigo;
    }

    public function getNiv_var_identificador() {
        return $this->niv_var_identificador;
    }

    public function getNiv_var_nome() {
        return $this->niv_var_nome;
    }

    public function getNiv_int_nivel() {
        return $this->niv_int_nivel;
    }

    public function setNiv_int_codigo($niv_int_codigo) {
        $this->niv_int_codigo = $niv_int_codigo;
    }

    public function setNiv_var_identificador($niv_var_identificador) {
        $this->niv_var_identificador = $niv_var_identificador;
    }

    public function setNiv_var_nome($niv_var_nome) {
        $this->niv_var_nome = $niv_var_nome;
    }

    public function setNiv_int_nivel($niv_int_nivel) {
        $this->niv_int_nivel = $niv_int_nivel;
    }

    public function getNiv_var_identificador_pai() {
        return $this->niv_var_identificador_pai;
    }

    public function setNiv_var_identificador_pai($niv_var_identificador_pai) {
        $this->niv_var_identificador_pai = $niv_var_identificador_pai;
    }

    public function getNiv_var_hierarquia() {
        return $this->niv_var_hierarquia;
    }

    public function setNiv_var_hierarquia($niv_var_hierarquia) {
        $this->niv_var_hierarquia = $niv_var_hierarquia;
    }

    public function getArray() {
        $array = array();
        $array["niv_int_codigo"] = $this->niv_int_codigo;
        $array["niv_var_identificador"] = $this->niv_var_identificador;
        $array["niv_var_nome"] = $this->niv_var_nome;
        $array["niv_int_nivel"] = $this->niv_int_nivel;
        $array["niv_var_identificador_pai"] = $this->niv_var_identificador_pai;
        $array["niv_var_hierarquia"] = $this->niv_var_hierarquia;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->niv_int_codigo;
        $array["Identificador"] = $this->niv_var_identificador;
        $array["Nome"] = $this->niv_var_nome;
        $array["Nível"] = $this->niv_int_nivel;
        $array["Pai"] = $this->niv_var_identificador_pai;
        $array["Hierarquia"] = $this->niv_var_hierarquia;
        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->niv_int_codigo . ' - ' . $this->niv_var_hierarquia : '' . $this->niv_var_hierarquia;
    }
}
