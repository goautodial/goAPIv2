<?php
    #######################################################
    #### Name: getAllCalltimes.php 	               ####
    #### Description: API to get all calltimes         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Warren Ipac Briones               ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");

    $groupId = go_get_groupid($goUser);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$log_ip = mysqli_real_escape_string($link, $_REQUEST['log_ip']);

	if (!checkIfTenant($groupId)) {
		$ul = "";
	} else {
		$ul = "AND user_group='$groupId'";
		$addedSQL = "WHERE user_group='$groupId'";
	}

	$query = "SELECT * FROM vicidial_call_times $ul $addedSQL ORDER BY call_time_id;";
	$rsltv = mysqli_query($link,$query);
	
	//$log_id = log_action($linkgo, 'VIEW', $log_user, $log_ip, "Viewed the Calltimes List", $log_group);
	
	while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataCalltimeID[] = $fresults['call_time_id'];
		$dataCalltimeName[] = $fresults['call_time_name'];
		$dataCtDefStart[] = $fresults['ct_default_start'];
		$dataCtDefStop[] = $fresults['ct_default_stop'];
		$dataCtSunStart[] = $fresults['ct_sunday_start'];
		$dataCtSunStop[] = $fresults['ct_sunday_stop'];
		$dataCtMonStart[] = $fresults['ct_monday_start'];
		$dataCtMonStop[] = $fresults['ct_monday_stop'];
		$dataCtTueStart[] = $fresults['ct_tuesday_start'];
		$dataCtTueStop[] = $fresults['ct_tuesday_stop'];
		$dataCtWedStart[] = $fresults['ct_wednesday_start'];
		$dataCtWedStop[] = $fresults['ct_wednesday_stop'];
		$dataCtThuStart[] = $fresults['ct_thursday_start'];
		$dataCtThuStop[] = $fresults['ct_thursday_stop'];
		$dataCtFriStart[] = $fresults['ct_friday_start'];
		$dataCtFriStop[] = $fresults['ct_friday_stop'];
		$dataCtSatStart[] = $fresults['ct_saturday_start'];
		$dataCtSatStop[] = $fresults['ct_saturday_stop'];
		$dataUserGroup[] = $fresults['user_group'];
	}
	$apiresults = array("result" => "success", "call_time_id" => $dataCalltimeID, "call_time_name" => $dataCalltimeName, "ct_default_start" => $dataCtDefStart, "ct_default_stop" => $dataCtDefStop, "ct_sunday_start" => $dataCtSunStart, "ct_sunday_stop" => $dataCtSunStop, "ct_monday_start" => $dataCtMonStart, "ct_monday_stop" => $dataCtMonStop, "ct_tuesday_start" => $dataCtTueStart, "ct_tuesday_stop" => $dataCtTueStop, "ct_wednesday_start" => $dataCtWedStart, "ct_wednesday_stop" => $dataCtWedStop, "ct_thursday_start" => $dataCtThuStart, "ct_thursday_stop" => $dataCtThuStop, "ct_friday_start" => $dataCtFriStart, "ct_friday_stop" => $dataCtFriStop, "ct_saturday_start" => $dataCtSatStart, "ct_saturday_stop" => $dataCtSatStop, "user_group" => $dataUserGroup);
?>
