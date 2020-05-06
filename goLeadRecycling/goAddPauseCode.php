<?php
 /**
 * @file        goAddPauseCode.php
 * @brief 	    API for Adding Pause Code
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	
    ### POST or GET Variables
	$camp = $astDB->escape($_REQUEST['pauseCampID']);
	$pause_code = $astDB->escape($_REQUEST['pause_code']);
	$pause_code_name = $astDB->escape($_REQUEST['pause_code_name']);
	$billable = strtoupper($astDB->escape($_REQUEST['billable']));
		
    ### Default values 
    $defBill = array('NO','YES','HALF');

    ### ERROR CHECKING 
	if($camp == null || strlen($camp) < 3) {
		$apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
	} else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code) || $pause_code == null){
            $apiresults = array("result" => "Error: Special characters found in pause code and must not be empty");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_code_name)){
                $apiresults = array("result" => "Error: Special characters found in pause code name");
			} else {
                if(!in_array($billable,$defBill)) {
                    $apiresults = array("result" => "Error: Default value for billable is No, Yes or half only.");
                } else {
					$groupId = go_get_groupid($goUser, $astDB);
	
					if (!checkIfTenant($groupId, $goDB)) {
						//$ul = "";
					} else {
						//$ul = "AND user_group='$groupId'";
						//$addedSQL = "WHERE user_group='$groupId'";
					}
		
					//$queryCheck = "SELECT * FROM vicidial_campaigns WHERE campaign_id='$camp'";
					$astDB->where('campaign_id', $camp);
					$sqlCheck = $astDB->get('vicidial_campaigns');
					$countCheck1 = $astDB->getRowCount();
					if($countCheck1 > 0) {
						//$queryCheck = "SELECT * FROM vicidial_pause_codes WHERE campaign_id='$camp' AND pause_code = '$pause_code';";
						$astDB->where('campaign_id', $camp);
						$astDB->where('pause_code', $pause_code);
						$sqlCheck = $astDB->get('vicidial_pause_codes');
						$countCheck = $astDB->getRowCount();
						if($countCheck <= 0) {	
							$newQuery = "INSERT INTO vicidial_pause_codes (pause_code,pause_code_name,campaign_id,billable) VALUES ('$pause_code', '$pause_code_name', '$camp', '$billable');";
							$rsltv = $astDB->rawQuery($newQuery);
							
							$log_id = log_action($goDB, 'ADD', $log_user, $log_ip, "Added a New Pause Code $pause_code under Campaign ID $camp", $log_group, $newQuery);

							if(!$rsltv) {
								$apiresults = array("result" => "Error: Add failed, check your details");
							} else {
								$apiresults = array("result" => "success");
							}
						} else {
							$apiresults = array("result" => "Error: Add failed, Pause Code already exist!");
						}
					} else {
						$apiresults = array("result" => "Error: Add failed, Campaign ID does not exist!");
					}
				}
			}
		}
	}
?>