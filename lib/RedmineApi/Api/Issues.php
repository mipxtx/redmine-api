<?php
/**
 * @author: mix
 * @date: 01.05.14
 */
namespace RedmineApi\Api;

/**
 * Class Issues
 *
 * @see http://www.redmine.org/projects/redmine/wiki/Rest_Issues
 *
 * @package RedmineApi\Api
 */
class Issues extends Base
{
    /**
     * finds an issue
     *
     * @param $id
     * @return array of issue fields
     */
    public function find($id) {
        $ret = $this->request("GET", "/issues/$id.json");

        return $ret["issue"];
    }

    /**
     * create an issue
     *
     * @param array $params
     * @return array of issue fields
     */
    public function create(array $params) {
        $ret = $this->request("POST", "/issues.json", ["issue" => $params]);

        return $ret["issue"];
    }
}