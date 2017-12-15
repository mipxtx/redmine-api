<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 12.09.16
 * Time: 14:33
 */

namespace RedmineApi\Api;

use RedmineApi\Sql\SqlWhere;

class Emails extends Base
{
    const TABLE = 'email_addresses';

    public function findByUserIds(array $ids) {
        $this->getAccellerator()->getAll(self::TABLE, new SqlWhere('user_id', "in",$ids));
    }
}