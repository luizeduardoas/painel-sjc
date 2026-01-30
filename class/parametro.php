<?php

class Parametro {

    private $par_int_codigo;
    private $par_var_chave;
    private $par_var_descricao;
    private $par_txt_valor;
    private $par_dti_atualizacao;
    private $par_dti_atualizacao_format;
    /* @var $usuario Usuario */
    private $usuario;

    public function getPar_int_codigo() {
        return $this->par_int_codigo;
    }

    public function setPar_int_codigo($par_int_codigo) {
        $this->par_int_codigo = $par_int_codigo;
    }

    public function getPar_var_chave() {
        return $this->par_var_chave;
    }

    public function setPar_var_chave($par_var_chave) {
        $par_var_chave = (is_null($par_var_chave)) ? null : strtoupper($par_var_chave);
        $this->par_var_chave = trim($par_var_chave);
    }

    public function getPar_var_descricao() {
        return $this->par_var_descricao;
    }

    public function setPar_var_descricao($par_var_descricao) {
        $this->par_var_descricao = trim($par_var_descricao);
    }

    public function getPar_txt_valor() {
        return $this->par_txt_valor;
    }

    public function getPar_dti_atualizacao() {
        return $this->par_dti_atualizacao;
    }

    public function getPar_dti_atualizacao_format() {
        return $this->par_dti_atualizacao_format;
    }

    public function setPar_txt_valor($par_txt_valor) {
        $this->par_txt_valor = $par_txt_valor;
    }

    public function setPar_dti_atualizacao($par_dti_atualizacao) {
        $this->par_dti_atualizacao = $par_dti_atualizacao;
    }

    public function setPar_dti_atualizacao_format($par_dti_atualizacao_format) {
        $this->par_dti_atualizacao_format = $par_dti_atualizacao_format;
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
        $array["par_int_codigo"] = $this->par_int_codigo;
        $array["par_var_chave"] = $this->par_var_chave;
        $array["par_var_descricao"] = $this->par_var_descricao;
        $array["par_txt_valor"] = $this->par_txt_valor;
        $array["par_dti_atualizacao"] = $this->par_dti_atualizacao;
        $array["par_dti_atualizacao_format"] = $this->par_dti_atualizacao_format;
        $array["usu_int_codigo"] = $this->usuario->getUsu_int_codigo();
        $array["Usuário"] = $this->usuario->getDescricao();
        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->par_int_codigo;
        $array["Chave"] = $this->par_var_chave;
        $array["Descrição"] = $this->par_var_descricao;
        $array["Valor"] = $this->par_txt_valor;
        $array["Atualização"] = $this->par_dti_atualizacao_format;
        $array["Usuário"] = $this->usuario->getDescricao();
        return $array;
    }

    public function getDescricao($codigo = true) {
        return ($codigo) ? $this->par_var_codigo . ' - ' . $this->par_var_chave : '' . $this->par_var_chave;
    }

}

?>