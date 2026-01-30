<?php

class GF {

    /**
     * Converter Chartset de UTF-8 para ISO ou de ISO para UTF-8
     *
     * @param string $texto
     * @param bool $utf8
     * @return string
     */
    public static function converter($texto, $utf8 = TRUE) {
        $retorno = null;
        if (!empty($texto)) {
            if ($utf8) {
                $retorno = iconv("ISO-8859-1", "UTF-8", $texto);
            } else {
                $retorno = iconv("UTF-8", "ISO-8859-1", $texto);
            }
        }
        return $retorno;
    }

    /**
     * Converter Chartset de UTF-8 para ISO ou de ISO para UTF-8 para insersão no banco
     *
     * @param string $texto
     * @param bool $utf8
     * @return string
     */
    public static function converterCrud($texto, $utf8 = TRUE) {
        return gConverter(stripslashes(trim($texto)), $utf8);
    }

    /**
     * Converter Chartset de UTF-8 para ISO ou de ISO para UTF-8 para form
     *
     * @param string $texto
     * @param bool $utf8
     * @return string
     */
    public static function converterForm($texto, $utf8 = TRUE) {
        return converter(htmlentities($texto), $utf8);
    }

    /**
     * Retirar as formatações para exibir no grid
     *
     * @param string $texto
     * @return string
     */
    public static function formatarTexto($texto) {
        $retorno = $texto;
        $retorno = str_replace("\n", "", $retorno);
        $retorno = str_replace("\t", "", $retorno);
        $retorno = str_replace("&ldquo;", '"', $retorno);
        $retorno = str_replace("&rdquo;", '"', $retorno);
        $retorno = str_replace("<p>&nbsp;</p>", '', $retorno);
        $retorno = str_replace("&nbsp;", " ", $retorno);
        $retorno = strip_tags($retorno);
        $retorno = trim($retorno);
        $retorno = addslashes($retorno);

        return $retorno;
    }

    /**
     * Função para importar as classes DAO
     *
     * @param array classes
     */
    public static function import($arrClass) {
        foreach ($arrClass as $class) {
            require_once(ROOT_SYS_CLASS . $class . 'Dao.php');
        }
    }

