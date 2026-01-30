<?php

class Escola {

    private $esc_int_codigo;
    private $esc_var_nome;

    public function getEsc_int_codigo() {
        return $this->esc_int_codigo;
    }

    public function getEsc_var_nome() {
        return $this->esc_var_nome;
    }

    public function setEsc_int_codigo($esc_int_codigo) {
        $this->esc_int_codigo = $esc_int_codigo;
    }

    public function setEsc_var_nome($esc_var_nome) {
        $this->esc_var_nome = $esc_var_nome;
    }

    public function getArray() {
        $array = array();
        $array["esc_int_codigo"] = $this->esc_int_codigo;
        $array["esc_var_nome"] = $this->esc_var_nome;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->esc_int_codigo;
        $array["Nome"] = $this->esc_var_nome;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->esc_int_codigo . ' - ' . $this->esc_var_nome : '' . $this->esc_var_nome;
    }
}
