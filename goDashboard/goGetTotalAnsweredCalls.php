<?php
 /**
 * @file 		goGetTotalAnsweredCalls.php
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


$allowed_campaigns									= allowed_campaigns($log_group, $goDB, $astDB);
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
	/*$astDB->where('user_group', $log_group);
	$allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
	$allowed_campaigns = $allowed_camps['allowed_campaigns'];
	$allowed_campaigns = explode(" ", trim($allowed_campaigns));*/

	// check if goUser and goPass are valid
	$fresults										= $astDB
		->where("user", $goUser)
		->where("pass_hash", $goPass)
		->getOne("vicidial_users", "user,user_level");

	$goapiaccess									= $astDB->getRowCount();
	$userlevel										= $fresults["user_level"];

	if ($goapiaccess > 0 && $userlevel > 7) {
		if (is_array($allowed_campaigns)) {
			if ($log_group !== "ADMIN") {
				$astDB->where("campaign_id", $allowed_campaigns, "IN");
			}

			//$astDB->where("update_time", array("$NOW 09:00:00", "$NOW 21:00:00"), "BETWEEN")
			$astDB->where("update_time", ["$NOW 00:00:00", "$NOW 23:59:59"], "BETWEEN");
			$data									= $astDB->getValue("vicidial_campaign_stats", "sum(answers_today)");
			//$err 									= $astDB->getLastQuery();
			$apiresults 							= [
				"result" 								=> "success",
				"data" 									=> $data
				//"query" => $err
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
