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
    public function find($id, $params = []) {
        $ret = $this->request("GET", "/issues/$id.json", $params);

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

    /**
     * update an issue
     *
     * @param $id
     * @param array $params
     * @return mixed
     */
    public function update($id, array $params) {
        return $this->request("PUT", "/issues/$id.json", ["issue" => $params]);
    }

    /**
     * issues multiget
     *
     * @param array $ids
     * @return array|bool
     */
    public function findByIds(array $ids) {
        return $this->multiAccelerate("issues", $ids, function ($id) {return $this->find($id);});
    }
}