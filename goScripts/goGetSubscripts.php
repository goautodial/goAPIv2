<?php 
	####################################################
	#### Name: goGetStandardFields.php              ###
	#### Description: API to edit specific Script	####
	#### Version: 0.9                               ####
	#### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
	#### Written by: Jeremiah Sebastian V. Samatra  ####
	#### License: AGPLv2                            ####
	####################################################

	include_once ("../goFunctions.php");

	$queryGetSubscripts = "SELECT script_id, script_name, script_text FROM vicidial_scripts WHERE subscript='1';";
	$sql = mysqli_query($link, $queryGetSubscripts);
	
	while($fresults = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
		$script_id[] = $fresults['script_id'];
		$script_name[] = $fresults['script_name'];
		$script_text[] = $fresults['script_text'];
	}

	$apiresults = array("result" => "success", "script_id" => $script_id, "script_name" => $script_name, "script_text" => $script_text);
?>