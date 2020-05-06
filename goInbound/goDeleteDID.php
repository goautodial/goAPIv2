<?php
/**
 * @file        goDeleteDID.php
 * @brief       API to delete specific DID
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jeremiah Sebastian V. Samatra  <jeremiah@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
    
    // POST or GET Variables
    $did_id = $astDB->escape($_REQUEST['did_id']);
    $ip_address = $_REQUEST['hostname'];
    
	if($did_id == null) { 
		$apiresults = array("result" => "Error: Set a value for DID ID."); 
	} else {
 
    	$groupId = go_get_groupid($goUser, $astDB);
		$log_user = $goUser;
		$log_group = $groupId;

		if(!empty($did_id)){
			$exploded = explode(",", $did_id);
		}
		for($i=0;$i < count($exploded);$i++){
			$id = $exploded[$i];

			if (!checkIfTenant($groupId, $goDB)) {
				$astDB->where("did_id", $id);
	    		//$ul = "WHERE did_id='$did_id'";
			} else { 
				$astDB->where("did_id", $id);
				$astDB->where("user_group", $groupId);
				//$ul = "WHERE did_id='$did_id' AND user_group='$groupId'";  
			}

			$selectData = $astDB->getOne("vicidial_inbound_dids");
			//$query = "SELECT did_id,did_pattern from vicidial_inbound_dids $ul order by did_pattern LIMIT 1";
	   		
			if($astDB->count > 0) {
				$dataDIDID = $selectData['did_id'];

				if(!empty($dataDIDID)) {
					$astDB->where("did_id", $dataDIDID);
					$deleteAction = $astDB->delete("vicidial_inbound_dids");
					$deleteQuery = "DELETE from vicidial_inbound_dids where did_id='$dataDIDID' limit 1;"; 
	   				
	       			$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted DID ID $dataDIDID", $log_group, $deleteQuery);

					if($deleteAction)
						$apiresults = array("result" => "success");
					else
						$apiresults = array("result" => "Error: Error in Query");
					
				} else {
					$apiresults = array("result" => "Error: DID doesn't exist.");
				}

			} else {
				$apiresults = array("result" => "Error: DID doesn't exist.");
			}
		}//end of loop
	}
?>
