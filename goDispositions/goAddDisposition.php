<?php
 /**
 * @file 		goAddDisposition.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Chris Lomuntad
 * @author     	Jeremiah Sebastian Samatra 
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

 
	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);
    $campaign_id 										= $astDB->escape(($_REQUEST['campaign_id'] ?? ''));
	$category 											= "UNDEFINED"; //($_REQUEST['category'] ?? '');
	$userid 											= $astDB->escape(($_REQUEST['userid'] ?? ''));
	$status 											= $astDB->escape(($_REQUEST['status'] ?? ''));	
	$status_name 										= $astDB->escape(($_REQUEST['status_name'] ?? ''));
	$selectable 										= $astDB->escape(($_REQUEST['selectable'] ?? ''));
	$human_answered 									= $astDB->escape(($_REQUEST['human_answered'] ?? ''));
	$sale												= $astDB->escape(($_REQUEST['sale'] ?? ''));
	$dnc 												= $astDB->escape(($_REQUEST['dnc'] ?? ''));
	$customer_contact 									= $astDB->escape(($_REQUEST['customer_contact'] ?? ''));
	$not_interested 									= $astDB->escape(($_REQUEST['not_interested'] ?? ''));
	$unworkable 										= $astDB->escape(($_REQUEST['unworkable'] ?? ''));
	$scheduled_callback 								= $astDB->escape(($_REQUEST['scheduled_callback'] ?? ''));	
	$color 												= (isset($_REQUEST['color'])) ? $astDB->escape($_REQUEST['color']) : '#b5b5b5';
	$priority 											= (isset($_REQUEST['priority'])) ? $astDB->escape($_REQUEST['priority']) : 1;
	$type 												= (isset($_REQUEST['type'])) ? $astDB->escape($_REQUEST['type']) : 'CUSTOM';
    $defVal 											= [ "Y", "N" ];

	// ERROR CHECKING 
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
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= [
			"code" 											=> "40001",
			"result" 										=> $err_msg
		];
    }  elseif (empty($status) || is_null($status)) {
		$apiresults 									= [
			"result" 										=> "Error: Set a value for status."
		];
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', (string) $status_name) || $status_name == null){
		$err_msg 										= error_handle("10003", "status_name");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Special characters found in status name and must not be empty");
	} elseif (!in_array($scheduled_callback, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "scheduled_callback");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for scheduled_callback is Y or N only.");
	} elseif (!in_array($unworkable, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "unworkable");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for unworkable is Y or N only.");
	} elseif (!in_array($selectable, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "selectable");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for selectable is Y or N only.");
	} elseif (!in_array($human_answered, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "human_answered");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for human_answered is Y or N only.");
	} elseif (!in_array($sale, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "sale");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for sale is Y or N only.");
	} elseif (!in_array($dnc, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "dnc");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for dnc is Y or N only.");
	} elseif (!in_array($customer_contact, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "customer_contact");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for customer_contact is Y or N only.");
	} elseif (!in_array($not_interested, (is_array($defVal) ? $defVal : []))) {
		$err_msg 										= error_handle("10003", "not_interested");
		$apiresults 									= [
			"code" 											=> "10003", 
			"result" 										=> $err_msg
		];
		//$apiresults = array("result" 			=> "Error: Default value for not_interested is Y or N only.");
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			if (in_array($campaign_id, (is_array($campaigns) ? $campaigns : [])) || $campaign_id == 'ALL') {			
				$astDB->where('status', $status);
				$astDB->get('vicidial_statuses', null, 'status');
				
				if ($astDB->count <= 0) {
					if ($campaign_id == 'ALL') {
						$astDB->where("status", $status);
						$astDB->get("vicidial_campaign_statuses", NULL, "status");
						
						if($astDB->count < (is_countable($campaigns) ? count($campaigns) : 0)) {
							foreach ($campaigns as $campaignid) {
                                $astDB->where("campaign_id", $campaignid);
                                $astDB->where("status", $status);
                                $astDB->get("vicidial_campaign_statuses", NULL, "status");
                                
                                if ($astDB->count < 1) {
                                    $data					= [
                                        "status"				=> $status, 	
                                        "status_name"			=> $status_name,
                                        "selectable"			=> $selectable, 
                                        "campaign_id"			=> $campaignid,
                                        "human_answered"		=> $human_answered,
                                        "category"				=> $category,
                                        "sale"					=> $sale,
                                        "dnc"					=> $dnc,
                                        "customer_contact"		=> $customer_contact,
                                        "not_interested"		=> $not_interested,
                                        "unworkable"			=> $unworkable,
                                        "scheduled_callback"	=> $scheduled_callback
                                    ];
                                    
                                    $astDB->insert("vicidial_campaign_statuses", $data);
                                    $log_id 				= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Disposition $status on Campaign $campaignid", $log_group, $astDB->getLastQuery());							
                                    
                                    $tableQuery 			= "SHOW tables LIKE 'go_statuses';";
                                    $checkTable 			= $goDB->rawQuery($tableQuery);
    
                                    if ($checkTable) {
                                        $datago				= [
                                            "status"			=> $status, 	
                                            "campaign_id"		=> $campaignid,
                                            "priority"			=> $priority,
                                            "color"				=> $color,
                                            "type"				=> $type
                                        ];
                                        
                                        $qgo_insert			= $goDB->insert("go_statuses", $datago);
                                        $log_id 			= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Disposition $status on Campaign $campaignid", $log_group, $goDB->getLastQuery());							
                                    }
                                }
							}
							
							$apiresults 				= [
								"result" 					=> "success"
							];						
						} else {
							$err_msg 					= error_handle("41004", "status. Campaign Status already exists");
							$apiresults					= [
								"code" 						=> "41004", 
								"result" 					=> $err_msg
							];
						}
					} else {
						$astDB->where("campaign_id", $campaign_id);
						$astDB->where("status", $status);
						$astDB->get("vicidial_campaign_statuses", NULL, "status");
						
						if($astDB->count <= 0) {						
							$data						= [
								"status"					=> $status, 	
								"status_name"				=> $status_name,
								"selectable"				=> $selectable, 
								"campaign_id"				=> $campaign_id,
								"human_answered"			=> $human_answered,
								"category"					=> $category,
								"sale"						=> $sale,
								"dnc"						=> $dnc,
								"customer_contact"			=> $customer_contact,
								"not_interested"			=> $not_interested,
								"unworkable"				=> $unworkable,
								"scheduled_callback"		=> $scheduled_callback
							];
							
							$astDB->insert("vicidial_campaign_statuses", $data);
							$log_id 					= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Disposition $status on Campaign $campaign_id", $log_group, $astDB->getLastQuery());							
							
							$tableQuery 				= "SHOW tables LIKE 'go_statuses';";
							$checkTable 				= $goDB->rawQuery($tableQuery);

							if ($checkTable) {
								$datago					= [
									"status"				=> $status, 	
									"campaign_id"			=> $campaign_id,
									"priority"				=> $priority,
									"color"					=> $color,
									"type"					=> $type
								];
								
								$qgo_insert				= $goDB->insert("go_statuses", $datago);
								$log_id 				= log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Disposition $status on Campaign $campaign_id", $log_group, $goDB->getLastQuery());							
							}
							
							$apiresults 				= [
								"result" 					=> "success"
							];
						} else {
							$err_msg 					= error_handle("41004", "status. Campaign Status already exists");
							$apiresults					= [
								"code" 						=> "41004", 
								"result" 					=> $err_msg
							];
						}					
					}
				} else {
					$err_msg 							= error_handle("41004", "status. Status already exists in the default statuses");
					$apiresults 						= [
						"code" 								=> "41004", 
						"result" 							=> $err_msg
					];
				}
			} else {		
				$err_msg 								= error_handle("10108", "status. No campaigns available");
				$apiresults								= [
					"code" 									=> "10108", 
					"result" 								=> $err_msg
				];
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= [
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			];		
		}
	}
	
?>
