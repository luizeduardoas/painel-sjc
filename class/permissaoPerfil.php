<?php

class PermissaoPerfil {
    /* @var $perfil Perfil */

    private $perfil;
    /* @var $permissao Permissao */
    private $permissao;

    /** @return Perfil */
    public function getPerfil() {
        return $this->perfil;
    }

    /** @param Perfil $perfil */
    public function setPerfil($perfil) {
        $this->perfil = $perfil;
    }

    /** @param Permissao */
    public function getPermissao() {
        return $this->permissao;
    }

    /** @param Permissao $permissao */
    public function setPermissao($permissao) {
        $this->permissao = $permissao;
    }

    public function getArray() {
        $array = array();
        $array["perfil"] = $this->getPerfil()->getDescricao();
        $array["permissao"] = $this->getPermissao()->getDescricao();
        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Perfil"] = $this->getPerfil()->getDescricao();
        $array["Permissão"] = $this->getPermissao()->getDescricao();
        return $array;
    }

    public function getDescricao($codigo = false) {
        $retorno = ($codigo) ? $this->getPerfil()->getPef_int_codigo() . '||' . $this->getPermissao()->getPem_var_codigo() . ' - ' : '' . $this->getPerfil()->getPef_var_descricao();
        return $retorno .= $this->getPermissao()->getPem_var_codigo() . ' - ' . $this->getPerfil()->getPef_var_descricao();
    }

}

?>