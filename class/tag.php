<?php

class Tag {

    private $tag_int_codigo;
    private $tag_var_titulo;
    private $tag_var_url;
    private $tag_txt_valores;
    private $tag_var_informacoes;
    private $pem_var_codigo;

    public function getTag_int_codigo() {
        return $this->tag_int_codigo;
    }

    public function setTag_int_codigo($tag_int_codigo) {
        $this->tag_int_codigo = $tag_int_codigo;
    }

    public function getTag_var_titulo() {
        return $this->tag_var_titulo;
    }

    public function setTag_var_titulo($tag_var_titulo) {
        $this->tag_var_titulo = $tag_var_titulo;
    }

    public function getTag_var_url() {
        return $this->tag_var_url;
    }

    public function setTag_var_url($tag_var_url) {
        $this->tag_var_url = $tag_var_url;
    }

    public function getTag_txt_valores() {
        return $this->tag_txt_valores;
    }

    public function setTag_txt_valores($tag_txt_valores) {
        $this->tag_txt_valores = $tag_txt_valores;
    }

    public function getTag_var_informacoes() {
        return $this->tag_var_informacoes;
    }

    public function setTag_var_informacoes($tag_var_informacoes) {
        $this->tag_var_informacoes = $tag_var_informacoes;
    }

    public function getPem_var_codigo() {
        return $this->pem_var_codigo;
    }

    public function setPem_var_codigo($pem_var_codigo) {
        $pem_var_codigo = (is_null($pem_var_codigo)) ? null : strtoupper($pem_var_codigo);
        $this->pem_var_codigo = trim($pem_var_codigo);
    }

    public function getArray() {
        $array = array();
        $array["tag_int_codigo"] = $this->tag_int_codigo;
        $array["tag_var_url"] = $this->tag_var_url;
        $array["tag_txt_valores"] = $this->tag_txt_valores;
        $array["tag_var_informacoes"] = $this->tag_var_informacoes;
        $array["pem_var_codigo"] = $this->pem_var_codigo;
        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->tag_int_codigo;
        $array["Url"] = $this->tag_var_url;
        $array["Valores"] = $this->tag_txt_valores;
        $array["Informações"] = $this->tag_var_informacoes;
        $array["Permissão"] = $this->pem_var_codigo;
        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->tag_int_codigo . ' - ' . $this->tag_var_url : '' . $this->tag_var_url;
    }

}
