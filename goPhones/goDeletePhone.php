<?php
/**
 * @file 		goDeletePhone.php
 * @brief 		API to delete specific Phone 
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja <alex@goautodial.com>
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
**/
    
    include_once("goAPI.php");
    
    // POST or GET Variables
    $extension = $_REQUEST['extension'];
	$action = $_REQUEST['action'];
	$ip_address = $_REQUEST['hostname'];
    
    // Check campaign_id if its null or empty
	if(empty($session_user)){
		$apiresults = array("result" => "Error: Session User Not Defined.");
	}elseif(empty($extension)) { 
		$apiresults = array("result" => "Error: Set a value for Extension.");
	} else {
		$groupId = go_get_groupid($session_user, $astDB);
		$log_user = $session_user;

		if (!checkIfTenant($groupId)) {
			$astDB->where("extension", $extension);
			//$ul = "WHERE extension='$extension'";
		} else {
			$astDB->where("extension", $extension);
			$astDB->where("user_group", $groupId);
			//$ul = "WHERE extension='$extension' AND user_group='$groupId'";
		}
		
		$exploded = explode(",",$extension);
		$error_count = 0;
		$string_return = "";
		for($i=0;$i < count($exploded);$i++){
			$astDB->where("extension", $exploded[$i]);
			$fresults = $astDB->getOne("phones", "extension");
			//$query = "SELECT extension  FROM phones WHERE extension='".$exploded[$i]."';";
			$dataExtension = $fresults['extension'];
			
			$astDB->where("extension", $dataExtension);
			$deleteQuery = $astDB->delete("phones");
				//$deleteQuery = "DELETE FROM phones WHERE extension = '$dataExtension'"; 
			
			$kamDB->where("username", $dataExtension);
			$kamDB->delete("subscriber");
				//$deleteQueryB = "DELETE FROM subscriber where username= '$dataExtension'"; 
			
			$astDB->where("extension", $dataExtension);
			$astDB->getOne("phones", "extension");
				//$query = "SELECT extension  FROM phones  WHERE extension='".$dataExtension."';";
			$countResult = $astDB->count;
			
			if($countResult > 0) {
				$error_count = $error_count + 1;
			}
			
			$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Phone $extension", $groupId, $deleteQuery);
				
		}
		
		if($error_count > 0) {
			$apiresults = array("result" => "Error: Delete Failed. Number of Errors: ".$error_count);
		} else {
			$apiresults = array("result" => "success"); 
		}
			
	}//end
?>
