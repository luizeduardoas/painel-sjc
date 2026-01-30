<?php

require_once("PHPMailer-master/PHPMailerAutoload.php");

class GEmail {

    private $phpMailer;
    private $assunto;
    private $mensagem;
    private $destinatario = SYS_CONTATO_SMTP;
    private $copia;
    private $copiaOculta;

    function __construct() {
        if (SYS_MAIL_SMTP == 'V') {
            $mail = new PHPMailer;
            $mail->setLanguage('br');
            $mail->CharSet = 'UTF-8';
            $mail->Debugoutput = 'html';
            //$mail->SMTPDebug = 3; // Habilita a saída do tipo "verbose"

            $mail->isSMTP();
            $mail->Host = SYS_HOST_SMTP;
            $mail->SMTPAuth = true;
            $mail->Username = SYS_USUARIO_SMTP;
            $mail->Password = SYS_SENHA_SMTP;
            $mail->SMTPSecure = SYS_AUTENTICACAO_SMTP;
            $mail->Port = SYS_PORTA_SMTP;

            $mail->From = SYS_CONTATO_SMTP; // Endereço previamente verificado no painel do SMTP
            $mail->FromName = SYS_NOME_EMAIL; // Nome no remetente
            $this->setPhpMailer($mail);
        } else {
            
        }
    }

    function enviar() {
        $return = array();
        if (SYS_MAIL_SMTP == 'V') {
            $mail = $this->getPhpMailer();
            $destinatario = explode("<", $this->getDestinatario());
            if (count($destinatario) == 1)
                $mail->addAddress($destinatario[0]);
            else
                $mail->addAddress(str_replace(">", "", $destinatario[1]), $destinatario[0]);
            $mail->addReplyTo(SYS_CONTATO_SMTP);
            if (!is_null($this->getCopia()))
                $mail->addCC($this->getCopia());
            if (!is_null($this->getCopiaOculta()))
                $mail->addBCC($this->getCopiaOculta());
            $mail->isHTML(true);
            $mail->Subject = $this->getAssunto();
            $mail->Body = $this->getMensagem();
            $mail->AltBody = strip_tags($this->getMensagem());
            if (!$mail->send()) {
                $return["status"] = false;
                $return["msg"] = 'A mensagem não pode ser enviada. Mensagem de erro: ' . $mail->ErrorInfo;
            } else {
                $return["status"] = true;
                $return["msg"] = 'Mensagem enviada com sucesso';
            }
        } else {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
//            $headers .= "Content-Transfer-Encoding: base64\n";
//            $headers .= "From: " . SYS_CONTATO . "\n";
//            $headers .= "Return-Path: " . SYS_CONTATO . "\n";
//            $headers .= ( is_null($this->getCopia())) ? "" : "Cc: " . GF::converter($this->getCopia(), false) . "\n";
//            $headers .= ( is_null($this->getCopiaOculta())) ? "" : "Cco: " . GF::converter($this->getCopiaOculta(), false) . "\n";
            if (!mail($this->getDestinatario(), $this->getAssunto(), $this->getMensagem(), $headers)) {
                $return["status"] = false;
                $return["msg"] = 'A mensagem não pode ser enviada.';
            } else {
                $return["status"] = true;
                $return["msg"] = 'Mensagem enviada com sucesso';
            }
        }
        return $return;
    }

    public function getPhpMailer() {
        return $this->phpMailer;
    }

    public function setPhpMailer($phpMailer) {
        $this->phpMailer = $phpMailer;
    }

    public function getAssunto() {
        return $this->assunto;
    }

    public function setAssunto($assunto) {
        $this->assunto = $assunto;
    }

    public function getMensagem() {
        return $this->mensagem;
    }

    public function setMensagem($mensagem) {
        $this->mensagem = $mensagem;
    }

    public function getDestinatario() {
        return (SYS_SANDBOX_SMTP == 'V') ? SYS_CONTATO_SMTP : $this->destinatario;
    }

    public function setDestinatario($destinatario) {
        $this->destinatario = $destinatario;
    }

    public function getCopia() {
        return $this->copia;
    }

    public function setCopia($copia) {
        $this->copia = $copia;
    }

    public function getCopiaOculta() {
        return $this->copiaOculta;
    }

    public function setCopiaOculta($copiaOculta) {
        $this->copiaOculta = $copiaOculta;
    }
}

?>
