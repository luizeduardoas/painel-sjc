<?php

class AvaCurso {

    private $cur_int_codigo;
    private $cur_var_nome;
    private $cur_int_courseid;
    /* @var $nivel Nivel */
    private $nivel;
    private $cur_cha_visivel;
    private $cur_cha_visivel_format;

    public function getCur_int_codigo() {
        return $this->cur_int_codigo;
    }

    public function getCur_var_nome() {
        return $this->cur_var_nome;
    }

    public function setCur_int_codigo($cur_int_codigo) {
        $this->cur_int_codigo = $cur_int_codigo;
    }

    public function setCur_var_nome($cur_var_nome) {
        $this->cur_var_nome = $cur_var_nome;
    }

    public function getCur_int_courseid() {
        return $this->cur_int_courseid;
    }

    public function setCur_int_courseid($cur_int_courseid) {
        $this->cur_int_courseid = $cur_int_courseid;
    }

    /** @return Nivel */
    public function getNivel() {
        return $this->nivel;
    }

    /** @param Nivel $nivel */
    public function setNivel($nivel) {
        $this->nivel = $nivel;
    }

    public function getCur_cha_visivel() {
        return $this->cur_cha_visivel;
    }

    public function getCur_cha_visivel_format() {
        return $this->cur_cha_visivel_format;
    }

    public function setCur_cha_visivel($cur_cha_visivel) {
        $this->cur_cha_visivel = $cur_cha_visivel;
    }

    public function setCur_cha_visivel_format($cur_cha_visivel_format) {
        $this->cur_cha_visivel_format = $cur_cha_visivel_format;
    }

    public function getArray() {
        $array = array();
        $array["cur_int_codigo"] = $this->cur_int_codigo;
        $array["cur_var_nome"] = $this->cur_var_nome;
        $array["cur_int_courseid"] = $this->cur_int_courseid;
        $array["niv_int_codigo"] = $this->nivel->getNiv_int_codigo();
        $array["nivel"] = $this->nivel->getDescricao();
        $array["cur_cha_visivel"] = $this->cur_cha_visivel;
        $array["cur_cha_visivel_format"] = $this->cur_cha_visivel_format;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->cur_int_codigo;
        $array["Nome"] = $this->cur_var_nome;
        $array["Identificador"] = $this->cur_int_courseid;
        $array["Nível"] = $this->nivel->getDescricao();
        $array["Visível"] = $this->cur_cha_visivel_format;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->cur_int_codigo . ' - ' . $this->cur_var_nome : '' . $this->cur_var_nome;
    }
}

?>