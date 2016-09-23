<?php
/**
 * @author: mix
 * @date: 16.08.14
 */

namespace RedmineApi;

class MysqlClient
{
    private $host;

    private $user;

    private $passwd;

    private $dbname;

    private $connect;

    function __construct($host, $username, $passwd, $dbname) {
        $this->host = $host;
        $this->user = $username;
        $this->passwd = $passwd;
        $this->dbname = $dbname;
    }

    private function getConnect() {
        if (!$this->connect) {
            $this->connect = new \mysqli($this->host, $this->user, $this->passwd, $this->dbname);
            $this->connect->set_charset("utf8");
        }

        return $this->connect;
    }

    public function request(array $ids, $table, $field = "id") {
        if (!$ids) {
            return [];
        }

        $map = array_map(
            function ($i) {
                return is_int($i) ? $i : "'$i'";
            },
            $ids
        );

        if ($table == 'users') {
            $from = "t.*, e.address as mail FROM users as t left join email_addresses e on e.user_id=t.id";
        } else {
            $from = "* FROM {$table} as t";
        }

        $sql = "SELECT {$from} WHERE t.{$field} IN (" . implode(",", $map) . ")";

        $result = $this->getConnect()->query($sql);
        if (!$result) {
            return false;
        }
        $out = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
            $out[$row[$field]] = $row;
        }

        return $out;
    }

    public function query($table, $where, $index = 'id') {
        $sql = "SELECT * FROM {$table} WHERE {$where}";

        echo $sql . "\n";

        $result = $this->getConnect()->query($sql);
        if (!$result) {
            return false;
        }
        $out = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $row) {
            $out[$row[$index]] = $row;
        }

        return $out;
    }

    public function queryIn($table, $key, $list, $index = 'id'){
        $where = $key . " IN ('" . implode("','", $list) . "')";

        return $this->query($table, $where, $index);
    }
}
