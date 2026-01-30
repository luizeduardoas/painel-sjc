<?php

global $genesis;
if (is_null($genesis))
    require_once("../global.php");

require_once(ROOT_SYS . "vendor/autoload.php");

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use Aws\S3\Transfer;
use Aws\Result;

class AwsS3 {

    protected $s3;

    /**
     * Construtor da classe
     * Faz a conexão com o cliente do S3
     * 
     */
    function __construct() {
        $this->connectS3();
    }

    /**
     * Cria uma conexão com o cliente S3 da Amazon
     *
     */
    private function connectS3() {
        $this->s3 = new S3Client(array(
            'region' => 'us-east-1',
            'version' => 'latest', //'2006-03-01',
            'credentials' => array('key' => SYS_S3_CHAVE, 'secret' => SYS_S3_VALOR)
        ));
    }

    /**
     * Essa função busca as imagens que estão na pasta passada como parâmetro 
     * no diretório de Uploads do sistema e enviam todas para um diretório com
     * o mesmo nome na Amazon S3 dentro do bucket configurado no sistema
     * 
     * @param string $folder Ex: 'usuario'
     * @return array Ex: array('status' => true, 'msg' => 'Arquivos transferidos com sucesso!');
     */
    public function Transferir($folder) {
        $retorno = array();
        try {
            //$source = ROOT_SYS . 'uploads_old/' . $folder;
            $source = ROOT_UPLOAD . $folder;
            $dest = 's3://' . SYS_S3_BUCKET . '/' . $folder;
            $manager = new Transfer($this->s3, $source, $dest);
            $manager->transfer();

            // setar bucket como público
            //$this->TornarPublico();

            $retorno["status"] = true;
            $retorno["msg"] = 'Arquivos transferidos com sucesso!';
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
        }
        return $retorno;
    }

    // TO-DO: Tornar todos os arquivos públicos
    public function TornarPublico() {
        $retorno = array();
        try {
            $this->s3->putBucketAcl(array(
                'Bucket' => SYS_S3_BUCKET,
                'ACL' => 'public-read'
            ));
            $retorno["status"] = true;
            $retorno["msg"] = 'Bucket alterado para público com sucesso!';
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
        }
        return $retorno;
    }

