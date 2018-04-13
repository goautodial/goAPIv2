<?php
 /**
 * @file        goGetAllLeadRecycling.php
 * @brief 	    API for Getting All Lead Recycling
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

    $campaign_id = $astDB->escape($_REQUEST['campaign_id']);
    
    if(empty($session_user)) {
        $err_msg = error_handle("40001", "session_user");
        $apiresults = array("code" => "40001", "result" => $err_msg);
        //$apiresults = array("result" => "Error: Set a value for Campaign ID.");
    } else {
        $groupId = go_get_groupid($session_user, $astDB);

        //$query = "SELECT * FROM vicidial_lead_recycle ORDER BY recycle_id;";
        $astDB->orderBy('recycle_id', 'desc');
        $rsltv = $astDB->get('vicidial_lead_recycle');
        $count = $astDB->getRowCount();
        $x = 0;
        foreach ($rsltv as $fresults) {
            $output[] = array("recycle_id" => $fresults['recycle_id'], "campaign_id" => $fresults['campaign_id'], "status" => $fresults['status'], "attempt_delay" => $fresults['attempt_delay'], "attempt_maximum" => $fresults['attempt_maximum'],"active" => $fresults['active']);
            $x++;
        }
        $apiresults = array("result" => "success", "data" => $output);
    }
?>