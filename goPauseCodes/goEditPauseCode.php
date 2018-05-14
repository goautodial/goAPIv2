<?php
 /**
 * @file 		goEditPauseCode.php
 * @brief 		API to edit specific Pause Code
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Noel Umandap  <noel@goautodial.com>
 * @author     	Alexander Jim Abenoja  <alex@goautodial.com>
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
 
    ### POST or GET Variables
    $camp = $_REQUEST['pauseCampID'];
    $pause_code = $_REQUEST['pause_code'];
    $pause_code_name = $_REQUEST['pause_code_name'];
    $billable = strtoupper($_REQUEST['billable']);
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['log_ip']);
	
    ### Default values 
		$defBill = array('NO','YES','HALF');

    ### ERROR CHECKING ...
    if($camp == null || strlen($camp) < 3) {
        $apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_co)){
        $apiresults = array("result" => "Error: Special characters found in pause code and must not be empty");
    } elseif(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_codname)){
        $apiresults = array("result" => "Error: Special characters found in pause code name and must not be empty");
    } elseif(!in_array($billable,$defBill) && $billable != null) {
        $apiresults = array("result" => "Error: Default value for billable is No, Yes or half only.");
    } else {
		$astDB->where('campaign_id', $camp);
    	$astDB->where('pause_code', $pause_code);
    	$checkPC = $astDB->get('vicidial_pause_codes', null, '*');
		if($checkPC){
			$data_update = array(
				'pause_code_name' => $pause_code_name,  
				'billable' => $billable
			);
			$astDB->where('campaign_id', $camp);
    		$astDB->where('pause_code', $pause_code);
			$pausecodeUpdate = $astDB->update('vicidial_pause_codes', $data_update);
			$updateQuery = $astDB->getLastQuery();
				
			if($pausecodeUpdate){
				$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Pause Code $pause_code under Campaign ID $camp", $log_user, $updateQuery);
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Try updating Pause Code Again");
			}				   
		} else {
			$apiresults = array("result" => "Error: Pause code doesn't exist!");
		}
	}
?>
