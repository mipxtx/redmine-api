<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 12.09.16
 * Time: 13:55
 */

namespace RedmineApi\Api;

class TimeEntries extends Base
{
    const TABLE = 'time_entries';

    public function find($userId, $start = null, $end = null) {

        $where = "user_id={$userId}";

        if ($start) {
            $where .= " AND spent_on > '{$start}'";
        }

        if ($end) {
            $where .= " AND spent_on < '{$end}'";
        }

        return $this->getAccellerator()->getAll(self::TABLE, $where);
    }
}