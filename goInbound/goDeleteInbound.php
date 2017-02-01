<?php
    #######################################################
    #### Name: goDeleteInbound.php	               ####
    #### Description: API to delete specific Inbound   ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $group_id = $_REQUEST['group_id'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
	
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
    
	if($group_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Group ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE group_id='$group_id'";
    		} else { 
			$ul = "WHERE group_id='$group_id' AND user_group='$groupId'";  
		}

		$query = "SELECT group_id,group_name FROM vicidial_inbound_groups $ul ORDER BY group_id LIMIT 1;";

   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataGroupID = $fresults['group_id'];
			}
			if(!$dataGroupID == null) {
				$deleteQueryA = "DELETE from vicidial_inbound_groups where group_id='$dataGroupID' and group_id NOT IN('AGENTDIRECT') limit 1;"; 
   				$deleteResultA = mysqli_query($link, $deleteQueryA);
				$deleteQueryB ="DELETE from vicidial_inbound_group_agents where group_id='$dataGroupID';";
   				$deleteResultB = mysqli_query($link, $deleteQueryB);
				$deleteQueryC = "DELETE from vicidial_live_inbound_agents where group_id='$dataGroupID';";
   				$deleteResultC = mysqli_query($link, $deleteQueryC);
				$deleteQueryD = "DELETE from vicidial_campaign_stats where campaign_id='$dataGroupID';";
   				$deleteResultD = mysqli_query($link, $deleteQueryD);
				//echo $deleteQueryA.$deleteQueryB.$deleteQueryC.$deleteQueryD;
				//echo $deleteResultA.$deleteResultA.$deleteResultA.$deleteResultA;

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted  In-group $dataGroupID','DELETE from vicidial_inbound_groups where group_id=$dataGroupID and group_id NOT IN(AGENTDIRECT) limit 1;DELETE from vicidial_inbound_group_agents where group_id=$dataGroupID;DELETE from vicidial_live_inbound_agents where group_id=$dataGroupID;DELETE from vicidial_campaign_stats where campaign_id=$dataGroupID;');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
				$log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Inbound Group $dataGroupID", $log_group, $deleteQueryA);



				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Group  doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Group doesn't exist.");
		}
	}
?>
