<?php
 /**
 * @file 		goAddMOH.php
 * @brief 		API for Adding Music On Hold
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
	$active = strtoupper($astDB->escape($_REQUEST['active']));
	$random = strtoupper($astDB->escape($_REQUEST['random']));
	$values = $astDB->escape($_REQUEST['item']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	$goUser = $astDB->escape($_REQUEST['goUser']);
	
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);


    ### Default values 
    $defActive = array("Y","N");
    $defRandom = array("Y","N");


    ### ERROR CHECKING 
    if($moh_id == null || strlen($moh_id) < 3) {
            $apiresults = array("result" => "Error: Set a value for MOH ID not less than 3 characters.");
    } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $moh_name) || $moh_name == null) {
            $apiresults = array("result" => "Error: Special characters found in moh_name and must not be empty");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $moh_id)) {
                $apiresults = array("result" => "Error: Special characters found in moh_id");
			} else {
                if(!in_array($active,$defActive)) {
                    $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
					if(!in_array($random,$defRandom)) {
                        $apiresults = array("result" => "Error: Default value for random is Y or N only.");
					} else {
						$groupId = go_get_groupid($goUser, $astDB);
						
						if (!checkIfTenant($groupId, $goDB)) {
							//$ul = "WHERE user_group='$user_group'";
							$group_type = "Multi-tenant";
							$astDB->where('user_group', $user_group);
						} else {
							//$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
							$group_type = "Default";
							$astDB->where('user_group', $user_group);
							$astDB->where('user_group', $groupId);
						}
		
						//$query = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
						$astDB->orderBy('user_group', 'desc');
						$rsltv = $astDB->getOne('vicidial_user_groups', 'user_group,group_name,forced_timeclock_login');
						$countResult = $astDB->getRowCount();
						
						if($user_group == "---ALL---") {  // temporary
							$countResult = 1;
						}
						
						if($countResult > 0) {
							/*
							$items = $values;
							$itemSQL = "INSERT INTO vicidial_music_on_hold SET ";
							foreach (explode("&",$items) as $item)
							{
									$itemX = explode("=",$item);
	
									if ($itemX[0]=="moh_id")
											$moh_id = $itemX[1];
	
									$itemSQL .= $itemX[0]."='".str_replace("+"," ",$itemX[1])."',";
							}
							$itemSQL = rtrim($itemSQL,",");
							*/
							//$newQuery = "INSERT INTO vicidial_music_on_hold SET moh_id = '$moh_id', moh_name = '$moh_name', user_group = '$user_group', active = '$active', random = '$random';";
							$insertData = array(
								'moh_id' => $moh_id,
								'moh_name' => $moh_name,
								'user_group' => $user_group,
								'active' => $active,
								'random' => $random
							);
							$rsltv = $astDB->insert('vicidial_music_on_hold', $insertData);
							
							$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added Music On-Hold: $moh_id", $log_group, $newQuery);
	
							if($rsltv == false){
								$apiresults = array("result" => "Error: Add failed, check your details");
							} else {
	
								//$insertQuery = "INSERT INTO vicidial_music_on_hold_files SET filename='conf',rank='1',moh_id='$moh_id';";
								$insertData = array(
									'filename' => 'conf',
									'rank' => '1',
									'moh_id' => $moh_id
								);
								$insertResult = $astDB->insert('vicidial_music_on_hold_files', $insertData);
								//$updateQuery = "UPDATE servers SET rebuild_conf_files='Y',rebuild_music_on_hold='Y',sounds_update='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
								$updateData = array(
									'rebuild_conf_files' => 'Y',
									'rebuild_music_on_hold' => 'Y',
									'sounds_update' => 'Y'
								);
								$astDB->where('generate_vicidial_conf', 'Y');
								$astDB->where('active_asterisk_server', 'Y');
								$astDB->update('servers', $updateData);
								$apiresults = array("result" => "success");
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