    /**
     * Essa função envia um arquivo para um bucket na Amazon S3 que está 
     * configurado no sistema.
     * Obs.: As pastas são passadas como endereço do arquivo
     * 
     * @param string $nome Ex: 'Imagens/foto.png'
     * @param string $arquivo Ex: '/var/www/html/uploads/usuario/foto.png'
     * @return array Ex: array('status' => true, 'msg' => 'Arquivo enviado com sucesso!', 'url' => 'http://$arquivo');
     */
    public function EnviarLocal($nome, $arquivo) {
        $retorno = array();
        try {
            $ret_put = $this->s3->putObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $nome,
                'SourceFile' => $arquivo
            ));
            if (!is_null($ret_put->get('ObjectURL'))) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Arquivo enviado com sucesso!';
                $retorno["url"] = $ret_put->get('ObjectURL');
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível enviar esse arquivo para o S3';
                $retorno["url"] = null;
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
            $retorno["url"] = null;
        }
        return $retorno;
    }

    /**
     * Essa função envia um arquivo para um bucket na Amazon S3 que está 
     * configurado no sistema.
     * Obs.: As pastas são passadas como endereço do arquivo
     * 
     * @param string $nome Ex: 'Imagens/foto.png'
     * @param string $file Ex: $_FILES['file']
     * @return array Ex: array('status' => true, 'msg' => 'Arquivo enviado com sucesso!', 'url' => 'http://$arquivo');
     */
    public function Enviar($nome, $file) {
        $retorno = array();
        try {
            $ret_put = $this->s3->putObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $nome,
                'Body' => fopen($file['tmp_name'], 'r'),
                'ContentType' => buscarContentType(buscarExtensaoArquivo($nome))
            ));
            if (!is_null($ret_put->get('ObjectURL'))) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Arquivo enviado com sucesso!';
                $retorno["url"] = $ret_put->get('ObjectURL');
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível enviar esse arquivo para o S3';
                $retorno["url"] = null;
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
            $retorno["url"] = null;
        }
        return $retorno;
    }

    /**
     * Essa função busca uma url assinada de um arquivo em um bucket na Amazon S3 
     * 
     * @param string $nome Ex: 'Imagens/foto.png'
     * @param string $expires Default: '+5 minutes'
     * @return string 
     */
    public function BuscarURL($nome, $expires = '+5 minutes') {
        $presignedUrl = null;
        try {
            if ($this->Existe($nome)) {
                $command = $this->s3->getCommand('GetObject', [
                    'Bucket' => SYS_S3_BUCKET,
                    'Key' => $nome,
                    'ResponseContentDisposition' => 'inline'
                ]);
                $request = $this->s3->createPresignedRequest($command, $expires);
                $presignedUrl = (string) $request->getUri();
            }
        } catch (S3Exception $ex) {
            echo $ex->getMessage();
        }
        return $presignedUrl;
    }

    /**
     * Essa função envia um arquivo para um bucket na Amazon S3 que está 
     * configurado no sistema.
     * Obs.: As pastas são passadas como endereço do arquivo
     * 
     * @param string $nome Ex: 'Imagens/foto.png'
     * @param string $propiedade Ex: 'Body' Default: 'LastModified'
     * @return array Ex: array('status' => true, 'msg' => 'Arquivo encontrado com sucesso!', $propiedade => $ret_get->get($propiedade));
     */
    public function Buscar($nome, $propiedade = 'LastModified') {
        $retorno = array();
        try {
            $ret_get = $this->s3->getObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $nome
            ));
            if (!is_null($ret_get->get($propiedade))) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Arquivo encontrado com sucesso!';
                $retorno[$propiedade] = $ret_get->get($propiedade);
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível enviar esse arquivo para o S3';
                $retorno[$propiedade] = null;
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
            $retorno[$propiedade] = null;
        }
        return $retorno;
    }

    /**
     * Essa função busca o arquivo no S3 e salva no destino passado como parâmetro
     * 
     * @param string $nome EX: 'Images/foto.png'
     * @param string $destino Ex: ROOT_UPLOADS . 'tmp/foto.png'
     * @return array Ex: array('status' => true, 'msg' => 'Arquivo salvo com sucesso!');
     */
    public function Baixar($nome, $destino) {
        $retorno = array();
        try {
            $ret_get = $this->s3->getObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $nome,
                'SaveAs' => $destino
            ));
            if (!is_null($ret_get->get('Body'))) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Arquivo encontrado com sucesso!';
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível enviar esse arquivo para o S3';
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
        }
        return $retorno;
    }

    /**
     * Essa função apaga o arquivo no S3 da Amazon, buscando pelo nome passado
     * como parâmetro.
     * Obs.: As pastas são passadas como endereço do arquivo
     * 
     * @param string $nome Ex: 'Imagens/foto.png'
     * @return array Ex: array('status' => true, 'msg' => 'Arquivo apagado com sucesso!');
     */
    public function Apagar($nome) {
        $retorno = array();
        try {
            $ret_del = $this->s3->deleteObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $nome
            ));

            if (!is_null($ret_del->get('DeleteMarker'))) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Arquivo apagado com sucesso!';
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível apagar esse arquivo on S3';
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
        }
        return $retorno;
    }

    /**
     * Essa função move um arquivo de uma pasta para outra dentro do bucket do S3
     * na Amazon.
     * 
     * 
     * @param string $nome Ex: 'Imagens/foto.png'
     * @param string $destino Ex: 'Imagens/lixo'
     * @return array Ex: array('status' => true, 'msg' => 'Arquivo movido com sucesso!', 'url' => 'http://$arquivo');
     */
    public function Mover($nome, $destino) {
        $retorno = array();
        try {
            // <editor-fold defaultstate="collapsed" desc="Variáveis">
            $arrBarra = explode('/', $nome);
            $key = array_pop($arrBarra);
            $pastas = implode('/', $arrBarra);
            $keyObj = $pastas . '/' . $key;

            $arrPonto = explode('.', $key);
            $extension = array_pop($arrPonto);
            $arquivo = implode('.', $arrPonto);

            $original = ROOT_UPLOAD . $pastas . '/' . $arquivo . '.' . $extension;
            // </editor-fold>

            $ret_get = $this->s3->getObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $nome,
                'SaveAs' => $original
            ));

            $ret_put = $this->s3->putObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $destino . '/' . $arquivo . '.' . $extension,
                'SourceFile' => $original
            ));

            $ret_del = $this->s3->deleteObject(array(
                'Bucket' => SYS_S3_BUCKET,
                'Key' => $keyObj
            ));

            if (!is_null($ret_put->get('ObjectURL'))) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Arquivo movido com sucesso!';
                $retorno["url"] = $ret_put->get('ObjectURL');
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível mover esse arquivo no S3';
                $retorno["url"] = null;
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
            $retorno["url"] = null;
        }
        return $retorno;
    }

    /**
     * Essa função retorna um array de objetos listados através do filtro da pasta
     * passada como parâmetro.
     * 
     * @param string $folder Ex: 'Imagens/'
     * @return array Ex: array('status' => true, 'msg' => 'Listagem gerada com sucesso!', 'arrObjs' => $arrObjs);
     */
    public function Listar($folder) {
        $retorno = array();
        try {
            $arrObjs = $this->s3->listObjects(array(
                'Bucket' => SYS_S3_BUCKET,
                'Prefix' => $folder
            ));

            if (!is_null($arrObjs)) {
                $retorno["status"] = true;
                $retorno["msg"] = 'Listagem gerada com sucesso!';
                $retorno["arrObjs"] = $arrObjs;
            } else {
                $retorno["status"] = false;
                $retorno["msg"] = 'ERRO: Não foi possível encontrar a listagem no S3';
                $retorno["arrObjs"] = null;
            }
        } catch (S3Exception $ex) {
            $retorno["status"] = false;
            $retorno["msg"] = 'ERRO: ' . $ex->getMessage();
            $retorno["arrObjs"] = null;
        }
        return $retorno;
    }

    /**
     * Verifica se o arquivo existe no bucket
     * 
     * @param string $nome
     * @return boolean
     */
    public function Existe($nome) {
        return $this->s3->doesObjectExist(SYS_S3_BUCKET, $nome);
    }
}

?>