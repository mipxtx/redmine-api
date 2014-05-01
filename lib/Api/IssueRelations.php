<?php
/**
 * @author: mix
 * @date: 01.05.14
 */
namespace RedmineApi\Api;

/**
 * Class Deploy_RedmineBot_Api_IssueRelations
 * @see http://www.redmine.org/projects/redmine/wiki/Rest_IssueRelations
 * @package Redmine\Api
 */
class IssueRelations extends Base
{

    /**
     * finds relations for issue
     *
     * @param $issueId
     * @return array of relations
     */
    public function findFor($issueId) {
        $res = $this->request("GET", "/issues/$issueId/relations.json");

        return $res["relations"];
    }

    /**
     * @var array available types
     */
    private $types = ["relates", "duplicates", "duplicated", "blocks", "blocked", "precedes", "follows"];

    /**
     * create an issue
     *
     * @param int $idFrom
     * @param int $idTo
     * @param string $type
     * @param int $delay
     * @return array of relation fields
     */
    public function create($idFrom, $idTo, $type, $delay = null) {

        if(!in_array($type, $this->types)){
            return false;
        }

        $params = ["issue_to_id" => $idTo, "relation_type" => $type];
        if ($delay) {
            $params["delay"] = $delay;
        }
        $res = $this->request(
            "POST",
            "/issues/$idFrom/relations.json",
            ["relation" => $params]
        );

        return $res;
    }
}