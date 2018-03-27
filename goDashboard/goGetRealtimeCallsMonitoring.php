<?php
 /**
 * @file 		goGetRealtimeCallesMonitoring.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author      Jericho James Milo  <james@goautodial.com>
 * @author     	Demian Lizandro A. Biscocho  <demian@goautodial.com>
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

    $groupId = go_get_groupid($goUser, $astDB);

    if (!checkIfTenant($groupId, $goDB)) {
        $ul = "";
    } else {
        $stringv = go_getall_allowed_campaigns($goUser, $astDB);
        $ul = " where campaign_id IN ('$stringv') ";
    }   
        
    //$query = "SELECT status,phone_number,call_type,UNIX_TIMESTAMP(call_time) as 'call_time',vac.campaign_id from vicidial_auto_calls as vac, vicidial_campaigns as vc, vicidial_inbound_groups as vig where (vac.campaign_id=vc.campaign_id OR vac.campaign_id=vig.group_id) $ul GROUP BY status,call_type,phone_number";
    $query = "SELECT status,phone_number,call_type,UNIX_TIMESTAMP(call_time) as 'call_time',vac.campaign_id from vicidial_auto_calls as vac, vicidial_campaigns as vc, vicidial_inbound_groups as vig $ul GROUP BY status,call_type,phone_number";
    $rsltv = $astDB->rawQuery($query);
    $countResult = $astDB->getRowCount();
    //echo "<pre>";
    //var_dump($rsltv);   
        
    if($countResult > 0) {
        $data = array();
        foreach ($rsltv as $fresults){       
            array_push($data, $fresults);
        }
        $apiresults = array("result" => "success", "data" => $data);
    } 
    
?>
