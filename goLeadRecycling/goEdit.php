<?php
 /**
 * @file        goEdit.php
 * @brief 	    API for Modifying Pause Codes
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author	    Warren Ipac Briones  <warren@goautodial.com>
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
	$camp = $astDB->escape($_REQUEST['leadRecCampID']);
	$status = $astDB->escape($_REQUEST['status']);
	$attempt_delay = $astDB->escape($_REQUEST['attempt_delay']);
	$active = strtoupper($astDB->escape($_REQUEST['active']));
	
    ### Default values
    $defActive = array('N','Y');

    ### ERROR CHECKING ...
	if($camp == null || strlen($camp) < 3) {
		$apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
	} else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status)){
            $apiresults = array("result" => "Error: Special characters found in pause code and must not be empty");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $attempt_delay)){
                $apiresults = array("result" => "Error: Special characters found in pause code name and must not be empty");
			} else {
                if(!in_array($active,$defActive) && $active != null) {
                    $apiresults = array("result" => "Error: Default value for active is No, Yes or half only.");
                } else {
					$groupId = go_get_groupid($goUser, $astDB);

					if (!checkIfTenant($groupId, $goDB)) {
                        //$ul = "";
					} else {
                        //$ul = "AND user_group='$groupId'";
						//$addedSQL = "WHERE user_group='$groupId'";
					}

					//$queryCheck = "SELECT * FROM vicidial_lead_recycle WHERE campaign_id='$camp'";
					$astDB->where('campaign_id', $camp);
					$sqlCheck = $astDB->get('vicidial_lead_recycle');
					$countCheck1 = $astDB->getRowCount();
                    if($countCheck1 > 0) {
                        //$queryCheck = "SELECT status,attempt_delay,campaign_id,active FROM vicidial_lead_recycle WHERE campaign_id='$camp' AND status = '$status';";
						$astDB->where('campaign_id', $camp);
						$astDB->where('status', $status);
                        $sqlCheck = $astDB->get('vicidial_lead_recycle', null, 'status,attempt_delay,campaign_id,active');
                        $countCheck = $astDB->getRowCount();
						if($countCheck <= 0){
							foreach ($sqlCheck as $fresults){
								$dataStatus = $fresults['status'];
								$dataAttemptDelay = $fresults['attempt_delay'];
								$dataCampID = $fresults['campaign_id'];
								$dataActive = $fresults['active'];
							}
                        }
						$countVM = $astDB->getRowCount();

						if($countVM > 0) {
							if($status == null){$status = $dataStatus;}
							if($attempt_delay == null){$attempt_delay = $dataAttemptDelay;}
							if($camp == null){$camp = $dataCampID;}
							if($active == null){$active = $dataActive;}
	
							//$queryVM ="UPDATE vicidial_lead_recycle SET  attempt_delay='$attempt_delay',  active='$active' WHERE status='$status'";
							$astDB->where('status', $status);
							$rsltv1 = $astDB->update('vicidial_lead_recycle', array('attempt_delay' => $attempt_delay, 'active' => $active));
							
                            if (!$rsltv1) {
                                $apiresults = array("result" => "Error: Try updating Pause Code Again");
                            } else {
                                $apiresults = array("result" => "success");
								
                                $log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Lead Recycling: $status", $log_group, $astDB->getLastQuery());
							}
                        } else {
                            $apiresults = array("result" => "Error: Pause code doesn't exist");
                        }
                    } else {
                        $apiresults = array("result" => "Error: Add failed, Campaign ID does not exist!");
					}
				}
			}
		}
	}
?>