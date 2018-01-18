<?php
/**
 * @author: mix
 * @date: 01.05.14
 */

namespace RedmineApi\Api;

use RedmineApi\Sql\SqlWhere;

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
        return $this->multiAccelerate(
            "issues",
            $ids,
            function ($id) {
                return $this->find($id);
            }
        );
    }

    /**
     * @param SqlWhere $where
     * @param string $fields
     * @param string $join
     * @param string $order
     * @return array|bool
     */
    public function findByConditions(SqlWhere $where, $fields = "*", $join = "", $order = "", $groupBy = "") {
        $table = "issues i";
        if ($join) {
            $table .= " " . $join;
        }

        return $this->getAccellerator()->getAll($table, $where, $fields, $order, $groupBy);
    }

    public function updatePosition($id, $position) {
        $this->getAccellerator()->update('issues', $id, ['position' => (int)$position]);
    }

    public function findWithChildren(array $ids) {
        $out = [];
        $items = $this->findByIds($ids);
        $out[0] = $items;
        $ids = [];
        foreach ($items as $item) {
            $ids[] = $item['id'];
        }

        do {
            $ids = [];
            foreach ($items as $item) {
                $ids[] = $item['id'];
                $parent = $item['parent_id'];
                if(!isset($out[$parent])){
                    $out[$parent] = [];
                }
                $out[$parent][] = $item;
            }
            $items = $this->findByConditions(SqlWhere::_new('parent_id', 'in', $ids));

        } while (count($items) > 0);

        return $out;
    }
}