<?php
/**
 * @author: mix
 * @date: 16.08.14
 */

namespace RedmineApi\Sql;

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

        return $this->perform($from, $field, "t.{$field} IN (" . implode(",", $map) . ")");
    }

    private function perform($from, $field, $where = "", $order = "") {
        $sql = "select {$from}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }

        if ($order) {
            $sql .= " ORDER BY {$order}";
        }

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

    public function getAll($table, $where = "", $order = "") {
        return $this->perform("* FROM {$table}", 'id', $where, $order);
    }

    public function update($table, int $id, array $set) {

        $params = [];
        foreach ($set as $key => $value) {
            $params[] = "{$key}='$value'";
        }

        $sql = "update {$table} set " . implode(', ', $params) . " WHERE id=$id";
        $this->getConnect()->query($sql);
    }
}
