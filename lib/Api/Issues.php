<?php
/**
 * @author: mix
 * @date: 01.05.14
 */
namespace RedmineApi\Api;

/**
 * Class Issues
 * @see http://www.redmine.org/projects/redmine/wiki/Rest_Issues
 *
 * @package RedmineApi\Api
 */
class Issues extends Base
{
    public function find($id) {
        $ret = $this->request("GET", "/issues/$id.json");

        return $ret["issue"];
    }

    public function create(array $params) {
        $ret = $this->request("POST", "/issues.json", ["issue" => $params]);
        return $ret["issue"];
    }
}