<?php
 /**
 * @file 		goEditPauseCode.php
 * @brief 		API to edit specific Pause Code
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
	$pause_code_name 						= $astDB->escape($_REQUEST['pause_code_name']);
	$billable 								= $astDB->escape(strtoupper($_REQUEST['billable']));	
	
    ### Default values 
	$defBill = array('NO','YES','HALF');

    ### ERROR CHECKING ...
	if (!isset($log_user) || is_null($log_user)){
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
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code_name)) {
		$apiresults 						= array(
			"result" 							=> "Error: Special characters found in pause code name"
		);
	} elseif(!in_array($billable,$defBill)) {
		$apiresults 						= array(
			"result" 							=> "Error: Default value for billable is No, Yes or half only."
		);
	} else {
		$astDB->where('campaign_id', $campaign_id);
    	$astDB->where('pause_code', $pause_code);
    	$checkPC = $astDB->get('vicidial_pause_codes', null, '*');
		if($checkPC){
			$data_update = array(
				'pause_code_name' => $pause_code_name,  
				'billable' => $billable
			);
			$astDB->where('campaign_id', $campaign_id);
    		$astDB->where('pause_code', $pause_code);
			$pausecodeUpdate = $astDB->update('vicidial_pause_codes', $data_update);
			$updateQuery = $astDB->getLastQuery();
				
			if($pausecodeUpdate){
				$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Pause Code $pause_code under Campaign ID $campaign_id", $log_user, $updateQuery);
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Try updating Pause Code Again");
			}				   
		} else {
			$apiresults = array("result" => "Error: Pause code doesn't exist!");
		}
	}
?>
