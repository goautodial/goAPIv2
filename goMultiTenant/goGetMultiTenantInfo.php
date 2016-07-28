<?php
    #######################################################
    #### Name: goGetMultiTenantInfo.php	               ####
    #### Description: API to get specific Tenant       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include "../goFunctions.php";
    
    ### POST or GET Variables
    $tenant_id = $_REQUEST['tenant_id'];
    
    ### Check tenant_id if its null or empty
	if($tenant_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Tenant ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE tenant_id='$tenant_id'";
    		} else { 
			$ul = "WHERE tenant_id='$tenant_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT tenant_id,tenant_name,active FROM go_multi_tenant $ul ORDER BY tenant_id LIMIT 1;";
   		$rsltv = mysqli_query($linkgo, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
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

		} else {
			$apiresults = array("result" => "Error: Tenant doesn't exist.");
		}
	}
?>
