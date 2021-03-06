<?php
/**
 * Created by PhpStorm.
 * User: mix
 * Date: 13.12.2017
 * Time: 0:57
 */

namespace RedmineApi\Api;

use RedmineApi\Sql\SqlParams;
use RedmineApi\Sql\SqlWhere;

class CustomFields extends Base
{
    public function getField($id) {
        $fields = $this->getAccellerator()->request([$id], 'custom_fields');
        $field = array_shift($fields);
        if ($field['field_format'] == 'list') {
            $field['possible_values'] = $this->convertList($field['possible_values']);
        }

        return $field;
    }

    private function convertList($rubyList) {
        $out = [];
        $array = explode("\n", $rubyList);
        foreach ($array as $item) {
            $item = trim($item, " \r\n\t-");
            if ($item) {
                $out[] = $item;
            }
        }

        return $out;
    }

    public function getValues(array $issues, $fieldId, $order = "") {
        $params = new SqlParams();
        $params->setOrder($order);

        $result = $this->getAccellerator()->getAll(
            'custom_values',
            SqlWhere::_new('custom_field_id', '=', $fieldId)->_and('customized_id', 'in', $issues),
            'id, customized_id, value',
            $params
        );
        $out = [];
        foreach ($result as $row) {
            $out[$row['customized_id']] = $row;
        }

        return $out;
    }

    public function getValue($id, $fieldId) {
        $rr = $this->getValues([$id], $fieldId);
        $rr = array_pop($rr);

        return $rr['value'];
    }

    public function update(array $issueIds, $fieldId, $value) {
        $cond = SqlWhere::_new('customized_type', '=', 'Issue')
            ->_and('customized_id', 'in', $issueIds)
            ->_and('custom_field_id', '=', $fieldId);

        $issues = $this->getAccellerator()->getAll('custom_values', $cond);

        if (count($issues) != count($issueIds)) {
            $existed = [];
            foreach ($issues as $isseValue) {
                $existed[] = $isseValue['customized_id'];
            }
            foreach (array_diff($issueIds, $existed) as $id) {
                $new = [
                    'customized_type' => 'Issue',
                    'customized_id' => $id,
                    'custom_field_id' => $fieldId,
                ];
                $this->getAccellerator()->insert('custom_values', $new);
            }
        }
        $this->getAccellerator()->updateByCondition('custom_values', $cond, ['value' => $value]);
    }
}

