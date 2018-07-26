<?php
 /**
 * @file 		goGetCampaignsResources.php
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

    include_once ("goAPI.php");
 
	$log_user 										= $session_user;
	$log_group 										= go_get_groupid($session_user, $astDB); 
	$log_ip 										= $astDB->escape($_REQUEST['log_ip']);    
	
    // ERROR CHECKING 
	if (!isset($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} else {			    
		if (checkIfTenant($log_group, $goDB)) {
			$ul 									= "WHERE vl.user_group = '$log_group' or vl.user_group = '---ALL---' ";
		} else {
			if($log_group !== "ADMIN"){
				$ul 								= "WHERE vl.user_group = '$log_group' or vl.user_group = '---ALL---' ";
			} else {
				$ul 								= "";
			}
		}
		
        $query 										= "
			SELECT 
				COUNT(vh.campaign_id) as mycnt, 
				vl.campaign_id, 
				vl.campaign_name,
				vl.local_call_time, 
				vl.user_group 
			FROM 
				vicidial_hopper as vh 
			RIGHT OUTER JOIN 
				vicidial_campaigns as vl 
			ON (
				vl.campaign_id=vh.campaign_id
			) 
			RIGHT OUTER JOIN 
				vicidial_call_times as vct 
			ON (
				call_time_id=local_call_time
			)
			$ul
			AND
				vl.active='Y'
			AND 
				ct_default_start 
			BETWEEN 
				'SELECT NOW ();' AND ct_default_stop > 'SELECT NOW ();' 
			GROUP BY 
				vl.campaign_id HAVING COUNT(vh.campaign_id) < '200' 
			ORDER BY 
				mycnt DESC , 
				campaign_id ASC
			LIMIT 100
		";
		
        $rsltv = $astDB->rawQuery($query); 
		
		$data 										= array();
        if($astDB->count > 0) {
            //$data = array();
            
			foreach ($rsltv as $fresults){
				array_push($data, $fresults);
			}						
        }
        
        $apiresults 								= array(
			"result" 									=> "success", 
			//"query"										=> $astDB->getLastQuery(),
			"data" 										=> $data
		);
	}

?>
