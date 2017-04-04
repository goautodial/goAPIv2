<?php
    ####################################################
    #### Name: goGetTotalActiveLeads.php            ####
    #### Description: API to get total active leads ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jeremiah Sebastian V. Samatra  ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    include_once("../goDBasterisk.php");
	
    $user = mysqli_real_escape_string($link, $_POST['user']);
    $groupId = go_get_groupid($user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = getall_allowed_users($groupId, $link);
        $ul = " and campaign_id IN ($stringv) and user_level != 4";
    }
	
    $query = "SELECT count(*) as getTotalActiveLeads from vicidial_lists as vls,vicidial_list as vl where vl.list_id=vls.list_id and active='Y' $ul"; 
    $rsltv = mysqli_query($link,$query);
    $fresults = mysqli_fetch_assoc($rsltv);
    $apiresults = array_merge( array( "result" => "success" ), $fresults );
	
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
