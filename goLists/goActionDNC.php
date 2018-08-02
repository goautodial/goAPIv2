<?php
/**
 * @file        goActionDNC.php
 * @brief       API to Add or Delete DNC
 * @copyright   Copyright (C) GOautodial Inc.
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

	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB);
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);
	$allowed_campaigns 								= allowed_campaigns($log_group, $goDB, $astDB);
	
	$campaign_id 									= $astDB->escape($_REQUEST['campaign_id']);
	$phone_numbers									= rawurldecode($_REQUEST['phone_numbers']);
	$phone_numbers									= explode("\r\n", $phone_numbers);
	//$phone_numbers 								= str_replace(" ", "\n", rawurldecode($astDB->escape($_REQUEST['phone_numbers'])));
	$stage 											= $astDB->escape($_REQUEST['stage']);	
	//$cnt 											= 0;
	
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif ( empty($allowed_campaigns) || is_null($allowed_campaigns) ) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
	} elseif ( empty($phone_numbers) || is_null($phone_numbers) ) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
    } elseif ($stage == "ADD") {
		$error_count								= 0;
		foreach ($phone_numbers as $phone_number) {	
			$dnc									= $phone_number;
			if ($campaign_id == "INTERNAL" && $log_group == "ADMIN") {
				$astDB->where('phone_number', $dnc);
				$idnc 								= $astDB->getOne('vicidial_dnc', 'phone_number');				
			
				if ($idnc) {
					$error_count 					= 1;
				} else {					
					$astDB->insert( 'vicidial_dnc', array( 'phone_number' => $dnc) );
					
					$log_id 						= log_action($goDB, $stage, $log_user, $log_ip, "$dnc added to internal DNC list", $log_group, $astDB->getLastQuery());				
				}								
			} elseif ($campaign_id == "INTERNAL" && $log_group != "ADMIN") {
				$error_count 						= 2;					
			} elseif ($campaign_id != "INTERNAL") {
				$cdnc_exist 						= $astDB
					->where('phone_number', $dnc)
					->where('campaign_id', $campaign_id)
					->get('vicidial_campaign_dnc', null, 'phone_number');
					
				if (!$cdnc_exist) {
					$error_count 					= 0;
					$data							= array(
						'phone_number' 					=> $dnc,
						'campaign_id'					=> $campaign_id
					);
					
					$astDB->insert('vicidial_campaign_dnc', $data);
					
					$log_id 						= log_action($goDB, $stage, $log_user, $log_ip, "$dnc added to internal DNC list", $log_group, $astDB->getLastQuery());
				} else {
					$error_count 					= 1;					
				}				
			}
			
			if ( $error_count == 0 ) { 
				$apiresults 						= array ( 
					"result" 							=> "success"
				); 
			}	
			
			if ( $error_count == 1 ) {
				$err_msg 							= error_handle( "10116", " internal DNC list" );
				$apiresults 						= array(
					"code" 								=> "10116", 
					"result" 							=> $err_msg
				);
			}	
			
			if ( $error_count == 2 ) {
				$err_msg 							= error_handle( "10001", "Insufficient permision. You must belong to the ADMIN user group" );
				$apiresults 						= array(
					"code" 								=> "10001", 
					"result" 							=> $err_msg
				);	
			}			
		}
				
	} elseif ($stage == "DELETE") {
		$error_count 								= 0;
		foreach ($phone_numbers as $phone_number) {	
			$dnc									= $phone_number;
			if ($campaign_id == "INTERNAL" || $campaign_id == "" && $log_group == "ADMIN") {
				$idnc_exist 						= $astDB
					->where('phone_number', $dnc)
					->get('vicidial_dnc', null, 'phone_number');				
			
				if ($idnc_exist) {
					$astDB->where('phone_number', $dnc);
					$astDB->delete('vicidial_dnc');
					
					$log_id 						= log_action($goDB, $stage, $log_user, $log_ip, "$dnc removed from internal DNC list", $log_group, $astDB->getLastQuery());

				} else {
					$error_count 					= 1;			
				}						
			} elseif ($campaign_id == "INTERNAL" && $log_group != "ADMIN") {
				$error_count 						= 2;						
			} elseif ($campaign_id != "INTERNAL") {
				
				$cdnc_exist 						= $astDB
					->where('phone_number', $dnc)
					->where('campaign_id', $campaign_id)
					->get('vicidial_campaign_dnc', null, 'phone_number');
					
				if ($cdnc_exist) {
					$error_count 					= 0;
					$data							= array(
						'phone_number' 					=> $dnc,
						'campaign_id'					=> $campaign_id
					);
					
					$astDB->delete('vicidial_campaign_dnc', $data);
					
					$log_id 						= log_action($goDB, $stage, $log_user, $log_ip, "$dnc removed from campaign ID: $campaign_id DNC list", $log_group, $astDB->getLastQuery());
					
				} else {
					$error_count 					= 1;					
				}				
			}	
			
			if ( $error_count == 0 ) { 
				$apiresults 						= array ( 
					"result" 							=> "success"
				); 
			}	
			
			if ( $error_count == 1 ) {
				$err_msg 							= error_handle("10117");
				$apiresults 						= array(
					"code" 								=> "10117", 
					"result" 							=> $err_msg
				);	
			}	
			
			if ( $error_count == 2 ) {
				$err_msg 							= error_handle( "10004", " goAPIs. You must belong to the ADMIN user group" );
				$apiresults 						= array(
					"code" 								=> "10004", 
					"result" 							=> $err_msg
				);
			}			
		}		
	} 

?>
