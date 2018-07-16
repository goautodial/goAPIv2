<?php
/**
 * @file        goGetSuggestedDIDs.php
 * @brief       API to get suggested DIDs
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho 
 * @author      Noel Umandap
 * @author      Alexander Jim Abenoja
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
	
    $keyword 										= $astDB->escape($_REQUEST['keyword']);
    
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($keyword) || is_null($keyword)) {
		$err_msg 									= error_handle("40001");
        $apiresults 								= array(
			"code" 										=> "40001",
			"result" 									=> $err_msg
		);
    } else {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			$astDB->orWhere('user_group', "---ALL---");
		} else {
			if($log_group !== "ADMIN"){
				$astDB->where('user_group', $log_group);
				$astDB->orWhere('user_group', "---ALL---");
			}
		}
		
		$astDB->where('did_pattern', "$keyword%", 'like');
		$rsltv 										= $astDB->get('vicidial_inbound_dids', NULL, 'did_pattern');
		
		if ($rsltv) {
			foreach ($rsltv as $fresults){
				$dids[] 							= $fresults['did_pattern'];
			}
			
			$dataDID 								= "[";
			foreach ($dids as $did) {
				$dataDID 							.= '"'.$did.'",';
			}
			
			$dataDID 								= rtrim($dataDID, ",");
			$dataDID 								.= "]";
			
			$apiresults 							= array(
				"result" 								=> "success", 
				"data" 									=> $dataDID
			);			
		} else {
			$apiresults 							= array(
				"result" 								=> "error"
			);
		}    
    }  
    
?>
