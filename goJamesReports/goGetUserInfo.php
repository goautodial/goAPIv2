<?php
    #######################################################
    #### Name: goGetUserInfo.php	               ####
    #### Description: API to get specific user	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    ### POST or GET Variables
	$user_id = $_REQUEST['user_id'];
    
    ### Check user_id if its null or empty
	if($user_id == null) { 
		$apiresults = array("result" => "Error: Set a value for User ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "AND user='$user_id'";
    		} else { 
			$ul = "AND user='$user_id' AND user_group='$groupId'";  
		}

        	if ($groupId != 'ADMIN') {
        		$notAdminSQL = "AND user_group != 'ADMIN'";
                }

   		$query = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
	                $dataUser[] = $fresults['user'];
        	        $dataFullName[] = $fresults['full_name'];
                	$dataUserLevel[] = $fresults['user_level'];
                	$dataUserGroup[] = $fresults['user_group'];
                	$dataActive[]   = $fresults['active'];

        	        $apiresults = array("result" => "success","user_group" => $dataUserGroup, "userno" => $dataUser, "full_name" => $dataFullName, "user_level" => $dataUserLevel, "active" => $dataActive);
			}
		} else {
			$apiresults = array("result" => "Error: User doesn't exist.");
		}
	}
?>
