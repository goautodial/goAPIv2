<?php
    #######################################################
    #### Name: goGetHelpdeskDepartmentInfo.php	       ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $deptid = $_REQUEST['deptid'];
    ### Check user_id if its null or empty
    if($deptid == null && $deptid == 0) { 
            $apiresults = array("result" => "Error: Set a value for Department ID."); 
    } else {         
        $deptid = $deptid;
        $groupId = go_get_groupid($goUser);

        if (!checkIfTenant($groupId)) {
                $ul='';
        } else { 
                $ul = "AND p.user_group='$groupId'";  
        }

        $query = "SELECT id, name from ost_department WHERE id='$deptid'";

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
