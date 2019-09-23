<?php
/**
 * @file 		goGetAllVoicemails.php
 * @brief 		API for getting call carriers
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author		Alexander Jim Abenoja
 * @author     	Chris Lomuntad 
 * @author     	Jeremiah Sebastian Samatra
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
    
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} 
	
	if (isset($_REQUEST['limit'])) {
		$limit 							= $astDB->escape($_REQUEST['limit']);
	} else { 
		$limit 							= "50"; 
	} 
	
	if (checkIfTenant($log_group, $goDB)) {
		$astDB->where("user_group", $log_group);
		$astDB->orWhere("user_group", "---ALL---");
	}

	$astDB->orderBy('voicemail_id', 'desc');
   	$rsltv 								= $astDB->get('vicidial_voicemail', $limit);

   	if ($astDB->count > 0) {	
		foreach ($rsltv as $fresults) {
			$dataVoicemailID[] 			= $fresults['voicemail_id'];
			$dataFullname[] 			= $fresults['fullname'];
			$dataActive[] 				= $fresults['active'];
			$dataMessages[] 			= $fresults['messages'];
			$dataOldMessages[] 			= $fresults['old_messages'];
			$dataDeleteVMAfterEmail[] 	= $fresults['delete_vm_after_email'];
			$dataUserGroup[] 			= $fresults['user_group'];			
		}
		
		$apiresults 					= array(
			"result" 						=> "success", 
			"voicemail_id" 					=> $dataVoicemailID, 
			"fullname" 						=> $dataFullname, 
			"active" 						=> $dataActive, 
			"messages" 						=> $dataMessages, 
			"old_messages" 					=> $dataOldMessages, 
			"delete_vm_after_email" 		=> $dataDeleteVMAfterEmail, 
			"user_group" 					=> $dataUserGroup
		);
	} else {
		$apiresults 					= array(
			"result" 						=> "Empty"
		);
	}

?>
