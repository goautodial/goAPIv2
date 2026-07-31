<?php
 /**
 * @file 		goGetTotalAgentsCall.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
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
 
	// $campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);

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
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {
			//get all allowed campaigns
			$userGroupCamps                             = $astDB->where("user_group", $log_group)
                ->get("vicidial_user_groups", null, ['allowed_campaigns']);

            foreach ($userGroupCamps as $key) {
                $camps = $key["allowed_campaigns"];
            }

            if (preg_match("/ALL-CAMPAIGNS/", $camps)) {
                $campQuery                              = $astDB->where('active', 'Y')
                    ->get('vicidial_campaigns', null, ['campaign_id']);

                foreach ($campQuery as $key) {
                    $campaigns[]    = $key["campaign_id"];
                }   
            } else {
                $trimCamps  = trim($camps, " -");
                $campaigns = explode(" ", $trimCamps);
            }

			if (is_array($campaigns)) {
			// 	if (strtoupper($log_group) != 'ADMIN') {
			// 		if ($userlevel < 9) {
            //             $astDB->where("user_group", $log_group);
			// 		}
			// 	}
                
				$calls 									= [ 'INCALL', 'QUEUE', '3-WAY', 'PARK' ];		
				$data									= $astDB
					->where("campaign_id", $campaigns, "IN")
					->where("status", $calls, "IN")
					->where("user_level", 4, "!=")
					->getValue("vicidial_live_agents", "count(*)");
				
				$apiresults 							= [
					"result" 								=> "success", 
					"data" 									=> $data
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
