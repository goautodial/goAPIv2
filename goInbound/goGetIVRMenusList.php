<?php
    #######################################################
    #### Name: getAllCampaigns.php	               ####
    #### Description: API to get all IVR menus         ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    $limit = $_REQUEST['limit'];
    if($limit < 1){ $limit = 20; } else { $limit = $limit; }

    $groupId = go_get_groupid($goUser);
    
    if (!checkIfTenant($groupId)) {
        $ul='';
    } else { 
	$ul = "AND user_group='$groupId'";  
  	   }

   $query = "SELECT menu_id,menu_name,menu_prompt,menu_timeout from vicidial_call_menu WHERE menu_id!='defaultlog' $ul order by menu_id LIMIT $limit";
   $rsltv = mysqli_query($link, $query);

	$data = array();
	while($fresults = mysqli_fetch_assoc($rsltv)){

	$dataMenuId[] =  $fresults['menu_id'];
	$dataMenuName[] =  $fresults['menu_name'];
	$dataMenuPrompt[] =  $fresults['menu_prompt'];
	$dataMenuTimeout[] =  $fresults['menu_timeout'];

	$apiresults = array( "result" => "success", "menu_id" => $dataMenuId, "menu_name" => $dataMenuName, "menu_prompt" => $dataMenuPrompt, "menu_timeout" => $dataMenuTimeout);
	}
?>
