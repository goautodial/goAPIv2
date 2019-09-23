<?php
 /**
 * @file       goEditLeadRec.php
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
	include_once ("goAPI.php");
	
 // POST or GET Variables
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$status = $astDB->escape($_REQUEST['status']);
	$attempt_delay = $astDB->escape($_REQUEST['attempt_delay']);
	$attempt_maximum = $astDB->escape($_REQUEST['attempt_maximum']);
	$active = $astDB->escape(strtoupper($_REQUEST['active']));

 // Default values
 $defActive = array('N','Y');

// ERROR CHECKING ...
if(empty($campaign_id) || empty($session_user) || empty($status)) {
 $err_msg = error_handle("40001", "campaign_id, session_user, and status");
 $apiresults = array("code" => "40001", "result" => $err_msg);
} elseif(preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $status)){
 $apiresults = array("result" => "Error: Special characters found in Status and must not be empty");
} elseif(strlen($attempt_delay) > 5 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_delay)){
 $apiresults = array("result" => "Error: Special characters found in Attempt Delay and must not be empty");
} elseif(strlen($attempt_maximum) > 3 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_maximum)){
 $apiresults = array("result" => "Error: Special characters found in Attempt Maximum and must not be empty");
} elseif(!in_array($active, $defActive) && !empty($active)) {
 $apiresults = array("result" => "Error: Default value for active is N for No and Y for Yes only.");
} else {
	$groupId = go_get_groupid($session_user, $astDB);
 $check_usergroup = go_check_usergroup_campaign($astDB, $groupId, $campaign_id);

	if($check_usergroup > 0){
		//$queryCheck = "SELECT status,attempt_delay,attempt_maximum,campaign_id,active FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' AND status = '$status';";
		$astDB->where('campaign_id', $campaign_id);
		$astDB->where('status', $status);
		$sqlCheck = $astDB->get('vicidial_lead_recycle', null, 'status,attempt_delay,attempt_maximum,campaign_id,active');
		$countCheck = $astDB->getRowCount();

		if($countCheck > 0) {
			foreach ($sqlCheck as $fresults){
				$dataStatus = $fresults['status'];
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

			$queryVM ="UPDATE vicidial_lead_recycle SET attempt_delay='$attempt_delay',attempt_maximum='$attempt_maximum', active='$active' WHERE status='$status' and campaign_id='$campaign_id'";
			$rsltv1 = $astDB->rawQuery($queryVM);
			if($rsltv1) {
				$apiresults = array("result" => "success");

				$log_id = log_action($goDB, 'MODIFY', $session, $log_ip, "Modified Lead Recycling: $status", $groupId, $queryVM);
   }
  } else {
			$apiresults = array("result" => "Error: Add failed, Campaign ID does not exist!");
		}
	} else {
		$apiresults = array("result" => "Error: Current user ".$session_user." doesn't have enough permission to access this feature");
	}
}
?>