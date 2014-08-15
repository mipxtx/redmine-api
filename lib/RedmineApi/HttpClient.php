<?php
/**
 * @author: mix
 * @date: 01.05.14
 */
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

    private $debugStringLength = 0;

    private $key;

    private $server_url;

    private $timeout;

    const READ_BLOCK_SIZE = 4096;

    private $rawResponse = "";

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
        return $this->getResponse(
            $this->sendRequest(
                $this->getRawRequest($method, $requestUrl, $data)
            )
        );
    }

    public function enableDebug($length = 1024) {
        $this->debug = true;
        $this->debugStringLength = $length;
    }

    public function log($str) {
        if ($this->debug) {

            if(php_sapi_name() != "cli"){
                echo "<pre>";
            }
            echo (
                strlen($str) > $this->debugStringLength
                    ? (mb_strcut($str, 0, $this->debugStringLength) . "...")
                    : $str
                ) . "\n";
            if(php_sapi_name() != "cli"){
                echo "</pre>";
            }
        }
    }

    public function getRawRequest($method, $requestUrl, array $data = []) {

        $serverUrl = parse_url($this->server_url);
        $host = $serverUrl["host"];

        if(in_array($method, ["GET", "DELETE"]) && $data){
            $params = [];
            foreach($data as $key => $value){
                $params[] = urlencode($key) . "=" . urlencode($value);
            }
            $requestUrl .= "?" . implode("&",$params);

        }

        $str = "{$method} {$requestUrl} HTTP/1.1\r\n" .
            "Host: {$host}\r\n" .
            "Connection: close\r\n" .
            "X-Redmine-API-Key: {$this->key}\r\n";

        if (in_array($method, ["POST", "PUT"])) {
            $data = (json_encode($data));
            $str .= "Content-Type: application/json\r\n";
            $str .= "Content-Length: " . strlen($data) . "\r\n\r\n";
            $str .= $data . "\r\n";
        }

        $str .= "\r\n";

        return $str;
    }

    public function sendRequest($request) {
        $scheme = "tcp";
        $port = "80";
        $serverUrl = parse_url($this->server_url);
        $host = $serverUrl["host"];
        if (isset($serverUrl["port"])) {
            $port = $serverUrl["port"];
            if ($serverUrl["scheme"] == "https") {
                $scheme = "ssl";
            } else {
                $scheme = "tcp";
            }
        } else {
            if ($serverUrl["scheme"] == "https") {
                $port = "443";
                $scheme = "ssl";
            }
        }

        $uri = "$scheme://$host:$port";
        $this->log("connecting to $uri");
        $h = stream_socket_client($uri, $errno, $errstr, $this->timeout);
        fwrite($h, $request);
        $this->log($request);

        return $h;
    }

    public function readLine($h) {
        $line = fgets($h);
        if ($this->debug) {
            $this->rawResponse .= $line;
        }

        return trim($line);
    }

    public function read($h, $length) {
        $toRead = $length;
        $out = "";
        do {
            $blockLength = ($toRead > self::READ_BLOCK_SIZE) ? self::READ_BLOCK_SIZE : $toRead;

            $block = fread($h, $blockLength);
            $toRead -= $blockLength;

            if ($this->debug) {
                $this->rawResponse .= $block;
            }
            $out .= $block;
        } while ($toRead > 0);

        return $out;
    }

    public function getResponse($h) {
        $headers = [];
        $line = $this->readLine($h);
        do {
            $headers[] = $line;
            $line = $this->readLine($h);
        } while ($line);
        $length = $this->getBlockLenght($h);
        $body = "";
        do {
            $block = $this->read($h, $length);
            $body .= $block;
            $this->readLine($h);
            $length = $this->getBlockLenght($h);

        } while ($length > 0);

        $this->log($this->rawResponse);
        $this->rawResponse = "";

        $result = json_decode($body, 1);
        if (isset($result["error"])) {
            throw new Exception($result["error"]);
        }

        return $result;
    }

    private function getBlockLenght($h){
        $delimiter = $this->readLine($h);
        $length = 0;
        for ($i = 0; $i < strlen($delimiter); $i++) {
            $char = $delimiter[$i];
            $length = ($length << 4) + (($char >= '0' && $char <= '9') ? (int)$char : (ord($char) - ord('a') + 10));
        }
        return $length;
    }
}