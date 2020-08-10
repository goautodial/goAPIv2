<?php
   ####################################################
   #### Name: goUpdateCampaignGoogleSheet.php      ####
   #### Description: API to edit specific campaign ####
   ####              that has google sheet enabled.####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Christopher Lomuntad           ####
   #### License: AGPLv2                            ####
   ####################################################
    
   include_once ("goAPI.php");
   $goUser                    = $astDB->escape($_REQUEST['goUser']);
   $ip_address                = $astDB->escape($_REQUEST['hostname']);
   $campaign_id               = $astDB->escape($_REQUEST['campaign_id']);
   $google_sheet_ids          = $astDB->escape($_REQUEST['google_sheet_ids']);
   $log_user                  = $astDB->escape($_REQUEST['log_user']);
   $log_group                 = $astDB->escape($_REQUEST['log_group']);
   
   if($campaign_id != null) {
      //$updateQuery = "UPDATE go_campaigns SET google_sheet_ids = '$google_sheet_ids' WHERE campaign_id='$campaign_id' LIMIT 1;";
      //echo $updateQuery;
      $updateData = array(
         'google_sheet_ids' => $google_sheet_ids
      );
      $goDB->where('campaign_id', $campaign_id);
      $updateResult = $goDB->update('go_campaigns', $updateData, 1);
      $updateQuery = $goDB->getLastQuery();
      
      $log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Updated Google Sheets for Campaign ID: $campaign_id", $log_group, $updateQuery);
      
      $apiresults = array("result" => "success");
   }else{
      $apiresults = array("result" => "Error: Campaign doens't exist.");
   }
?>
