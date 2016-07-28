<?php
    #######################################################
    #### Name: goGetAllMusicOnHold.php	               ####
    #### Description: API to get all MOH	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }
 
    	$groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
        	$ul='';
    	} else { 
		$ul = "AND user_group='$groupId'";  
	}

   	$query = "SELECT moh_id, moh_name, active, random, user_group FROM vicidial_music_on_hold WHERE remove='N' $ul ORDER BY moh_id LIMIT $limit;";
   	$rsltv = mysqli_query($link, $query);

	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataModId[] = $fresults['moh_id'];
       		$dataMohName[] = $fresults['moh_name'];
		$dataActive[] = $fresults['active'];
		$dataRandom[] = $fresults['random'];
		$dataUserGroup[] = $fresults['user_group'];
   		$apiresults = array("result" => "success", "moh_id" => $dataModId, "moh_name" => $dataMohName, "active" => $dataActive, "random" => $dataRandom, "user_group" => $dataUserGroup);
	}

?>
