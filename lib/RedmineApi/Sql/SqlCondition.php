<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 06.12.2017
 * Time: 2:06
 */

namespace RedmineApi\Sql;

class SqlCondition
{
    private $name;

    private $value;

    private $cond;

    /**
     * SqlCondition constructor.
     *
     * @param $name
     * @param $value
     * @param $cond
     */
    public function __construct($name, $cond, $value) {
        $this->name = $name;
        $this->value = $value;
        $this->cond = $cond;
    }

    public function toString() {
        return "{$this->name} {$this->cond} {$this->value}";
    }
}