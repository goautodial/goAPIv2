<?php
 /**
 * @file        goGetLeadRecycleInfo.php
 * @brief 	    API for Getting Lead Recycling Info
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Alexander Abenoja  <alex@goautodial.com>
 * @author      Chris Lomuntad  <chris@goautodial.com>
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
    
    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    $status = $astDB->escape($_REQUEST['status']);

    $groupId = go_get_groupid($session_user, $astDB);
    $check_usergroup = go_check_usergroup_campaign($astDB, $groupId, $campaign_id);

    if(empty($campaign_id) || empty($status) || empty($session_user)) {
        $err_msg = error_handle("40001", "campaign_id, session_user and status");
        $apiresults = array("code" => "40001", "result" => $err_msg);
    } elseif($check_usergroup <= 0){
        $apiresults = array("result" => "Error: Usergroup error. You don't have permission to access this feature.");
    }else {
        //$query = "SELECT * FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id' AND status='$status' ORDER BY status LIMIT 1;";
        $astDB->where('campaign_id', $campaign_id);
        $astDB->where('status', $status);
        $astDB->orderBy('status', 'desc');
        $rsltv = $astDB->getOne('vicidial_lead_recycle');
        $exist = $astDB->getRowCount();

        if($exist >= 1) {
            foreach ($rsltv as $fresults){
                $dataCampID[] = $fresults['campaign_id'];
                $dataStatus[] = $fresults['status'];
                $dataAttemptDelay[] = $fresults['attempt_delay'];
                $dataAttemptMax[] = $fresults['attempt_maximum'];
                $dataActive[] = $fresults['active'];
            }
            $apiresults = array("result" => "success", "campaign_id" => $dataCampID, "status" => $dataStatus, "attempt_delay" => $dataAttemptDelay, "attempt_maximum" => $dataAttemptMax, "active" => $dataActive);
        } else {
            $apiresults = array("result" => "Error: Lead Filter does not exist.");
        }
    }
?>
