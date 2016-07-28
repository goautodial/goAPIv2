<?php
   ####################################################
   #### Name: goEditHotkey.php                      ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
    include_once("goFunctions.php");
    
    $campaign_id = $_REQUEST['campaign_id'];
    
    $query = "SELECT
                status,
                hotkey,
                status_name
            FROM vicidial_campaign_hotkeys
            WHERE campaign_id='$campaign_id'
            ORDER BY hotkey";
            
    $rsltv = mysqli_query($link, $query);
    //$countResult = mysqli_num_rows($rsltv);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		//$dataStatus[]   = $fresults['status'];
       	//$dataStatusName[] = $fresults['status_name'];
		$dataHotkey[]   = $fresults['hotkey'];
   		$apiresults = array(
                        "result"        => "success",
                        //"status"   => $dataStatus,
                        //"status_name" => $dataStatusName,
                        "hotkey"        => $dataHotkey,
                    );
	}
    
?>