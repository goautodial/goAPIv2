<?php
 /**
 * @file 		goGetTotalCalls.php
 * @brief 		API for Dashboard
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
        $stringv = go_getall_allowed_campaigns($groupId, $astDB);
		if($stringv !== "'ALLCAMPAIGNS'")
			$ul = " AND campaign_id IN ($stringv)";
		else
			$ul = "";
    }

    $NOW = date("Y-m-d");

    $queryTotalcalls = "select sum(calls_today) as getTotalCalls from vicidial_campaign_stats where calls_today > -1 and update_time BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $queryInboundcalls = "select count(call_date) as getTotalInboundCalls from vicidial_closer_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $queryOutboundcalls = "select count(call_date) as getTotalOutboundCalls from vicidial_log where call_date BETWEEN '$NOW 00:00:00' AND '$NOW 23:59:59' $ul";
    
    $dataTotalCalls = $astDB->rawQuery($queryTotalcalls);
    $dataIncalls = $astDB->rawQuery($queryInboundcalls);
    $dataOutcalls = $astDB->rawQuery($queryOutboundcalls);
	
	//$dataTotalCalls = mysqli_fetch_array($rsltvTotalcalls,MYSQLI_ASSOC);
	//$dataIncalls = mysqli_fetch_array($rsltvIncalls,MYSQLI_ASSOC);
	//$dataOutcalls = mysqli_fetch_array($rsltvOutcalls,MYSQLI_ASSOC);
	
    $data = array("getTotalCalls" => $dataTotalCalls['getTotalCalls'], "getTotalInboundCalls" => $dataIncalls['getTotalInboundCalls'], "getTotalOutboundCalls" => $dataOutcalls['getTotalOutboundCalls']);
	
    $apiresults = array("result" => "success", "data" => $data, "query" => $queryOutboundcalls ); 
?>
