<?php

class Mensagem {

    private $men_int_codigo;
    private $men_var_titulo;
    private $men_txt_texto;
    /* @var $remetente Usuario */
    private $remetente;
    private $men_dti_envio;
    private $men_dti_envio_format;
    private $tempo;
    private $destinatarios;

    public function getMen_int_codigo() {
        return $this->men_int_codigo;
    }

    public function setMen_int_codigo($men_int_codigo) {
        $this->men_int_codigo = $men_int_codigo;
    }

    public function getMen_var_titulo() {
        return $this->men_var_titulo;
    }

    public function setMen_var_titulo($men_var_titulo) {
        $this->men_var_titulo = $men_var_titulo;
    }

    public function getMen_txt_texto() {
        return $this->men_txt_texto;
    }

    public function setMen_txt_texto($men_txt_texto) {
        $this->men_txt_texto = $men_txt_texto;
    }

    public function getRemetente() {
        return $this->remetente;
    }

    public function setRemetente($remetente) {
        $this->remetente = $remetente;
    }

    public function getMen_dti_envio() {
        return $this->men_dti_envio;
    }

    public function setMen_dti_envio($men_dti_envio) {
        $this->men_dti_envio = $men_dti_envio;
    }

    public function getMen_dti_envio_format() {
        return $this->men_dti_envio_format;
    }

    public function setMen_dti_envio_format($men_dti_envio_format) {
        $this->men_dti_envio_format = $men_dti_envio_format;
    }

    public function getTempo() {
        return $this->tempo;
    }

    public function setTempo($tempo) {
        $this->tempo = $tempo;
    }

    public function getDestinatarios() {
        return $this->destinatarios;
    }

    public function setDestinatarios($destinatarios) {
        $this->destinatarios = $destinatarios;
    }

    public function getArray() {
        $array = array();
        $array["men_int_codigo"] = $this->men_int_codigo;
        $array["men_var_titulo"] = $this->men_var_titulo;
        $array["men_txt_texto"] = $this->men_txt_texto;
        $array["men_int_remetente"] = $this->remetente->getUsu_int_codigo();
        $array["Remetente"] = $this->remetente->getDescricao();
        $array["men_dti_envio"] = $this->men_dti_envio;
        $array["men_dti_envio_format"] = $this->men_dti_envio_format;
        $array["tempo"] = $this->tempo;

        return $array;
    }

    public function getExport() {
        $array = array();
        $array["Código"] = $this->men_int_codigo;
        $array["Título"] = $this->men_var_titulo;
        $array["Texto"] = $this->men_txt_texto;
        $array["Remetente"] = $this->remetente->getDescricao();
        $array["Data de Envio"] = $this->men_dti_envio_format;

        return $array;
    }

    public function getDescricao($codigo = false) {
        return ($codigo) ? $this->men_int_codigo . ' - ' . $this->men_var_titulo : '' . $this->men_var_titulo;
    }

}
