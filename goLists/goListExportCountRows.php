 <?php
 /**
 * @file        goListExportCountRows.php
 * @brief       API to Count export list Rows
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 * @author      Thom Bernarth Patacsil
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
 
	ini_set('memory_limit', '2048M');
	include_once ("goAPI.php");
	
	$list_id 											= $astDB->escape($_REQUEST["list_id"]);
	
	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($list_id) || is_null($list_id)) {
		$err_msg 										= error_handle("10107");
	        $apiresults 									= array(
			"code" 											=> "10107",
			"result" 										=> $err_msg
		);
    } else {
		
		$result 									= $astDB->where('list_id', $list_id)
													->getOne("vicidial_list", "count(lead_id) as row_count");
		
		$apiresults 								= array(
			"result" 								=> "success", 
			"row_count" 								=> $result['row_count']
		);
	}
	

?>

