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

	$queryGetStandardFields = "SELECT column_name FROM information_schema.columns WHERE table_name='vicidial_list';";
	$sql = mysqli_query($link, $queryGetStandardFields);

	while($fresults = mysqli_fetch_array($sql, MYSQLI_ASSOC)){
		$field_name[] = $fresults['column_name'];
	}

	$apiresults = array("result" => "success", "field_name" => $field_name);
?>