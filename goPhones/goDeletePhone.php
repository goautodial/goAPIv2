<?php
    #######################################################
    #### Name: goDeletePhone.php	               ####
    #### Description: API to delete specific Phone     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $extension = $_REQUEST['extension'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
    
    ### Check campaign_id if its null or empty
	if($extension == null) { 
		$apiresults = array("result" => "Error: Set a value for Extension."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "WHERE extension='$extension'";
    		} else { 
			$ul = "WHERE extension='$extension' AND user_group='$groupId'";  
		}

   		$query = "SELECT extension  FROM phones $ul ORDER BY extension LIMIT 1;";
   		$rsltv = mysqli_query($link,$query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataExtension = $fresults['extension'];
			}

			if(!$dataExtension == null) {
				$deleteQuery = "DELETE FROM phones WHERE extension = '$dataExtension'"; 
   				$deleteResult = mysqli_query($link,$deleteQuery);
				//echo $deleteQuery;
				$deleteQueryB = "DELETE FROM subscriber where username= '$dataExtension'"; 
   				$deleteResultB = mysqli_query($linkgokam,$deleteQueryB);

				/* Create connection to kamilioDB
                                $kamquery = "DELETE FROM subscriber where username='$exten';";
                                $this->kamilioDB->query($kamquery);
				*/

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted New Phone $extension','DELETE FROM phones WHERE extension = $dataExtension;');";
                                        $rsltvLog = mysqli_query($linkgo,$queryLog);

				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Extension doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: Extension doesn't exist.");
		}
	}//end
?>