    /**
     * Criar PermaLink
     *
     * @param String $str
     * @return String
     */
    public static function criarPermalink($str) {
        $clean = GF::retirarAcento($str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", '-', $clean);
        return $clean;
    }

    /**
     * Função para retirar acentos de uma string
     * 
     * @param String $texto
     * @return String
     */
    public static function retirarAcento($texto) {
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ';
        $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
        $texto = utf8_decode($texto);
        $texto = strtr($texto, utf8_decode($a), $b);
        return utf8_encode($texto);
    }

    /**
     * Formata a data para o formato do brasil ou inglês
     *
     * @param <type> $data
     * @param <type> $brasil
     */
    public static function formatarData($string, $brasil = true) {
        $retorno = $string;
        if ($brasil) {
            $dataHora = explode(" ", $string);
            $data = explode("/", $dataHora[0]);
            if (count($data) > 1) {
                if (isset($dataHora[1]) && strlen($dataHora[1]) > 1)
                    $retorno = $data[2] . "-" . $data[1] . "-" . $data[0] . ' ' . $dataHora[1];
                else
                    $retorno = $data[2] . "-" . $data[1] . "-" . $data[0];
            }
        } else {
            $dataHora = explode(" ", $string);
            $data = explode("-", $dataHora[0]);
            if (count($data) > 1) {
                if (isset($dataHora[1]) && strlen($dataHora[1]) > 1)
                    $retorno = $data[2] . "/" . $data[1] . "/" . $data[0] . ' ' . $dataHora[1];
                else
                    $retorno = $data[2] . "/" . $data[1] . "/" . $data[0];
            }
        }
        return $retorno;
    }

    /**
     * Formata a data para o formato do brasil ou inglês
     *
     * @param <type> $data
     * @param <type> $brasil
     */
    public static function truncarData($string, $brasil = true) {
        $retorno = $string;
        if ($brasil) {
            $dataHora = explode(" ", $string);
            $data = explode("/", $dataHora[0]);
            if (count($data) > 1) {
                if (strlen($dataHora[1]) > 1)
                    $retorno = $data[2] . "-" . $data[1] . "-" . $data[0];
                else
                    $retorno = $data[2] . "-" . $data[1] . "-" . $data[0];
            }
        } else {
            $dataHora = explode(" ", $string);
            $data = explode("-", $dataHora[0]);
            if (count($data) > 1) {
                if (strlen($dataHora[1]) > 1)
                    $retorno = $data[2] . "/" . $data[1] . "/" . $data[0];
                else
                    $retorno = $data[2] . "/" . $data[1] . "/" . $data[0];
            }
        }
        return $retorno;
    }

    /**
     * Formata a data para o formato de usuário
     *
     * @param <type> $data
     */
    public static function formatarDataUsuario($string) {
        $retorno = $string;
        $dataHora = explode(" ", $string);
        $data = explode("-", $dataHora[0]);
        $hora = explode(":", $dataHora[1]);
        if (count($data) > 1) {
            $retorno = $data[2] . "/" . $data[1] . "/" . $data[0] . ' às ' . $hora[0] . 'h' . $hora[1] . 'min';
            if ($hora[2] > 0)
                $retorno .= $hora[2] . 's';
        }
        return $retorno;
    }

    /**
     * Encurtar uma URL usando o migre.me
     * 
     * @param string $url
     * @return string 
     */
    public static function encurtarUrl($longUrl) {

        // initialize the cURL connection
        $ch = curl_init(sprintf('https://www.googleapis.com/urlshortener/v1/url?key=%s', GOOGLE_API_KEY));

        // tell cURL to return the data rather than outputting it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // create the data to be encoded into JSON
        $requestData = array(
            'longUrl' => $longUrl
        );

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));

        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        return $result['id'];
    }

    /**
     * Função para include de arquivos
     *
     * @param array arquivos
     */
    public static function includes($arrArquivos) {
        foreach ($arrArquivos as $arquivo) {
            include_once(ROOT_SYS . $arquivo);
        }
    }

    /**
     * Função para require de arquivos
     *
     * @param array arquivos
     */
    public static function requires($arrArquivos) {
        foreach ($arrArquivos as $arquivo) {
            require_once(ROOT_SYS . $arquivo);
        }
    }

    /**
     * Carrega arquivos php, js e css de uma biblioteca no Genesis
     *
     * @param array $bibliotecas
     */
    public static function carregarLib($bibliotecas) {
        $allLib = getLibs();
        foreach ($bibliotecas as $bib) {
            if ($bib != "") {
                $arquivos = $allLib[$bib];
                foreach ($arquivos as $arq) {
                    $tipo = explode("/", $arq);
                    switch ($tipo[0]) {
                        case "css":
                            echo '<link href="' . URL_STATIC_GN . $arq . '" rel="stylesheet" type="text/css" />';
                            break;
                        case "js":
                            echo '<script src="' . URL_STATIC_GN . $arq . '" type="text/javascript" charset="utf-8"></script>';
                            break;
                        default:
                            require_once($arq);
                            break;
                    }
                }
            }
        }
    }

    /**
     * Carrega uma folha de estilo de CSS
     *
     * @param String $css
     */
    public static function carregarCss($css) {
        return '<link href="' . $css . '" rel="stylesheet" type="text/css" />';
    }

    /**
     * Carrega um script JavaScript
     *
     * @param String $js
     */
    public static function carregarJs($js) {
        return '<script src="' . $js . '" type="text/javascript" charset="utf-8"></script>';
    }

    /**
     * Executa a função stripslashes em todos os elementos do array
     *
     * @param array $array
     * @return array
     */
    public static function unstrip_array($array) {
        foreach ($array as &$val) {
            if (is_array($val)) {
                $val = GF::unstrip_array($val);
            } else {
                $val = stripslashes($val);
            }
        }
        return $array;
    }

    /**
     *
     * @param String $str
     * @param int $len
     * @param String $etc
     * @return String
     */
    public static function truncate($str, $len, $etc = '') {
        $end = array(' ', '.', ',', ';', ':', '!', '?');
        if (strlen($str) <= $len)
            return $str;
        $str = strip_tags($str);
        if (!in_array($str[$len - 1], $end) && !in_array($str[$len], $end))
            while (--$len && !in_array($str[$len - 1], $end)
            );
        return rtrim(substr($str, 0, $len)) . $etc;
    }

    /**
     * Retira maskara de uma string
     *
     * @param string $texto
     * @return string
     */
    public static function retirarEspeciais($texto) {
        return str_replace(array(".", "-", "/", "(", ")", "*", "@", "!", "$", "%", "&", "_", "+", "=", ";", ",", "|", "\\", "[", "]", "{", "}"), "", $texto);
    }

    /**
     * Retira maskara de uma string
     *
     * @param string $texto
     * @return string
     */
    public static function retirarMask($texto) {
        return str_replace(array(".", "-", "/"), "", $texto);
    }

    /**
     * Retira maskara de uma data
     *
     * @param string $texto
     * @return string
     */
    public static function retirarMaskData($texto) {
        return str_replace(array(" ", "-", ":", "/"), "", $texto);
    }

    /**
     * Formata o valor retirando o ponto e colocando vírgula ou vice versa
     * 
     * @param string $valor
     * @param string $tipo Default: '' -> ($tipo == 'V') ? str_replace(',', '.', $valor) : str_replace('.', ',', $valor);
     * @return string
     */
    public static function trocarPonto($valor, $tipo = '') {
        return ($tipo == 'V') ? str_replace(',', '.', $valor) : str_replace('.', ',', $valor);
    }

    /**
     * Formatar quebra de linha do texto transformando em html ou não
     * 
     * @param string $valor
     * @param bool $html Default: False -> ($html) ? str_replace('\n', 'br/>', $valor) : str_replace('br/>', '\n', $valor);
     * @return string
     */
    public static function quebraLinhaHtml($valor, $html = true) {
        return ($html) ? str_replace('\n', '<br/>', $valor) : str_replace('<br/>', '&#13;&#10;', $valor);
    }

    public static function formatarDouble($valor) {
        $ret = self::trocarPonto($valor, 'V');
        return number_format($ret, 2);
    }

    public static function formatarNumero($valor) {
        return number_format($valor, 0, ',', '.');
    }

    public static function formatarValor($valor) {
        $ret = self::trocarPonto($valor, 'V');
        return number_format($ret, 2, ',', '.');
    }

    public static function formatarMoney($valor) {
        $ret = self::trocarPonto($valor, 'V');
        return 'R$ ' . number_format($ret, 2, ',', '.');
    }

    /**
     * 
     * $prefix (string) - Prefixo para a data por extenso
     * $time (string)  - Se o fuso horário do seu servidor é diferente do seu, basta ajustar adicionando ou diminuindo horas. Ex.: "- 3 hours" ou "+ 1 hours"
     * @return (string): Ex.: Quinta-feira, 01 de Abril de 2011
     * @exemple: echo dataExtenso('Aracaju (SE) - ', '- 3 hours');
     */
    public static function dataExtenso($prefix = '', $time = 'now') {
        $hoje = strtotime($time);
        $i = getdate($hoje); // Consegue informações data/hora
        $data = $i["mday"]; //Representação numérica do dia do mês (1 a 31)
        $dia = $i["wday"]; // representação numérica do dia da semana com 0 (para Domingo) a 6 (para Sabado)
        $mes = $i["mon"]; // Representação numérica de um mês (1 a 12)
        $ano = $i["year"]; // Ano com 4 digitos, lógico, né?
        $data = str_pad($data, 2, "0", STR_PAD_LEFT); // só para colocar um zerinho à esquerda caso seja de 1 à 9, sacou?
        $nomedia = array("Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado");
        $nomemes = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
        return "$prefix{$nomedia[$dia]}, $data de {$nomemes[$mes]} de $ano";
    }

    /**
     * Servidor exibir erros
     */
    public static function showErrors() {
        ini_set('display_errors', 'On');
    }

    /**
     * Converte string em maiúsculas inclusive as acentuações
     *
     * @param string $str
     * @return string
     */
    public static function upper($str) {
        return strtoupper(strtr($str, "áéíóúâêôãõàèìòùç", "ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ"));
    }

    /**
     * Converte string em minúsculas inclusive as acentuações
     *
     * @param string $str
     * @return string 
     */
    public static function lower($str) {
        return strtolower(strtr($str, "ÁÉÍÓÚÂÊÔÃÕÀÈÌÒÙÇ", "áéíóúâêôãõàèìòùç"));
    }

    public static function destacarPalavra($texto, $palavra, $exibirEncontrado = false, $truncate = false) {
        $retorno = '';
        $pos = stripos(strip_tags($texto), $palavra);
        if (($truncate) && ($pos > 50))
            $texto = '...' . substr($texto, $pos - 50);
        if ($truncate)
            $texto = GF::truncate($texto, 250, '...');
        if ($exibirEncontrado) {
            if ($pos)
                $retorno = str_ireplace($palavra, '<span class="pesquisado">' . $palavra . '</span>', $texto);
            else
                $retorno = '';
        } else {
            $retorno = str_ireplace($palavra, '<span class="pesquisado">' . $palavra . '</span>', $texto);
        }
        return $retorno;
    }

    public static function retirarTag($texto, $tag1, $tag2 = false) {
        $texto = str_replace($tag1, "", $texto);
        if ($tag2)
            $texto = str_replace($tag2, "", $texto);
        return $texto;
    }

    public static function salvarLog($tipo, $msg) {
        $arq = ROOT_LOGS . "app/" . date("Y-m-d") . ".txt";
        $fp = fopen($arq, "a");
        fwrite($fp, date("d/m/Y H:i:s") . " - " . $tipo . " - " . $msg . "\r\n");
        fclose($fp);
    }

    public static function seExiste($url) {
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_NOBODY, true); // não baixa o corpo
//        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // timeout total
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // timeout de conexão
//        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // seguir redirecionamentos
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // evitar erro SSL
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        curl_exec($ch);
//        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
//        curl_close($ch);
//
//        return ($httpCode == 200);
        return true;
    }
}

?>