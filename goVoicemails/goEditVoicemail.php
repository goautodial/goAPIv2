<?php
 /**
 * @file 		goEditVoicemail.php
 * @brief 		API for Voicemails
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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

### POST or GET Variables
$pass = $astDB->escape($_REQUEST['pass']);
$fullname = $astDB->escape($_REQUEST['fullname']);
$email = $astDB->escape($_REQUEST['email']);
$active = $astDB->escape($_REQUEST['active']);
$delete_vm_after_email = $astDB->escape($_REQUEST['delete_vm_after_email']);
$voicemail_id = $astDB->escape($_REQUEST['voicemail_id']);

$log_user = $astDB->escape($_REQUEST['log_user']);
$log_group = $astDB->escape($_REQUEST['log_group']);
$ip_address = $astDB->escape($_REQUEST['hostname']);
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
					$groupId = go_get_groupid($goUser, $astDB);
					
					if (!checkIfTenant($groupId, $goDB)) {
						//$ul = "";
					} else {
						//$ul = "AND user_group='$groupId'";
						//$addedSQL = "WHERE user_group='$groupId'";
						$astDB->where('user_group', $groupId);
					}
					//$queryCheck = "SELECT voicemail_id,pass,active,fullname,email,delete_vm_after_email from vicidial_voicemail where voicemail_id='$voicemail_id' $ul;";
					$astDB->where('voicemail_id', $voicemail_id);
					$sqlCheck = $astDB->get('vicidial_voicemail', null, 'voicemail_id,pass,active,fullname,email,delete_vm_after_email');
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
							$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified Voicemail ID: $voicemail_id", $log_group, $queryVM);
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
