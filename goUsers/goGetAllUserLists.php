<?php
    #######################################################
    #### Name: goGetAllUserLists.php 	               ####
    #### Description: API to get all User Lists        ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");


    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
	$ul = "AND user_group='$groupId'";  
    }
    if ($groupId != 'ADMIN') {
        $notAdminSQL = "AND user_group != 'ADMIN'";
    }

#	$query = "SELECT user_id, user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC;";
	$query = "SELECT user_id, user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' ORDER BY user ASC;";
   	$rsltv = mysqli_query($link, $query);
        $countResult = mysqli_num_rows($rsltv);

        if($countResult > 0) {
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataUserID[] = $fresults['user_id'];
		$dataUser[] = $fresults['user'];
                $dataFullName[] = $fresults['full_name'];
                $dataUserLevel[] = $fresults['user_level'];
                $dataUserGroup[] = $fresults['user_group'];
		$dataActive[]	= $fresults['active'];
                $apiresults = array("result" => "success", "user_id" => $dataUserID,"user_group" => $dataUserGroup, "user" => $dataUser, "full_name" => $dataFullName, "user_level" => $dataUserLevel, "active" => $dataActive);
        }
	} else {
		$apiresults = array("result" => "Error: No data to show.");
	}

?>
