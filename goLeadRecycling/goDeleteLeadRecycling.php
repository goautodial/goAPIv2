<?php
 /**
 * @file        goAddLeadRecycling.php
 * @brief 	    API for Adding Lead Recycling
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
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
	$recycle_id = $astDB->escape($_REQUEST['recycle_id']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']); //optional
	$ip_address = $astDB->escape($_REQUEST['hostname']);
    
    // Check Voicemail ID if its null or empty
    if(empty($session_user) || (empty($recycle_id) && empty($campaign_id))) {
		$err_msg = error_handle("40001", "recycle_id or session_user");
        $apiresults = array("code" => "40001", "result" => $err_msg);
	} else {
        $groupId = go_get_groupid($session_user, $astDB);
        if(empty($campaign_id)){
            //$get_campaign = mysqli_query($link, "SELECT campaign_id FROM vicidial_lead_recycle WHERE recycle_id = '$recycle_id';");
			$astDB->where('recycle_id', $recycle_id);
            $fetch_campaign = $astDB->getOne('vicidial_lead_recycle', 'campaign_id');
            $campaign_id = $fetch_campaign['campaign_id'];
        }

        $check_usergroup = go_check_usergroup_campaign($astDB, $groupId, $campaign_id);
        
        $confirmed_exist = 0;
        if($check_usergroup > 0) {
            if(!empty($campaign_id) && empty($recycle_id)){
                //$check = "SELECT recycle_id FROM vicidial_lead_recycle WHERE campaign_id = '$campaign_id';";
				$astDB->where('campaign_id', $campaign_id);
                $query_check = $astDB->get('vicidial_lead_recycle');
                $num_check = $astDB->getRowCount();
                $confirmed_exist = $confirmed_exist + $num_check;

                //$deleteQuery = "DELETE FROM vicidial_lead_recycle WHERE campaign_id = '$campaign_id';";
				$astDB->where('campaign_id', $campaign_id);
                $deleteResult = $astDB->delete('vicidial_lead_recycle');

                if($deleteResult){
                    $apiresults = array("result" => "success");
                    $log_id = log_action($goDB, 'DELETE', $session_user, $ip_address, "Deleted All Lead Recycling under Campaign ID: $campaign_id", $groupId, $astDB->getLastQuery());
                }
            } else {
                $arr_id = explode(",",$recycle_id);

                for($i=0; $i<count($arr_id);$i++) {
                    $id = $arr_id[$i];
                    //$check = "SELECT recycle_id FROM vicidial_lead_recycle WHERE recycle_id = '$id';";
					$astDB->where('recycle_id', $id);
                    $query_check = $astDB->get('vicidial_lead_recycle');
                    $num_check = $astDB->getRowCount();
                    $confirmed_exist = $confirmed_exist + $num_check;

                    //$deleteQuery = "DELETE FROM vicidial_lead_recycle WHERE recycle_id = '$id';";
					$astDB->where('recycle_id', $id);
                    $deleteResult = $astDB->delete('vicidial_lead_recycle');
                    if($deleteResult)
                        $deleted_id[] = $id;
					
                    $log_id = log_action($goDB, 'DELETE', $session_user, $ip_address, "Deleted Lead Recycling ID: $d", $groupId, $astDB->getLastQuery());
                }

                if((count($deleted_id) === count($arr_id)) && $confirmed_exist === count($deleted_id)) {
                    $imploded_ids = implode(",", $deleted_id);
                    if(empty($imploded_ids)) $imploded_ids=0;
                    $apiresults = array("result" => "success", "Deleted Lead Recycles:" => $imploded_ids);
                } else {
                    $imploded_ids = implode(",", $deleted_id);
                    if(empty($imploded_ids)) $imploded_ids=0;
                    $apiresults = array("result" => "Error: Some IDs are not deleted because they may not exist.");
                }
            }
        }else{
            $apiresults = array("result" => "Error: Current user ".$session_user." doesn't have enough permission to access this feature");
        }
	}//end
?>