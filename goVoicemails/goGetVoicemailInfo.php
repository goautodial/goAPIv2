<?php
/**
 * @file 		goGetVoicemailinfo.php
 * @brief 		API for getting voicemail details
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author		Alexander Jim Abenoja
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
		
	### POST or GET Variables
	$voicemail_id 						= $astDB->escape($_REQUEST["voicemail_id"]);
	$active								= $astDB->escape($_REQUEST['active']);
   
	### Default values
    $defActive 							= array(
		"Y",
		"N"
	);  

	if (!isset($session_user) || is_null($session_user)){
		$apiresults 					= array(
			"result" 						=> "Error: Session User Not Defined."
		);
	} elseif ($voicemail_id == null || strlen($voicemail_id) < 3) {
		$apiresults 					= array(
			"result" 						=> "Error: Set a value for Voicemail ID."
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$apiresults 					= array(
			"result" 						=> "Error: Default value for active is Y or N only."
		);
	} else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere("user_group", "---ALL---");
		}

		$astDB->where("voicemail_id", $voicemail_id);
		$astDB->orderBy("voicemail_id", "desc");
		$rsltv 							= $astDB->getOne("vicidial_voicemail");
		//$log_id 						= log_action($goDB, "VIEW", $log_user, $log_ip, "Viewed voicemail ID: $carrier_id", $astDB->getLastQuery());
		
		if ($astDB->count > 0) {						
			$apiresults 				= array(
				"result" 					=> "success",
				"data"						=> $rsltv
			);
		} else {
			$apiresults 				= array(
				"result" 					=> "Error: Empty."
			);
		}
	}
?>
