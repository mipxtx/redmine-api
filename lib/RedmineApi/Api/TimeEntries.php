<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 12.09.16
 * Time: 13:55
 */

namespace RedmineApi\Api;

use RedmineApi\Sql\SqlWhere;

class TimeEntries extends Base
{
    const TABLE = 'time_entries';

    public function find($userId, $start = null, $end = null) {

        $where = new SqlWhere('user_id', 'in', $userId);

        if ($start) {
            $where = $where->_and('spent_on', ">", $start);
        }

        if ($end) {
            $where = $where->_and('spent_on', ">", $end);
        }

        return $this->getAccellerator()->getAll(self::TABLE, $where);
    }
}