<?php
/**
 * @file        goDeleteCustomField.php
 * @brief       API to delete a custom field
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Noel Umandap  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
$field_label = str_replace(' ', '_', trim((string) ($_REQUEST['field_label'] ?? '')));
$field_label = preg_replace('/[^A-Za-z0-9_]/', '', (string) $field_label);
$field_label = $astDB->escape($field_label);
$field_id = $astDB->escape(($_REQUEST['field_id'] ?? ''));
$table_name = 'custom_' . $list_id;

if ($list_id === '' || $field_label === '' || $field_id === '') {
    $apiresults = ["result" => "Error: Custom Field does not exist"];
    return;
}

$tableResult = $astDB->rawQuery("SHOW TABLES LIKE '$table_name'");
$tableExists = is_array($tableResult) && count($tableResult) > 0;

if (!$tableExists) {
    $apiresults = ["result" => "Error: List does not exist"];
    return;
}

$astDB->where('field_label', $field_label);
$astDB->where('field_id', $field_id);
$astDB->where('list_id', $list_id);
$fieldResult = $astDB->getOne('vicidial_lists_fields');
$fieldExists = $astDB->getRowCount() > 0;

if (!$fieldExists || $field_label === 'lead_id') {
    $apiresults = ["result" => "Error: $field_label does not exist"];
    return;
}

$astDB->where('field_label', $field_label);
$astDB->where('field_id', $field_id);
$astDB->where('list_id', $list_id);
$queryDeleteCF = $astDB->delete('vicidial_lists_fields');

if (!$queryDeleteCF) {
    $apiresults = ["result" => "Error: Custom Field does not exist"];
    return;
}

$columnResult = $astDB->rawQuery("SHOW COLUMNS FROM `$table_name` LIKE '$field_label'");
if (is_array($columnResult) && count($columnResult) > 0) {
    try {
        $astDB->dropColumnFromTable($table_name, $field_label);
    } catch (Throwable $exception) {
        error_log("Delete custom field failed for $table_name.$field_label: " . $exception->getMessage());
        $apiresults = ["result" => "Error: Unable to delete custom field column $field_label"];
        return;
    }
}

$apiresults = ["result" => "success"];
?>
