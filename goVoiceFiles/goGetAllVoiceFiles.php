<?php
 /**
 * @file 		goGetAllVoiceFiles.php
 * @brief 		API for Voice Files
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

	include_once("goAPI.php");
	
	$limit 											= 50; 
	
	if (isset($_REQUEST['limit'])) {
		$limit 										= $astDB->escape($_REQUEST['limit']);
	}
	
    // Error Checking
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {	
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where('user_group', $log_group);
		}

		$astDB->where("user", $log_user);
		$astDB->where('user_level', '6', '>');
		$astDB->get('vicidial_users');

		if ($astDB->count < 1) {
			$result 								= 'ERROR';
			$result_reason 							= "sounds_list USER DOES NOT HAVE PERMISSION TO VIEW SOUNDS LIST";
			$apiresults 							= array(
				"result" 								=> "Error".$result_reason
			);
		} else {
			//$query = "SELECT goFilename, goFileDate, goFilesize, goDirectory FROM sounds $ul";
			if($log_group === 'ADMIN'){
				$exec_query = $goDB->get('sounds');
			} else {
				$exec_query = $goDB->rawQuery("SELECT * FROM `sounds` WHERE `uploaded_by` IN (SELECT `name` FROM `users` WHERE `user_group` = '$log_group')");
			}

			if ($goDB->count > 0) {
				foreach ($exec_query as $rslt) {
					$file_names[] 					= $rslt['goFilename'];
					$file_dates[] 					= $rslt['goFileDate'];
					$file_size[] 					= $rslt['goFilesize'];
					$file_directory[] 				= $rslt['goDirectory'];
				}
				
				$apiresults 						= array(
					"result" 							=> "success", 
					"file_name" 						=> $file_names, 
					"file_date" 						=> $file_dates, 
					"file_size" 						=> $file_size, 
					"file_directory" 					=> $file_directory
				);
			} else {
				$apiresults 						= array(
					"result" 							=> "Error"
				);
			}
		}
	}
	
?>
