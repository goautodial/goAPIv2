<?php
    /********************************************************************
    * Name: goGetAllLeadRecycling.php                                   *
    * Description: API to get all Lead Recycle in a specific campaign   *
    * Version: 4.0                                                      *
    * Copyright: GOAutoDial Inc. (c) 2011-2016                          *
    * Written by: Alexander Jim H. Abenoja                              *
    * License: AGPLv2                                                   *
    *********************************************************************/

    include_once("../goFunctions.php");

    $campaign_id = $_REQUEST['campaign_id'];
    
    if(empty($session_user)) {
        $err_msg = error_handle("40001", "session_user");
        $apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for Campaign ID.");
    } else {
        $groupId = go_get_groupid($session_user);

        $query = "SELECT * FROM vicidial_lead_recycle ORDER BY recycle_id;";
        $rsltv = mysqli_query($link,$query) or die(mysqli_error($query));
        $count = mysqli_num_rows($rsltv);
        $x = 0;
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
            $output[] = array("recycle_id" => $fresults['recycle_id'], "campaign_id" => $fresults['campaign_id'], "status" => $fresults['status'], "attempt_delay" => $fresults['attempt_delay'], "attempt_maximum" => $fresults['attempt_maximum'],"active" => $fresults['active']);
            $x++;
        }
        $apiresults = array("result" => "success", "data" => $output);
    }

?>
