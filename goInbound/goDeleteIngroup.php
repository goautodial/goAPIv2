<?php
/**
 * @file        goDeleteInbound.php
 * @brief       API to delete specific Inbound
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
    $group_id = $astDB->escape($_REQUEST['group_id']);
    
    $ip_address = $_REQUEST['hostname'];
    
	if(empty($group_id)) { 
		$apiresults = array("result" => "Error: Set a value for Group ID."); 
	} else {
 		$groupId = go_get_groupid($goUser, $astDB);
    	$log_user = $goUser;
		$log_group = $groupId;

		if(!empty($group_id)){
			$exploded = explode(",", $group_id);
		}

		for($i=0;$i > count($exploded);$i++){
			if (!checkIfTenant($groupId, $goDB)) {
				$astDB->where("group_id", $exploded[$i]);
	    		//$ul = "WHERE group_id='$group_id'";
			} else { 
				$astDB->where("group_id", $exploded[$i]);
				$astDB->where("user_group", $groupId);
				//$ul = "WHERE group_id='$group_id' AND user_group='$groupId'";  
			}

			$selectData = $astDB->getOne("vicidial_inbound_groups");
			//$query = "SELECT group_id,group_name FROM vicidial_inbound_groups $ul ORDER BY group_id LIMIT 1;";

			if($astDB->count > 0) {
				$dataGroupID = $selectData['group_id'];
				
				if(!$dataGroupID == null) {
					$astDB->where("group_id", $dataGroupID);
					$astDB->where("group_id", Array("AGENTDIRECT"), "NOT IN");
					$astDB->delete("vicidial_inbound_groups");
					$deleteQueryA = "DELETE from vicidial_inbound_groups where group_id='$dataGroupID' and group_id NOT IN('AGENTDIRECT') limit 1;"; 
	   				//$deleteResultA = mysqli_query($link, $deleteQueryA);
					
					$astDB->where("group_id", $dataGroupID);
					$astDB->delete("vicidial_inbound_group_agents");
					//$deleteQueryB ="DELETE from vicidial_inbound_group_agents where group_id='$dataGroupID';";
	   				//$deleteResultB = mysqli_query($link, $deleteQueryB);

					$astDB->where("group_id", $dataGroupID);
					$astDB->delete("vicidial_live_inbound_agents");
					// $deleteQueryC = "DELETE from vicidial_live_inbound_agents where group_id='$dataGroupID';";
	   				//$deleteResultC = mysqli_query($link, $deleteQueryC);

					$astDB->where("campaign_id", $dataGroupID);
					$astDB->delete("vicidial_campaign_stats");
					//$deleteQueryD = "DELETE from vicidial_campaign_stats where campaign_id='$dataGroupID';";
	   				//$deleteResultD = mysqli_query($link, $deleteQueryD);
					
					$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Inbound Group $dataGroupID", $log_group, $deleteQueryA);

					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "Error: Group  doesn't exist.");
				}
			} else {
				$apiresults = array("result" => "Error: Group doesn't exist.");
			}
		}// end of loop
	}
?>
