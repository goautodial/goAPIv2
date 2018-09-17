<?php
/**
 * @file        goEditAgentRank.php
 * @brief       API to Update Ingroup Agents 
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James F. Milo  <jerico@goautodial.com>
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

	$log_user 											= $session_user;
	$log_group 											= go_get_groupid($session_user, $astDB);
	$log_ip 											= $astDB->escape($_REQUEST['log_ip']);

	$goUser												= $astDB->escape($_REQUEST['goUser']);
	$goPass												= (isset($_REQUEST['log_pass']) ? $astDB->escape($_REQUEST['log_pass']) : $astDB->escape($_REQUEST['goPass'])); 	
	$goItemRank											= $astDB->escape($_REQUEST['itemrank']);
	$goIDgroup 											= $astDB->escape($_REQUEST['idgroup']);
	
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
	} elseif (empty($goIDgroup) || is_null($goIDgroup)) {
        $apiresults 									= array(
			"result" 										=> "Error: Set a value for Group ID."
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
			$itemsumitexplode 							= explode('&', $goItemRank);
			$group_id 									= $goIDgroup;

			for( $i = 0; $i < count( $itemsumitexplode ); $i++ ) {

				$itemsumitsplit 						= explode('=', $itemsumitexplode[$i]);
				$showval 								= htmlspecialchars(urldecode($itemsumitsplit[0]));
				$datavals 								= htmlspecialchars(urldecode($itemsumitsplit[1]));
				$finalvalues 							= $showval."||".$datavals.""; 

				if(preg_match("/CHECK/i", "$itemsumitexplode[$i]")) {					
					if (preg_match("/YES/i", "$itemsumitexplode[$i]")) {
						$checked 						= $itemsumitexplode[$i]."\n";	
						$repcheck 						= str_replace("CHECK_", "", $itemsumitexplode[$i]);
						$user 							= str_replace("=YES", "", $repcheck);
						
						$astDB->where("user", $user);
						$closer_campaigns 				= $astDB->getValue("vicidial_users", "closer_campaigns");
						//$query = "SELECT closer_campaigns FROM vicidial_users WHERE user='$user'";
						$closer_campaigns 				= rtrim($closer_campaigns,"-");
						$closer_campaigns 				= str_replace(" $group_id", "", $closer_campaigns);
						$closer_campaigns 				= trim($closer_campaigns);
						
						if (strlen($closer_campaigns) > 1) {
							$closer_campaigns 			= " $closer_campaigns";
						}
						
						$NEWcloser_campaigns 			= " $group_id{$closer_campaigns} -";
					} else {
						$checked 						= $itemsumitexplode[$i]."\n";	
						$repcheck						= str_replace("CHECK_", "", $itemsumitexplode[$i]);
						$user 							= str_replace("=NO", "", $repcheck);
						
						$astDB->where("user", $user);
						$closer_campaigns 				= $astDB->getValue("vicidial_users", "closer_campaigns");
						//$query2 = "SELECT closer_campaigns FROM vicidial_users WHERE user='$user'";
						$closer_campaigns 				= rtrim($closer_campaigns,"-");
						$closer_campaigns 				= str_replace(" $group_id", "", $closer_campaigns);
						$closer_campaigns 				= trim($closer_campaigns);
						$NEWcloser_campaigns 			= "{$closer_campaigns} -";
					}
					
					$datum 								= array(
						"closer_campaigns" 					=> $NEWcloser_campaigns
					);
					
					$astDB->where("user", $user);
					$astDB->update("vicidial_users", $datum);
					//$query3 = "UPDATE vicidial_users set closer_campaigns='$NEWcloser_campaigns' where user='$user';";
				}
				
				if (preg_match("/RANK/i", "$itemsumitexplode[$i]")) {
					$itemsumitsplit1 					= explode('=', $itemsumitexplode[$i]);
					$datavals1 							= htmlspecialchars(urldecode($itemsumitsplit1[1]));					
					$itemsexplode 						= explode("_",$itemsumitsplit1[0]);
					
					$data 								= array(
						"group_rank" 						=> $datavals1, 
						"group_weight" 						=> $datavals1
					);
					
					$astDB->where("user", "{$itemsexplode[1]}"); //CHECK
					$astDB->where("group_id", $group_id); //CHECK
					$astDB->update("vicidial_inbound_group_agents", $data);
					//$query4 = "UPDATE vicidial_inbound_group_agents SET group_rank='$datavals1',group_weight='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';";
					
					if ($datavals1 != 0) {
						$ranknotzero 					.= $itemsumitexplode[$i]."\n";
					}
				}
				
				if (preg_match("/GRADE/i", "$itemsumitexplode[$i]")) {
					$itemsumitsplit1 					= explode('=', $itemsumitexplode[$i]);
					$datavals1 							= htmlspecialchars(urldecode($itemsumitsplit1[1]));					
					$itemsexplode 						= explode("_",$itemsumitsplit1[0]);
					
					$datum 								= array(
						"group_grade" 						=> $datavals1
					);
					
					$astDB->where("user", "{$itemsexplode[1]}");
					$astDB->where("group_id", $group_id);
					$astDB->update("vicidial_inbound_group_agents", $datum);
					//$query5 = "UPDATE vicidial_inbound_group_agents SET group_grade='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';";
				}
				
				$log_id 								= log_action($goDB, "MODIFY", $log_user, $log_ip, "Modified Agent Rank(s) on Group $group_id", $log_group);
				
				$apiresults 							= array(
					"result" 								=> "success"
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
