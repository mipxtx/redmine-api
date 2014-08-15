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

    public function request($table, array $ids) {
        if (!$ids) {
            return [];
        }

        $map = array_map(
            function ($i) {
                return (int)$i;
            },
            $ids
        );
        $sql = "Select * from $table where id in (" . implode(",", $map) . ")";
        $result = $this->connect->query($sql);
        if (!$result) {
            return false;
        }
        $out = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
            $out[$row["id"]] = $row;
        }

        return $out;
    }
}