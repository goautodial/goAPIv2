<?php
    /////////////////////////////////////////////////////
    /// Name: goDeleteLead.php                        ///
    /// Description: API to delete specific contact   ///
    /// Copyright: GOAutoDial Inc. (c) 2016           ///
    /// Written by: Alexander Abenoja _m/             ///
    /////////////////////////////////////////////////////
    include_once("../goFunctions.php");

    // POST or GET Variables
        $lead_id = $_REQUEST['lead_id'];
		$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
		
    // Check user_id if its null or empty
        if($lead_id == null) {
			$err_msg = error_handle("40001");
			$apiresults = array("code" => "40001", "result" => $err_msg);
            //$apiresults = array("result" => "Error: Set a value for Lead ID.");
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
                $querygo = "DELETE FROM go_customers WHERE lead_id='$lead_id'";
                $rsltvg = mysqli_query($linkgo, $querygo) or die(mysqli_error($link));                
                $rsltv = mysqli_query($link, $query) or die(mysqli_error($link));
                $countResult = mysqli_num_rows($rsltv);

                if($rsltv != false){
                        $log_id = log_action($linkgo, 'DELETE', $log_user, $ip_address, "Deleted Lead ID: $lead_id", $log_group, $query, $querygo);
						
                        $apiresults = array("result" => "success");
                }else{
					$err_msg = error_handle("10010");
					$apiresults = array("code" => "10010", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Lead ID does not exist.");
                }
		
        }
?>
