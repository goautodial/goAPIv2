<?php
/**
 * @file 		goGetSystemSettingInfo.php
 * @brief 		API for getting System Settings Info
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author     		Thom Bernarth Patacsil
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
	} else {
		$rsltv 							= $astDB->getOne("system_settings");
		
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
