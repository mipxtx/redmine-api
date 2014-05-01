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
class Client
{
    private $debug = false;

    private $debugStringLength = 0;

    private $key;

    private $server_url;

    private $timeout;

    /**
     * @param string $server_url ie https://redmine.example.com
     * @param string $key redmine api key (http://www.redmine.org/projects/redmine/wiki/Rest_api#Authentication)
     * @param float $timeout request timeout
     * @throws Exception
     */
    public function __construct($server_url, $key, $timeout = 0.1) {
        $this->server_url = $server_url;
        $this->key = $key;
        $this->timeout = $timeout;
    }

    public function request($method, $requestUrl, array $data = []) {

        if ($data) {
            $data = (json_encode($data));
        }

        $serverUrl = parse_url($this->server_url);
        $host = $serverUrl["host"];

        $str = "{$method} {$requestUrl} HTTP/1.1\r\n" .
            "Host: {$host}\r\n" .
            "Connection: close\r\n" .
            "X-Redmine-API-Key: {$this->key}\r\n";

        if (in_array($method, ["POST", "PUT"])) {
            $str .= "Content-Type: application/json\r\n";
            $str .= "Content-Length: " . strlen($data) . "\r\n\r\n";
            $str .= $data . "\r\n";
        }

        $str .= "\r\n";

        $scheme = "tcp";
        $port = "80";

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

        $h = stream_socket_client($uri, $errno, $errstr, $this->timeout);
        $this->log("connecting to $uri");
        fwrite($h, $str);
        $this->log($str);
        $ret = stream_get_contents($h);
        $this->log($ret);
        list($headers, $body) = $this->parseResponse($ret);
        $result = json_decode($body, 1);
        if(isset($result["error"])){
            throw new Exception($result["error"]);
        }
        return $result;
    }

    public function parseResponse($str) {
        $arr = explode("\n", $str);
        $headers = [];
        $count = count($arr);
        for ($i = 0; $i < $count; $i++) {
            $line = array_shift($arr);
            if (strlen(trim($line)) == 0) {
                $headers[] = $line;
                break;
            }
        }
        array_shift($arr);

        do {
            $line = trim(array_pop($arr));
        } while ($line !== '0');

        return [$headers, implode("\n", $arr)];
    }

    public function enableDebug($length = 1024) {
        $this->debug = true;
        $this->debugStringLength = $length;
    }

    public function log($str) {
        if ($this->debug) {
            error_log(
                strlen($str) > $this->debugStringLength
                    ? (mb_strcut($str, 0, $this->debugStringLength) . "...")
                    : $str
            );
        }
    }
}