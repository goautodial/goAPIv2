<?php
    #######################################################
    #### Name: goGetIVRInfo.php		   ####
    #### Description: API to edit specific IVR	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $menu_id = $_REQUEST['menu_id'];
    
	if($menu_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Menu ID."); 
	} else {
 
    $groupId = go_get_groupid($goUser);
    
	if (!checkIfTenant($groupId)) {
			$ul = "menu_id='$menu_id'";
	} else { 
		$ul = "menu_id='$menu_id' AND user_group='$groupId'";  
	}

   		$query = "SELECT *	FROM vicidial_call_menu_options WHERE $ul;";
		
   		$rsltv = mysqli_query($link, $query);

		while($fresults = mysqli_fetch_array($rsltv)){
			$id[] = $fresults["menu_id"];
			$option_value[] = $fresults["option_value"];
			$option_description[] = $fresults["option_description"];
			$option_route[] = $fresults["option_route"];
			$option_route_value[] = $fresults["option_route_value"];
			$option_route_value_context[] = $fresults["option_route_value_context"];
		}
		
		$apiresults = array( "result" => "success", "menu_id" => $id, "option_value" => $option_value, "option_description" => $option_description, "option_route" => $option_route, "option_route_value" => $option_route_value, "option_route_value_context" => $option_route_value_context, "query" => $query);
			
	}
?>
