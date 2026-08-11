 <?php
 /**
 * @file        goListExport.php
 * @brief       API to export list
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho
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

	ini_set('memory_limit', '2048M');
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


	$list_id 											= $astDB->escape(($_REQUEST["list_id"] ?? ''));
	$limit 												= $astDB->escape(($_REQUEST['limit'] ?? ''));
	$offset 											= $astDB->escape(($_REQUEST['offset'] ?? ''));

	if($limit != NULL && $offset != NULL){
		$limit_SQL = "LIMIT $offset, $limit";
	} else {
		$limit_SQL = "";
	}

	$csv_row = "";

	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI User Not Defined."
		];
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI Password Not Defined."
		];
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= [
			"result" 										=> "Error: Session User Not Defined."
		];
	} elseif (empty($list_id) || is_null($list_id)) {
		$err_msg 										= error_handle("10107");
        $apiresults 									= [
			"code" 											=> "10107",
			"result" 										=> $err_msg
		];
    } else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");

		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= is_array($fresults) ? (int) ($fresults["user_level"] ?? 0) : 0;

		if ($goapiaccess > 0 && $userlevel > 7) {
			$fetch 										= $astDB->getOne('system_settings', 'custom_fields_enabled');
			$custom_fields_enabled 						= is_array($fetch) ? (int) ($fetch["custom_fields_enabled"] ?? 0) : 0;
			$added_custom_SQL  							= "";
			$added_custom_SQL2 							= "";
			$added_custom_SQL3  						= "";
			$added_custom_SQL4 							= "";

			if ($custom_fields_enabled > 0) {
				$custom_list_id 						= preg_replace('/[^0-9]/', '', (string) $list_id);
				$custom_table 							= "custom_".$custom_list_id;
				$cllist 								= [];
				$clcount 								= 0;
				$header_columns 						= "";

				if ($custom_list_id !== '') {
					$tableCheck 						= $astDB->rawQuery("SHOW TABLES LIKE '{$custom_table}'");
					if (is_array($tableCheck) && count($tableCheck) > 0) {
						$cllist_query 					= "SHOW COLUMNS FROM `$custom_table`;";
						$cllist 						= $astDB->rawQuery($cllist_query);
						$clcount 						= $astDB->getRowCount();
					}
				}

				foreach ((is_array($cllist) ? $cllist : []) as $clrow) {
					$fieldName = is_array($clrow) ? (string) ($clrow['Field'] ?? '') : '';
					if ($fieldName !== '' && $fieldName != 'lead_id' && preg_match('/^[A-Za-z0-9_]+$/', $fieldName)) {
						$header_columns 				.= ",ct.`".$fieldName."`";
					}
				}

				if ($clcount > 0) {
					$added_custom_SQL  					= ", `$custom_table` ct";
					$added_custom_SQL2 					= "AND vl.lead_id=ct.lead_id";
					$added_custom_SQL3  				= "`$custom_table` ct";
					$added_custom_SQL4 					= "vl.lead_id=ct.lead_id";
				}
			}

			if ($added_custom_SQL3 !== "") {
				$stmt 									= "SELECT vl.lead_id AS lead_id,vl.entry_date,vl.modify_date,vl.status,vl.user,vl.vendor_lead_code,vl.source_id,vl.list_id,vl.gmt_offset_now,vl.called_since_last_reset,vl.phone_code,vl.phone_number,vl.title,vl.first_name,vl.middle_initial,vl.last_name,vl.address1,vl.address2,vl.address3,vl.city,vl.state,vl.province,vl.postal_code,vl.country_code,vl.gender,vl.date_of_birth,vl.alt_phone,vl.email,vl.security_phrase,vl.comments,vl.called_count,vl.last_local_call_time,vl.rank,vl.owner $header_columns FROM vicidial_list vl LEFT OUTER JOIN $added_custom_SQL3 ON $added_custom_SQL4 WHERE vl.list_id='$list_id' $limit_SQL;";
			} else {
				$stmt 									= "SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner FROM vicidial_list WHERE list_id='$list_id' $limit_SQL; ";
			}

			$dllist 									= $astDB->rawQuery($stmt);
			$dllist 									= is_array($dllist) ? $dllist : [];
			$header 									= isset($dllist[0]) && is_array($dllist[0]) ? array_keys($dllist[0]) : [];

			$u											= 0;
			$x											= 0;
			$row										= [];
			$count_header 								= (is_countable($header) ? count($header) : 0);

			foreach ($dllist as $fetch_row) {
				if ($header === []) {
					continue;
				}
				$array_fetch 							= $fetch_row[$header[0]] ?? '';
				$u += 1;

				while ($u < $count_header) {
					$fieldValue = $fetch_row[$header[$u]] ?? '';
					$array_fetch 						.= "|".mb_convert_encoding((string) $fieldValue, 'UTF-8', 'ISO-8859-1');
					$u++;
				}

				$explode_array 							= explode("|",(string) $array_fetch);
				$row[$x] 								= $explode_array;
				$array_fetch 							= "";
				$u 										= 0;
				$x++;

				$data_row = implode(',', $explode_array);
 	                        $csv_row .= $data_row . "\n";
			}

			//$data_row = implode(',', $row);
			//$csv_row .= $data_row . "\n";

			$apiresults 								= [
				"result" 									=> "success",
				"header" 									=> $header,
				"row" 										=> $csv_row,
				"query" 									=> $stmt,
				"query_custom_list" 						=> $custom_table ?? ''
			];
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= [
				"code" 										=> "10001",
				"result" 									=> $err_msg
			];
		}
	}


?>
