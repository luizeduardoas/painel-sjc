<?php

class gUploadedFileXhr {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()) {
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }

    function getName() {
        return $_GET['qqfile'];
    }

    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])) {
            return (int) $_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

class gUploadedFileForm {

    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if (!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)) {
            return false;
        }
        return true;
    }

    function getName() {
        return $_FILES['qqfile']['name'];
    }

    function getSize() {
        return $_FILES['qqfile']['size'];
    }

//
//    function getTamanho() {
//        return getimagesize($_FILES['qqfile']);
//    }
}

class gFileUploader {

    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;

    /**
     * 
     * @param array $allowedExtensions
     * @param type $sizeLimit
     */
    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760) {
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        $this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new gUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new gUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings() {
//        echo ini_get('post_max_size'),' - ',ini_get('upload_max_filesize');
//        echo $this->sizeLimit;
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit) {
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str) {
        $val = (int) trim($str);
        $last = strtolower($str[strlen($str) - 1]);
        switch ($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    /**
     *
     * @param string $uploadDirectory
     * @param string $actionOldFileName replace or concat
     * @param string $newFileName
     * @param boolean $replaceOldFile
     * @return array array('success'=>true) or array('error'=>'error message')
     */
    function handleUpload($uploadDirectory, $actionOldFileName = '', $newFileName = '', $replaceOldFile = FALSE) {
        if (!is_writable($uploadDirectory)) {
            return array('error' => "Diretório não é gravável.");
        }

        if (!$this->file) {
            return array('error' => 'Sem arquivos para fazer upload');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'O arquivo está vazio');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'O arquivo é muito grande');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename_old = $pathinfo['filename'];
        $filename = $pathinfo['filename'];

        //$filename = md5(uniqid());
        $ext = $pathinfo['extension'];

        if ($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)) {
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'Extensão inválida, use: ' . $these . '.');
        }

        if ($actionOldFileName == 'r') {
            $filename = (!empty($newFileName)) ? $newFileName : $filename;
        } else if ($actionOldFileName == 'c') {
            $filename = (!empty($newFileName)) ? $newFileName . '_' . $filename : $filename;
        }

        if (!$replaceOldFile) {
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= '(' . rand(10, 99) . ')';
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)) {
            return array('success' => true, 'filename' => $filename . '.' . $ext, 'size' => $size, 'filename_old' => $filename_old);
        } else {
            return array('error' => 'Upload não pode ser feito.' .
                'Foi cancelado ou ocorreu um erro no servidor');
        }
    }
}

?>
