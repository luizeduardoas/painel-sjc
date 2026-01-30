<?php

class Auditoria {

    private $aud_int_codigo;
    private $aud_cha_acao;
    private $aud_cha_acao_format;
    private $aud_dti_registro;
    private $aud_dti_registro_format;
    private $aud_int_usuario;
    private $aud_var_nome;

    public function getAud_int_codigo() {
        return $this->aud_int_codigo;
    }

    public function getAud_cha_acao() {
        return $this->aud_cha_acao;
    }

    public function getAud_cha_acao_format() {
        return $this->aud_cha_acao_format;
    }

    public function getAud_dti_registro() {
        return $this->aud_dti_registro;
    }

    public function getAud_dti_registro_format() {
        return $this->aud_dti_registro_format;
    }

    public function getAud_int_usuario() {
        return $this->aud_int_usuario;
    }

    public function getAud_var_nome() {
        return $this->aud_var_nome;
    }

    public function setAud_int_codigo($aud_int_codigo) {
        $this->aud_int_codigo = $aud_int_codigo;
    }

    public function setAud_cha_acao($aud_cha_acao) {
        $this->aud_cha_acao = $aud_cha_acao;
    }

    public function setAud_cha_acao_format($aud_cha_acao_format) {
        $this->aud_cha_acao_format = $aud_cha_acao_format;
    }

    public function setAud_dti_registro($aud_dti_registro) {
        $this->aud_dti_registro = $aud_dti_registro;
    }

    public function setAud_dti_registro_format($aud_dti_registro_format) {
        $this->aud_dti_registro_format = $aud_dti_registro_format;
    }

    public function setAud_int_usuario($aud_int_usuario) {
        $this->aud_int_usuario = $aud_int_usuario;
    }

    public function setAud_var_nome($aud_var_nome) {
        $this->aud_var_nome = $aud_var_nome;
    }

    public function getArray() {
        $array = array();
        $array["aud_int_codigo"] = $this->aud_int_codigo;
        $array["aud_cha_acao"] = $this->aud_cha_acao;
        $array["aud_cha_acao_format"] = $this->aud_cha_acao_format;
        $array["aud_dti_registro"] = $this->aud_dti_registro;
        $array["aud_dti_registro_format"] = $this->aud_dti_registro_format;
        $array["aud_int_usuario"] = $this->aud_int_usuario;
        $array["aud_var_nome"] = $this->aud_var_nome;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->aud_int_codigo;
        $array["Ação"] = $this->aud_cha_acao_format;
        $array["Data e Hora"] = $this->aud_dti_registro_format;
        $array["Usuário"] = $this->aud_int_usuario;
        $array["Nome"] = $this->aud_var_nome;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->aud_int_codigo . ' - ' . $this->aud_var_nome . ' - ' . $this->aud_dti_registro_format : $this->aud_var_nome . ' - ' . $this->aud_dti_registro_format;
    }
}
