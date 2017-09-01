<?php
/***********************************************************
**** Name: goEditLeadRecycling.php                  	****
**** Description: API to edit specific Lead Recycling   ****
**** Version: 4.0                                   	****
**** Copyright: GOAutoDial Ltd. (c) 2016-2017       	****
**** Written by: Alexander Jim Abenoja			      	****
**** License: AGPLv2                                	****
************************************************************/

 include_once("../goFunctions.php");

 // POST or GET Variables
 	$recycle_id = mysqli_real_escape_string($link, $_REQUEST['recycle_id']);
	$attempt_delay = mysqli_real_escape_string($link, $_REQUEST['attempt_delay']);
	$attempt_maximum = mysqli_real_escape_string($link, $_REQUEST['attempt_maximum']);
	$active = mysqli_real_escape_string($link, strtoupper($_REQUEST['active']));

	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);

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
		$groupId = go_get_groupid($session_user);
		$get_campaign = mysqli_query($link, "SELECT campaign_id FROM recycle_id = '$recycle_id';");
		$fetch_campaign = mysqli_fetch_array($get_campaign);
		$campaign_id = $fetch_campaign['campaign_id'];
	    $check_usergroup = go_check_usergroup_campaign($groupId, $campaign_id);

	    if($check_usergroup > 0){
	        $queryCheck = "SELECT status,attempt_delay,attempt_maximum,campaign_id,active FROM vicidial_lead_recycle WHERE recycle_id='$recycle_id';";
	        $sqlCheck = mysqli_query($link,$queryCheck);
	        $countCheck = mysqli_num_rows($sqlCheck);

			if($countCheck > 0){
				while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
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
				$rsltv1 = mysqli_query($link,$queryVM) or die(mysqli_error($link));


	            if($rsltv1){
	                $apiresults = array("result" => "success");

					$log_id = log_action($linkgo, 'MODIFY', $session, $ip_address, "Modified Lead Recycling: $status", $groupId, $queryVM);
	            }
	            
	        } else {
		        $apiresults = array("result" => "Error: Lead Recycle ID does not exist!");
		    }
	    }else{
	    	$apiresults = array("result" => "Error: Current user ".$session_user." doesn't have enough permission to access this feature");
	    }
	}
?>
