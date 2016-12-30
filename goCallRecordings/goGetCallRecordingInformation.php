<?php
    #######################################################
    #### Name: goGetCallRecordingInformation.php       ####
    #### Description: API to get specific recordings   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro A. Biscocho       ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $recording_id = $_REQUEST['recording_id'];

        if($recording_id == null) {
                $apiresults = array("result" => "Error: Set a value for Recording ID");
        } else {
 
		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
			$ul='';
		} else { 
			$ul = "WHERE user_group='$groupId'";  
		}
		
		
		$query = "
		SELECT        
                        recording_id,
                        length_in_sec,
                        filename,
			location,
			lead_id,
			user,
			start_time, 
                        end_time
		FROM    
                        recording_log 
		WHERE
			recording_id = '$recording_id'";
			
		$rsltv = mysqli_query($link, $query);
		
		$countResult = mysqli_num_rows($rsltv);
		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                                $dataRecordingId[] = $fresults['recording_id'];
				$dataLeadId[] = $fresults['lead_id'];
				$dataUser[] = $fresults['user'];
				//$dataLocation[] = $fresults['location'];
			
                        $querygo = "
                        SELECT      
                                    data, 
                                    mimetype 
                        FROM        
                                    go_recordings 
                        WHERE 
                                    recording_id='{$fresults['recording_id']}';";
                                    
                        $rsltvgo = mysqli_query($linkgo, $querygo);
	
			while($fresultsgo = mysqli_fetch_array($rsltvgo, MYSQLI_ASSOC)){
				$dataData[] = $fresultsgo['data'];	
				$dataMimetype[] = $fresultsgo['mimetype'];
			}
		
				$apiresults = array(
					"result" => "success",
					"recording_id" => $dataRecordingId,
					"lead_id" => $dataLeadId,
					"users" => $dataUser,
					"location" => $dataData,
					"mimetype" => $$dataMimetype
				);
			}
		}else {
                        $apiresults = array("result" => "Error: No information available.");
		}

	}
?>
