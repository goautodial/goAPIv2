<?php
   ####################################################
   #### Name: goAddList.php                        ####
   #### Description: API to add new list           ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
	### POST or GET Variables
        $list_id = $_REQUEST['list_id'];
        $list_name = $_REQUEST['list_name'];
        $campaign_id = $_REQUEST['campaign_id'];
        $active = $_REQUEST['active'];
        $list_description = $_REQUEST['list_description'];
	$ip_address = $_REQUEST['hostname'];
	$goUser = $_REQUEST['goUser'];


    ### Default values 
    $defActive = array("Y","N");
    
    ### Check campaign_id if its null or empty
	if($list_id == null || $list_id == "") { 
		$apiresults = array("result" => "Error: List ID field is required."); 
	} else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_name) || $list_name == null){
                $apiresults = array("result" => "Error: Special characters found in list_name and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $list_description)){
                $apiresults = array("result" => "Error: Special characters found in list_description");
        } else {

    		### Check value compare to default values
		if(!in_array($active,$defActive) && $active != null) { 
			$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
		} else {
			if(!is_numeric($list_id)){
			$apiresults = array("result" => "Error: List ID must be a number or combination of number");
			} else {

     				$groupId = go_get_groupid($goUser);

		                if (!checkIfTenant($groupId)) {
                		        $ul = "WHERE list_id='$list_id'";
					$ulcamp = "WHERE campaign_id='$campaign_id'";
                		} else {
                        		$ul = "WHERE list_id='$list_id' AND user_group='$groupId'";
					$ulcamp = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
                		}

		                $queryCamp = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ulcamp ORDER BY campaign_id LIMIT 1;";
                		$rsltvCamp = mysqli_query($link, $queryCamp);
                		$countResultCamp = mysqli_num_rows($rsltvCamp);

                		if($countResultCamp > 0) {

   					$query = "SELECT list_id from vicidial_lists $ul order by list_id LIMIT 1";
   					$rsltv = mysqli_query($link, $query);
		                	$countResult = mysqli_num_rows($rsltv);

	                		if($countResult > 0) {
        			                $apiresults = array("result" => "Error: there is already a LIST ID in the system with this ID.");
					} else {

						$SQLdate = date("Y-m-d H:i:s");
						$addQuery = "INSERT INTO vicidial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values('$list_id','$list_name','$campaign_id','$active','$list_description','$SQLdate');";
						$addResult = mysqli_query($link, $addQuery);

	###Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New List $list_id','INSERT INTO vicidial_lists (list_id,list_name,campaign_id,active,list_description,list_changedate) values($list_id,$list_name,$campaign_id,$active,$list_description,$SQLdate);');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

						if($addResult == false){
							$apiresults = array("result" => "Error: Failed to add");
						} else {
							$apiresults = array("result" => "success");
						}
					}
				} else {
					$apiresults = array("result" => "Error: Invalid Campaign ID");
				}
			}
	} }		
}  }

?>
