<?php
/**
 * @author: mix
 * @date: 16.08.14
 */

namespace RedmineApi;

class MysqlClient
{
    private $connect;

    function __construct($host, $username, $passwd, $dbname) {
        $this->connect = new \mysqli($host, $username, $passwd, $dbname);
    }

    public function request(array $ids, $table, $field = "id") {
        if (!$ids) {
            return [];
        }

        $map = array_map(function ($i) { return is_int($i) ? $i : "'$i'";}, $ids);

        $sql = "SELECT * FROM {$table} WHERE {$field} IN (" . implode(",", $map) . ")";

        $result = $this->connect->query($sql);
        if (!$result) {
            return false;
        }
        $out = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
            $out[$row[$field]] = $row;
        }

        return $out;
    }
}