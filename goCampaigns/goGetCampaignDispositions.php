<?php
 /**
 * @file 		goGetCampaignDispositions.php
 * @brief 		API for getting dispositions for specific campaigns
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
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


	$campaigns											= allowed_campaigns($log_group, $goDB, $astDB);
	$campaign_id 										= $astDB->escape(($_REQUEST["campaign_id"] ?? ''));
		
    // Check campaign_id if its null or empty
	if (empty ($goUser) || is_null ($goUser)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI User Not Defined."
		];
	} elseif (empty ($goPass) || is_null ($goPass)) {
		$apiresults 									= [
			"result" 										=> "Error: goAPI Password Not Defined."
		];
	} elseif (empty ($log_user) || is_null ($log_user)) {
		$apiresults 									= [
			"result" 										=> "Error: Session User Not Defined."
		];
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= [
			"code" 											=> "40001",
			"result" 										=> $err_msg
		];
    } elseif (in_array($campaign_id, (is_array($campaigns) ? $campaigns : []))) {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {    
			$astDB->where("campaign_id", $campaign_id);
			$astDB->orderBy("campaign_id");
			$fresultsv 									= $astDB->get("vicidial_campaign_statuses");	
				
			if ($astDB->count > 0) {
				foreach ($fresultsv as $fresults) {
					$dataStat[] 						= $fresults["status"];
					$dataStatName[] 					= $fresults["status_name"];
					$dataSel[] 							= $fresults["selectable"];
					$dataCamp[] 						= $fresults["campaign_id"];
					$dataHumAns[] 						= $fresults["human_answered"];
					$dataCat[] 							= $fresults["category"];
					$dataSale[] 						= $fresults["sale"];
					$dataDNC[] 							= $fresults["dnc"];
					$dataCusCon[] 						= $fresults["customer_contact"];
					$dataNotInt[] 						= $fresults["not_interested"];
					$dataUnwork[] 						= $fresults["unworkable"];
					$dataSched[] 						= $fresults["scheduled_callback"];			
				}

				$apiresults 							= [
					"result" 								=> "success",
					"status" 								=> $dataStat,
					"status_name"							=> $dataStatName,
					"selectable"							=> $dataSel,
					"campaign_id"							=> $dataCamp,
					"human_answered"						=> $dataHumAns,
					"category"								=> $dataCat,
					"sale"									=> $dataSale,
					"dnc"									=> $dataDNC,
					"customer_contact"						=> $dataCusCon,
					"not_interested"						=> $dataNotInt,
					"unworkable"							=> $dataUnwork,
					"scheduled_callback"					=> $dataSched
				];

				//$log_id 								= log_action($goDB, 'VIEW', $log_user, $log_ip, "Viewed the info of campaign id: $campaign_id", $log_group); 		
			} else {
				$err_msg 								= error_handle("10108", "status. No campaigns available");
				$apiresults								= [
					"code" 									=> "10108", 
					"result" 								=> $err_msg
				];
			}
		} else {
			$err_msg 									= error_handle("10001", "Insufficient permision");
			$apiresults 								= [
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			];			
		}
	}
	
?>

