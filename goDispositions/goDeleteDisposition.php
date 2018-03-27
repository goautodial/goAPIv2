<?php
 /**
 * @file 		goDeleteDisposition.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
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

    ### POST or GET Variables
	$campaign_id = $astDB->escape($_REQUEST["campaign_id"]);
	//$campaign_id = $astDB->escape($campaign_id);
	
	$statuses = $astDB->escape($_REQUEST["statuses"]);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
    
    ### Check Campaign ID if its null or empty
	if( empty($campaign_id) && empty($statuses)) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
 
		$groupId = go_get_groupid($goUser, $astDB);

		if (!checkIfTenant($groupId, $goDB)) {
			//$ul = "";
		} else {
			//$ul = "AND user_group='$groupId'";
			//$addedSQL = "WHERE user_group='$groupId'";
			$astDB->where('user_group', $groupId);
		}
		
   		//$queryOne = "SELECT campaign_id, status FROM vicidial_campaign_statuses $ul where campaign_id='$campaign_id';";
		$astDB->where('campaign_id', $campaign_id);
   		$rsltvOne = $astDB->get('vicidial_campaign_statuses', null, 'campaign_id, status');
		$countResult = $astDB->getRowCount();

		if($countResult > 0) {
			
			if($statuses != NULL){
				//$deleteQuery = "DELETE FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id' AND status = '$statuses' LIMIT 1;";
				$astDB->where('status', $statuses);
				$astDB->where('campaign_id', $campaign_id);
   				$deleteResult = $astDB->delete('vicidial_campaign_statuses', 1);			
			}else{
				//$deleteQuery = "DELETE FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id';";
				$astDB->where('campaign_id', $campaign_id);
   				$deleteResult = $astDB->delete('vicidial_campaign_statuses');
				//echo $deleteQuery;
			}
			
			$tableQuery = "SHOW tables LIKE 'go_statuses';";
			$checkTable = $goDB->rawQuery($tableQuery);
			$tableExist = $goDB->getRowCount();
			if ($tableExist > 0) {
				//$statusQuery = "DELETE FROM go_statuses WHERE campaign_id='$campaign_id' AND status='$statuses';";
				$goDB->where('campaign_id', $campaign_id);
				$goDB->where('status', $statuses);
				$statusRslt = $goDB->delete('go_statuses');
			}
			
        ### Admin logs
			//$SQLdate = date("Y-m-d H:i:s");
			//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Status $statuses from Campaign $campaign_id','DELETE FROM vicidial_campaign_statuses  WHERE status IN ($statuses) AND campaign_id=$campaign_id;');";
			//$rsltvLog = mysqli_query($linkgo, $queryLog);
			$log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Status $statuses from Campaign $campaign_id", $log_group, $deleteQuery);

			
			$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Campaign statuses doesn't exist.");
		}
	}//end

?>
