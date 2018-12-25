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

    public function findForMonth(int $year, int $month, $ids = []) {
        $where = SqlWhere::_new('tmonth', '=', $month)->_and('tyear', '=', $year);

        if ($ids) {
            $where = $where->_and('user_id', 'in', $ids);
        }

        return $this->getAccellerator()->getAll(self::TABLE, $where);
    }

    public function findForIssues(array $ids) {
        if (!$ids) {
            return [];
        }
        $where = new SqlWhere('issue_id', 'in', $ids);

        return $this->getAccellerator()->getAll(self::TABLE, $where);
    }

    public function findForUser($id, $from, $to) {
        $where = (new SqlWhere('user_id', '=', $id))
            ->_and('spent_on', '>=', $from)
            ->_and('spent_on', '<=', $to);

        return $this->getAccellerator()->getAll(self::TABLE, $where);
    }
}