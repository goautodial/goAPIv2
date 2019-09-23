<?php
 /**
 * @file 		goGetCallRecordingInfo.php
 * @brief 		API for getting specific recordings
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Demian Lizandro A. Biscocho
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	include_once ("goAPI.php");	

	### POST or GET Variables
    $recording_id 						= $astDB->escape($_REQUEST['recording_id']);

	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif($recording_id == null) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Recording ID"
		);
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
		}
		
		$cols 							= array(
			"recording_id", 
			"length_in_sec", 
			"filename", 
			"location", 
			"lead_id", 
			"user", 
			"start_time", 
			"end_time"
		);
		
		$astDB->where('recording_id', $recording_id);
		$rsltv 							= $astDB->get('recording_log', NULL, $cols);
		
		if ($astDB->count > 0) {
			foreach ($rsltv as $fresults) {
				$dataRecordingId[] 		= $fresults['recording_id'];
				$dataLeadId[] 			= $fresults['lead_id'];
				$dataUser[] 			= $fresults['user'];
				//$dataLocation[] = $fresults['location'];
				
				//$querygo = "SELECT data, mimetype FROM go_recordings WHERE recording_id='{$rsltv['recording_id']}';";
				$goDB->where('recording_id', $fresults['recording_id']);
				$rsltvgo				= $goDB->get('go_recordings', null, 'data, mimetype');
				
				foreach ($rsltvgo as $fresultsgo){
					$dataData[] 		= $fresultsgo['data'];	
					$dataMimetype[] 	= $fresultsgo['mimetype'];
				}
				
				$apiresults 			= array(
					"result" 				=> "success",
					"recording_id" 			=> $dataRecordingId,
					"lead_id" 				=> $dataLeadId,
					"users" 				=> $dataUser,					
					"mimetype" 				=> $dataMimetype,
					"location" 				=> $dataData
				);
			}
		} else {
            $apiresults 				= array(
				"result" 					=> "Error: No information available."
			);
		}
	}
?>
