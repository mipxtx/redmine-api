<?php
/**
 * @author: mix
 * @date: 01.05.14
 */
namespace RedmineApi\Api;

use \RedmineApi\Client as Client;

/**
 * Class Base
 *
 * @package Redmine\Api
 */
abstract class Base
{
    /**
     * @var \RedmineApi\Client
     */
    private $client;

    public function __construct(Client $client) {
        $this->client = $client;
    }

    /**
     * performs a request
     *
     * @param $method
     * @param $url
     * @param array $data
     * @return mixed
     * @throws \RedmineApi\Exception
     */
    protected function request($method, $url, $data = []) {
        return $this->client->request($method, $url, $data);
    }
}