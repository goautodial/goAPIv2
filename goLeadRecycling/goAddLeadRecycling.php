<?php
  /***************************************************
  **** Name: goAddLeadRecycling.php               ****
  **** Description: API to add new Lead Recycling ****
  **** Version: 4.0                               ****
  **** Copyright: GOAutoDial Ltd. (c) 2016-2017   ****
  **** Written by: Alexander Jim Abenoja          ****
  **** License: AGPLv2                            ****
  ****************************************************/
  
  include_once("../goFunctions.php");
  // POST or GET Variables
  $campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
  $status = mysqli_real_escape_string($link, $_REQUEST['status']);
  $attempt_delay = mysqli_real_escape_string($link, $_REQUEST['attempt_delay']);
  $attempt_maximum = mysqli_real_escape_string($link, $_REQUEST['attempt_maximum']);
  $active = mysqli_real_escape_string($link, strtoupper($_REQUEST['active']));

  if(empty($attempt_delay))
    $attempt_delay = 1800;
  if(empty($attempt_maximum))
    $attempt_maximum = 2;
  if(empty($active))
    $active = "Y";

  //optional
    $log_ip = mysqli_real_escape_string($link, $_REQUEST['log_ip']);

  // Default values 
  $defActive = array('Y','N');

  // ERROR CHECKING 
  if(empty($campaign_id) || empty($session_user) || empty($status) ) {
    $err_msg = error_handle("40001", "campaign_id, session_user, and status");
      $apiresults = array("code" => "40001", "result" => $err_msg);
  } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status) ){
    $apiresults = array("result" => "Error: Special characters found in status");
  } elseif(strlen($attempt_delay) > 5 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_delay) || $attempt_delay > 120){
    $apiresults = array("result" => "Error: Maximum is 5 digits. No special characters allowed. Must be atleast 120 seconds");
  } elseif(strlen($attempt_maximum) > 3 || preg_match("/[\'^£$%&*()}{@#~?><>,|=_+¬-]/", $attempt_maximum)){
    $apiresults = array("result" => "Error: Maximum is 3 digits. No special characters allowed.");
  } elseif(!in_array($active,$defActive) && !empty($active)) {
    $apiresults = array("result" => "Error: Default value for Active is Y or N only.");
  } else {

    $groupId = go_get_groupid($session_user);
    $check_usergroup = go_check_usergroup_campaign($groupId, $campaign_id);

    if($check_usergroup > 0){
        $queryCheck = "SELECT * FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id' and status = '$status';";
        $sqlCheck = mysqli_query($link,$queryCheck);
        $countCheck1 = mysqli_num_rows($sqlCheck);

        $queryCheck2 = "SELECT * FROM vicidial_statuses WHERE status='$status';";
        $sqlCheck2 = mysqli_query($link,$queryCheck2);
        $countCheck2 = mysqli_num_rows($sqlCheck2);

      if($countCheck1 > 0 || $countCheck2 > 0){
        if($campaign_id === "ALL"){
          $all_campaigns = "SELECT campaign_id FROM vicidial_campaigns;";
          $query_all_campaigns = mysqli_query($link, $all_campaigns);
          
          while($row = mysqli_fetch_array($query_all_campaigns)){
            $camp_id = $row['campaign_id'];
            //$arr_campaign[] = $camp_id;

            $newQuery = "INSERT INTO vicidial_lead_recycle (campaign_id,status,attempt_delay,attempt_maximum,active) VALUES ('$camp_id', '$status', '$attempt_delay', '$attempt_maximum', '$active');";
            $rsltv = mysqli_query($link,$newQuery) or die(mysqli_error($link));

            if($rsltv){
              $log_id = log_action($linkgo, 'ADD', $session_user, $log_ip, "Added a New Lead Recycling under Status: $status in Campaign ID: $camp_id", $groupId, $newQuery);
            }

            $queryCheck3 = "SELECT MAX(recycle_id) as last_recycle_id FROM vicidial_lead_recycle;";
            $sqlCheck3 = mysqli_query($link,$queryCheck3) or die(mysqli_error($link));
            $fetch_check = mysqli_fetch_array($sqlCheck3);
            $inserted_id[] = $fetch_check['last_recycle_id'];
          }

          if(count($inserted_id) > 0){
            $imploded_ids = implode(",",$inserted_id);
            $apiresults = array("result" => "success", "recycle_id" => $imploded_ids);
          }


        }else{
          $newQuery = "INSERT INTO vicidial_lead_recycle (campaign_id,status,attempt_delay,attempt_maximum,active) VALUES ('$campaign_id', '$status', '$attempt_delay', '$attempt_maximum', '$active');";
          $rsltv = mysqli_query($link,$newQuery) or die(mysqli_error($link));

          $queryCheck3 = "SELECT MAX(recycle_id) as last_recycle_id FROM vicidial_lead_recycle;";
          $sqlCheck3 = mysqli_query($link,$queryCheck3) or die(mysqli_error($link));
          $num_check3 = mysqli_num_rows($sqlCheck3);
          $fetch_check = mysqli_fetch_array($sqlCheck3);

          if($rsltv === true && $num_check3 > 0){
            $apiresults = array("result" => "success", "recycle_id" => $fetch_check['last_recycle_id']);
            $log_id = log_action($linkgo, 'ADD', $session_user, $log_ip, "Added a New Lead Recycling under Status: $status in Campaign ID: $campaign_id", $groupId, $newQuery);
          }
        }
        

        

      }else{
        $apiresults = array("result" => "Error: Campaign ID or Status does not exist.");
      }
    }else{
      $apiresults = array("result" => "Error: Current user ".$session_user." doesn't have permission to access this feature");
    }

    

  }

?>
