<?php
    #######################################################
    #### Name: getAllScripts.php                	   ####
    #### Description: API to get all scripts  		   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### Modified by: Alexander Jim H. Abenoja         ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
		
		$userid = mysqli_real_escape_string($link, $_REQUEST['userid']);
		
		## GET USER GROUP ##
		$get_usergroup = "SELECT user_group FROM vicidial_users WHERE user = '$userid'";
		$exec_get_usergroup = mysqli_query($link, $get_usergroup);
		$fetch_user_group = mysqli_fetch_array($exec_get_usergroup);
		$user_group = $fetch_user_group['user_group'];
		
                $groupId = go_get_groupid($goUser);

                //if (!checkIfTenant($groupId)) {
				if ($user_group == 'ADMIN') {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$user_group'";
                  
                }

		// getting script count
		if($user_group != "ADMIN"){
				$getLastScript = "SELECT script_id FROM vicidial_scripts;";
		}else{
				$getLastScript = "SELECT script_id FROM vicidial_scripts WHERE user_group = 'ADMIN';";
		}
		
		$queryScriptCount = mysqli_query($link, $getLastScript);
		$max_script = mysqli_num_rows($queryScriptCount);
	
		// condition
		if($max_script > 0){
			while($get_last_script = mysqli_fetch_array($queryScriptCount)){
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
			
			if($user_group != "ADMIN"){
				$script_num = $script_num;
			}else{
				$script_num = "script".$script_num;
			}
		}else{
			// return data
			$script_num = "script001";
		}
		
		
		### GETTING ACTUAL DATA ###
				
				$query = "SELECT script_id, script_name, active, user_group FROM vicidial_scripts $ul ORDER BY script_id ASC;";
				$rsltv = mysqli_query($link, $query);
				
				while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$dataScriptID[] = $fresults['script_id'];
					$dataScriptName[] = $fresults['script_name'];
					$dataActive[] = $fresults['active'];
					$dataUserGroup[] = $fresults['user_group'];
				}
				
				$apiresults = array(
							"result" => "success",
							"script_id" => $dataScriptID,
							"script_name" => $dataScriptName,
							"active" => $dataActive,
							"user_group" => $dataUserGroup,
							"script_count" => $script_num
					);

?>
