<?php
 /**
 * @file        goGetPauseCodeInfo.php
 * @brief       API to get specific Pause Code
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author     	Noel Umandap
 * @author     	Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho
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
 
	$log_user 								= $session_user;
	$log_group 								= go_get_groupid($session_user, $astDB); 
	$log_ip 								= $astDB->escape($_REQUEST["log_ip"]);
	
	### POST or GET Variables
	$campaign_id		 					= $astDB->escape($_REQUEST['pauseCampID']);
	$pause_code 							= $astDB->escape($_REQUEST['pause_code']);

    ### ERROR CHECKING ...
	if (!isset($session_user) || is_null($session_user)){
		$apiresults 						= array(
			"result" 							=> "Error: Session User Not Defined."
		);
	} elseif ($campaign_id == null || strlen($campaign_id) < 3) {
		$apiresults 						= array(
			"result" 							=> "Error: Set a value for CAMP ID not less than 3 characters."
		);
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code) || $pause_code == null) {
		$apiresults 						= array(
			"result" 							=> "Error: Special characters found in pause code and must not be empty"
		);
	} else {
		$cols 								= array(
			"pause_code", 
			"pause_code_name", 
			"billable", 
			"campaign_id"
		);
		
        $astDB->where('campaign_id', $campaign_id);
        $astDB->where('pause_code', $pause_code);
        $hotkey 							= $astDB->get('vicidial_pause_codes', null, $cols);

        if($hotkey){
            foreach($hotkey as $fresults){
                $dataCampID[]   			= $fresults['campaign_id'];
                $dataPC[]       			= $fresults['pause_code'];
                $dataPCN[]      			= $fresults['pause_code_name'];
                $dataBill[]     			= $fresults['billable']; 
            }

            $apiresults					 	= array(
				"result" 						=> "success", 
				"campaign_id" 					=> $dataCampID, 
				"pause_code" 					=> $dataPC, 
				"pause_code_name" 				=> $dataPCN, 
				"billable" 						=> $dataBill
			);
        } else {
            $apiresults 					= array(
				"result" 						=> "Error: Pause Code does not exist."
			);
    	}
    }
?>
