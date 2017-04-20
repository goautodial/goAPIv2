<?php
    #######################################################
    #### Name: goGetUserGroupsList.php	               ####
    #### Description: API to get all user group        ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 100; } else { $limit = $limit; }
 
    //$groupId = go_get_groupid($session_user);
    $groupId = $_REQUEST['group_id'];
    
	if (checkIfTenant($groupId)) {
        $ul='';
    } else {
		if($groupId !== "ADMIN")
		$ul = "WHERE user_group='$groupId'";
		else
		$ul = "";
	}

	$group_type = "Default";
   	$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY group_name LIMIT $limit;";
   	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$group_type = 'Default';
		$gQuery = "SELECT * FROM go_multi_tenant WHERE tenant_id='{$fresults['user_group']}';";
		$gRslt = mysqli_query($linkgo, $gQuery);
		
		if (mysqli_num_rows($gRslt)) {
			$group_type = 'Multi-tenant';
		}
		$dataUserGroup[] = $fresults['user_group'];
       	$dataGroupName[] = $fresults['group_name'];// .$fresults['dial_method'].$fresults['active'];
		$dataGroupType[] = $group_type;
		$dataForced[] = $fresults['forced_timeclock_login'];
   		$apiresults = array("result" => "success", "user_group" => $dataUserGroup, "group_name" => $dataGroupName, "group_type" => $dataGroupType, "forced_timeclock_login" => $dataForced);
	}

?>
