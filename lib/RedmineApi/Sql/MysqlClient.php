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

    private $logQueries = true;

    function __construct($host, $username, $passwd, $dbname) {
        $this->host = $host;
        $this->user = $username;
        $this->passwd = $passwd;
        $this->dbname = $dbname;
    }

    private function getConnect() {
        if (!$this->connect) {
            $this->log("connecting {$this->host}");
            $this->connect = new \mysqli($this->host, $this->user, $this->passwd, $this->dbname);
            $this->connect->set_charset("utf8");
            $this->connect->query("USE " . $this->dbname);
        }

        return $this->connect;
    }

    public function request(array $ids, $table, $field = "id") {
        if (!$ids) {
            return [];
        }

        if ($table == 'users') {
            $from = "t.*, e.address as mail FROM users as t left join email_addresses e on e.user_id=t.id";
        } else {
            $from = "* FROM {$table} as t";
        }

        $params = new SqlParams();
        $params->setKey($field);

        return $this->perform($from, new SqlWhere("t.{$field}", 'in', $ids), $params);
    }

    private function perform($from, SqlWhere $where = null, SqlParams $params) {

        $sql = "select {$from}";
        if ($where) {
            $sql .= " WHERE " . $where->toString($this->getConnect());
        }

        if ($params->getOrder()) {
            $sql .= " ORDER BY " . $params->getOrder();
        }

        if ($params->getGroupBy()) {
            $sql .= " GROUP BY " . $params->getGroupBy();
        }

        $result = $this->performQuery($sql);
        if (!$result) {
            return false;
        }

        $out = [];
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $key = $params->getKey();

        foreach ($rows as $row) {
            if ($params->getResultMode() == SqlParams::MODE_REPLACE) {
                $out[$row[$key]] = $row;
            } else {
                $out[$row[$key]][] = $row;
            }
        }

        return $out;
    }

    public function getAll($table, SqlWhere $where = null, $fields = "*", SqlParams $params = null) {
        if (!$params) {
            $params = new SqlParams();
        }
        $from = $fields . " FROM {$table}";

        return $this->perform($from, $where, $params);
    }

    public function update($table, int $id, array $set) {

        $this->updateByCondition($table, SqlWhere::_new('id', '=', $id), $set);
    }

    public function updateByCondition($table, SqlWhere $where, array $set) {
        $params = [];
        foreach ($set as $key => $value) {
            $params[] = "{$key}='$value'";
        }
        $sql = "update {$table} set " . implode(', ', $params) . " WHERE " . $where->toString($this->getConnect());
        $this->performQuery($sql);
    }

    public function insert($table, $set) {
        $params = [];
        foreach ($set as $value) {
            $params[] = "'$value'";
        }
        $sql = "insert into {$table} (" . implode(",", array_keys($set)) . ") VALUES (" . implode(",", $params) . ")";

        $this->performQuery($sql);
    }

    private function performQuery($sql) {
        $conect = $this->getConnect();
        $this->log($sql);
        $result = $conect->query($sql);

        if ($result === false) {
            throw new \Exception('mysql error ' . $conect->error . "\nat query " . $sql);
        }

        return $result;
    }

    private function log($sql) {
        if ($this->logQueries) {
            error_log($sql);
        }
    }
}
