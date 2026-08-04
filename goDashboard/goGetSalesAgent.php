<?php
/**
 * @file        goGetSalesAgent.php
 * @brief       API for Sales Agent Report on Dashboard for Statewide
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author		Thom Bernarth D. Patacsil
 * @author		Demian Lizandro A. Biscocho
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it AND/or modify
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

    include_once(__DIR__ . "/goAPI.php");

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


    $fromDate 											= $astDB->escape(($_REQUEST['fromDate'] ?? date("Y-m-d")));
    $toDate 											= $astDB->escape(($_REQUEST['toDate'] ?? date("Y-m-d")));
    $campaignID											= $astDB->escape(($_REQUEST['campaign_id'] ?? 'ALL'));

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
	} elseif ( empty($campaignID) || is_null($campaignID) ) {
		$err_msg 										= error_handle("40001");
        	$apiresults 								= [
			"code" 											=> "40001",
			"result" 										=> $err_msg
		];
	} else {
		// check if goUser and goPass are valid
		$fresults											= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");

		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= is_array($fresults) ? ($fresults["user_level"] ?? 0) : 0;

		if ($goapiaccess > 0 && $userlevel > 7) {
			$cols 										= [
				"user",
				"full_name",
				"sum(sales) as sale",
				"sum(amount) as amount"
			];

			$sql_sales = $goDB->where('entry_date', [$fromDate, $toDate], 'BETWEEN')
					//->where('amount', 0, '>')
					->groupBy('user')
					->get('go_sales_count', null, $cols);

			$apiresults = [
				"result"									=> "success",
				"amount"									=> $sql_sales,
			];
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= [
				"code" 									=> "10001",
				"result" 								=> $err_msg
			];
		}
	}

?>
