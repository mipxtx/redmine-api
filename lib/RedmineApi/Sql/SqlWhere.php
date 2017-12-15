<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 13.12.2017
 * Time: 16:13
 */

namespace RedmineApi\Sql;

class SqlWhere
{
    private $operation;

    private $operand1;

    private $operand2;

    private $priority = [
        'in' => 4,
        '!' => 3,
        '=' => 2,
        'and' => 1,
        'or' => 0
    ];

    public function __construct($operand1, $operation, $operand2 = null) {
        $this->operand1 = $operand1;
        $this->operand2 = $operand2;
        $this->operation = strtolower($operation);
    }

    public function _and($name, $cond, $value) {
        return $this->append('and', new self($name, $cond, $value));
    }

    public function _or($name, $cond, $value) {
        return $this->append('or', new self($name, $cond, $value));
    }

    public function append($operation, SqlWhere $condition) {
        return new self($this, $operation, $condition);
    }

    public static function _new($name, $cond, $value) {
        return new self($name, $cond, $value);
    }

    public function toString(\mysqli $mysql) {
        switch (strtolower($this->operation)) {
            case "!" :
                return $this->operation . $this->processValue($mysql, $this->operand1);
            default:

                $pattern = "%s %s %s";

                if ($this->operand1 instanceof SqlWhere) {
                    $str1 = $this->operand1->toString($mysql);

                    if (!isset($this->priority[$this->operation])) {
                        trigger_error('priority ' . "'{$this->operation}' not found");
                    }

                    if (!isset($this->priority[$this->operand1->operation])) {
                        trigger_error('priority ' . "'{$this->operation}' not found");
                    }

                    $p1 = $this->priority[$this->operation];
                    $p2 = $this->priority[$this->operand1->operation];
                    if ($p1 > $p2) {
                        $pattern = "(%s) %s %s";
                    }
                } else {
                    $str1 = $this->operand1;
                }

                if ($this->operand2 instanceof SqlWhere) {
                    $str2 = $this->operand2->toString($mysql);
                } else {
                    $str2 = $this->processValue($mysql, $this->operand2);
                }

                return sprintf($pattern, $str1, strtoupper($this->operation), $str2);
        }
    }

    protected function processValue(\mysqli $mysql, $argument) {
        if (is_array($argument)) {
            $out = [];
            foreach ($argument as $item) {
                $out[] = $this->processValue($mysql, $item);
            }

            return "(" . implode(", ", $out) . ")";
        }

        if (is_string($argument)) {
            return "'" . $mysql->real_escape_string($argument) . "'";
        }

        if (is_bool($argument)) {
            return (int)$argument;
        }

        return $argument;
    }

    /**
     * @return mixed
     */
    public function getOperation() {
        return $this->operation;
    }
}