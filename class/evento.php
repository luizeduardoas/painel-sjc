<?php

class Evento {

    private $eve_int_codigo;
    private $eve_var_titulo;
    private $eve_cha_tipo;
    private $eve_cha_tipo_format;
    private $eve_txt_dados;
    private $eve_dti_criacao;
    private $eve_dti_criacao_format;
    /* @var $usuario Usuario */
    private $usuario;

    public function getEve_int_codigo() {
        return $this->eve_int_codigo;
    }

    public function setEve_int_codigo($eve_int_codigo) {
        $this->eve_int_codigo = $eve_int_codigo;
    }

    public function getEve_var_titulo() {
        return $this->eve_var_titulo;
    }

    public function setEve_var_titulo($eve_var_titulo) {
        $this->eve_var_titulo = trim($eve_var_titulo);
    }

    public function getEve_cha_tipo() {
        return $this->eve_cha_tipo;
    }

    public function setEve_cha_tipo($eve_cha_tipo) {
        $this->eve_cha_tipo = $eve_cha_tipo;
    }

    public function getEve_cha_tipo_format() {
        return $this->eve_cha_tipo_format;
    }

    public function setEve_cha_tipo_format($eve_cha_tipo_format) {
        $this->eve_cha_tipo_format = $eve_cha_tipo_format;
    }

    public function getEve_txt_dados() {
        return $this->eve_txt_dados;
    }

    public function getEve_dti_criacao() {
        return $this->eve_dti_criacao;
    }

    public function getEve_dti_criacao_format() {
        return $this->eve_dti_criacao_format;
    }

    public function setEve_txt_dados($eve_txt_dados) {
        $this->eve_txt_dados = $eve_txt_dados;
    }

    public function setEve_dti_criacao($eve_dti_criacao) {
        $this->eve_dti_criacao = $eve_dti_criacao;
    }

    public function setEve_dti_criacao_format($eve_dti_criacao_format) {
        $this->eve_dti_criacao_format = $eve_dti_criacao_format;
    }

    /** @return Usuario */
    public function getUsuario() {
        return $this->usuario;
    }

    /** @param Usuario $usuario */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    public function getArray() {
        $array = array();
        $array["eve_int_codigo"] = $this->eve_int_codigo;
        $array["eve_var_titulo"] = $this->eve_var_titulo;
        $array["eve_cha_tipo"] = $this->eve_cha_tipo;
        $array["eve_cha_tipo_format"] = $this->eve_cha_tipo_format;
        $array["eve_dti_criacao"] = $this->eve_dti_criacao;
        $array["eve_dti_criacao_format"] = $this->eve_dti_criacao_format;
        $array["eve_txt_dados"] = $this->eve_txt_dados;
        $array["eve_int_usuario"] = $this->usuario->getUsu_int_codigo();
        $array["Usuário"] = $this->usuario->getDescricao();
        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->eve_int_codigo;
        $array["Data"] = $this->eve_dti_criacao_format;
        $array["Tipo"] = $this->eve_cha_tipo_format;
        $array["Título"] = $this->eve_var_titulo;
        $array["Dados"] = $this->eve_txt_dados;
        $array["Usuário"] = $this->usuario->getDescricao();
        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->eve_int_codigo . ' - ' . $this->eve_var_titulo : '' . $this->eve_var_titulo;
    }

}

?>