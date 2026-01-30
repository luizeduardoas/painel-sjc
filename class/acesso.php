<?php

class Acesso {

    private $ace_int_codigo;
    private $ace_dti_criacao;
    private $ace_dti_criacao_format;
    private $ace_int_usuario;
    private $ace_int_usuario_nome;
    private $ace_var_ip;
    private $ace_var_sessao;
    private $ace_var_server;
    private $ace_var_url;
    private $ace_txt_request;
    private $ace_var_agent;
    private $ace_txt_json;
    private $ace_int_lead;

    public function getAce_int_codigo() {
        return $this->ace_int_codigo;
    }

    public function setAce_int_codigo($ace_int_codigo) {
        $this->ace_int_codigo = $ace_int_codigo;
    }

    public function getAce_dti_criacao() {
        return $this->ace_dti_criacao;
    }

    public function setAce_dti_criacao($ace_dti_criacao) {
        $this->ace_dti_criacao = $ace_dti_criacao;
    }

    public function getAce_dti_criacao_format() {
        return $this->ace_dti_criacao_format;
    }

    public function setAce_dti_criacao_format($ace_dti_criacao_format) {
        $this->ace_dti_criacao_format = $ace_dti_criacao_format;
    }

    public function getAce_int_usuario() {
        return $this->ace_int_usuario;
    }

    public function setAce_int_usuario($ace_int_usuario) {
        $this->ace_int_usuario = $ace_int_usuario;
    }

    public function getAce_int_usuario_nome() {
        return $this->ace_int_usuario_nome;
    }

    public function setAce_int_usuario_nome($ace_int_usuario_nome) {
        $this->ace_int_usuario_nome = $ace_int_usuario_nome;
    }

    public function getAce_var_ip() {
        return $this->ace_var_ip;
    }

    public function setAce_var_ip($ace_var_ip) {
        $this->ace_var_ip = $ace_var_ip;
    }

    public function getAce_var_sessao() {
        return $this->ace_var_sessao;
    }

    public function setAce_var_sessao($ace_var_sessao) {
        $this->ace_var_sessao = $ace_var_sessao;
    }

    public function getAce_var_server() {
        return $this->ace_var_server;
    }

    public function setAce_var_server($ace_var_server) {
        $this->ace_var_server = $ace_var_server;
    }

    public function getAce_var_url() {
        return $this->ace_var_url;
    }

    public function setAce_var_url($ace_var_url) {
        $this->ace_var_url = $ace_var_url;
    }

    public function getAce_txt_request() {
        return $this->ace_txt_request;
    }

    public function setAce_txt_request($ace_txt_request) {
        $this->ace_txt_request = $ace_txt_request;
    }

    public function getAce_var_agent() {
        return $this->ace_var_agent;
    }

    public function setAce_var_agent($ace_var_agent) {
        $this->ace_var_agent = $ace_var_agent;
    }

    public function getAce_txt_json() {
        return $this->ace_txt_json;
    }

    public function setAce_txt_json($ace_txt_json) {
        $this->ace_txt_json = $ace_txt_json;
    }

    public function getAce_int_lead() {
        return $this->ace_int_lead;
    }

    public function setAce_int_lead($ace_int_lead) {
        $this->ace_int_lead = $ace_int_lead;
    }

    public function getArray() {
        $array = array();
        $array["ace_int_codigo"] = $this->ace_int_codigo;
        $array["ace_dti_criacao"] = $this->ace_dti_criacao;
        $array["ace_dti_criacao_format"] = $this->ace_dti_criacao_format;
        $array["ace_int_usuario"] = $this->ace_int_usuario;
        $array["ace_var_ip"] = $this->ace_var_ip;
        $array["ace_var_sessao"] = $this->ace_var_sessao;
        $array["ace_var_server"] = $this->ace_var_server;
        $array["ace_var_url"] = $this->ace_var_url;
        $array["ace_txt_request"] = $this->ace_txt_request;
        $array["ace_var_agent"] = $this->ace_var_agent;
        $array["ace_txt_json"] = $this->ace_txt_json;
        $array["ace_int_lead"] = $this->ace_int_lead;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->ace_int_codigo;
        $array["Data e Hora"] = $this->ace_dti_criacao;
        $array["Data e Hora"] = $this->ace_dti_criacao_format;
        $array["Usuário"] = $this->ace_int_usuario;
        $array["IP"] = $this->ace_var_ip;
        $array["Sessão"] = $this->ace_var_sessao;
        $array["Server"] = $this->ace_var_server;
        $array["URL"] = $this->ace_var_url;
        $array["Request"] = $this->ace_txt_request;
        $array["Agent"] = $this->ace_var_agent;
        $array["Json"] = $this->ace_txt_json;
        $array["Lead"] = $this->ace_int_lead;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->ace_int_codigo . ' - ' . $this->ace_var_ip : '' . $this->ace_var_ip;
    }
}
