<?php
    #######################################################
    #### Name: goDeleteIVR.php		               ####
    #### Description: API to delete specific IVR menu  ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $menu_id = $_REQUEST['menu_id'];
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
	if($menu_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Menu ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "AND menu_id='$menu_id'";
    		} else { 
			$ul = "AND menu_id='$menu_id' AND user_group='$groupId'";  
		}

		$query = "SELECT menu_id from vicidial_call_menu WHERE menu_id!='defaultlog' $ul order by menu_id LIMIT 1";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataMenuID = $fresults['menu_id'];
			}

			if(!$dataMenuID == null) {
				$deleteQueryA = "DELETE from vicidial_call_menu where menu_id='$dataMenuID' limit 1;";
   				$deleteResultA = mysqli_query($link, $deleteQueryA);
				$deleteQueryB = "DELETE from vicidial_call_menu_options where menu_id='$dataMenuID';";
   				$deleteResultB = mysqli_query($link, $deleteQueryB);
				//echo $deleteQuery;

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Call Menu ID $dataMenuID','DELETE from vicidial_call_menu where menu_id=$dataMenuID limit 1;DELETE from vicidial_call_menu_options where menu_id=$dataMenuID;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Call Menu ID $dataMenuID", $log_group, $deleteQueryA);


				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Menu doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Menu doesn't exist.");
		}
	}
?>
