<?php
   ####################################################
   #### Name: goAddHotkey.php                      ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Noel Umandap                   ####
   #### License: AGPLv2                            ####
   ####################################################
    $campaign_id    = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
    $hotkey         = mysqli_real_escape_string($link, $_REQUEST['hotkey']);
    $status         = mysqli_real_escape_string($link, $_REQUEST['status']);
    $status_name    = mysqli_real_escape_string($link, $_REQUEST['status_name']);
    $ip_address     = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
    $log_user       = mysqli_real_escape_string($link, $_REQUEST['log_user']);
    $log_group      = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    $astDB->where('campaign_id', $campaign_id);
    $astDB->where('hotkey', $hotkey);
    $astDB->orwhere('status', $status);
    $hotkeys = $astDB->get('vicidial_campaign_hotkeys', null, '*');
    
    if(count($hotkeys) > 0) {
        $apiresults = array("result" => "duplicate");
    } else {
        $data_insert = array(
            'status'        => $status,
            'hotkey'        => $hotkey,
            'status_name'   => $status_name,
            'selectable'    => 'Y',
            'campaign_id'   => $campaign_id
        );
        $insertHotkey = $astDB->insert('vicidial_campaign_hotkeys', $data_insert);
        $insertQuery = $astDB->getLastQuery();
        
        if($insertHotkey) {
            $log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Hotkey $status on Campaign $campaign_id", $log_group, $insertQuery);
            
            $apiresults = array("result" => "success");
        } else {
            $apiresults = array("result" => "Error: Failed to add campaign hotkey.");
        }
    }
?>