<?php
    #######################################################
    #### Name: goCreateTicket.php	                   ####
    #### Description: API to edit ticket               ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2017      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $goUser     = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
    $threadID   = mysqli_real_escape_string($linkost, $_REQUEST['thread_id']);
    $title      = mysqli_real_escape_string($linkost, $_REQUEST['title']);
    $body       = mysqli_real_escape_string($linkost, $_REQUEST['body']);
    $date       = date('Y-m-d H:i:s');
    
    //update thread entry
    $queryUpdateThreadEntry = "UPDATE vicidial_campaigns SET
                            title = '$title',
                            body = '$body',
                            updated = '$date'
                        WHERE thread_id='$threadID' LIMIT 1;";
    $resultUpdateThreadEntry = mysqli_query($linkost,$queryUpdateThreadEntry);
    
    if(mysqli_num_rows($resultUpdateThreadEntry) > 0){
        $apiresults = array("result" => "success");
    }else{
        $apiresults = array("result" => "Error: Something went wrong.");
    }
    
?>