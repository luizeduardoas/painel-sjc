<?php

class AvaUsuario {

    private $usu_int_codigo;
    /* @var $escola Escola */
    private $escola;
    private $usu_int_userid;
    private $usu_var_cpf;
    private $usu_var_matricula;
    private $usu_var_nome;
    private $usu_var_cargo;
    private $usu_var_funcao;
    private $usu_var_email;

    public function getUsu_int_codigo() {
        return $this->usu_int_codigo;
    }

    public function setUsu_int_codigo($usu_int_codigo) {
        $this->usu_int_codigo = $usu_int_codigo;
    }

    /** @return Escola */
    public function getEscola() {
        return $this->escola;
    }

    /** @param Escola $escola */
    public function setEscola($escola) {
        $this->escola = $escola;
    }

    public function getUsu_int_userid() {
        return $this->usu_int_userid;
    }

    public function setUsu_int_userid($usu_int_userid) {
        $this->usu_int_userid = $usu_int_userid;
    }

    public function getUsu_var_cpf() {
        return $this->usu_var_cpf;
    }

    public function setUsu_var_cpf($usu_var_cpf) {
        $this->usu_var_cpf = $usu_var_cpf;
    }

    public function getUsu_var_matricula() {
        return $this->usu_var_matricula;
    }

    public function setUsu_var_matricula($usu_var_matricula) {
        $this->usu_var_matricula = $usu_var_matricula;
    }

    public function getUsu_var_nome() {
        return $this->usu_var_nome;
    }

    public function setUsu_var_nome($usu_var_nome) {
        $this->usu_var_nome = $usu_var_nome;
    }

    public function getUsu_var_cargo() {
        return $this->usu_var_cargo;
    }

    public function setUsu_var_cargo($usu_var_cargo) {
        $this->usu_var_cargo = $usu_var_cargo;
    }

    public function getUsu_var_funcao() {
        return $this->usu_var_funcao;
    }

    public function setUsu_var_funcao($usu_var_funcao) {
        $this->usu_var_funcao = $usu_var_funcao;
    }

    public function getUsu_var_email() {
        return $this->usu_var_email;
    }

    public function setUsu_var_email($usu_var_email) {
        $this->usu_var_email = $usu_var_email;
    }

    public function getArray() {
        $array = array();
        $array["usu_int_codigo"] = $this->usu_int_codigo;
        $array["esc_int_codigo"] = $this->escola->getEsc_int_codigo();
        $array["Escola"] = $this->escola->getDescricao();
        $array["usu_int_userid"] = $this->usu_int_userid;
        $array["usu_var_cpf"] = $this->usu_var_cpf;
        $array["usu_var_matricula"] = $this->usu_var_matricula;
        $array["usu_var_nome"] = $this->usu_var_nome;
        $array["usu_var_cargo"] = $this->usu_var_cargo;
        $array["usu_var_funcao"] = $this->usu_var_funcao;
        $array["usu_var_email"] = $this->usu_var_email;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->usu_int_codigo;
        $array["Escola"] = $this->escola->getDescricao();
        $array["Userid"] = $this->usu_int_userid;
        $array["CPF"] = $this->usu_var_cpf;
        $array["Matrícula"] = $this->usu_var_matricula;
        $array["Nome"] = $this->usu_var_nome;
        $array["Cargo"] = $this->usu_var_cargo;
        $array["Função"] = $this->usu_var_funcao;
        $array["Email"] = $this->usu_var_email;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->usu_int_codigo . ' - ' . $this->usu_var_nome : '' . $this->usu_var_nome;
    }
}
