<?php
    #######################################################
    #### Name: goGetHelpdeskAgentAccess.php	       ####
    #### Description: API to get agent department      ####
    ####              access                           ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $userid = $_REQUEST['userid'];
    ### Check user_id if its null or empty
    if($userid == null && $userid == 0) { 
            $apiresults = array("result" => "Error: Set a value for User ID."); 
    } else {         
        $userid = $userid;
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
                $ul='';
        } else { 
                $ul = "AND p.user_group='$groupId'";  
        }

        $query = "SELECT dept_id FROM ost_staff_dept_access WHERE staff_id='$userid'";

        $rsltv = mysqli_query($linkost,$query);
        //var_dump($rsltv);
        $countResult = mysqli_num_rows($rsltv);
        
        if($countResult > 0) {
            $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                array_push($data, ($fresults));
            }
            $apiresults = array("result" => "success", "data" => $data);
        } else {
            $apiresults = array("result" => "Error: No data to show.");
        }
    }
    
?>
