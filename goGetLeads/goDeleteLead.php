<?php
    #######################################################
    #### Name: goDeleteLead.php                        ####
    #### Description: API to delete specific contact   ####
    #### Copyright: GOAutoDial Inc. (c) 2016           ####
    #### Written by: Alexander Abenoja _m/             ####
    #######################################################
    include_once("../goFunctions.php");

    ### POST or GET Variables
        $lead_id = $_REQUEST['lead_id'];
		$ip_address = $_REQUEST['hostname'];
		
    ### Check user_id if its null or empty
        if($lead_id == null) {
                $apiresults = array("result" => "Error: Set a value for Lead ID.");
        } else {

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "AND user='$user_id'";
                } else {
                        $ul = "AND user='$user_id' AND user_group='$groupId'";
                }

                if ($groupId != 'ADMIN') {
                        $notAdminSQL = "AND user_group != 'ADMIN'";
                }

                $query = "DELETE FROM vicidial_list WHERE lead_id='$lead_id'";
                $rsltv = mysqli_query($link, $query);
				$countResult = mysqli_num_rows($rsltv);

				if($rsltv != false){
					 ### Admin logs
							$SQLdate = date("Y-m-d H:i:s");
							$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted Lead ID $lead_id','DELETE FROM vicidial_list WHERE lead_id=$lead_id');";
								$rsltvLog = mysqli_query($linkgo, $queryLog);
		
								$apiresults = array("result" => "success");
				}else{
					 $apiresults = array("result" => "Error: Lead ID does not exist.");
		
				}
		
        }
?>
