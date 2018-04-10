<?php
 /**
 * @file        goAddLeadRecycling.php
 * @brief 	    API for Adding Lead Recycling
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

  // POST or GET Variables
  $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
  $status = $astDB->escape($_REQUEST['status']);
  $attempt_delay = $astDB->escape($_REQUEST['attempt_delay']);
  $attempt_maximum = $astDB->escape($_REQUEST['attempt_maximum']);
  $active = $astDB->escape(strtoupper($_REQUEST['active']));

  //optional
  $log_ip = $astDB->escape($_REQUEST['log_ip']);

  // Default values 
  $defActive = array('Y','N');

  // ERROR CHECKING 
  if(empty($campaign_id) || empty($session_user) || empty($status) ) {
    $err_msg = error_handle("40001", "campaign_id, session_user, and status");
    $apiresults = array("code" => "40001", "result" => $err_msg);
  } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status) ){
    $apiresults = array("result" => "Error: Special characters found in status");
  } elseif(preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_delay) || $attempt_delay < 120 || $attempt_delay > 99999 ){
    $apiresults = array("result" => "Error: Attempt Delay Maximum is 5 digits. No special characters allowed. Must be atleast 120 seconds");
  } elseif($attempt_maximum < 1 || strlen($attempt_maximum) > 3 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_maximum)){
    $apiresults = array("result" => "Error: Attempt Maximum is 3 digits. No special characters allowed.");
  } elseif(!in_array($active,$defActive) && !empty($active)) {
    $apiresults = array("result" => "Error: Default value for Active is Y or N only.");
  } else {
    if(empty($attempt_delay))
      $attempt_delay = 1800;
    if(empty($attempt_maximum))
      $attempt_maximum = 2;
    if(empty($active))
      $active = "Y";

    $groupId = go_get_groupid($session_user, $astDB);
    $check_usergroup = go_check_usergroup_campaign($astDB, $groupId, $campaign_id);

    if($check_usergroup > 0){
      //$queryCheck = "SELECT * FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id' and status = '$status';";
      $astDB->where('campaign_id', $campaign_id);
      $astDB->where('status', $status);
      $sqlCheck = $astDB->get('vicidial_campaign_statuses');
      $countCheck1 = $astDB->getRowCount();

      //$queryCheck2 = "SELECT * FROM vicidial_statuses WHERE status='$status';";
      $astDB->where('status', $status);
      $sqlCheck2 = $astDB->get('vicidial_statuses');
      $countCheck2 = $astDB->getRowCount();

      if($countCheck1 > 0 || $countCheck2 > 0 || $campaign_id === "ALL") {
        if($campaign_id === "ALL") {
          //$all_campaigns = "SELECT campaign_id FROM vicidial_campaigns;";
          $query_all_campaigns = $astDB->get('vicidial_campaigns', null, 'campaign_id');
          
          foreach ($query_all_campaigns as $row){
            $camp_id = $row['campaign_id'];
            $newQuery = "INSERT INTO vicidial_lead_recycle (campaign_id,status,attempt_delay,attempt_maximum,active) VALUES ('$camp_id', '$status', '$attempt_delay', '$attempt_maximum', '$active');";
            $rsltv = $astDB->rawQuery($newQuery);

            if($rsltv){
              $log_id = log_action($goDB, 'ADD', $session_user, $log_ip, "Added a New Lead Recycling under Status: $status in Campaign ID: $camp_id", $groupId, $newQuery);
            }

            //$queryCheck3 = "SELECT MAX(recycle_id) as last_recycle_id FROM vicidial_lead_recycle;";
            $sqlCheck3 = $astDB->getOne('vicidial_lead_recycle', 'MAX(recycle_id) AS last_recycle_id');
            $inserted_id[] = $sqlCheck3['last_recycle_id'];
          }

          if(count($inserted_id) > 0) {
            $imploded_ids = implode(",",$inserted_id);
            $apiresults = array("result" => "success", "recycle_id" => $imploded_ids);
          }
        } else {
          $newQuery = "INSERT INTO vicidial_lead_recycle (campaign_id,status,attempt_delay,attempt_maximum,active) VALUES ('$campaign_id', '$status', '$attempt_delay', '$attempt_maximum', '$active');";
          $rsltv = $astDB->rawQuery($newQuery);

          //$queryCheck3 = "SELECT MAX(recycle_id) as last_recycle_id FROM vicidial_lead_recycle;";
          $sqlCheck3 = $astDB->getOne('vicidial_lead_recycle', 'MAX(recycle_id) AS last_recycle_id');
          $num_check3 = $astDB->getRowCount();

          if($rsltv && $num_check3 > 0){
            $apiresults = array("result" => "success", "recycle_id" => $sqlCheck3['last_recycle_id']);
            $log_id = log_action($goDB, 'ADD', $session_user, $log_ip, "Added a New Lead Recycling under Status: $status in Campaign ID: $campaign_id", $groupId, $newQuery);
          }
        }
      } else {
        $apiresults = array("result" => "Error: Campaign ID or Status does not exist.");
      }
    } else {
      $apiresults = array("result" => "Error: Current user ".$session_user." doesn't have permission to access this feature");
    }
  }
?>