<?php
 /**
 * @file 		goGetRealtimeCallsMonitoring.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author      Jericho James Milo
 * @author     	Demian Lizandro A. Biscocho
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

    include_once (__DIR__ . "/goAPI.php");
 
	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);

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
	} else {
        $astDB->where('user_group', $log_group);
        $allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
        $allowed_campaigns = $allowed_camps['allowed_campaigns'];
        $allowed_campaigns = explode(" ", trim($allowed_campaigns));
        
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			$cols										= [
				"status",
				"phone_number",
				"call_type",
				"UNIX_TIMESTAMP(call_time) as call_time",
				"vac.campaign_id"
			];
			
			$table 										= "vicidial_auto_calls as vac, vicidial_campaigns as vc, vicidial_inbound_groups as vig ";
            if (!preg_match("/ALL-CAMPAIGN/", $allowed_camps['allowed_campaigns'])) {
                $astDB->where("vac.campaign_id", $allowed_campaigns, "IN");
            }
			$rsltv										= $astDB
				->groupBy("status,call_type,phone_number")	
				->get($table, 1000, $cols);
			
			if ($astDB->count > 0) {
				$data 									= [];				
				foreach ($rsltv as $fresults) {       
					$data[] = $fresults;
				}
				
				$apiresults 							= [
					"result" 								=> "success", 
					//"query"								=> $astDB->getLastQuery(),
					"data" 									=> $data
				];
			} else {			
				$apiresults 							= [
					"result" 								=> "success", 
					"data" 									=> 0
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
