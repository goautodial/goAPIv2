<?php
 /**
 * @file 		goGetLiveInbound.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Jeremiah Sebastian Samatra
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


	$campaigns 											= array_values(array_filter((array) allowed_campaigns($log_group, $goDB, $astDB), static fn($campaign) => (string) $campaign !== ''));

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
		$userlevel										= (int) ($fresults["user_level"] ?? 0);

		if ($goapiaccess > 0 && $userlevel > 7) {
			if (is_array($campaigns)) {
				if ($campaigns === []) {
					$apiresults = ["result" => "success", "data" => 0];
					return;
				}
				$data									= $astDB
					->where("campaign_id", $campaigns, "IN")
					->where("vla.user = vu.user")
					->where("vu.user_level", 4, "!=")
					->where("status = 'INCALL'")
					->where("comments = 'INBOUND'")
					->getValue("vicidial_live_agents as vla,vicidial_users as vu", "count(*)");

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
