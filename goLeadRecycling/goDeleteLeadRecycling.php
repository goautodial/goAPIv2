<?php
    ###############################################################
    #### Name: goDeleteLeadRecycling.php	               ####
    #### Description: API to delete specific Lead Recycling    ####
    #### Version: 0.9                                          ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016              ####
    #### Written by: Warren Ipac Briones                       ####
    #### License: AGPLv2                                       ####
    ###############################################################
    include "goFunctions.php";
    
    ### POST or GET Variables
        $camp = $_REQUEST['leadRecCampID'];
        $stat = $_REQUEST['status'];
        $ip_address = $_REQUEST['hostname'];
    
    ### Check Voicemail ID if its null or empty
	if($camp == null || $stat == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID and Pause Code."); 
	} else {
 
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }


   		$queryOne = "SELECT status, campaign_id FROM vicidial_lead_recycle WHERE campaign_id='$camp' AND status='$stat';";
   		$rsltvOne = mysqli_query($link,$queryOne);
		$countResult = mysqli_num_rows($rsltvOne);

		if($countResult > 0) {
				$deleteQuery = "DELETE FROM vicidial_lead_recycle WHERE campaign_id = '$camp';"; 
   				$deleteResult = mysqli_query($link,$deleteQuery);
				$deleteQueryTwo = "DELETE FROM vicidial_lead_recycle WHERE campaign_id = '$camp' AND status='$stat';"; 
   				$deleteResultTwo = mysqli_query($link,$deleteQueryTwo);
				//echo $deleteQuery;

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Lead Recycling $stat','DELETE FROM vicidial_lead_recycle WHERE campaign_id=$camp AND status=$stat;');";
                                        $rsltvLog = mysqli_query($linkgo,$queryLog);


				$apiresults = array("result" => "success");

		} else {
			$apiresults = array("result" => "Error: Pause Code doesn't exist.");
		}
	}//end
?>
