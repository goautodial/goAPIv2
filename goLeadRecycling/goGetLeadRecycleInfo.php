<?php

    /**************************************************
    * Name: goGetAllLeadRecycling.php                 *
    * Description: API to get specific Lead Recycle   *
    * Version: 4.0                                    *
    * Copyright: GOAutoDial Inc. (c) 2011-2016        *
    * Written by: Alexander Jim H. Abenoja            *
    * License: AGPLv2                                 *
    ***************************************************/

    include_once("../goFunctions.php");

    $campaign_id = $_REQUEST['campaign_id'];
    $status = $_REQUEST['status'];

    $groupId = go_get_groupid($session_user);
    $check_usergroup = go_check_usergroup_campaign($groupId, $campaign_id);

    if(empty($campaign_id) || empty($status) || empty($session_user)) {
        $err_msg = error_handle("40001", "campaign_id, session_user and status");
        $apiresults = array("code" => "40001", "result" => $err_msg);
    } elseif($check_usergroup <= 0){
        $apiresults = array("result" => "Error: Usergroup error. You don't have permission to access this feature.");
    }else {

        $query = "SELECT * FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' AND status='$status' ORDER BY status LIMIT 1;";
        $rsltv = mysqli_query($link,$query);
        $exist = mysqli_num_rows($rsltv);

        if($exist >= 1){
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataCampID[] = $fresults['campaign_id'];
                $dataStatus[] = $fresults['status'];
                $dataAttemptDelay[] = $fresults['attempt_delay'];
                $dataAttemptMax[] = $fresults['attempt_maximum'];
                $dataActive[] = $fresults['active'];
            }

            $apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status" => $dataStatus, "attempt_delay" => $dataAttemptDelay, "attempt_maximum" => $dataAttemptMax, "active" => $dataActive);
        } else {

            $apiresults = array("result" => "Error: Lead Filter does not exist.");

        }
    }
?>
