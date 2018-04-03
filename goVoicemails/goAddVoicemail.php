<?php
 /**
 * @file 		goAddVoicemail.php
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
$voicemail_id = $_REQUEST['voicemail_id'];
$pass = $_REQUEST['pass'];
$fullname = $_REQUEST['fullname'];
$email = $_REQUEST['email'];
$user_group = $_REQUEST['user_group'];
$active = strtoupper($_REQUEST['active']);
$ip_address = $_REQUEST['hostname'];
$goUser = $_REQUEST['goUser'];

$voicemail_id = $astDB->escape($voicemail_id);
$pass = $astDB->escape($pass);
$fullname = $astDB->escape($fullname);
$email = $astDB->escape($email);
$user_group = $astDB->escape($user_group);
$active = $astDB->escape($active);

$log_user = $astDB->escape($_REQUEST['log_user']);
$log_group = $astDB->escape($_REQUEST['log_group']);

### Default values 
$defActive = array("Y","N");


### ERROR CHECKING 
if($voicemail_id == null || strlen($voicemail_id) < 3) {
	$apiresults = array("result" => "Error: Set a value for VOICEMAIL ID not less than 3 characters.");
} else {
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname) || $fullname == null){
		$apiresults = array("result" => "Error: Special characters found in fullname and must not be empty");
	} else {
		if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_id)){
			$apiresults = array("result" => "Error: Special characters found in voicemail_id");
		} else {
			if(!in_array($active,$defActive)) {
				$apiresults = array("result" => "Error: Default value for active is Y or N only.");
			} else {
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
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
					
					//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups WHERE user_group='".$user_group."' $ul ORDER BY user_group LIMIT 1;";
					$astDB->where('user_group', $user_group);
					$astDB->orderBy('user_group', 'desc');
					$rsltv = $astDB->getOne('vicidial_user_groups', 'user_group,group_name,forced_timeclock_login');
					$countResult = $astDB->getRowCount();
			
					if($countResult > 0) {
						//$queryCheck = "SELECT voicemail_id from vicidial_voicemail where voicemail_id='".$voicemail_id."';";
						$astDB->where('voicemail_id', $voicemail_id);
						$sqlCheck = $astDB->get('vicidial_voicemail');
						$countCheck = $astDB->getRowCount();
						if($countCheck <= 0) {
							//$newQuery = "INSERT INTO vicidial_voicemail (voicemail_id, pass, fullname, active, email, user_group) VALUES ('".$voicemail_id."', '".$pass."', '".$fullname."', '".$active."', '".$email."', '".$user_group."');";
							$insertData = array(
								'voicemail_id' => $voicemail_id,
								'pass' => $pass,
								'fullname' => $fullname,
								'active' => $active,
								'email' => $email,
								'user_group' => $user_group
							);
							$rsltv = $astDB->insert('vicidial_voicemail', $insertData);
							
							$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added New Voicemail: $voicemail_id", $log_group, $newQuery);
							
							if($rsltv == false) {
								$apiresults = array("result" => "Error: Add failed, check your details");
							} else {
								$apiresults = array("result" => "success");
							}
						} else {
							$apiresults = array("result" => "Error: Add failed, Voicemail already already exist!");
						}
					} else {
						$apiresults = array("result" => "Error: Invalid User Group");
					}
				}
			}
		}
	}
}
?>