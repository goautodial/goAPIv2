<?php
 /**
 * @file 		goDeleteLead.php
 * @brief 		API for Deleting Leads
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Abenoja  <alex@goautodial.com>
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

    // POST or GET Variables
        $lead_id = $astDB->escape($_REQUEST['lead_id']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
		
    // Check user_id if its null or empty
        if($lead_id == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for Lead ID.");
        } else {
                $groupId = go_get_groupid($goUser, $astDB);
                
		if (!checkIfTenant($groupId, $goDB)) {
			$ul = "AND user='$user_id'";
                } else {
			$ul = "AND user='$user_id' AND user_group='$groupId'";
                }
		
                if ($groupId != 'ADMIN') {
                        $notAdminSQL = "AND user_group != 'ADMIN'";
                }
				
                //$query = "DELETE FROM vicidial_list WHERE lead_id='$lead_id'";
                //$querygo = "DELETE FROM go_customers WHERE lead_id='$lead_id'";
		$goDB->where('lead_id', $lead_id);
                $rsltvg = $goDB->delete('go_customers');
		$astDB->where('lead_id', $lead_id);
                $rsltv = $astDB->delete('vicidial_list');
                $countResult = $astDB->getRowCount();

                if($rsltv != false){
                        $log_id = log_action($goDB, 'DELETE', $log_user, $ip_address, "Deleted Lead ID: $lead_id", $log_group, $query, $querygo);
			
                        $apiresults = array("result" => "success");
                }else{
			$err_msg = error_handle("10010");
			$apiresults = array("code" => "10010", "result" => $err_msg);
			//$apiresults = array("result" => "Error: Lead ID does not exist.");
                }
		
        }
?>
