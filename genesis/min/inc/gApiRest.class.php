<?php

/**
 * HTTP Connection Class - used in API calls (CURL library required)
 */
class GApiRest {

    private $status;
    private $response;

    public function __construct() {
        if (!function_exists('curl_init')) {
            throw new Exception('CURL library is required.');
        }
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }

    public function getResponse() {
        return $this->response;
    }

    public function setResponse($response) {
        $this->response = $response;
    }

    public function post($url, $data, $token = null, $timeout = 200, $charset = 'ISO-8859-1') {
        return $this->curlConnection('POST', $url, $data, $token, $timeout, $charset);
    }

    public function get($url, $data, $token = null, $timeout = 200, $charset = 'ISO-8859-1') {
        return $this->curlConnection('GET', $url, $data, $token, $timeout, $charset);
    }

    public function curlConnection($method = 'GET', $url, $data = null, $token, $timeout, $charset) {
        $curl = curl_init();
        if (strtoupper($method) === 'POST') {
            $methodOptions = Array(
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($data)
            );
        } else {
            $methodOptions = Array(
                CURLOPT_HTTPGET => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => json_encode($data)
            );
        }
        $arrHttpHeader = array();
        if (!seNuloOuVazio($token)) {
            $arrHttpHeader[] = "Authorization: " . $token;
        }
        $arrHttpHeader[] = "cache-control: no-cache";
        if (strtoupper($method) === 'POST') {
            $arrHttpHeader[] = "Content-Type: application/x-www-form-urlencoded";
        } else {
            $arrHttpHeader[] = "Content-Type: application/json";
        }
        $arrHttpHeader[] = "Accept: */*; charset=" . $charset;

        $options = Array(
            CURLOPT_HTTPHEADER => $arrHttpHeader,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_HTTPAUTH => CURLAUTH_ANY,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_FOLLOWLOCATION => true,
        );
        $options = ($options + $methodOptions);

        curl_setopt_array($curl, $options);
        $resp = curl_exec($curl);
        $info = curl_getinfo($curl);
        $error = curl_errno($curl);
        $errorMessage = curl_error($curl);
        curl_close($curl);
        $this->setStatus((int) $info['http_code']);
        $this->setResponse((String) $resp);
        if ($error) {
            throw new Exception("CURL can't connect: $errorMessage");
            return false;
        } else {
            return true;
        }
    }
}
