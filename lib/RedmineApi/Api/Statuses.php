<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 10.12.2017
 * Time: 1:58
 */

namespace RedmineApi\Api;

class Statuses extends Base
{
    public function findAll(){
        return $this->getAccellerator()->getAll('issue_statuses');
    }

}