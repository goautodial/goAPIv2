<?php
    #######################################################
    #### Name: goGetCallRecordingInformation.php       ####
    #### Description: API to get specific recordings   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Demian Lizandro A. Biscocho       ####
    #### License: AGPLv2                               ####
    #######################################################
    
    $recording_id = $_REQUEST['recording_id'];

	if($recording_id == null) {
			$apiresults = array("result" => "Error: Set a value for Recording ID");
	} else {
		$groupId = go_get_groupid($goUser, $astDB);
		
		if (!checkIfTenant($groupId, $goDB)) {
			$ul='';
		} else { 
			$ul = "WHERE user_group='$groupId'";  
		}
		
		//$query = "SELECT recording_id, length_in_sec, filename, location, lead_id, user, start_time, end_time FROM recording_log WHERE recording_id = '$recording_id'";
		$astDB->where('recording_id', $recording_id);
		$rsltv = $astDB->get('recording_log', null, 'recording_id, length_in_sec, filename, location, lead_id, user, start_time, end_time');
		$countResult = $astDB->getRowCount();
		
		if($countResult > 0) {
			foreach ($rsltv as $fresults) {
				$dataRecordingId[] = $fresults['recording_id'];
				$dataLeadId[] = $fresults['lead_id'];
				$dataUser[] = $fresults['user'];
				//$dataLocation[] = $fresults['location'];
				
				//$querygo = "SELECT data, mimetype FROM go_recordings WHERE recording_id='{$rsltv['recording_id']}';";
				$goDB->where('recording_id', $fresults['recording_id']);
				$rsltvgo = $goDB->get('go_recordings', null, 'data, mimetype');
				
				foreach ($rsltvgo as $fresultsgo){
					$dataData[] = $fresultsgo['data'];	
					$dataMimetype[] = $fresultsgo['mimetype'];
				}
				
				$apiresults = array(
					"result" => "success",
					"recording_id" => $dataRecordingId,
					"lead_id" => $dataLeadId,
					"users" => $dataUser,					
					"mimetype" => $dataMimetype,
					"location" => $dataData
				);
			}
		}else {
            $apiresults = array("result" => "Error: No information available.");
		}
	}
?>
