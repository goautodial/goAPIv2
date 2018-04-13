<?php
 /**
 * @file 		goEditMOH.php
 * @brief 		API for Modifying Music On Hold
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
	$moh_id = $astDB->escape($_REQUEST['moh_id']);
	$moh_name = $astDB->escape($_REQUEST['moh_name']);
	$user_group = $astDB->escape($_REQUEST['user_group']);
	$active = $astDB->escape($_REQUEST['active']);
	$random = $astDB->escape($_REQUEST['random']);
	$values = $astDB->escape($_REQUEST['item']);
	$filename = $astDB->escape($_REQUEST['filename']);
	$ranks = $astDB->escape($_REQUEST['rank']);
    ### Default values 
    $defActive = array("Y","N");
    $defRandom = array("N","Y");
	
	$ip_address = $astDB->escape($_REQUEST['log_ip']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);

    ### ERROR CHECKING ...
    if($moh_id == null) { 
        $apiresults = array("result" => "Error: Set a value for MOH ID."); 
    } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $moh_name)){
            $apiresults = array("result" => "Error: Special characters found in moh_name");
        } else {
            if(!in_array($active,$defActive) && $active != null) {
				$apiresults = array("result" => "Error: Default value for active is Y or N only.");
			} else {
                if(!in_array($random,$defRandom) && $random != null) {
                    $apiresults = array("result" => "Error: Default value for random is Y or N only.");
                } else {
					$groupId = go_get_groupid($goUser, $astDB);
	
					if (!checkIfTenant($groupId, $goDB)) {
						//$ul = "WHERE user_group='$user_group'";
						//$ulMoh = "AND moh_id='$moh_id'";
						$astDB->where('moh_id', $moh_id);
					} else {
						//$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
						//$ulMoh = "AND moh_id='$moh_id' AND user_group='$groupId'";
						$astDB->where('moh_id', $moh_id);
						$astDB->where('user_group', $groupId);
					}
	
	
					//$queryMoh = "SELECT moh_id, moh_name, active, random, user_group FROM vicidial_music_on_hold WHERE remove='N' $ulMoh ORDER BY moh_id LIMIT 1;";
					$astDB->where('remove', 'N');
					$astDB->orderBy('moh_id', 'desc');
					$rsltvMoh = $astDB->getOne('vicidial_music_on_hold', 'moh_id, moh_name, active, random, user_group');
					foreach ($rsltvMoh as $fresults){
						$datamoh_id = $fresults['moh_id'];
						$datamoh_name = $fresults['moh_name'];
						$dataactive = $fresults['active'];
						$datarandom = $fresults['random'];
						$datauser_group = $fresults['user_group'];
					}
					$countMoh = $astDB->getRowCount();
	
					if($countMoh > 0) {
						if($user_group !== null){
							if (!checkIfTenant($groupId, $goDB)) {
								$astDB->where('user_group', $user_group);
							} else {
								$astDB->where('user_group', $user_group);
								$astDB->where('user_group', $groupId);
							}
							//$queryCheck = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
							$astDB->orderBy('user_group', 'desc');
							$resultCheck = $astDB->getOne('vicidial_user_groups', 'user_group,group_name,forced_timeclock_login');
							$countResult = $astDB->getRowCount();
						
							if($countResult == 0 && $filename == NULL) {
								$apiresults = array("result" => "Error: User Group doesn't exist.");
							}
						}

						if($filename != null) {
							//$queryFiles = "INSERT INTO vicidial_music_on_hold_files SET filename='$filename', rank='$rank', moh_id='$moh_id';";
							$insertData = array(
								'filename' => $filename,
								'rank' => $rank,
								'moh_id' => $moh_id
							);
							$rsltvFILES = $astDB->insert('vicidial_music_on_hold_files', $insertData);
						}
						if($moh_name == null){$moh_name = $datamoh_name;}
						if($active == null){$active = $dataactive;}
						if($user_group == null){$user_group = $datauser_group;}
						if($random == null){$random = $datarandom;}
	
						//$queryMOH = "UPDATE vicidial_music_on_hold SET moh_name='$moh_name', active='$active', user_group='$user_group', random='$random' WHERE moh_id='$moh_id';";
						$updateData = array(
							'moh_name' => $moh_name,
							'active' => $active,
							'user_group' => $user_group,
							'random' => $random
						);
						$astDB->where('moh_id', $moh_id);
						$rsltv1 = $astDB->update('vicidial_music_on_hold', $updateData);
						$queryMOH = $astDB->getLastQuery();
	
						if(!$rsltv1) {
							$apiresults = array("result" => "Error: Try updating Moh Again");
						} else {
							$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified Music On-Hold: $moh_id", $log_group, $queryMOH);
							
							$apiresults = array("result" => "success");
							$affected_rows++;
							if ($affected_rows) {
								//$newQuery2 = "UPDATE servers SET rebuild_conf_files='Y',rebuild_music_on_hold='Y',sounds_update='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
								$updateData = array(
									'rebuild_conf_files' => 'Y',
									'rebuild_music_on_hold' => 'Y',
									'sounds_update' => 'Y'
								);
								$astDB->where('generate_vicidial_conf', 'Y');
								$astDB->where('active_asterisk_server', 'Y');
								$astDB->update('servers', $updateData);
								$apiresults = array("result" => "success");
							} else {
								$apiresults = array("result" => "Error: Try updating Moh Again");
							}
						}
					} else {
						$apiresults = array("result" => "Error: MOH doesn't exist");
					}
				}
			}
		}
	}
?>