<?php
 /**
 * @file 		goUpdateCustomer.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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

if (isset($_GET['goLeadInfo'])) { $lead_info = $_GET['goLeadInfo']; }
    else if (isset($_POST['goLeadInfo'])) { $lead_info = $_POST['goLeadInfo']; }
if (isset($_GET['goCustomInfo'])) { $custom_info = $_GET['goCustomInfo']; }
    else if (isset($_POST['goCustomInfo'])) { $custom_info = $_POST['goCustomInfo']; }
if (isset($_GET['goSaveAsCustomer'])) { $save_as_customer = $astDB->escape($_GET['goSaveAsCustomer']); }
    else if (isset($_POST['goSaveAsCustomer'])) { $save_as_customer = $astDB->escape($_POST['goSaveAsCustomer']); }

$lead_info = is_array($lead_info ?? null) ? $lead_info : [];
$custom_info = is_array($custom_info ?? null) ? $custom_info : [];
$save_as_customer = $save_as_customer ?? 0;
$lead_id = '';
$list_id = '';

$agent = get_settings('user', $astDB, $goUser);

$lead_info = is_array($lead_info ?? null) ? $lead_info : [];
$custom_info = is_array($custom_info ?? null) ? $custom_info : [];
$lead_id = '';
$list_id = '';
$save_as_customer = $save_as_customer ?? 0;

$lead_array = [];
foreach ($lead_info as $idx => $info) {
    if (!is_array($info) || !isset($info['name'])) {
        continue;
    }
    $lName = str_replace('viewCust_', '', (string) $info['name']);
    $delim = "";
    if (!preg_match("/lead_id|list_id/", $lName)) {
        if (array_key_exists($lName, $lead_array)) {
            $delim = ",";
        }
        $lead_array[$lName] = $delim . $astDB->escape((string) ($info['value'] ?? ''));
    } else {
        ${$lName} = $astDB->escape((string) ($info['value'] ?? ''));
    }
}

$astDB->where('lead_id', $lead_id);
$rslt = $astDB->update('vicidial_list', $lead_array);
$errorMsg = $astDB->getLastError();

if (strlen((string) $errorMsg) < 1) {
    if (count($custom_info) > 0) {
        $CF_array = [];
        $custom_list_id = preg_replace('/[^0-9]/', '', (string) $list_id);
        $custom_table = "custom_{$custom_list_id}";
        $table_columns = [];

        if ($custom_list_id !== '') {
            $tableCheck = $astDB->rawQuery("SHOW TABLES LIKE '{$custom_table}'");
            if (is_array($tableCheck) && count($tableCheck) > 0) {
                $columns = $astDB->rawQuery("SHOW COLUMNS FROM `{$custom_table}`");
                foreach ((is_array($columns) ? $columns : []) as $column) {
                    $column_name = is_array($column) ? ($column['Field'] ?? '') : '';
                    if ((string) $column_name !== '') {
                        $table_columns[$column_name] = true;
                    }
                }
            }
        }

        foreach ($custom_info as $info) {
            $lName = preg_replace("/^viewCustom_|\[\]$/", '', (string) ($info['name'] ?? ''));
            if ($lName === '' || $lName === 'lead_id' || !preg_match('/^[A-Za-z0-9_]+$/', $lName) || !isset($table_columns[$lName])) {
                continue;
            }
            $delim = "";
            if (array_key_exists($lName, $CF_array)) {
                $delim = "{$CF_array[$lName]},";
            }
            $CF_array[$lName] = $delim . $astDB->escape((string) ($info['value'] ?? ''));
        }

        if (count($CF_array) > 0 && $custom_list_id !== '' && count($table_columns) > 0) {
            $astDB->where('lead_id', $lead_id);
            $rslt = $astDB->getOne($custom_table, 'lead_id');
            $CF_cnt = $astDB->getRowCount();

            if ($CF_cnt > 0) {
                $astDB->where('lead_id', $lead_id);
                $rslt = $astDB->update($custom_table, $CF_array);
            } else {
                $CF_array['lead_id'] = $lead_id;
                $rslt = $astDB->insert($custom_table, $CF_array);
            }

            $errorMsg = $astDB->getLastError();
        }
    }

    $result = 'success';
    $message = "Lead file '{$lead_id}' updated successfully.";

    $agent_user_group = is_object($agent) ? (string) ($agent->user_group ?? '') : '';
    $goDB->where('user_group', $agent_user_group);
    $rslt = $goDB->getOne('user_access_group', 'group_list_id');
    $group_list_id = is_array($rslt) ? ($rslt['group_list_id'] ?? '') : '';

    if ($save_as_customer) {
        $goDB->where('lead_id', $lead_id);
        $rslt = $goDB->getOne('go_customers');
        $cust_exist = $goDB->getRowCount();

        if ($cust_exist < 1) {
            $rslt = $goDB->insert('go_customers', ['lead_id' => $lead_id, 'group_list_id' => $group_list_id]);
            $message .= "<br><br>Lead file also converted to customer.";
        }
    }

    if ((string) $errorMsg !== '') {
        $message = "Lead file '{$lead_id}' updated but encountered an error on custom fields: {$errorMsg}";
    }
} else {
    $result = 'error';
    $message = "Failed to updated lead file '{$lead_id}'";
}

$APIResult = [ "result" => $result, "lead_id" => $lead_id, "message" => $message ];
?>
