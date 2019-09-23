<?php
 /**
 * @file 		goGetAllDialStatuses.php
 * @brief 		API for Dial Status
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Noel Umandap
 * @author     	Chris Lomuntad 
 * @author     	Alexander Jim Abenoja
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
	$is_selectable 										= $astDB->escape($_REQUEST['is_selectable']);
	$add_hotkey											= $astDB->escape($_REQUEST['add_hotkey']);
	$campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);
	
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
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			//if ((is_array($campaigns) && in_array($campaign_id, $campaigns)) || preg_match("/ALL/", $campaign_id)) {
                $cols                                   = array(
                    "status",
                    "status_name"
                );
                
				if ($is_selectable === "1") {
                    $astDB->where("selectable", "Y");
                }
                $astDB->orderBy("status", "desc");
                $rsltv                                  = $astDB->get("vicidial_statuses", NULL, $cols);
                
                if ($astDB->count > 0) {
                    foreach ($rsltv as $fresults){
                        $thisStatus                     = $fresults['status'];
                        //$dataStatusName[]               = $fresults['status_name'];
                        $systemStatuses[$thisStatus]    = $fresults['status_name'];
                    }
                }
                
                ksort($systemStatuses);
                foreach ($systemStatuses as $key => $status) {
                    $dataStatus['system'][]             = $key;
                    $dataStatusName['system'][]         = $status;
                }
				
				if ((is_array($campaigns) && in_array($campaign_id, $campaigns)) || preg_match("/ALL/", $campaign_id)) {
					$cols 								= array(
						"status", 
						"status_name"
					);
					
                    if ($is_selectable === "1") {
                        $astDB->where("selectable", "Y");
                    }
                    
					if (!preg_match("/ALL/", $campaign_id)) {
						$astDB->where("campaign_id", $campaign_id);
					}
					
					$astDB->orderBy("status", "desc");			
					$rsltv 								= $astDB->get("vicidial_campaign_statuses", NULL, $cols);			
							
					if ($astDB->count > 0) {
						foreach ($rsltv as $fresults){
                            $thisStatus                 = $fresults['status'];
							//$dataStatusName[] 			= $fresults['status_name'];
                            $campStatuses[$thisStatus]  = $fresults['status_name'];
						}		
					}			
				}
                
                ksort($campStatuses);
                foreach ($campStatuses as $key => $status) {
                    $dataStatus['campaign'][]           = $key;
                    $dataStatusName['campaign'][]       = $status;
                }
				
				$apiresults                             = array(
                    "result"                                                => "success",
                    "status"                                                => $dataStatus,
                    "status_name"                                           => $dataStatusName,
                );
			//} else {
			//	$err_msg 								= error_handle("10108", "status. No campaigns available");
			//	$apiresults								= array(
			//		"code" 									=> "10108", 
			//		"result" 								=> $err_msg
			//	);
			//}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
?>
