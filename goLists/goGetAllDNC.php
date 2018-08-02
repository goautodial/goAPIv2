<?php
/**
 * @file        goGetAllDNC.php
 * @brief       API to get all DNC or to search for a specific one
 * @copyright   Copyright (C) GOautodial Inc.
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
	$log_group 											= go_get_groupid( $session_user, $astDB );
	//$log_ip 											= $astDB->escape($_REQUEST['log_ip']);
	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);	
	$search 											= $astDB->escape($_REQUEST['search']);
	
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} else {
		if (is_array($campaigns)) {	
			$campaign_ids								= implode(",", $campaigns);
			
			$query = "(
				SELECT 
					a.phone_number as phone_number, 
					'' as campaign_id
					FROM vicidial_dnc a
			) UNION (
				SELECT 
					b.phone_number as phone_number,
					b.campaign_id as campaign_id
					FROM vicidial_campaign_dnc b
					WHERE phone_number LIKE '%$search%'
					AND campaign_id IN ($campaign_ids)
					LIMIT 100
			)";

			$rsltv 											= $astDB->rawQuery($query);
			$countResult = $astDB->getRowCount();
			
			if ($rsltv) {
				foreach ($rsltv as $fresults) {
					$dataPhoneNumber[]       				= $fresults['phone_number'];
					$dataCampaign[]       					= $fresults['campaign_id'];
				}
				
				$apiresults 								= array(
					"result"            						=> "success",
					"phone_number"      						=> $dataPhoneNumber,
					"campaign"									=> $dataCampaign
				);
			} else {
				$apiresults 								= array(
					"result" 									=> "Error: No record found."
				);
			}
		}
	}
	
?>
