<?php
    #######################################################
    #### Name: goDeleteUser.php  	               ####
    #### Description: API to delete specific User      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2015      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $user_id = $_REQUEST['user_id'];
    $ip_address = $_REQUEST['hostname'];
    $goUser = $_REQUEST['goUser'];

	if($user_id == null) { 
		$apiresults = array("result" => "Error: Set a value for User ID."); 
	} else {
 
    		$groupId = go_get_groupid($goUser);
    
		if (!checkIfTenant($groupId)) {
        		$ul = "AND user_id='$user_id'";
    		} else { 
			$ul = "AND user_id='$user_id' AND user_group='$groupId'";  
		}
		if ($groupId != 'ADMIN') {
        		$notAdminSQL = "AND user_group != 'ADMIN'";
                }

//   		$query = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC LIMIT 1;";
		$query = "SELECT user,phone_login from vicidial_users WHERE user_id='$user_id';";
   		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);

		if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataUserID = $fresults['user'];
				$dataPhoneLogin = $fresults['phone_login'];
			}
			if(!$dataUserID == null) {
				$deleteQuery = "DELETE FROM vicidial_users WHERE user='$dataUserID' AND user != 'ADMIN'"; 
   				$deleteResult = mysqli_query($link, $deleteQuery);

				$deleteQueryGo = "DELETE FROM users WHERE name='$dataUserID'"; 
   				$deleteResultGo = mysqli_query($linkgo, $deleteQueryGo);
   				
				$deleteQueryA = "DELETE FROM phones WHERE extension='$dataPhoneLogin';";
             			$deleteResultA = mysqli_query($link, $deleteQueryA);
				
				$deleteQueryCB = "DELETE FROM subscriber WHERE username='$dataPhoneLogin';";
             			$deleteResultCB = mysqli_query($linkgokam, $deleteQueryCB);

### admin lgs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','DELETE','Deleted User: $dataUserID','DELETE FROM vicidial_users WHERE user=$dataUserID AND user != ADMIN');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

				//kamilio
		                //$deleteQueryB = "DELETE FROM subscriber where username='$phone_login';";
				//$deleteResultB = mysqli_query($deleteQueryB, $linkka);
				        $querydel = "SELECT user, full_name, user_level, user_group, active FROM vicidial_users WHERE user NOT IN ('VDAD','VDCL') AND user_level != '4' $ul $notAdminSQL ORDER BY user ASC LIMIT 1;";
        	        		$rsltvdel = mysqli_query($link, $querydel);
                			$countResult = mysqli_num_rows($rsltvdel);

                			if($countResult > 0) {	
						$apiresults = array("result" => "Error: Delete failed");
					} else {
						$apiresults = array("result" => "success"); 
					}
			} else {
				$apiresults = array("result" => "Error: User doesn't exist.");
			}

		} else {
			$apiresults = array("result" => "Error: User doesn't exist.");
		}
	}
?>
