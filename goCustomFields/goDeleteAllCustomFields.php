<?php
/**
 * @file        goDeleteAllCustomFields.php
 * @brief       API to delete all custom fields for a list
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <jeremiah@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/** @var MySQLiDB $astDB */

$list_id = preg_replace('/\D+/', '', (string) ($_REQUEST['list_id'] ?? ''));
$list_id = $astDB->escape($list_id);
$goTableName = "custom_" . $list_id;

if ($list_id === '') {
    $apiresults = ["result" => "Error: List does not exist"];
    return;
}

$tableResult = $astDB->rawQuery("SHOW TABLES LIKE '$goTableName'");
$tableExists = is_array($tableResult) && count($tableResult) > 0;

$astDB->where('list_id', $list_id);
$fieldsResult = $astDB->get('vicidial_lists_fields', null, 'field_label');
$fieldsExist = is_array($fieldsResult) && count($fieldsResult) > 0;

if (!$tableExists && !$fieldsExist) {
    $apiresults = ["result" => "Error: List does not exist"];
    return;
}

if ($tableExists && $fieldsExist) {
    foreach ($fieldsResult as $fieldRow) {
        $fieldLabel = is_array($fieldRow) ? ($fieldRow['field_label'] ?? '') : '';
        $fieldLabel = str_replace(' ', '_', trim((string) $fieldLabel));

        if ($fieldLabel === '' || $fieldLabel === 'lead_id') {
            continue;
        }

        $columnResult = $astDB->rawQuery("SHOW COLUMNS FROM `$goTableName` LIKE '$fieldLabel'");
        if (!is_array($columnResult) || count($columnResult) < 1) {
            continue;
        }

        try {
            $astDB->dropColumnFromTable($goTableName, $fieldLabel);
        } catch (Throwable $exception) {
            error_log("Delete all custom fields failed for $goTableName.$fieldLabel: " . $exception->getMessage());
            $apiresults = ["result" => "Error: Unable to delete custom field column $fieldLabel"];
            return;
        }
    }
}

$astDB->where('list_id', $list_id);
$queryDeleteCF = $astDB->delete('vicidial_lists_fields');

if ($queryDeleteCF) {
    $apiresults = ["result" => "success"];
} else {
    $apiresults = ["result" => "Error: Custom Field does not exist"];
}
?>
