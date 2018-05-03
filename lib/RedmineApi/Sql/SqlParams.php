<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 03.05.2018
 * Time: 19:29
 */

namespace RedmineApi\Sql;

class SqlParams
{
    const MODE_REPLACE = 'replace';
    const MODE_APPEND = 'append';


    private $key = 'id';

    private $order = '';

    private $groupBy = '';

    private $resultMode = self::MODE_REPLACE;

    /**
     * @return
     */
    public function getKey() {
        return $this->key;
    }

    /**
     * @param $key
     */
    public function setKey($key) {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getOrder(): string {
        return $this->order;
    }

    /**
     * @param string $order
     */
    public function setOrder(string $order) {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getGroupBy(): string {
        return $this->groupBy;
    }

    /**
     * @param string $groupBy
     */
    public function setGroupBy(string $groupBy) {
        $this->groupBy = $groupBy;
    }

    /**
     * @return string
     */
    public function getResultMode(): string {
        return $this->resultMode;
    }

    /**
     * @param string $resultMode
     */
    public function setResultMode(string $resultMode) {
        $this->resultMode = $resultMode;
    }

    
}