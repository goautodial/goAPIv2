<?php 
	####################################################
	#### Name: goGetStandardFields.php              ###
	#### Description: API to edit specific Script	####
	#### Version: 0.9                               ####
	#### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
	#### Written by: Jeremiah Sebastian V. Samatra  ####
	#### License: AGPLv2                            ####
	####################################################
	$astDB->where('subscript' , 1);
	$subScripts = $astDB->get('vicidial_scripts', null, 'script_id, script_name, script_text');
	
	foreach($subScripts as $fresults){
		$script_id[] = $fresults['script_id'];
		$script_name[] = $fresults['script_name'];
		$script_text[] = $fresults['script_text'];
	}

	$apiresults = array("result" => "success", "script_id" => $script_id, "script_name" => $script_name, "script_text" => $script_text);
?>