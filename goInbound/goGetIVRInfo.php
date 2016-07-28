<?php
    #######################################################
    #### Name: goGetIVRInfo.php		               ####
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
        		$ul = "AND menu_id='$menu_id'";
    		} else { 
			$ul = "AND menu_id='$menu_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT menu_id,menu_name,menu_prompt,menu_timeout,menu_timeout_prompt,menu_invalid_prompt,menu_repeat from vicidial_call_menu WHERE menu_id!='defaultlog' $ul order by menu_id LIMIT 1;";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
        $dataMenuId[] =  $fresults['menu_id'];
        $dataMenuName[] =  $fresults['menu_name'];
        $dataMenuPrompt[] =  $fresults['menu_prompt'];
        $dataMenuTimeout[] =  $fresults['menu_timeout'];
	$dataMenuTimeoutPrompt[] = $fresults['menu_timeout_prompt'];
	$dataMenuInvalidPrompt[] = $fresults['menu_invalid_prompt'];
	$dataMenuRepeat[] = $fresults['menu_repeat'];

        $apiresults = array( "result" => "success", "menu_id" => $dataMenuId, "menu_name" => $dataMenuName, "menu_prompt" => $dataMenuPrompt, "menu_timeout" => $dataMenuTimeout, "menu_timeout_prompt" => $dataMenuTimeoutPrompt, "menu_invalid_prompt" => $dataMenuInvalidPrompt, "menu_repeat" => $dataMenuRepeat);
			}
		} else {
			$apiresults = array("result" => "Error: IVR Menu doesn't exist.");
		}
	}
?>
