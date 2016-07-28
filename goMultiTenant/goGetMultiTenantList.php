<?php
    #######################################################
    #### Name: getAllCampaigns.php	               ####
    #### Description: API to get all campaigns         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include "../goFunctions.php";
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    	$groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
        	$ul='';
    	} else { 
		$ul = "WHERE user_group='$groupId'";  
	}

   	$query = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT $limit;";
   	$rsltv = mysqli_query($linkgo, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataTenantId[] = $fresults['tenant_id'];
       		$dataTenantName[] = $fresults['tenant_name'];// .$fresults['dial_method'].$fresults['active'];
		//$dataDialMethod[] = $fresults['active'];
		$dataActive[] = $fresults['active'];

	$query1 = "SELECT count(*) as cnt FROM vicidial_users WHERE user_group='{$fresults['tenant_id']}' AND user_level < '7';";
	$rsltv1 = mysqli_query($link, $query1);
	while($fresults1 = mysqli_fetch_array($rsltv1, MYSQLI_ASSOC)){
		$dataCount[] = $fresults1['cnt'];
	}


   		$apiresults = array("result" => "success", "cnt" => $dataCount, "tenant_id" => $dataTenantId, "tenant_name" => $dataTenantName, "active" => $dataActive);
	}

?>
