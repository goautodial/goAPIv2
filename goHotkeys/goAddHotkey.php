<?php
/**
 * @file        goAddHotkey.php
 * @brief       API to add new hotkey
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Noel Umandap
 * @author      Alexander Jim Abenoja
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
 
	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB); 
	$log_ip 											= $astDB->escape($_REQUEST['log_ip']);
	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass']));	
	
	### POST or GET Variables
	$campaign_id 										= $astDB->escape($_REQUEST["campaign_id"]);	
    $hotkey         									= $astDB->escape($_REQUEST['hotkey']);
    $status         									= $astDB->escape($_REQUEST['status']);
    $status_name    									= $astDB->escape($_REQUEST['status_name']);

    
	// ERROR CHECKING 
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for Campaign ID."
		);
	} elseif (empty($hotkey) || is_null($hotkey)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Set a value for hotkey.");
	} elseif (empty($status) || is_null($status)) {
		$err_msg 										= error_handle("40001");
		$apiresults 									= array(
			"code" 											=> "40001", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Set a value for status.");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status_name) || empty($status_name)) {
		$err_msg 										= error_handle("10003", "status_name");
		$apiresults 									= array(
			"code" 											=> "10003", 
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
			$astDB->where('campaign_id', $campaign_id);
			$astDB->where('hotkey', $hotkey);
			//$astDB->orwhere('status', $status);
			$hotkeys 									= $astDB->get('vicidial_campaign_hotkeys');
			
			if (count($hotkeys) > 0) {
				$apiresults 							= array(
					"result" 								=> "Duplicate Hotkey!");
			} else {
				$data_insert 							= array(
					'status'        						=> $status,
					'hotkey'        						=> $hotkey,
					'status_name'   						=> $status_name,
					'selectable'    						=> 'Y',
					'campaign_id'   						=> $campaign_id
				);
				
				$insertHotkey 							= $astDB->insert('vicidial_campaign_hotkeys', $data_insert);
				
				if ($insertHotkey) {
					$log_id 							= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Hotkey $hotkey on Campaign $campaign_id", $log_group, $astDB->getLastQuery());
					
					$apiresults 						= array(
						"result" 							=> "success"
					);
				} else {
					$apiresults 						= array(
						"result" 							=> "Error: Failed to add campaign hotkey."
					);
				}
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
