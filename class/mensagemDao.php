<?php

require_once(ROOT_SYS_CLASS . "mensagem.php");

GF::import(array("usuario", "destinatario"));

class MensagemDao {

    private $men_dti_envio;
    private $sql;
    private $sqlCount;

    function __construct() {

        $this->men_dti_envio = gerarDate_format("men_dti_envio", false);
        $this->sql = "SELECT men.men_int_codigo,men.men_var_titulo,men.men_txt_texto,men.men_int_remetente,$this->men_dti_envio, UNIX_TIMESTAMP(NOW()) - UNIX_TIMESTAMP(men_dti_envio) as tempo, (SELECT GROUP_CONCAT(des_int_destinatario) FROM destinatario des WHERE des.men_int_codigo = men.men_int_codigo) as destinatarios FROM mensagem men ";
        $this->sqlCount = "SELECT COUNT(men_int_codigo) FROM mensagem men ";
    }

    public function select($where = false, $param = false, $loadObj = true) {
        $array = array();
        try {
            $mysql = new GDbMysql();
            if ($param)
                $mysql->execute($this->sql . $where, $param);
            else
                $mysql->execute($this->sql . $where);
            while ($mysql->fetch()) {
                $array[] = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $array;
    }

    public function selectCount($where = false, $param = false) {
        $qtd = 0;
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . $where, $param);
            if ($mysql->fetch())
                $qtd = $mysql->res[0];
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $qtd;
    }

    /** @param Mensagem $mensagem */
    public function ifExists($mensagem) {
        $retorno = false;
        $param = array("i", $mensagem->getMen_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sqlCount . " WHERE men.men_int_codigo = ?  ", $param);
            $mysql->fetch();
            $retorno = ($mysql->res[0] > 0) ? true : false;
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            $retorno = false;
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $retorno;
    }

    /** @param Mensagem $mensagem */
    public function selectById($mensagem, $loadObj = true) {
        $param = array("i", $mensagem->getMen_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE men.men_int_codigo = ?  ", $param);
            if ($mysql->fetch()) {
                $mensagem = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $mensagem;
    }

    /** @param Usuario $remetente */
    public function selectByRemetente($remetente, $loadObj = true) {
        $array = array();
        $param = array("i", $remetente->getUsu_int_codigo());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE men.men_int_remetente = ? ", $param);
            while ($mysql->fetch()) {
                $array[] = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $array;
    }

    /** @param Destinatario $destinatario */
    public function selectByDestinatario($destinatario, $loadObj = true) {
        $array = array();
        $param = array("i", $destinatario->getDes_int_destinatario());
        try {
            $mysql = new GDbMysql();
            $mysql->execute($this->sql . " WHERE EXISTS (SELECT 1 FROM destinatario d WHERE men.men_int_codigo = d.men_int_codigo AND d.des_int_destinatario = ? )", $param);
            while ($mysql->fetch()) {
                $array[] = $this->carregarObjeto($mysql, $loadObj);
            }
            $mysql->close();
        } catch (GDbException $e) {
            echo $e->getError();
            salvarEvento('E', $e->getError(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $array;
    }

    /** @param Mensagem $mensagem */
    public function enviar($mensagem) {
        $return = array();
        $param = array("ssis", $mensagem->getMen_var_titulo(), $mensagem->getMen_txt_texto(), $mensagem->getRemetente()->getUsu_int_codigo(), $mensagem->getDestinatarios());
        try {
            $mysql = new GDbMysql();
            $mysql->execute("CALL sp_mensagem_enviar(?,?,?,?);", $param);
            $mysql->fetch();
            $return["status"] = ($mysql->res[0]) ? true : false;
            $return["msg"] = $mysql->res[1];
            $return["insertId"] = $mysql->res[2];
            if ($return["status"]) {
                salvarEvento('S', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            } else {
                salvarEvento('A', $return["msg"], json_encode($param, JSON_UNESCAPED_UNICODE));
            }
            $mysql->close();
        } catch (GDbException $e) {
            $return["status"] = false;
            $return["msg"] = $e->getError();
            $return["exception"] = $e->getMessage();
            salvarEvento('E', $e->getErrorLog(), json_encode($param, JSON_UNESCAPED_UNICODE));
        }
        return $return;
    }

    private function carregarObjeto($mysql, $loadObj = true) {
        $mensagem = new Mensagem();
        $mensagem->setMen_int_codigo($mysql->res["men_int_codigo"]);
        $mensagem->setMen_var_titulo($mysql->res["men_var_titulo"]);
        $mensagem->setMen_txt_texto($mysql->res["men_txt_texto"]);

        $remetente = new Usuario();
        $remetente->setUsu_int_codigo($mysql->res["men_int_remetente"]);
        if ($loadObj) {
            $remetenteDao = new UsuarioDao();
            $remetente = $remetenteDao->selectById($remetente);
        }
        $mensagem->setRemetente($remetente);

        $mensagem->setMen_dti_envio($mysql->res["men_dti_envio"]);
        $mensagem->setMen_dti_envio_format($mysql->res["men_dti_envio_format"]);
        $mensagem->setTempo($mysql->res["tempo"]);
        $mensagem->setDestinatarios($mysql->res["destinatarios"]);

        return $mensagem;
    }

}
