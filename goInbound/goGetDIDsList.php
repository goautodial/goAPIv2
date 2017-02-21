<?php
    #######################################################
    #### Name: goGetDIDsList.php	               ####
    #### Description: API to get all DID Lists         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = mysqli_real_escape_string($link, $_REQUEST['limit']);
    $user_group = mysqli_real_escape_string($link, $_REQUEST['user_group']);
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser);
    
    //if (!checkIfTenant($groupId)) {
	if ($user_group == 'ADMIN') {
        $ul='';
    } else { 
		$ul = "WHERE user_group='$user_group'";  
  	}

   $query = "SELECT did_id,did_pattern,did_description,did_active,did_route from vicidial_inbound_dids $ul order by did_pattern LIMIT $limit";
   $rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_assoc($rsltv)){
	$dataDidID[] = $fresults['did_id'];
	$dataDidPattern[] =  $fresults['did_pattern'];
	$dataDidDescription[] =  $fresults['did_description'];
	$dataActive[] =  $fresults['did_active'];
	$dataDidRoute[] =  $fresults['did_route'];

	$apiresults = array( "result" => "success","did_id" => $dataDidID,  "did_pattern" => $dataDidPattern, "did_description" => $dataDidDescription, "active" => $dataActive, "did_route" => $dataDidRoute);
	}
?>
