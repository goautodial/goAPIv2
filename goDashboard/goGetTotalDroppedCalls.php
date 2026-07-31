<?php
/**
 * @file 		goGetTotalDroppedCalls.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (c) 2026 GOautodial Inc.
 * @author		Demian Lizandro Biscocho
 * @author		Jeremiah Sebastian Samatra
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
$NOW 												= date("Y-m-d");

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
		if (is_array($campaigns)) {
			if ($log_group !== "ADMIN") {
				$astDB->where("campaign_id", $campaigns, "IN");
			}

			//$astDB->where("campaign_id", $campaigns, "IN");
			$astDB->where("call_date", ["$NOW 00:00:00", "$NOW 23:59:59"], "BETWEEN");
			$astDB->where("status", ["DROP", "IVRXFR"], "IN");
			$data_out 								= $astDB->getValue("vicidial_log", "count(lead_id)");

			$getIngroups                        	= $astDB->where('user_group', $log_group)
				->get('vicidial_inbound_groups', NULL, ['group_id']);

			$ingroups                           	= [];
			foreach ($getIngroups as $fresults) {
				$ingroups[]                     	= $fresults['group_id'];
			}

			if ($log_group !== "ADMIN") {
				$astDB->where("campaign_id", $ingroups, "IN");
			}
			//$astDB->where("campaign_id", $campaigns, "IN");
			$astDB->where("call_date", ["$NOW 00:00:00", "$NOW 23:59:59"], "BETWEEN");
			$astDB->where("status", ["XDROP", "TIMEOT", "NANQUE", "PDROP", "MAXCAL", "INBND"], "IN");

			$data_in 								= $astDB->getValue("vicidial_closer_log", "count(lead_id)");
			$data 									= intval($data_out) + intval($data_in);

			$apiresults 							= [
				"result" 								=> "success",
				//"query"								=> $astDB->getLastQuery(),
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
