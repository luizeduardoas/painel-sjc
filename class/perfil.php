<?php

class Perfil {

    private $pef_int_codigo;
    private $pef_var_descricao;
    private $pef_cha_status;
    private $pef_cha_status_format;


    public function getPef_int_codigo() {
        return $this->pef_int_codigo;
    }

    public function setPef_int_codigo($pef_int_codigo) {
        $this->pef_int_codigo = $pef_int_codigo;
    }

    public function getPef_var_descricao() {
        return $this->pef_var_descricao;
    }

    public function setPef_var_descricao($pef_var_descricao) {
        $this->pef_var_descricao = trim($pef_var_descricao);
    }

    public function getPef_cha_status() {
        return $this->pef_cha_status;
    }

    public function setPef_cha_status($pef_cha_status) {
        $this->pef_cha_status = $pef_cha_status;
    }

    public function getPef_cha_status_format() {
        return $this->pef_cha_status_format;
    }

    public function setPef_cha_status_format($pef_cha_status_format) {
        $this->pef_cha_status_format = $pef_cha_status_format;
    }


    public function getArray() {
        $array = array();
        $array["pef_int_codigo"] = $this->pef_int_codigo;
        $array["pef_var_descricao"] = $this->pef_var_descricao;
        $array["pef_cha_status"] = $this->pef_cha_status;
        $array["pef_cha_status_format"] = $this->pef_cha_status_format;
        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->pef_int_codigo;
        $array["Descrição"] = $this->pef_var_descricao;
        $array["Status"] = $this->pef_cha_status_format;
        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->pef_int_codigo . ' - ' . $this->pef_var_descricao : '' . $this->pef_var_descricao;
    }

}

?>