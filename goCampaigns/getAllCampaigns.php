<?php
    ///////////////////////////////////////////////////////
    /// Name: getAllCampaigns.php 		///
    /// Description: API to get all campaigns 		///
    /// Version: 0.9 		///
    /// Copyright: GOAutoDial Inc. (c) 2011-2014 		///
    /// Written by: Jeremiah Sebastian V. Samatra 		///
    /// License: AGPLv2 		///
    ///////////////////////////////////////////////////////
	$agent = get_settings('user', $astDB, $goUser);

	if(!empty($agent)){
		if(!empty($agent->user_group)){
			// Getting Allowed Campigns
			$astDB->where('user_group', $agent->user_group);
			$allowedCamp = $astDB->getOne('vicidial_user_groups', "TRIM(REPLACE(allowed_campaigns,' -','')) AS allowed_campaigns");

			if (checkIfTenant($agent->user_group)) {
				//do nothing
			} else {
				if($agent->user_group !== "ADMIN"){
					$astDB->where('user_group', $agent->user_group);
				}
			}
			
			if (isset($user_group) && strlen($user_group) > 0) {
				if (!preg_match("/ALLCAMPAIGNS/",  $allowedCamp['allowed_campaigns'])) {
					$cl = explode(' ', $query['allowed_campaigns']);
    				$astDB->where('campaign_id', $cl, 'in');
				}
			}

			$astDB->orderBy('campaign_id');
			$result = $astDB->get('vicidial_campaigns', null, 'campaign_id,campaign_name,dial_method,active');

			foreach($result as $fresults){
				$dataCampID[] = $fresults['campaign_id'];
				$dataCampName[] = $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
				$dataDialMethod[] = $fresults['dial_method'];
				$dataActive[] = $fresults['active'];
			}

			$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "campaign_name" => $dataCampName, "dial_method" => $dataDialMethod, "active" => $dataActive);
			
		}else{
			$err_msg = error_handle("40001");
			$apiresults = array("code" => "40001", "result" => $err_msg);
		}
	}else{
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
	}
	
	
?>
