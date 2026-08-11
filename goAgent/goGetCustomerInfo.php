<?php
 /**
 * @file 		goGetCustomerInfo.php
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

include_once (__DIR__ . "/goAPI.php");

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var MySQLiDB $kamDB */
/** @var string $goUser */
/** @var string $goPass */
/** @var string $goAction */
/** @var string $goURL */
/** @var string $userResponseType */
/** @var string $session_user */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */

$lead_id = '';
$custom_info = [];

if (isset($_GET['goLeadID'])) { $lead_id = $astDB->escape((string) $_GET['goLeadID']); }
    else if (isset($_POST['goLeadID'])) { $lead_id = $astDB->escape((string) $_POST['goLeadID']); }

$system_settings = get_settings('system', $astDB);

if (!function_exists('go_quote_custom_field_identifier')) {
    function go_quote_custom_field_identifier($field)
    {
        return '`' . str_replace('`', '``', (string) $field) . '`';
    }
}
if (!function_exists('go_get_custom_field_row')) {
    function go_get_custom_field_row($astDB, $custom_table, $select_fields, $lead_id)
    {
        if (!preg_match('/^custom_\d+$/', (string) $custom_table) || (string) $select_fields === '') {
            return [];
        }
        $rows = $astDB->rawQuery("SELECT {$select_fields} FROM `{$custom_table}` WHERE lead_id = ? LIMIT 1", [$lead_id]);
        return (is_array($rows) && isset($rows[0]) && is_array($rows[0])) ? $rows[0] : [];
    }
}

$custom_info = [];

if (isset($lead_id) && $lead_id !== '') {
    $astDB->where('lead_id', $lead_id);
    $lead_info = $astDB->getOne('vicidial_list', 'lead_id,list_id,title,first_name,middle_initial,last_name,phone_number,alt_phone,email,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,status,user,comments');
    $leadIDExist = $astDB->getRowCount();

    $custom_fields_enabled = is_object($system_settings) ? (int) ($system_settings->custom_fields_enabled ?? 0) : 0;
    if ($custom_fields_enabled > 0) {
        $astDB->where('lead_id', $lead_id);
        $rslt = $astDB->getOne('vicidial_list', 'list_id');
        $list_id = is_array($rslt) ? ($rslt['list_id'] ?? '') : '';
        $list_id = preg_replace('/[^0-9]/', '', (string) $list_id);
        $custom_listid = "custom_{$list_id}";

        if ($list_id !== '') {
            $tableCheck = $astDB->rawQuery("SHOW TABLES LIKE '{$custom_listid}'");
            if (is_array($tableCheck) && count($tableCheck) > 0) {
            $CFields = [];
            $rslt = $astDB->rawQuery("SHOW COLUMNS FROM `$custom_listid`;");
            foreach ((is_array($rslt) ? $rslt : []) as $field) {
                $field_name = is_array($field) ? ($field['Field'] ?? '') : '';
                if ($field_name == 'lead_id' || $field_name === '') continue;
                $CFields[] = go_quote_custom_field_identifier($field_name);
            }
            $CFields = implode(',', $CFields);

            if ($CFields !== '') {
                $custom_info = go_get_custom_field_row($astDB, $custom_listid, $CFields, $lead_id);
            }
            }
        }
    }

    if ($leadIDExist > 0) {
        $goDB->where('lead_id', $lead_id);
        $rslt = $goDB->getOne('go_customers');
        $is_customer = $goDB->getRowCount();

        $APIResult = [ "result" => "success", "lead_info" => $lead_info, "custom_info" => $custom_info, "is_customer" => $is_customer ];
    } else {
        $APIResult = [ "result" => "error", "message" => "Lead ID '$lead_id' does NOT exist on the database" ];
    }
} else {
    $APIResult = [ "result" => "error", "message" => "You did NOT specify a valid Lead ID" ];
}
?>
