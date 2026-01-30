<?php

class Permissao {

    private $pem_var_codigo;
    private $pem_var_descricao;
    /* @var $vinculo Permissao */
    private $vinculo;

    public function getPem_var_codigo() {
        return $this->pem_var_codigo;
    }

    public function setPem_var_codigo($pem_var_codigo) {
        $pem_var_codigo = (is_null($pem_var_codigo)) ? null : strtoupper($pem_var_codigo);
        $this->pem_var_codigo = trim($pem_var_codigo);
    }

    public function getPem_var_descricao() {
        return $this->pem_var_descricao;
    }

    public function setPem_var_descricao($pem_var_descricao) {
        $this->pem_var_descricao = trim($pem_var_descricao);
    }

    /** @return Permissao */
    public function getVinculo() {
        return $this->vinculo;
    }

    /** @param Permissao $vinculo */
    public function setVinculo($vinculo) {
        $this->vinculo = $vinculo;
    }

    public function getArray() {
        $array = array();
        $array["pem_var_codigo"] = $this->pem_var_codigo;
        $array["pem_var_descricao"] = $this->pem_var_descricao;
        $array["pem_var_vinculo"] = $this->vinculo->getPem_var_codigo();
        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->pem_var_codigo;
        $array["Descrição"] = $this->pem_var_descricao;
        $array["Vínculo"] = $this->vinculo->getPem_var_codigo();
        return $array;
    }

    public function getDescricao($codigo = true) {
        return ($codigo) ? $this->pem_var_codigo . ' - ' : '' . $this->pem_var_descricao;
    }

}

?>