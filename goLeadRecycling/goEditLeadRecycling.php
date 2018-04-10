<?php
 /**
 * @file       goEditLeadRecycling.php
 * @brief 	    API for Modifying Lead Recycling
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
 * @author     Chris Lomuntad  <chris@goautodial.com>
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

 // POST or GET Variables
 $recycle_id = $astDB->escape($_REQUEST['recycle_id']);
	$attempt_delay = $astDB->escape($_REQUEST['attempt_delay']);
	$attempt_maximum = $astDB->escape($_REQUEST['attempt_maximum']);
	$active = $astDB->escape(strtoupper($_REQUEST['active']));

	$ip_address = $astDB->escape($_REQUEST['log_ip']);

	// Default values
	$defActive = array('N','Y');

	// ERROR CHECKING ...
	if(empty($session_user) || empty($recycle_id)) {
	 $err_msg = error_handle("40001", "recycle_id or session_user");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	} elseif(!empty($attempt_delay) && ($attempt_delay > 99999 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_delay) || $attempt_delay < 120) ){
		$apiresults = array("result" => "Error: Maximum is 5 digits. No special characters allowed. Must be atleast 120 seconds");
	} elseif(!empty($attempt_delay) && (strlen($attempt_maximum) > 3 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_maximum)) ){
		$apiresults = array("result" => "Error: Special characters found in Attempt Maximum and must not be empty");
	} elseif(!in_array($active, $defActive) && $active != null) {
		$apiresults = array("result" => "Error: Default value for active is N for No and Y for Yes only.");
	} else {
		$groupId = go_get_groupid($session_user, $astDB);
		//$get_campaign = mysqli_query($link, "SELECT campaign_id FROM recycle_id = '$recycle_id';");
		$astDB->where('recycle_id', $recycle_id);
		$fetch_campaign = $astDB->getOne('vicidial_lead_recycle', 'campaign_id');
		$campaign_id = $fetch_campaign['campaign_id'];
	 $check_usergroup = go_check_usergroup_campaign($astDB, $groupId, $campaign_id);

		if($check_usergroup > 0){
			//$queryCheck = "SELECT status,attempt_delay,attempt_maximum,campaign_id,active FROM vicidial_lead_recycle WHERE recycle_id='$recycle_id';";
			$astDB->where('recycle_id', $recycle_id);
			$sqlCheck = $astDB->get('vicidial_lead_recycle', null, 'status,attempt_delay,attempt_maximum,campaign_id,active');
			$countCheck = $astDB->getRowCount();

			if($countCheck > 0) {
				foreach ($sqlCheck as $fresults){
					$dataAttemptDelay = $fresults['attempt_delay'];
					$dataAttemptMaximum = $fresults['attempt_maximum'];
					$dataCampID = $fresults['campaign_id'];
					$dataActive = $fresults['active'];
				}
				if(empty($status)){$status = $dataStatus;}
				if(empty($attempt_delay)){$attempt_delay = $dataAttemptDelay;}
				if(empty($attempt_maximum)){$attempt_maximum = $dataAttemptMaximum;}
				if(empty($campaign_id)){$campaign_id = $dataCampID;}
				if(empty($active)){$active = $dataActive;}

				$queryVM ="UPDATE vicidial_lead_recycle SET attempt_delay='$attempt_delay',attempt_maximum='$attempt_maximum', active='$active' WHERE recycle_id='$recycle_id'";
				$rsltv1 = $astDB->rawQuery($queryVM);
				
				if($rsltv1){
					$apiresults = array("result" => "success");

					$log_id = log_action($goDB, 'MODIFY', $session, $ip_address, "Modified Lead Recycling: $status", $groupId, $queryVM);
	   }
			} else {
				$apiresults = array("result" => "Error: Lead Recycle ID does not exist!");
			}
		}else{
			$apiresults = array("result" => "Error: Current user ".$session_user." doesn't have enough permission to access this feature");
		}
	}
?>