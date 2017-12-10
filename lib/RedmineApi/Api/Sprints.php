<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 06.12.2017
 * Time: 1:30
 */

namespace RedmineApi\Api;

class Sprints extends Base
{
    /**
     * find user by id
     *
     * @param $id
     * @return null
     */
    public function findAll($active = true) {
        $where = "";
        if($active){
            $where = "status='open'";
        }
        return $this->getAccellerator()->getAll('sprints',$where);
    }
}