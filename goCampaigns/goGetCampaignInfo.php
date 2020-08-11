<?php
/**
 * @file 		goGetCampaignInfo.php
 * @brief 		API for Carriers
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author     	Alexander Jim Abenoja 
 * @author     	Jerico James Milo
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

	include_once ("goAPI.php");
	
    $campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);

	//variables
	$campaign_type 										= '';
	$numberoflines 										= '';
	$custom_fields_launch 								= '';
	$custom_fields_list_id 								= '';
	$url_tab_first_title 								= '';
	$url_tab_first_url 									= '';
	$url_tab_second_title 								= '';
	$url_tab_second_url 								= '';
	$location_id 										= '';
	$dynamic_cid 										= '';
	$manual_dial_min_digits								= '';
	$auto_dial_level								= '';

    // Check campaign_id if its null or empty
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
    } else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {    
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}

			$astDB->where('campaign_id', $campaign_id);
			$result 									= $astDB->get('vicidial_campaigns');
			
			if ($astDB->count > 0) {
				$location_id_COL 						= '';
				$checkColumn 							= $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'location_id'");
				
				if ($goDB->count > 0) {
					$location_id_COL = ", location_id";
				}
				
				$dynamic_cid_COL 						= '';
				$checkColumn 							= $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'dynamic_cid'");

				if ($goDB->count > 0) {
					$dynamic_cid_COL 					= ", dynamic_cid";
				}
				
				$google_COL 							= '';
				$checkColumn 							= $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'google_sheet_ids'");

				if ($goDB->count > 0) {
					$google_COL 						= ", google_sheet_ids";
				}
				
				$country_code_COL 						= '';
				$checkColumn 							= $goDB->rawQuery("SHOW COLUMNS FROM `go_campaigns` LIKE 'default_country_code'");

				if ($goDB->count > 0) {
					$country_code_COL 					= ", default_country_code";
				}
				
				

				$rslt 								= $astDB
					->where('tld', '', '!=')
					->join('vicidial_country_iso_tld', 'country=iso3', 'left')
					->groupBy('country_code,country')
					->get('vicidial_phone_codes', null, 'country_code,country,tld,country_name');

				$country_codes 							= array();
				if ($astDB->count > 0) {
					foreach ($rslt as $country) {
						$country_id 					= "{$country['country']}_{$country['country_code']}";
						$country_codes[$country_id]['code'] 		= htmlentities(addslashes($country['country_code']));
						$country_codes[$country_id]['tld'] 		= htmlentities(addslashes($country['tld']));
						$country_codes[$country_id]['name'] 		= htmlentities(addslashes($country['country_name']));
					}			
				}	
				
				$goDB->where('campaign_id', $campaign_id);
				$fresultsv 							= $goDB->get('go_campaigns');

				if ($goDB->count > 0) {
					foreach ((array)$fresultsv as $fresults) {
						$campaign_type 					= $fresults['campaign_type'];
						$custom_fields_launch 				= $fresults['custom_fields_launch'];
						$custom_fields_list_id	 			= $fresults['custom_fields_list_id'];
						$url_tab_first_title 				= $fresults['url_tab_first_title'];
						$url_tab_first_url 				= $fresults['url_tab_first_url'];
						$url_tab_second_title 				= $fresults['url_tab_second_title'];
						$url_tab_second_url 				= $fresults['url_tab_second_url'];
						$enable_callback_alert				= $fresults['enable_callback_alert'];
						$cb_noexpire 					= $fresults['cb_noexpire'];
						$cb_sendemail					= $fresults['cb_sendemail'];
						$manual_dial_min_digits				= $fresults['manual_dial_min_digits'];
						$auto_dial_level				= $fresults['auto_dial_level'];
						
						if ($location_id_COL !== '') {
							$location_id 				= $fresults['location_id'];
						}
						
						if ($dynamic_cid_COL !== '') {
							$dynamic_cid 				= $fresults['dynamic_cid'];
						}
						
						if (!empty($google_COL)) {
							$google_sheet_ids			= $fresults['google_sheet_ids'];
							$google_sheet_list_id			= $fresults['google_sheet_list_id'];
						}
						
						if (!empty($country_code_COL)) {
							$default_country_code			= $fresults['default_country_code'];
						}
					}
					
					if ($campaign_type == "SURVEY") {
						$astDB->where('campaign_id', $campaign_id);
						$fresultsvRA 					= $astDB->get('vicidial_remote_agents');
						
						if ($astDB->count > 0) { 
							foreach ($fresultsvRA as $fresultsRA) {
								$numberoflines			= $fresultsRA['number_of_lines'];
							}					
						}
					}
				
					if ($google_COL !== '') {
						//$queryList = "SELECT list_id,list_name FROM vicidial_lists WHERE campaign_id='$campaign_id'";
						$astDB->where('campaign_id', $campaign_id);
						$rsltList = $astDB->get('vicidial_lists');
						if ($astDB->count > 0) {
							foreach ($rsltList as $listResult) {
								$list_id = $listResult['list_id'];
								$campaign_list_ids[$list_id] = $listResult['list_name'];
							}
						}
					}
					
					$custom_fields_launch 					= (gettype($custom_fields_launch) != 'NULL') ? $custom_fields_launch : 'ONCALL';
					$custom_fields_list_id 					= (gettype($custom_fields_list_id) != 'NULL') ? $custom_fields_list_id : '';
					$url_tab_first_title 					= (gettype($url_tab_first_title) != 'NULL') ? $url_tab_first_title : '';
					$url_tab_first_url 					= (gettype($url_tab_first_url) != 'NULL') ? $url_tab_first_url : '';
					$url_tab_second_title 					= (gettype($url_tab_second_title) != 'NULL') ? $url_tab_second_title : '';
					$url_tab_second_url 					= (gettype($url_tab_second_url) != 'NULL') ? $url_tab_second_url : '';
					$enable_callback_alert 					= (gettype($enable_callback_alert) != 'NULL') ? $enable_callback_alert : '';
					$cb_noexpire 						= (gettype($cb_noexpire) != 'NULL') ? $cb_noexpire : '';
					$cb_sendemail 						= (gettype($cb_sendemail) != 'NULL') ? $cb_sendemail : '';				
					$location_id 						= (gettype($location_id) != 'NULL') ? $location_id : '';
					$dynamic_cid 						= (gettype($dynamic_cid) != 'NULL') ? $dynamic_cid : '';
					$manual_dial_min_digits					= (gettype($manual_dial_min_digits) != 'NULL') ? $manual_dial_min_digits : '';
					$auto_dial_level					= (gettype($auto_dial_level) != 'NULL') ? $auto_dial_level : '';
					$google_sheet_ids					= (gettype($google_sheet_ids) != 'NULL') ? $google_sheet_ids : '';
					$campaign_list_ids					= (gettype($campaign_list_ids) != 'NULL') ? $campaign_list_ids : '';
					$google_sheet_list_id					= (gettype($google_sheet_list_id) != 'NULL') ? $google_sheet_list_id : '';
					$default_country_code					= (gettype($default_country_code) != 'NULL') ? $default_country_code : '';
					
					$apiresults 						= array(
						"result" 					=> "success",
						"data" 						=> array_shift($result),
						"campaign_type" 				=> $campaign_type,
						"custom_fields_launch" 				=> $custom_fields_launch,
						'custom_fields_list_id' 			=> $custom_fields_list_id,
						'url_tab_first_title' 				=> $url_tab_first_title,
						'url_tab_first_url' 				=> $url_tab_first_url,
						'url_tab_second_title' 				=> $url_tab_second_title,
						'url_tab_second_url' 				=> $url_tab_second_url,
						'enable_callback_alert'				=> $enable_callback_alert,
						'cb_noexpire'					=> $cb_noexpire,
						'cb_sendemail'					=> $cb_sendemail,
						'number_of_lines' 				=> $numberoflines,
						'location_id' 					=> $location_id,
						'dynamic_cid' 					=> $dynamic_cid,
						'manual_dial_min_digits'			=> $manual_dial_min_digits,
						'auto_dial_level'				=> $auto_dial_level,
						'google_sheet_ids'				=> $google_sheet_ids,
						'campaign_list_ids'				=> $campaign_list_ids,
						'google_sheet_list_id'				=> $google_sheet_list_id,
						'country_codes'					=> $country_codes,
						'default_country_code'				=> $default_country_code
					);
					
					$log_id 							= log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the info of campaign id: $campaign_id", $log_group);
								
				} else {
					$apiresults 						= array(
						"result" 							=> "success",
						"data" 								=> array_shift($result)
					);			
				}
			} else {
				$err_msg 								= error_handle("41004", "campaign_id");
				$apiresults 							= array(
					"code" 									=> "41004", 
					"result" 								=> $err_msg
				);
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>
