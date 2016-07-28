<?php
    #######################################################
    #### Name: goDeleteMOH.php		               ####
    #### Description: API to delete specific moh       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    ### POST or GET Variables
    $moh_id = $_REQUEST['moh_id'];
    
    ### Check campaign_id if its null or empty
	if($moh_id == null) { 
		$apiresults = array("result" => "Error: Set a value for MOH ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "AND moh_id='$moh_id'";
    		} else { 
			$ul = "AND moh_id='$moh_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT moh_id FROM vicidial_music_on_hold WHERE remove='N' $ul ORDER BY moh_id LIMIT 1";
   		$rsltv = mysqli_query($link,$query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataMOHID = $fresults['moh_id'];
			}

			if(!$dataMOHID == null) {
				$deleteQueryA = "DELETE FROM vicidial_music_on_hold WHERE moh_id IN ('$dataMOHID')"; 
   				$deleteResultA = mysqli_query($link, $deleteQueryA);
				$deleteQueryB = "DELETE FROM vicidial_music_on_hold_files WHERE moh_id IN ('$dataMOHID')";
   				$deleteResultB = mysqli_query($link, $deleteQueryB);
				//echo $deleteQuery;
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: MOH doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: MOH doesn't exist.");
		}
	}
?>
