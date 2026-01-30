<?php

class AvaCurso {

    private $cur_int_codigo;
    private $cur_var_nome;
    private $cur_int_courseid;
    /* @var $nivel Nivel */
    private $nivel;

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

    public function getArray() {
        $array = array();
        $array["cur_int_codigo"] = $this->cur_int_codigo;
        $array["cur_var_nome"] = $this->cur_var_nome;
        $array["cur_int_courseid"] = $this->cur_int_courseid;
        $array["niv_int_codigo"] = $this->nivel->getNiv_int_codigo();
        $array["nivel"] = $this->nivel->getDescricao();

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->cur_int_codigo;
        $array["Nome"] = $this->cur_var_nome;
        $array["ID Curso"] = $this->cur_int_courseid;
        $array["Nível"] = $this->nivel->getDescricao();

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->cur_int_codigo . ' - ' . $this->cur_var_nome : '' . $this->cur_var_nome;
    }
}

?>