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
    
   include_once (__DIR__ . "/goAPI.php");

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var MySQLiDB $kamDB */
/** @var string $goUser */
/** @var string $goPass */
/** @var string $goAction */
/** @var string $goURL */
/** @var string $userResponseType */
/** @var string $session_user */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */

   $goUser                    = $astDB->escape(($_REQUEST['goUser'] ?? ''));
   $ip_address                = $astDB->escape(($_REQUEST['hostname'] ?? ''));
   $campaign_id               = $astDB->escape(($_REQUEST['campaign_id'] ?? ''));
   $google_sheet_ids          = $astDB->escape(($_REQUEST['google_sheet_ids'] ?? ''));
   $log_user                  = $astDB->escape(($_REQUEST['log_user'] ?? ''));
   $log_group                 = $astDB->escape(($_REQUEST['log_group'] ?? ''));
   
   if($campaign_id != null) {
      //$updateQuery = "UPDATE go_campaigns SET google_sheet_ids = '$google_sheet_ids' WHERE campaign_id='$campaign_id' LIMIT 1;";
      //echo $updateQuery;
      $updateData = [
         'google_sheet_ids' => $google_sheet_ids
      ];
      $goDB->where('campaign_id', $campaign_id);
      $updateResult = $goDB->update('go_campaigns', $updateData, 1);
      $updateQuery = $goDB->getLastQuery();
      
      $log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Updated Google Sheets for Campaign ID: $campaign_id", $log_group, $updateQuery);
      
      $apiresults = ["result" => "success"];
   }else{
      $apiresults = ["result" => "Error: Campaign doens't exist."];
   }
?>
