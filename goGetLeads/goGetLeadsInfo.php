<?php
    #######################################################
    #### Name: goGetLeadsInfo.php	               ####
    #### Description: API to get specific contact      ####
    #### Copyright: GOAutoDial Inc. (c) 2016           ####
    #### Written by: Alexander Abenoja                 ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
    $lead_id = $_REQUEST['lead_id'];
    
    ### Check user_id if its null or empty
    if($lead_id == null) { 
            $apiresults = array("result" => "Error: Set a value for Lead ID."); 
    } else { 
        $groupId = go_get_groupid($goUser);
    
        if (!checkIfTenant($groupId)) {
            $ul = "AND user='$user_id'";
        } else { 
            $ul = "AND user='$user_id' AND user_group='$groupId'";  
        }

        if ($groupId != 'ADMIN') {
            $notAdminSQL = "AND user_group != 'ADMIN'";
        }

        $query = "SELECT lead_id,list_id,first_name,middle_initial,last_name,email,phone_number,alt_phone,address1,address2,address3,city,state,province,postal_code,country_code,gender,status,user,date_of_birth FROM vicidial_list where lead_id='$lead_id'";
        $rsltv = mysqli_query($link, $query);
        $fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC);                    
        $apiresults = array("result" => "success", "data" => $fresults);

    }
?>
