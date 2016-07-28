<?php
    #######################################################
    #### Name: goGetAllScriptsList.php	               ####
    #### Description: API to get all Scripts           ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
	$ul = "WHERE user_group='$groupId'";  
  	   }

   $query = "SELECT * FROM vicidial_scripts $ul ORDER BY script_id ASC LIMIT $limit;";
   $rsltv = mysqli_query($link, $query);

	$data = array();
	while($fresults = mysqli_fetch_assoc($rsltv)){
	$html_table[] .= '<tr><td>' .$fresults['script_id']. '</td><td>' .$fresults['script_name']. '</td><td>' .$fresults['active']. '</td><td>' . $fresults['user_group']. '</td></tr>';

	$dataScriptId[] =  $fresults['script_id'];
	$dataScriptName[] =  $fresults['script_name'];
	$dataActive[] =  $fresults['active'];
	$dataUserGroup[] =  $fresults['user_group'];

	$apiresults = array( "result" => "success", "script_id" => $dataScriptId, "script_name" => $dataScriptName,  "active" =>$dataActive, "user_group" =>$dataUserGroup);
	}
?>
