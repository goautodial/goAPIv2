<?php
    #######################################################
    #### Name: goDeleteList.php		               ####
    #### Description: API to delete specific List      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian Samatra        ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $list_id = $_REQUEST['list_id'];
    $ip_address = $_REQUEST['hostname'];
    $goUser = $_REQUEST['goUser'];
    
	if($list_id == null) { 
		$apiresults = array("result" => "Error: Set a value for List ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE list_id='$list_id'";
    		} else { 
				$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";  
		}

   		$query = "SELECT list_id,list_name FROM vicidial_lists $ul order by list_id LIMIT 1";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataListID = $fresults['list_id'];
			}

			if($dataListID != null) {
				$deleteQuery = "DELETE FROM vicidial_lists WHERE list_id='$dataListID';"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);
				$deleteQueryLeads = "DELETE FROM vicidial_list WHERE list_id='$dataListID';"; 
   				$deleteResultLeads = mysqli_query($link, $deleteQueryLeads);
				$deleteQueryStmt = "DELETE FROM vicidial_lists_fields WHERE list_id='$dataListID' LIMIT 1;"; 
   				$deleteResultStmt = mysqli_query($link, $deleteQueryStmt);
				//echo $deleteQuery.$deleteQueryLeads.$deleteQueryStmt;

### Admin Logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted $list_id','DELETE FROM vicidial_lists WHERE list_id=$dataListID;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: List doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: List doesn't exist.");
		}
	}//end
?>
