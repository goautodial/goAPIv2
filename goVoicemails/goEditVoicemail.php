<?php
 /**
 * @file 		goEditVoicemail.php
 * @brief 		API for editing Voicemails
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Chris Lomuntad
 * @author		Jeremiah Sebastian Samatra
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
 
	$log_user = $session_user;
	$log_group = go_get_groupid($session_user, $astDB);
	$log_ip = $astDB->escape($_REQUEST['log_ip']);

	### POST or GET Variables
	$voicemail_id = $astDB->escape($_REQUEST['voicemail_id']);
	$pass = $astDB->escape($_REQUEST['pass']);
	$fullname = $astDB->escape($_REQUEST['fullname']);
	$email = $astDB->escape($_REQUEST['email']);
	$active = $astDB->escape($_REQUEST['active']);
	$delete_vm_after_email = $astDB->escape($_REQUEST['delete_vm_after_email']);

	### Default values 
	$defActive = array("Y","N");
	$defDelVM = array("N","Y"); 

	### ERROR CHECKING ...
	if($voicemail_id == null) { 
		$apiresults = array("result" => "Error: Set a value for VOICEMAIL ID."); 
	} else {
		if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname)){
			$apiresults = array("result" => "Error: Special characters found in fullname");
		} else {
			if(!in_array($active,$defActive) && $active != null) {
				$apiresults = array("result" => "Error: Default value for active is Y or N only.");
			} else {
				if(!in_array($delete_vm_after_email,$defDelVM) && $delete_vm_after_email != null) {
					$apiresults = array("result" => "Error: Default value for delete_vm_after_email is Y or N only.");
				} else {
					if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != null) {
						$apiresults = array("result" => "Error: Invalid email format.");
					} else {						
						if (!checkIfTenant($log_group, $goDB)) {
							//$ul = "";
						} else {
							$astDB->where('user_group', $log_group);
						}
						//$queryCheck = "SELECT voicemail_id,pass,active,fullname,email,delete_vm_after_email from vicidial_voicemail where voicemail_id='$voicemail_id' $ul;";
						$cols = array("voicemail_id", "pass", "active", "fullname", "email", "delete_vm_after_email");
						$astDB->where('voicemail_id', $voicemail_id);
						$sqlCheck = $astDB->get('vicidial_voicemail', null, $cols);
						
						foreach ($sqlCheck as $fresults) {
							$dataVM_id = $fresults['voicemail_id'];
							$dataVM_pass = $fresults['pass'];
							$dataactive = $fresults['active'];
							$datafullname = $fresults['fullname'];
							$dataemail = $fresults['email'];
							$datadeleteVMemail = $fresults['delete_vm_after_email'];
						}
						$countVM = $astDB->getRowCount();
						if($countVM > 0) {
							if($pass == null){$pass = $dataVM_pass;}
							if($active == null){$active = $dataactive;}
							if($fullname == null){$fullname = $datafullname;}
							if($email == null){$email = $dataemail;}
							if($delete_vm_after_email == null){$delete_vm_after_email = $datadeleteVMemail;}
							
							//$queryVM ="UPDATE vicidial_voicemail SET pass='$pass',  fullname='$fullname',  email='$email',  active='$active',  delete_vm_after_email='$delete_vm_after_email' WHERE voicemail_id='$voicemail_id'";
							$updateData = array(
											'pass' => $pass,
											'fullname' => $fullname,
											'email' => $email,
											'active' => $active,
											'delete_vm_after_email' => $delete_vm_after_email
							);
							$astDB->where('voicemail_id', $voicemail_id);
							$rsltv1 = $astDB->update('vicidial_voicemail', $updateData);
							
							if($rsltv1 == false){
								$apiresults = array("result" => "Error: Try updating Voicemail Again");
							} else {
								$apiresults = array("result" => "success");
								$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified Voicemail ID: $voicemail_id", $log_group, $rsltv1);
							}
						} else {
							$apiresults = array("result" => "Error: Voicemail doesn't exist");
						}
					}
				}
			}
		}
	}
?>
