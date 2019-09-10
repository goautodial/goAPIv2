<?php
/**
 * @file        goDeleteList.php
 * @brief       API to delete specific List
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	
    include_once ("goAPI.php");
        
    // POST or GET Variables
    $list_ids 											= $_REQUEST['list_id'];
	//$action 											= strtolower($astDB->escape($_REQUEST['action']));
	$action												= "delete_selected";
    
	// Error Checking
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
	} elseif (empty($list_ids) || is_null($list_ids)) {
		$err_msg 										= error_handle("10107");
		$apiresults 									= array(
			"code" 											=> "10107", 
			"result" 										=> $err_msg
		); 
	} elseif ($action == "delete_selected") {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			foreach ($list_ids as $list_id) {
				$listid									= $list_id;
				
				$astDB->where('list_id', $listid);
				$astDB->getOne('vicidial_lists', 'list_id');
				
				if ($astDB->count > 0) {
					$astDB->where('list_id', $listid);
					$astDB->delete('vicidial_lists');
					
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted List ID: $dataListID", $log_group, $astDB->getLastQuery());
					
					$astDB->where('list_id', $listid);
					$astDB->delete('vicidial_list');
					
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted List ID: $dataListID", $log_group, $astDB->getLastQuery());
					
					$astDB->where('list_id', $listid);
					$astDB->delete('vicidial_lists_fields', 1);					
			
					$log_id 							= log_action($goDB, 'DELETE', $log_user, $log_ip, "Deleted List ID: $dataListID", $log_group, $astDB->getLastQuery());
					
					$apiresults 						= array(
						"result" 							=> "success"
					);
				} else {				
					$apiresults 						= array(
						"result" 							=> "Error: List doesn't exist."
					);
				}
			}
			
			$apiresults 								= array(
				"result" 									=> "success"
			);			
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
