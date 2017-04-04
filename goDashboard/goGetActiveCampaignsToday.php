<?php
    ####################################################
    #### Name: goGetActiveCampaignsToday.php        ####
    #### Description: API to get active campaigns   ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
    #### Written by: Demian Lizandro A. Biscocho    ####
    #### License: AGPLv2                            ####
    ####################################################
    
    include_once("../goFunctions.php");
    include_once("../goDBasterisk.php");
	
	$user = mysqli_real_escape_string($link, $_POST['user']);
	$groupId = go_get_groupid($user);
    
    if (checkIfTenant($groupId)) {
        $ul='';
    } else { 
        $stringv = "'".go_getall_allowed_campaigns($groupId)."'";
        $ul = " and campaign_id IN ($stringv)";
    }

    $NOW = date("Y-m-d");

    $query = "SELECT campaign_id as getActiveCampaignsToday from vicidial_campaign_stats  where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59'  $ul LIMIT 1000"; 
    //$query = "SELECT sum(drops_today) as getTotalDroppedCalls from vicidial_campaign_stats where calls_today > -1 and  $ul"; 
    
    $rsltv = mysqli_query($link,$query);
    $countResult = mysqli_num_rows($rsltv);
    //echo "<pre>";
    //var_dump($rsltv);   
        
    if($countResult > 0) {
        $data = array();
            while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){       
                array_push($data, $fresults);
            }
            $apiresults = array("result" => "success", "data" => $data);
    }
	
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
