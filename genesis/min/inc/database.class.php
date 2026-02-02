<?php

class GDbMysql {

    protected $link;
    protected $stmt;
    public $res = array();

    /**
     * Cria um conexão com o banco mysql
     * 
     */
    function __construct() {
        // carrega a conexao com mysql
        $this->connect();
        //$this->execute("SET NAMES " . MYSQL_CHARSET . " COLLATE " . MYSQL_COLLATION . ";", null, false);
        if (SYS_DB_LOG) {
            $_usuario = getUsuarioSessao();
            if ($_usuario) {
                $this->execute("CALL sp_set_usuario(?,?);", array("is", $_usuario->getUsu_int_codigo(), $_usuario->getUsu_var_nome()), false);
            }
        }
    }

    /**
     * Abre a conexão com o mysql
     *
     */
    private function connect() {
        if (!$this->link) {
            $this->link = mysqli_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_BASE);
            if (!$this->link) {
                throw new GDbException(mysqli_error($this->link), mysqli_errno($this->link));
            }
        }
        mysqli_set_charset($this->link, MYSQL_CHARSET);
    }

    /**
     * Cria um array de referencia para o array de parametros
     * Função criada para solucionar o problema da versão 5.3 do PHP
     *
     * @param Array $arr
     * @return Array
     */
    public function refValues($arr) {
        if (strnatcmp(phpversion(), '5.3') >= 0) { //Reference is required for PHP 5.3+
            $refs = array();
            foreach ($arr as $key => $value)
                $refs[$key] = &$arr[$key];
            return $refs;
        }
        return $arr;
    }

    /**
     * Executa uma query no mysql
     *
     * @param String $query Sql para executar Ex: SELECT * FROM tabela
     * @param Array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @param boolean $consulta[optional] Se for uma consulta
     */
    public function execute($query, $param = NULL, $consulta = TRUE) {
        $paramOriginal = $param;
        try {
            $this->stmt = mysqli_prepare($this->link, $query);
            if ($param != NULL) {
                $indice = $param[0];
                array_shift($param);
                foreach ($param as $value) {
                    if (is_null($value))
                        $trimParam[] = $value;
                    else
                        $trimParam[] = trim($value);
                }
                $arr = array_merge(array($this->stmt, $indice), $trimParam);
                $ret = call_user_func_array('mysqli_stmt_bind_param', $this->refValues($arr));
            }
            if (mysqli_stmt_execute($this->stmt)) {
                if ($consulta) {
                    $nof = mysqli_num_fields(mysqli_stmt_result_metadata($this->stmt));
                    $fieldMeta = mysqli_fetch_fields(mysqli_stmt_result_metadata($this->stmt));
                    $fields = array();
                    for ($i = 0; $i < $nof; $i++) {
                        $fields[$i] = $fieldMeta[$i]->name;
                    }
                    $arg = array($this->stmt);
                    for ($i = 0; $i < $nof; $i++) {
                        $campo = $fields[$i];
                        $arg[$i + 1] = &$this->res[$campo];
                        $this->res[$i] = &$this->res[$campo];
                    }
                    call_user_func_array('mysqli_stmt_bind_result', $arg);
                    mysqli_stmt_store_result($this->stmt);
                }
            } else {
                throw new GDbException(mysqli_stmt_error($this->stmt), mysqli_stmt_errno($this->stmt), $query, $paramOriginal);
            }
        } catch (Exception $e) {
            throw new GDbException($e->getMessage(), $e->getCode(), $query, $paramOriginal);
        }
    }

    /**
     * Executa uma query no mysql
     *
     * @param String $query Sql para executar Ex: SELECT * FROM tabela
     * @param Array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @param boolean $consulta[optional] Se for uma consulta
     */
    public function executePrint($query, $param = NULL, $consulta = TRUE) {
        echo '<pre>';
        var_dump("query: ", $query);
        var_dump("param: ", $param);
        var_dump("consulta: ", $consulta);
        echo '</pre>';
    }

    /**
     * Executa uma consulta com a query passada e retorna um array com os valores
     *
     * @param String $query Consulta sql Ex: SELECT * FROM tabela
     * @param array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @param array $pos[optional] Posição dos valores de retorno Ex: array(0, 1)
     * @return array
     */
    public function executeCombo($query, $param = false, $pos = array(0, 1)) {
        $array = array();
        try {
            if ($param)
                $this->execute($query, $param);
            else
                $this->execute($query);
            while ($this->fetch()) {
                $array[$this->res[$pos[0]]] = $this->res[$pos[1]];
            }
            $this->close();
        } catch (GDbException $e) {
            echo $e->getError();
        }
        return $array;
    }

    /**
     * Executa uma consulta com a query passada e retorna uma variável com o valor
     *
     * @param String $query Consulta sql Ex: SELECT * FROM tabela
     * @param array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @return array
     */
    public function executeValue($query, $param = false) {
        $value = null;
        try {
            if ($param)
                $this->execute($query, $param);
            else
                $this->execute($query);
            if ($this->fetch()) {
                $value = $this->res[0];
            }
            $this->close();
        } catch (GDbException $e) {
            echo $e->getError();
        }
        return $value;
    }

    /**
     * Executa uma consulta com a query passada e retorna um array com os valores
     *
     * @param String $query Consulta sql Ex: SELECT * FROM tabela
     * @param array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @return array
     */
    public function executeValues($query, $param = false, $colunas = array(0, 1)) {
        $array = array();
        try {
            if ($param)
                $this->execute($query, $param);
            else
                $this->execute($query);
            if ($this->fetch()) {
                foreach ($colunas as $coluna) {
                    $array[] = $this->res[$coluna];
                }
            }
            $this->close();
        } catch (GDbException $e) {
            echo $e->getError();
        }
        return $array;
    }

    /**
     * Executa uma consulta com a query passada e retorna um array com os valores
     *
     * @param String $query Consulta sql Ex: SELECT * FROM tabela
     * @param array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @return array
     */
    public function executeArray($query, $param = false, $pos = array(0, 1)) {
        $array = array();
        try {
            if ($param)
                $this->execute($query, $param);
            else
                $this->execute($query);
            while ($this->fetch()) {
                $array[] = $this->res[$pos[0]];
            }
            $this->close();
        } catch (GDbException $e) {
            echo $e->getError();
        }
        return $array;
    }

    /**
     * Executa uma consulta com a query passada e retorna um array com os valores
     *
     * @param String $query Consulta sql Ex: SELECT * FROM tabela
     * @param array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @return array
     */
    public function executeArrayChaveValor($query, $param = false) {
        $array = array();
        try {
            if ($param)
                $this->execute($query, $param);
            else
                $this->execute($query);
            while ($this->fetch()) {
                $array[$this->res[0]] = $this->res[1];
            }
            $this->close();
        } catch (GDbException $e) {
            echo $e->getError();
        }
        return $array;
    }

    /**
     * Executa uma consulta com a query passada e retorna um array com os valores
     *
     * @param String $query Consulta sql Ex: SELECT * FROM tabela
     * @param array $param[optional] Parametros mysqli_stmt_bind_param Ex: array('i', 10)
     * @return array
     */
    public function executeListArray($query, $param = false, $colunas = array(0, 1)) {
        $array = array();
        try {
            if ($param)
                $this->execute($query, $param);
            else
                $this->execute($query);
            while ($this->fetch()) {
                $item = array();
                foreach ($colunas as $coluna) {
                    $item[$coluna] = $this->res[$coluna];
                }
                $array[] = $item;
            }
            $this->close();
        } catch (GDbException $e) {
            echo $e->getError();
        }
        return $array;
    }

    public function fetch() {
        return mysqli_stmt_fetch($this->stmt);
    }

    public function free_result() {
        return mysqli_stmt_free_result($this->stmt);
    }

    public function fieldCount() {
        return mysqli_stmt_field_count($this->stmt);
    }

    public function numRows() {
        return mysqli_stmt_num_rows($this->stmt);
    }

    public function affectedRows() {
        return mysqli_stmt_affected_rows($this->stmt);
    }

    public function insertId() {
        return mysqli_stmt_insert_id($this->stmt);
    }

    public function close() {
        return mysqli_stmt_close($this->stmt);
    }

    public function autoCommit($mode = false) {
        mysqli_autocommit($this->link, $mode);
    }

    public function commit() {
        return mysqli_commit($this->link);
    }

    public function rollback() {
        return mysqli_rollback($this->link);
    }
}

?>
