<?php

class GAppException extends Exception {

    public function __construct($message) {
        parent::__construct($message);
    }

    public function getErro() {
        switch (SERVIDOR) {
            case 'D':
            case 'H':
                return '<div class="__erro">Tipo: <b>AppException</b> <br>' .
                        'Erro no arquivo: <b>' . $this->file . '</b><br>' .
                        'Linha: <b>' . $this->line . '</b> <br/>' .
                        'Mensagem: <b>' . $this->message . '</b>' .
                        'Trace: ' . $this->getTraceAsString() . '</div>';
                break;
            case 'P':
                break;
        }
    }

}

?>
