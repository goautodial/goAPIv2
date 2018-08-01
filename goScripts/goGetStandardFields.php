<?php 
 /**
 * @file        goGetStandardFields.php
 * @brief       API for Getting Standard Fields for Scripts
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author      Jeremiah Sebastian V. Samatra
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
 
	$log_user 									= $session_user;
	$log_group 									= go_get_groupid($session_user, $astDB);    
	//$log_ip 									= $astDB->escape($_REQUEST["log_ip"]);
		
	if ( empty($log_user) || is_null($log_user) ) {
		$apiresults 							= array(
			"result" 								=> "Error: Session User Not Defined."
		);
	} else {
		$queryGetStandardFields					= "SELECT `column_name` FROM information_schema.columns WHERE table_name='vicidial_list';";
		$sql 									= $astDB->rawQuery($queryGetStandardFields);

		foreach ($sql as $fresults) {
			$field_name[] 						= $fresults['column_name'];
		}

		/*while ($fieldname = $fresults) {
			$field_name[] 						= $fresults['column_name'];
		}*/
		
		$apiresults 							= array(
			"result" 								=> "success", 
			"field_name" 							=> $field_name
		);	
	}

?>
