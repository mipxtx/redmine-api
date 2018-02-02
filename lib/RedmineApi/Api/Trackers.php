<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 02.02.2018
 * Time: 11:22
 */

namespace RedmineApi\Api;

use RedmineApi\Sql\SqlWhere;

class Trackers extends Base
{
    const TABLE = 'trackers';

    public function findByIds(array $ids) {

        if($ids){
            $where = SqlWhere::_new('id', 'in',$ids);
        }else{
          $where = null;
        }

        return $this->getAccellerator()->getAll(self::TABLE, $where);
    }
}