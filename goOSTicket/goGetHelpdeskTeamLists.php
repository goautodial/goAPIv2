<?php
    #######################################################
    #### Name: goGetHelpdeskTeamLists.php	       ####
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

    $query = "SELECT ost_team.team_id, ost_team.lead_id, ost_team.name, ost_team.flags, count(ost_team_member.team_id) as members FROM ost_team, ost_team_member WHERE ost_team.team_id=ost_team_member.team_id AND staff_id!=0 GROUP BY ost_team_member.team_id";

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
