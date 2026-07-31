<?php
/**
 * @file        goDeleteAreacode.php
 * @brief       API to delete a specific areacode
 * @copyright   Copyright (C) 2019 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
 
	### POST or GET Variables
	$campaign_id 										= $astDB->escape($_REQUEST["campaign_id"]);	
    	$areacode 										= $astDB->escape($_REQUEST["areacode"]);
    	$outbound_cid                                   = $astDB->escape($_REQUEST["outbound_cid"]);
    
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
		$apiresults 									= [
			"result" 										=> "Error: Set a value for Campaign ID."
		];
	} elseif (empty($areacode) || is_null($areacode)) {
                $apiresults                                                                     = [
                        "result"                                                                                => "Error: Set a value for Areacode."
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
			$cols 										= [
				"campaign_id", 
				"areacode",
                "outbound_cid"
			];
		
			$astDB->where("campaign_id", $campaign_id);
			$astDB->where("areacode", $areacode);
			$astDB->where("outbound_cid", $outbound_cid);
			$checkPC									= $astDB->get("vicidial_campaign_cid_areacodes", null, $cols);
			
			if ($checkPC) {
				$astDB->where("campaign_id", $campaign_id);
				$astDB->where("areacode", $areacode);
                $astDB->where("outbound_cid", $outbound_cid);
				$astDB->delete("vicidial_campaign_cid_areacodes");

				$log_id 								= log_action($goDB, "DELETE", $log_user, $log_ip, "Deleted Areacode: $areacode from Campaign ID $campaign_id", $log_group, $astDB->getLastQuery());
				
				$apiresults 							= [
					"result" 								=> "success"
				];
			} else {
				$apiresults 							= [
					"result" 								=> "Error: Areacode doesn't exist."
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
