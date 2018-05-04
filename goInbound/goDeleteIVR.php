<?php
/**
 * @file        goDeleteIVR.php
 * @brief       API to delete specific IVR Menu
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
 
    include_once ("../goFunctions.php");
    
    // POST or GET Variables
    $menu_id = $astDB->escape($_REQUEST['menu_id']);
    $goUser = $astDB->escape($_REQUEST['goUser']);
    $ip_address = $_REQUEST['hostname'];
	
	if(empty($menu_id)){
		$apiresults = array("result" => "Error: Set a value for Menu ID."); 
	} else {
 	
    	$groupId = go_get_groupid($goUser, $astDB);
    	$log_user = $session_user;
		$log_group = $groupId;

		if(!empty($menu_id)){
			$exploded = explode(",", $menu_id);
		}

		for($i=0;$i > count($exploded);$i++){
			if (!checkIfTenant($groupId, $goDB)) {
				$astDB->where("menu_id", $exploded[$i]);
	        	//$ul = "AND menu_id='$menu_id'";
	    	} else {
	    		$astDB->where("menu_id", $exploded[$i]);
	    		$astDB->where("user_group", $groupId);
				//$ul = "AND menu_id='$menu_id' AND user_group='$groupId'";  
			}

			$astDB->where("menu_id", Array("defaultlog") , "NOT IN");
			$selectQuery = $astDB->getOne("vicidial_call_menu");
			//$query = "SELECT menu_id from vicidial_call_menu WHERE menu_id!='defaultlog' $ul order by menu_id LIMIT 1";
	   		//$rsltv = mysqli_query($link, $query);
			//$countResult = mysqli_num_rows($rsltv);

			if($astDB->count > 0) {
				$dataMenuID = $selectQuery['menu_id'];

				if(!empty($dataMenuID)){
					$astDB->where("menu_id", $dataMenuID);
					$mainDelete = $astDB->delete("vicidial_call_menu");
					$deleteQueryA = "DELETE from vicidial_call_menu where menu_id='$dataMenuID' limit 1;";
	   				//$deleteResultA = mysqli_query($link, $deleteQueryA);
					
					$astDB->where("menu_id", $dataMenuID);
					$astDB->delete("vicidial_call_menu_options");
					//$deleteQueryB = "DELETE from vicidial_call_menu_options where menu_id='$dataMenuID';";
	   				//$deleteResultB = mysqli_query($link, $deleteQueryB);
					//echo $deleteQuery;

					$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Call Menu ID $dataMenuID", $log_group, $deleteQueryA);
					
					if($mainDelete)
						$apiresults = array("result" => "success");
					else
						$apiresults = array("result" => "Query Error");
				} else {
					$apiresults = array("result" => "Error: Menu doesn't exist.");
				}

			} else {
				$apiresults = array("result" => "Error: Menu doesn't exist.");
			}
		}//end of loop
	}
?>
