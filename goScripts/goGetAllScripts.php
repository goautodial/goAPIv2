<?php
 /**
 * @file 		goGetAllScripts.php
 * @brief 		API to get all scripts
 * @copyright 	Copyright (C) GOautodial Inc.
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
	$agent = get_settings('user', $astDB, $goUser);
	$userid = $astDB->escape($_REQUEST['userid']);
		
    $groupId = go_get_groupid($goUser);
    

    //if (!checkIfTenant($groupId)) {
	if ($agent->user_group != "ADMIN") {
        $astDB->where('user_group', $agent->user_group);
    }

	// getting script count
	$resultGetScript = $astDB->getOne('vicidial_scripts', null, 'script_id');
	
	// condition
	if(count($resultGetScript) > 0){
		foreach($resultGetScript as $get_last_script){
			if(preg_match("/^script/i", $get_last_script['script_id'])){
				$get_last_count = preg_replace("/^script/i", "", $get_last_script['script_id']);
				$last_pl[] = intval($get_last_count);
			}else{
				$get_last_count = $get_last_script['script_id'];
				$last_pl[] = intval($get_last_count);
			}
		}
		
		// return data
		$script_num = max($last_pl);
		$script_num = $script_num + 1;
		
		if($script_num < 100){
			if($script_num < 10){
					$script_num = "00".$script_num;
			}else{
					$script_num = "0".$script_num;
			}
		}
		
		if($agent->user_group != "ADMIN"){
			$script_num = $script_num;
		}else{
			$script_num = "script".$script_num;
		}
	}else{
		// return data
		$script_num = "script001";
	}
		
		
	// GETTING ACTUAL DATA //
	//if (!checkIfTenant($groupId)) {
	if ($agent->user_group != "ADMIN") {
        $astDB->where('user_group', $agent->user_group);
    }
	$scripts = $astDB->get('vicidial_scripts', null, 'script_id, script_name, active, user_group');
	
	foreach($scripts as $script){
		$dataScriptID[] 	= $script['script_id'];
		$dataScriptName[] 	= $script['script_name'];
		$dataActive[] 		= $script['active'];
		$dataUserGroup[] 	= $script['user_group'];
	}
	
	$apiresults = array(
			"result" 		=> "success",
			"script_id" 	=> $dataScriptID,
			"script_name" 	=> $dataScriptName,
			"active" 		=> $dataActive,
			"user_group" 	=> $dataUserGroup,
			"script_count" 	=> $script_num
	);

?>
