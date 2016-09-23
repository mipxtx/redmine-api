<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 12.09.16
 * Time: 14:33
 */

namespace RedmineApi\Api;

class Emails extends Base
{
    const TABLE = 'email_addresses';

    public function findBuUserIds(array $ids) {
        $this->queryIn(self::TABLE, 'user_id', $ids, 'user_id');
    }
}