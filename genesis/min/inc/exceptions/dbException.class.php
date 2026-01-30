<?php

/**
 * Description of MyException
 *
 * @author salvador_torres
 */
class GDbException extends Exception {

    private $query = '';
    private $param = null;
    private $arrMsg = array("1062" => "Registro duplicado", "1451" => "Registro possui dependência", "1452" => "Dependência não encontrada");

    public function __construct($message, $code, $query = '', $param = null) {
        $this->query = $query;
        $this->param = $param;
        parent::__construct($message, $code);
    }

    public function getError() {
        switch (SERVIDOR) {
            case 'D':
            case 'H':
                return '<div class="__erro"> Tipo: <b>DbException</b> <br>' .
                        'Arquivo: <b>' . $this->file . '</b><br>' .
                        'Linha: <b>' . $this->line . '</b> <br/>' .
                        'Código: <b>' . $this->code . '</b><br>' .
                        'Mensagem: <b><span style="color:red">' . $this->message . '</span></b><br/>' .
                        'Erro: <b><span style="color:blue">' . $this->getMsg($this->code, $this->message) . '</span></b><br/>' .
                        'Caminho: <b>' . $this->getTraceAsString() . '</b><br/>' .
                        'Query: <b>' . $this->query . '</b><br/>' .
                        'Param: <b>' . json_encode($this->param) . '</b></div>';
                break;
//            case 'H':
//                return $this->code . ' - ' . $this->getMsg($this->code, $this->message);
//                break;
            case 'P':
                return $this->getMsg($this->code, $this->message);
                break;
            default :
                return $this->getMsg($this->code, $this->message);
                break;
        }
    }

    public function getErrorLog() {
        return '<div class="__erro"> Tipo: <b>DbException</b> <br>' . 'Arquivo: <b>' . $this->file . '</b><br>' .
                'Linha: <b>' . $this->line . '</b> <br/>' .
                'Código: <b>' . $this->code . '</b><br>' .
                'Mensagem: <b><span style="color:red">' . $this->message . '</span></b><br/>' .
                'Erro: <b><span style="color:blue">' . $this->getMsg($this->code, $this->message) . '</span></b><br/>' .
                'Caminho: <b>' . $this->getTraceAsString() . '</b><br/>' .
                'Query: <b>' . $this->query . '</b><br/>' .
                'Param: <b>' . json_encode($this->param) . '</b></div>';
    }

    private function getMsg($code, $msg) {
        switch ($code) {
            case '':
                $erro = "Ops! Ocorreu um erro inesperado, desculpe o transtorno.";
                break;
            case '1644':
                $erro = $msg;
                break;
            case '1062':case '1451':case '1452':
                $erro = $this->arrMsg[$code];
                break;
            default:
                $erro = $msg;
                break;
        }
        return $erro;
    }
}

?>
