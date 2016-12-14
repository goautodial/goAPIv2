<?php
    #######################################################
    #### Name: goGetCannedResponseLists.php	       ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
     
    $groupId = go_get_groupid($goUser);

    if (!checkIfTenant($groupId)) {
            $ul='';
    } else { 
            $ul = "AND p.user_group='$groupId'";  
    }

    //$query = "SELECT canned_id, dept_id, isenabled, title, updated from ost_canned_response ORDER by updated DESC LIMIT 2000";
    $query = "select isenabled, title, ost_canned_response.updated, name from ost_canned_response LEFT OUTER JOIN ost_department ON ost_canned_response.dept_id=ost_department.id";
    $rsltv = mysqli_query($linkost,$query);
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

    function urlencode_array($array){
        $out_array = array();
        foreach($array as $key => $value){
        $out_array[urlencode($key)] = urlencode($value);
        }
    return $out_array;
    }

?>
