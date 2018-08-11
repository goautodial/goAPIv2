<?php
 /**
 * @file        goGetDIDInfo.php
 * @brief       API to get specific DID Details
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Alexander Jim Abenoja
 * @author      Jeremiah Sebastian V. Samatra 
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
    
    // POST or GET Variables
    $did_id 										= $astDB->escape($_REQUEST['did_id']);
    
	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($did_id) || is_null($did_id)) {
        $apiresults 								= array(
			"result" 									=> "Error: DID ID Not Defined."
		);
    } else {    
		if (checkIfTenant($log_group, $goDB)) {
            $astDB->where("user_group", $log_group);
            $astDB->orWhere("user_group", "---ALL---");
		}

        $astDB->where("did_id", $did_id);
        $fresults 									= $astDB->getOne("vicidial_inbound_dids");
   		//$query = "SELECT * FROM vicidial_inbound_dids $ul order by did_pattern LIMIT 1;";
   		
		if ($astDB->count > 0) {
			$apiresults 							= array(
				"result" 								=> "success", 
				"data" 									=> $fresults
			);
		} else {
			$apiresults 							= array(
				"result" 								=> "Error: DID doesn't exist."
			);
		}
	}
?>
