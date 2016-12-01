<?php
   ####################################################
   #### Name: goAddHotkey.php                      ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
    include_once("goFunctions.php");
   
    $campaign_id = $_REQUEST['campaign_id'];
    $hotkey = $_REQUEST['hotkey'];
    $status = $_REQUEST['status'];
    $status_name = $_REQUEST['status_name'];
    
    $checkHotkey = "SELECT * FROM vicidial_campaign_hotkeys WHERE campaign_id = '$campaign_id' AND hotkey = '$hotkey' OR status = '$status'";
    $checkResult = mysqli_query($link, $checkHotkey);
    $countCheck = mysqli_num_rows($checkResult);
    
    if($countCheck > 0) {
        $apiresults = array("result" => "duplicate");
    } else {
        $query = "INSERT into vicidial_campaign_hotkeys
                (
                    status,
                    hotkey,
                    status_name,
                    selectable,
                    campaign_id
                ) 
                VALUES(
                    '$status',
                    '$hotkey',
                    '$status_name',
                    'Y',
                    '$campaign_id'
                )
        ";
        
        $rsltv = mysqli_query($link, $query);
        $countResult = mysqli_num_rows($rsltv);
        
        if($rsltv) {
            $apiresults = array("result" => "success");
        } else {
            $apiresults = array("result" => "Error: Failed to add campaign hotkey.");
        }
    }
?>