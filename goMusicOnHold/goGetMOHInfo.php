<?php
    #######################################################
    #### Name: goGetMOHInfo.php		               ####
    #### Description: API to get specific MOH	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $moh_id = $_REQUEST['moh_id'];
	
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
    ### Check moh_id if its null or empty
	if($moh_id == null) { 
		$apiresults = array("result" => "Error: Set a value for MOH ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "";
    		} else { 
			$ul = "AND user_group='$groupId'";  
		}

   		$query = "SELECT moh_id, moh_name, active, random, user_group FROM vicidial_music_on_hold WHERE remove='N' AND moh_id='$moh_id' $ul ORDER BY moh_id LIMIT 1;";
   		$rsltv = mysqli_query($link,$query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		                $dataModId[] = $fresults['moh_id'];
                		$dataMohName[] = $fresults['moh_name'];
                		$dataActive[] = $fresults['active'];
       	 		        $dataRandom[] = $fresults['random'];
                		$dataUserGroup[] = $fresults['user_group'];
                	$apiresults = array("result" => "success", "moh_id" => $dataModId, "moh_name" => $dataMohName, "active" => $dataActive, "random" => $dataRandom, "user_group" => $dataUserGroup);
			}
			
			$log_id = log_action($linkgo, 'VIEW', $log_user, $ip_address, "Viewed info of Music On-Hold: $moh_id", $log_group);
		} else {
			$apiresults = array("result" => "Error: MOH doesn't exist.");
		}
	}
?>
