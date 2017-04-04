<?php
    ####################################################
    #### Name: goGetTotalAgentsCall.php             ####
    #### Type: API to get total agents onCall       ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Jerico James Flores Milo       ####
    ####             Demian Lizandro A. Biscocho    ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    include_once("../goDBasterisk.php");
	
    $user = mysqli_real_escape_string($link, $_POST['user']);
    $groupId = go_get_groupid($user);
    
    if (checkIfTenant($groupId)) {
		$stringv = '';
        $ul_online='';
		$ul_calls='';
    } else { 
        $stringv = "'".getall_allowed_users($groupId, $link)."'";
		$ul = " and user IN ($stringv) and user_level != '4'";
    }
    
    $query = "SELECT count(*) as getTotalAgentsCall FROM vicidial_live_agents WHERE status IN ('INCALL','QUEUE','3-WAY','PARK') $ul"; 
    $rsltv = mysqli_query($link, $query);
    $data = mysqli_fetch_assoc($rsltv);
    $apiresults = array("result" => "success", "data" => $data);
	
	function getall_allowed_users($groupId, $link) {
        
        if ($groupId=='ADMIN' || $groupId=='admin') {
			$query = "select user as userg from vicidial_users";
			$rsltv = mysqli_query($link,$query); 
        } else {
			$query = "select user as userg from vicidial_users where user_group='$groupId'";
			$rsltv = mysqli_query($link,$query); 
        }
        
        while($info = mysqli_fetch_array( $rsltv )) {
            $users[] = $info['userg'];
        }
		$allowed_users = implode("','", $users);
    
        return $allowed_users;
    }
?>
