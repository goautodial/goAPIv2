<?php
    #######################################################
    #### Name: goGetHelpdeskTeamLists.php	       ####
    #### Description: API to get all Phone	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro Biscocho          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once(__DIR__ . "/../goFunctions.php");
     
    $groupId = go_get_groupid($goUser);

    if (!checkIfTenant($groupId, $goDB)) {
            $ul='';
    } else { 
            $ul = "AND p.user_group='$groupId'";  
    }

    $query = "SELECT ost_team.team_id, ost_team.lead_id, ost_team.name, ost_team.flags, count(ost_team_member.team_id) as members FROM ost_team, ost_team_member WHERE ost_team.team_id=ost_team_member.team_id AND staff_id!=0 GROUP BY ost_team_member.team_id";

    $rsltv = mysqli_query($linkost,$query);
    //var_dump($rsltv);
    $countResult = mysqli_num_rows($rsltv);
    
    if($countResult > 0) {
        $data = [];
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
            $data[] = urlencode_array($fresults);
        }
        $apiresults = ["result" => "success", "data" => $data];
    } else {
        $apiresults = ["result" => "Error: No data to show."];
    }

    function urlencode_array($array){
        $out_array = [];
        foreach($array as $key => $value){
        $out_array[rawurlencode((string) $key)] = rawurlencode((string) $value);
        }
    return $out_array;
    }
    
?>
