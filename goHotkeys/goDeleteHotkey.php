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
    $hotkeys = explode(",", $_REQUEST['hotkey']);
    
    $ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    $result = array();
    foreach ($hotkeys as $hotkey){
        $query = "DELETE
                FROM vicidial_campaign_hotkeys
                WHERE campaign_id='$campaign_id'
                AND hotkey='$hotkey'";
                
        $rsltv = mysqli_query($link, $query);
        
        if($rsltv){
            array_push($result, "ok");
        }else{
            array_push($result, "error");
        }
    }
    
    if(in_array("error", $result)) {
        $apiresults = array("result" => "Error: Failed to delete campaign hotkey.");
    } else {
        $log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Hotkey $hotkey from Campaign $campaign_id", $log_group, $query);
        
        $apiresults = array("result" => "success");
    }
?>