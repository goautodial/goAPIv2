<?php
/**
 * @file        goDeletCalltime.php
 * @brief       API to delete specific Call Time
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Warren Ipac Briones <warren@goautodial.com>
 * @author      Alexander Jim H. Abenoja <alex@goautodial.com>
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
    
    // POST or GET Variables
    $call_time_id = $_REQUEST['call_time_id'];
	$ip_address = $_REQUEST['log_ip'];
	$log_user = $session_user;
    $groupId = go_get_groupid($session_user, $astDB);

    // Check Voicemail ID if its null or empty
	if(empty($call_time_id)) { 
		$apiresults = array("result" => "Error: Set a value for Calltime ID."); 
	} else {

        if (checkIfTenant($groupId, $goDB)) {
            $astDB->where("user_group", $groupId);
            //$ul = "AND user_group='$groupId'";
        }

        $astDB->where("call_time_id", $call_time_id);
        $astDB->getOne("vicidial_call_times", "call_time_id");
   		//$queryOne = "SELECT call_time_id FROM vicidial_call_times $ul where call_time_id='".$call_time_id."';";
   		$countResult = $astDB->count;

		if($countResult > 0) {
            $astDB->where("call_time_id", $call_time_id);
            $deleteQuery = $astDB->delete("vicidial_call_times");
            //$deleteQuery = "DELETE FROM vicidial_call_times WHERE call_time_id = '$call_time_id';"; 
			
            $log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Calltime ID: $call_time_id", $groupId);

            if($deleteQuery){
                $apiresults = array("result" => "success");
            }else{
                $astDB->getLastError();
            }

		} else {
			$apiresults = array("result" => "Error: Calltime doesn't exist.");
		}
	}//end
?>
