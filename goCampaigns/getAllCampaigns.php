<?php
    #######################################################
    #### Name: getAllCampaigns.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    	$groupId = go_get_groupid($goUser);
		$user_group = $_REQUEST['user_group'];
    
	if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
		$ul = "WHERE user_group='$groupId'";  
	}
	
	if (isset($user_group) && strlen($user_group) > 0 && $user_group !== 'ADMIN') {
		$query = "SELECT TRIM(allowed_campaigns) AS allowed_camps FROM vicidial_user_groups WHERE user_group='$user_group';";
		$rsltv = mysqli_query($link, $query);
		$frslt = mysqli_fetch_assoc($rsltv);
		
		if (!preg_match("/ALL-CAMPAIGNS/", $frslt['allowed_camps'])) {
			$allowed_camps = explode(' ', $frslt['allowed_camps']);
			$allowed_campaigns = "";
			if (count($allowed_camps) > 0) {
				$allowed_campaigns = ($ul !== '') ? "AND campaign_id IN (" : "WHERE campaign_id IN (";
				foreach ($allowed_camps as $camp) {
					if ($camp !== "-") {
						$allowed_campaigns .= "'{$camp}',";
					}
				}
				$allowed_campaigns = rtrim($allowed_campaigns, ",");
				$allowed_campaigns .= ")";
			}
		}
	}

   	$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul $allowed_campaigns ORDER BY campaign_id";
   	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCampID[] = $fresults['campaign_id'];
       		$dataCampName[] = $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
		$dataDialMethod[] = $fresults['dial_method'];
		$dataActive[] = $fresults['active'];
   		$apiresults = array("result" => "success", "campaign_id" => $dataCampID, "campaign_name" => $dataCampName, "dial_method" => $dataDialMethod, "active" => $dataActive);
	}
?>
