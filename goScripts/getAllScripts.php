<?php
    ///////////////////////////////////////////////////
    /// Name: getAllScripts.php 	///
    /// Description: API to get all scripts 	///
    /// Version: 0.9 	///
    /// Copyright: GOAutoDial Inc. (c) 2011-2016 	///
    /// Written by: Jeremiah Sebastian V. Samatra 	///
    /// Modified by: Alexander Jim H. Abenoja 	///
    /// License: AGPLv2 	///
    ///////////////////////////////////////////////////
	$agent = get_settings('user', $astDB, $goUser);
	$userid = mysqli_real_escape_string($link, $_REQUEST['userid']);
		
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
