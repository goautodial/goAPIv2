<?php
 /**
 * @file 		goGetOfflineActiveAgents.php
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

    $groupId = go_get_groupid($session_user, $astDB);
    
    if (checkIfTenant($groupId, $goDB)) {
        $ul=' AND vicidial_users.user_level != 4';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
        $ul = " AND vicidial_users.user IN ($stringv) AND vicidial_users.user_level != 4";
    }
    
    $query_OfflineActiveAgents = "SELECT vicidial_users.user_id as 'vu_user_id', vicidial_users.user as 'vu_user', vicidial_users.full_name as 'vu_full_name',vicidial_users.user_group as 'vu_user_group', vicidial_users.user_level as 'vu_user_level', vicidial_users.active as 'vu_status' from vicidial_users where vicidial_users.active='Y' AND vicidial_users.user NOT IN (SELECT vicidial_live_agents.user as 'vla_user' from vicidial_live_agents) $ul";     
    
    //$queryGo = "SELECT userid, avatar FROM users";
    $rsltvGo = $goDB->get('users', null, 'userid, avatar');
    $countResultGo = $goDB->getRowCount();
        
    if($countResultGo > 0) {
        $dataGo = array();
        foreach ($rsltvGo as $fresultsGo){
            array_push($dataGo, $fresultsGo);
        }
    }
    
    $rsltvOfflineAgents = $astDB->rawQuery($query_OfflineActiveAgents);
    $data = array();        
    foreach ($rsltvOfflineAgents as $resultsOfflineAgents){               
        array_push($data, $resultsOfflineAgents);            
    }
    
    //$dataM = array_merge($data, $dataGo);        
    $apiresults = array("result" => "success", "data" => $data, "dataGo" => $dataGo);

?>
