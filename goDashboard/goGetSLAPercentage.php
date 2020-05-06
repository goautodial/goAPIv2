<?php
 /**
 * @file 		goGetSLAPercentage.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
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
        $ul='';
    } else { 
        $stringv = go_getall_allowed_users($groupId, $astDB);
        $ul = "AND user_group not in ('','NULL','ADMIN') AND user_group in ($stringv)";
    }

    $NOW = date("Y-m-d");
    $queue_seconds = "queue_seconds <= 20";
    
    $query = "SELECT user_group, sum(term_reason in ('ABANDON','AFTERHOURS')) as abandon, sum(queue_seconds <= 20) as callsansweredlessthan20sec, sum(term_reason not in ('ABANDON','AFTERHOURS')) as answered, count(*) as calls_today, round((sum($queue_seconds <= 20)/count(*))*100) as SLA, round((sum(length_in_sec)/sum(user not in ('NULL','','VDCL')))/60,2) as AHT from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    //$query = "SELECT concat(round((sum($queue_seconds)/count(*))))*100 as SLA from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";

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
