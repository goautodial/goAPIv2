<?php
    #######################################################
    #### Name: goUserLogin.php	                       ####
    #### Description: API to get specific user	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
    #### Written by: Noel Umandap                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
	$user_name = $_REQUEST['user_name'];
	//$user_email = $_REQUEST['user_email'];
	$user_pass = $_REQUEST['user_pass'];
	
    ### Check if user_name or user_email
	if(!empty($user_name)){
		//username
		$user = "user='".$user_name."'";
	}else{
		//email
		$user = "email='".$user_name."'";
	}

	$query = "SELECT user_id, user, email, pass, full_name, user_level, user_group, active
			  FROM vicidial_users
			  WHERE ".$user."
			  AND pass = '".$user_pass."'
			  ORDER BY user ASC
			  LIMIT 1;";
	$rsltv = mysqli_query($link, $query);
	$countResult = mysqli_num_rows($rsltv);

	if($countResult > 0) {
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
				$dataUser = $fresults['user'];
				$dataFullName = $fresults['full_name'];
				$dataUserLevel = $fresults['user_level'];
				$dataUserGroup = $fresults['user_group'];
				$dataActive   = $fresults['active'];
				$dataUserId = $fresults['user_id'];
				$dataEmail = $fresults['email'];
				$dataPass = $fresults['pass'];
				
				$apiresults = array(
									"result" => "success",
									"user_group" => $dataUserGroup,
									"userno" => $dataUser,
									"full_name" => $dataFullName,
									"user_level" => $dataUserLevel,
									"active" => $dataActive,
									"user_id" => $dataUserId,
									"email" => $dataEmail,
									"pass" => $dataPass
							);
		}
	} else {
		$apiresults = array("result" => "Error: Invalid login credentials please try again.");
	}
	
?>
