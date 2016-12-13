<?php
    #######################################################
    #### Name: goGetHelpdeskAgentLists.php	       ####
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

    $query = "SELECT staff_id, dept_id, role_id, username, firstname, lastname, isactive, id, name as dept_name from ost_staff, ost_department WHERE dept_id=id ORDER by username DESC LIMIT 2000";

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

    function urlencode_array($array){
        $out_array = array();
        foreach($array as $key => $value){
        $out_array[rawurlencode($key)] = rawurlencode($value);
        }
    return $out_array;
    }

?>
