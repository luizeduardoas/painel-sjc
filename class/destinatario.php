<?php

class Destinatario {

    private $des_int_codigo;
    /* @var $mensagem Mensagem */
    private $mensagem;
    private $des_int_destinatario;
    private $des_cha_status;
    private $des_cha_status_format;

    public function getDes_int_codigo() {
        return $this->des_int_codigo;
    }

    public function setDes_int_codigo($des_int_codigo) {
        $this->des_int_codigo = $des_int_codigo;
    }

    /** @return Mensagem */
    public function getMensagem() {
        return $this->mensagem;
    }

    /** @param Mensagem $mensagem */
    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }

    public function getDes_int_destinatario() {
        return $this->des_int_destinatario;
    }

    public function setDes_int_destinatario($des_int_destinatario) {
        $this->des_int_destinatario = $des_int_destinatario;
    }

    public function getDes_cha_status() {
        return $this->des_cha_status;
    }

    public function setDes_cha_status($des_cha_status) {
        $this->des_cha_status = $des_cha_status;
    }

    public function getDes_cha_status_format() {
        return $this->des_cha_status_format;
    }

    public function setDes_cha_status_format($des_cha_status_format) {
        $this->des_cha_status_format = $des_cha_status_format;
    }

    public function getArray() {
        $array = array();
        $array["des_int_codigo"] = $this->des_int_codigo;
        $array["men_int_codigo"] = $this->mensagem->getMen_int_codigo();
        $array["Mensagem"] = $this->mensagem->getDescricao();
        $array["des_int_destinatario"] = $this->des_int_destinatario;
        $array["des_cha_status"] = $this->des_cha_status;
        $array["des_cha_status_format"] = $this->des_cha_status_format;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->des_int_codigo;
        $array["Mensagem"] = $this->mensagem->getDescricao();
        $array["Destinatário"] = $this->des_int_destinatario;
        $array["Status"] = $this->des_cha_status_format;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->des_int_codigo . ' - ' . $this->mensagem->getMen_int_codigo() : '' . $this->mensagem->getMen_int_codigo();
    }

}
