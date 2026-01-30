<?php

class Usuario {

    private $usu_int_codigo;
    /* @var $perfil Perfil */
    private $perfil;
    private $usu_var_identificador;
    private $usu_var_nome;
    private $usu_var_email;
    private $usu_var_senha;
    private $usu_var_senha_new;
    private $usu_cha_status;
    private $usu_cha_status_format;
    private $usu_var_motivo;
    private $usu_dti_criacao;
    private $usu_dti_criacao_format;
    private $usu_var_token;
    private $usu_var_foto;
    private $usu_cha_validado;
    private $usu_cha_validado_format;
    private $usu_dti_ultimo;
    private $usu_dti_ultimo_format;
    private $usu_var_sessao;

    public function getUsu_int_codigo() {
        return $this->usu_int_codigo;
    }

    public function setUsu_int_codigo($usu_int_codigo) {
        $this->usu_int_codigo = $usu_int_codigo;
    }

    /** @return Perfil */
    public function getPerfil() {
        return $this->perfil;
    }

    /** @param Perfil $perfil */
    public function setPerfil($perfil) {
        $this->perfil = $perfil;
    }

    public function getUsu_var_nome() {
        return $this->usu_var_nome;
    }

    public function setUsu_var_nome($usu_var_nome) {
        $this->usu_var_nome = trim($usu_var_nome);
    }

    public function getUsu_var_identificador() {
        return $this->usu_var_identificador;
    }

    public function setUsu_var_identificador($usu_var_identificador) {
        $this->usu_var_identificador = trim($usu_var_identificador);
    }

    public function getUsu_var_email() {
        return $this->usu_var_email;
    }

    public function setUsu_var_email($usu_var_email) {
        $this->usu_var_email = trim(strtolower($usu_var_email));
    }

    public function getUsu_var_senha() {
        return $this->usu_var_senha;
    }

    public function setUsu_var_senha($usu_var_senha) {
        ($usu_var_senha == "") ? null : $usu_var_senha;
        $this->usu_var_senha = $usu_var_senha;
    }

    public function getUsu_var_senha_new() {
        return $this->usu_var_senha_new;
    }

    public function setUsu_var_senha_new($usu_var_senha_new) {
        $this->usu_var_senha_new = trim($usu_var_senha_new);
    }

    public function getUsu_cha_status() {
        return $this->usu_cha_status;
    }

    public function setUsu_cha_status($usu_cha_status) {
        $this->usu_cha_status = $usu_cha_status;
    }

    public function getUsu_cha_status_format() {
        return $this->usu_cha_status_format;
    }

    public function setUsu_cha_status_format($usu_cha_status_format) {
        $this->usu_cha_status_format = $usu_cha_status_format;
    }

    public function getUsu_var_motivo() {
        return $this->usu_var_motivo;
    }

    public function setUsu_var_motivo($usu_var_motivo) {
        $this->usu_var_motivo = $usu_var_motivo;
    }

    public function getUsu_dti_criacao() {
        return $this->usu_dti_criacao;
    }

    public function setUsu_dti_criacao($usu_dti_criacao) {
        $this->usu_dti_criacao = $usu_dti_criacao;
    }

    public function getUsu_dti_criacao_format() {
        return $this->usu_dti_criacao_format;
    }

    public function setUsu_dti_criacao_format($usu_dti_criacao_format) {
        $this->usu_dti_criacao_format = $usu_dti_criacao_format;
    }

    public function getUsu_var_token() {
        return $this->usu_var_token;
    }

    public function setUsu_var_token($usu_var_token) {
        $this->usu_var_token = trim($usu_var_token);
    }

    public function getUsu_var_foto($url = true) {
        if ($url) {
            return (!seNuloOuVazio($this->usu_var_foto)) ? setUpload('usuario', $this->usu_var_foto) : setUpload('usuario', 'unknown.png');
        } else {
            return $this->usu_var_foto;
        }
    }

    public function setUsu_var_foto($usu_var_foto) {
        $this->usu_var_foto = (is_null($usu_var_foto)) ? null : trim($usu_var_foto);
    }

    public function getUsu_cha_validado() {
        return $this->usu_cha_validado;
    }

    public function setUsu_cha_validado($usu_cha_validado) {
        $this->usu_cha_validado = $usu_cha_validado;
    }

    public function getUsu_cha_validado_format() {
        return $this->usu_cha_validado_format;
    }

    public function setUsu_cha_validado_format($usu_cha_validado_format) {
        $this->usu_cha_validado_format = $usu_cha_validado_format;
    }

    function getUsu_dti_ultimo() {
        return $this->usu_dti_ultimo;
    }

    function setUsu_dti_ultimo($usu_dti_ultimo) {
        $this->usu_dti_ultimo = $usu_dti_ultimo;
    }

    function getUsu_dti_ultimo_format() {
        return $this->usu_dti_ultimo_format;
    }

    function setUsu_dti_ultimo_format($usu_dti_ultimo_format) {
        $this->usu_dti_ultimo_format = $usu_dti_ultimo_format;
    }

    function getUsu_var_sessao() {
        return $this->usu_var_sessao;
    }

    function setUsu_var_sessao($usu_var_sessao) {
        $this->usu_var_sessao = $usu_var_sessao;
    }

    public function getArray() {
        $array = array();
        $array["usu_int_codigo"] = $this->usu_int_codigo;
        $array["pef_int_codigo"] = $this->perfil->getPef_int_codigo();
        $array["perfil"] = $this->perfil->getDescricao();
        $array["usu_var_nome"] = $this->usu_var_nome;
        $array["usu_var_identificador"] = $this->usu_var_identificador;
        $array["usu_var_email"] = $this->usu_var_email;
        $array["usu_cha_status"] = $this->usu_cha_status;
        $array["usu_cha_status_format"] = $this->usu_cha_status_format;
        $array["usu_var_motivo"] = $this->usu_var_motivo;
        $array["usu_var_token"] = $this->usu_var_token;
        $array["usu_dti_criacao"] = $this->usu_dti_criacao;
        $array["usu_var_foto"] = $this->usu_var_foto;
        $array["usu_cha_validado"] = $this->usu_cha_validado;
        $array["usu_cha_validado_format"] = $this->usu_cha_validado_format;
        $array["usu_dti_ultimo"] = $this->usu_dti_ultimo;
        $array["usu_var_sessao"] = $this->usu_var_sessao;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Data de Cadastro"] = $this->usu_dti_criacao_format;
        $array["Código"] = $this->usu_int_codigo;
        $array["Perfil"] = $this->perfil->getDescricao();
        $array["Nome"] = $this->usu_var_nome;
        $array["Identificador"] = $this->usu_var_identificador;
        $array["E-mail"] = $this->usu_var_email;
        $array["Status"] = $this->usu_cha_status_format;
        $array["Motivo"] = $this->usu_var_motivo;
        $array["Foto"] = $this->usu_var_foto;
        $array["Validado"] = $this->usu_cha_validado_format;
        $array["Último Acesso"] = $this->usu_dti_ultimo;
        $array["Sessão"] = $this->usu_var_sessao;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->usu_int_codigo . ' - ' . $this->usu_var_nome : '' . $this->usu_var_nome;
    }

}

?>