<?php
    #######################################################
    #### Name: goGetHelpdeskAgentInfo.php	       ####
    #### Description: API to get all Phone	       ####
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

        $query = "SELECT staff_id, dept_id, role_id, firstname, lastname, email, isadmin, isactive, signature FROM ost_staff WHERE staff_id='$userid'";

        $rsltv = mysqli_query($linkost,$query);
        //var_dump($rsltv);
        $countResult = mysqli_num_rows($rsltv);
        
        if($countResult > 0) {
            $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                array_push($data, urlencode_array($fresults));
            }
            $apiresults = array("result" => "success", "data" => $data);
        } else {
            $apiresults = array("result" => "Error: No data to show.");
        }
    }
    
    function urlencode_array($array){
        $out_array = array();
        foreach($array as $key => $value){
        $out_array[rawurlencode($key)] = rawurlencode($value);
        }
    return $out_array;
    }    
    
?>
