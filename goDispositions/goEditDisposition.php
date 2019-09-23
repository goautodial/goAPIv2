<?php
 /**
 * @file 		goEditDisposition.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Jeremiah Sebastian Samatra
 * @author     	Chris Lomuntad
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
 
	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);
    $campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);
	$status 											= $astDB->escape($_REQUEST['status']);	
	$status_name 										= $astDB->escape($_REQUEST['status_name']);
	$selectable 										= $astDB->escape($_REQUEST['selectable']);
	$human_answered 									= $astDB->escape($_REQUEST['human_answered']);
	$sale 												= $astDB->escape($_REQUEST['sale']);
	$dnc												= $astDB->escape($_REQUEST['dnc']);
	$customer_contact 									= $astDB->escape($_REQUEST['customer_contact']);
	$not_interested 									= $astDB->escape($_REQUEST['not_interested']);
	$unworkable 										= $astDB->escape($_REQUEST['unworkable']);
	$scheduled_callback 								= $astDB->escape($_REQUEST['scheduled_callback']);	
	$priority 											= $astDB->escape($_REQUEST['priority']);
	$color 												= $astDB->escape($_REQUEST['color']);
	$edit_type 											= $astDB->escape($_REQUEST['type']);	
	$type 												= (!in_array($edit_type, array('SYSTEM', 'CUSTOM'))) ? 'CUSTOM' : $edit_type;
    $defVal 											= array("Y","N");

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
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
    }  elseif (empty($status) || is_null($status)) {
		$apiresults 									= array(
			"result" 										=> "Error: Set a value for status."
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status_name) && $status_name != null){
		$apiresults 									= array(
			"result" 										=> "Error: Special characters found in status name and must not be empty"
		);
	} elseif (!in_array($scheduled_callback,$defVal) && $scheduled_callback != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for scheduled_callback is Y or N only."
		);
	} elseif (!in_array($unworkable,$defVal) && $unworkable != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for unworkable is Y or N only."
		);
	} elseif (!in_array($selectable,$defVal) && $selectable != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for selectable is Y or N only."
		);
	} elseif (!in_array($human_answered,$defVal) && $human_answered != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for human_answered is Y or N only."
		);
	} elseif (!in_array($sale,$defVal) && $sale != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for sale is Y or N only."
		);
	} elseif (!in_array($dnc,$defVal) && $dnc != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for dnc is Y or N only."
		);
	} elseif (!in_array($customer_contact,$defVal) && $customer_contact != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for customer_contact is Y or N only."
		);
	} elseif (!in_array($not_interested,$defVal) && $not_interested != NULL) {
		$apiresults 									= array(
			"result" 										=> "Error: Default value for not_interested is Y or N only."
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
			if (is_array($campaigns) && in_array($campaign_id, $campaigns)) {
				$astDB->where("campaign_id", $campaign_id);
				$astDB->where('status', $status);
				//$astDB->getOne("vicidial_campaign_statuses", "campaign_id");
				$sqlCheck 								= $astDB->get('vicidial_campaign_statuses');
		
				if ($astDB->count > 0) {
					foreach ($sqlCheck as $fresults){
						$dataStat 						= $fresults['status'];
						$dataStatName 					= $fresults['status_name'];
						$dataSel 						= $fresults['selectable'];
						$dataCamp 						= $fresults['campaign_id'];
						$dataHumAns 					= $fresults['human_answered'];
						$dataCat 						= $fresults['category'];
						$dataSale 						= $fresults['sale'];
						$dataDNC 						= $fresults['dnc'];
						$dataCusCon 					= $fresults['customer_contact'];
						$dataNotInt 					= $fresults['not_interested'];
						$dataUnwork 					= $fresults['unworkable'];
						$dataSched 						= $fresults['scheduled_callback'];				
					}		

					if ($status_name == null) {
						$status_name 					= $dataStatName;
					}
					
					if ($selectable == null) { 
						$selectable 					= $dataSel;
					}
					
					if ($human_answered == null) { 
						$human_answered 				= $dataHumAns;
					}
					
					if ($sale == null) { 
						$sale 							= $dataSale;
					}
					
					if ($dnc == null) {
						$dnc 							= $dataDNC;
					}
					
					if ($customer_contact == null) {
						$customer_contact 				= $dataCusCon;
					}
					
					if ($not_interested == null) {
						$not_interested 				= $dataNotInt;
					}
					
					if ($unworkable == null) {
						$unworkable 					= $dataUnwork;
					}
					
					if ($scheduled_callback == null) {
						$scheduled_callback 			= $dataSched;
					}

					$updateData 						= array(
						'status_name' 						=> $status_name,
						'selectable' 						=> $selectable,
						'human_answered' 					=> $human_answered,
						'category' 							=> 'UNDEFINED',
						'sale' 								=> $sale,
						'dnc' 								=> $dnc,
						'customer_contact' 					=> $customer_contact,
						'not_interested' 					=> $not_interested,
						'unworkable' 						=> $unworkable,
						'scheduled_callback' 				=> $scheduled_callback
					);
					
					$astDB->where('status', $status);
					$astDB->where('campaign_id', $campaign_id);
					
					$rsltv1 							= $astDB->update('vicidial_campaign_statuses', $updateData);
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified dispositions on campaign $campaign_id", $log_group, $astDB->getLastQuery());
					
					if ($rsltv1 == false) {
						$apiresults 					= array(
							"result" 						=> "Error: Try updating Disposition Again"
						);
					} else {
						$apiresults 					= array(
							"result" 						=> "success"
						);
						
						$statusRslt 					= $goDB->rawQuery("SHOW TABLES LIKE 'go_statuses'");
						
						if ($goDB->count > 0) {
							$goDB->where('status', $status);
							$goDB->where('campaign_id', $campaign_id);
							$goDB->get('go_statuses');
							
							if ($goDB->count > 0) {
								$updateData 			= array(
									'priority' 				=> $priority,
									'color' 				=> $color,
									'type' 					=> $type
								);
								
								$goDB->where('status', $status);
								$goDB->where('campaign_id', $campaign_id);
								$goDB->update('go_statuses', $updateData);
								$log_id 				= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified dispositions on campaign $campaign_id", $log_group, $goDB->getLastQuery());
								
							} else {
								$insertData 			= array(
									'status' 				=> $status,
									'campaign_id' 			=> $campaign_id,
									'priority' 				=> $priority,
									'color' 				=> $color,
									'type' 					=> $type
								);
								
								$goDB->insert('go_statuses', $insertData);
								$log_id 				= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified dispositions on campaign $campaign_id", $log_group, $goDB->getLastQuery());
							}
						}														
					}
				} else {
					$apiresults 						= array(
						"result" 							=> "Error: Campaign Status doesn't exist"
					);
				}
			} else {
				$apiresults 							= array(
					"result" 								=> "Error: Campaign Status doesn't exist"
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
