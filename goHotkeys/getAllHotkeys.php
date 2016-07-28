<?php
   ####################################################
   #### Name: getAllHotkeys.php                    ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
    include_once("goFunctions.php");
   
    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
            $where='';
        } else { 
        $where = "WHERE user_group='$groupId'";  
    }
   
    $query = "SELECT
                campaign.campaign_id,
                campaign.campaign_name,
                hotkey.hotkey
            FROM
                vicidial_campaigns as campaign
            $ul
            LEFT JOIN vicidial_campaign_hotkeys as hotkey
            ORDER BY campaign.campaign_id
    ";
    
    $rsltv = mysqli_query($link, $query);
   
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCampID[]   = $fresults['campaign_id'];
       	$dataCampName[] = $fresults['campaign_name'];
		$dataHotkey[]   = $fresults['hotkey'];
   		$apiresults = array(
                        "result"        => "success",
                        "campaign_id"   => $dataCampID,
                        "campaign_name" => $dataCampName,
                        "hotkey"        => $dataHotkey,
                    );
	}
?>