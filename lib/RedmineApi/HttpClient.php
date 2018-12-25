<?php

namespace RedmineApi;

/**
 * Class Client
 *
 * Http Client
 *
 * @package Redmine
 */
class HttpClient
{
    private $debug = false;

    private $debugStringLength = 10000000;

    private $key;

    private $server_url;

    private $timeout;

    /**
     * @param string $server_url ie https://redmine.example.com
     * @param string $key redmine api key (http://www.redmine.org/projects/redmine/wiki/Rest_api#Authentication)
     * @param float $timeout request timeout
     */
    public function __construct($server_url, $key, $timeout = 0.1) {
        $this->server_url = $server_url;
        $this->key = $key;
        $this->timeout = $timeout;
    }

    /**
     * @param $method
     * @param $requestUrl
     * @param array $data
     * @return mixed
     * @throws Exception
     */
    public function request($method, $requestUrl, array $data = []) {
        $requestUrl = $this->getRequestUrl($method, $requestUrl, $data);

        $ch = $this->prepareCurl($method, $requestUrl, $data);

        return $this->getResponse($ch);
    }

    public function enableDebug($length = 1024) {
        $this->debug = true;
        $this->debugStringLength = $length;
    }

    public function log($str) {
        if ($this->debug) {

            if (php_sapi_name() != "cli") {
                //echo "<pre>";
            }

            $str = strlen($str) > $this->debugStringLength
                ? (mb_strcut($str, 0, $this->debugStringLength) . "...")
                : $str;
            error_log($str);
            echo($str . "\n");

            if (php_sapi_name() != "cli") {
                //echo "</pre>";
            }
        }
    }

    public function getRequestUrl($method, $requestUrl, array $data = []) {
        if ($method == 'GET' && $data) {
            $params = [];
            foreach ($data as $key => $value) {
                $params[] = urlencode($key) . '=' . urlencode($value);
            }
            $requestUrl .= '?' . implode('&', $params);
        }

        return $this->server_url . $requestUrl;
    }

    public function prepareCurl($method, $requestUrl, array $data = []) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $this->timeout * 1000000);
        $this->log("connecting to $requestUrl");

        $msg = $method . " " . $requestUrl;

        if ($data) {
            $msg .= "\n" . print_r($data,1);
        }

        $this->log('send: ' . $msg);

        if ($method != 'GET') {
            $data = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
        } else if ($method == 'PUT' || $method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        $serverUrl = parse_url($this->server_url);
        if ($serverUrl['scheme'] == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'X-Redmine-API-Key: ' . $this->key,
            ]
        );

        return $ch;
    }

    public function getResponse($ch) {
        $body = curl_exec($ch);
        curl_close($ch);

        $this->log('resp: ' . $body);
        $result = json_decode($body, 1);

        if (isset($result['error'])) {
            throw new \RuntimeException($result['error']);
        }

        return $result;
    }
}
