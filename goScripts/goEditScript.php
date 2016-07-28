<?php
   ####################################################
   #### Name: goEditScript.php                 ####
   #### Description: API to edit specific Script####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
    ### POST or GET Variables
        $script_id = $_REQUEST['script_id'];
        $script_name = $_REQUEST['script_name'];
        $script_comments = $_REQUEST['script_comments'];
        $script_text = $_REQUEST['script_text'];
        $active = $_REQUEST['active'];
        $ip_address = $_REQUEST['hostname'];
        $goUser = $_REQUEST['goUser'];

    ### Default values
    $defActive = array("Y","N");


    ### ERROR CHECKING ...
        if($script_id == null || strlen($script_id) < 3) {
                $apiresults = array("result" => "Error: Set a value for  Script ID not less than 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]',$script_id) && $script_id != null){
                $apiresults = array("result" => "Error: Special characters found in script ID");
        } else {
                ### Check value compare to default values
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {


		
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

			$queryCheck = "SELECT * from vicidial_scripts WHERE script_id='".mysqli_escape_string($script_id)."'$ul $addedSQL;";
                        $sqlCheck = mysqli_query($link, $queryCheck);
                				while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
					$datascript_id = $fresults['script_id'];
					$datascript_name = $fresults['script_name'];
					$datascript_comments = $fresults['script_comments'];
					$datascript_text = $fresults['script_text'];
					$dataactive = $fresults['active'];
					$datauser_group = $fresults['user_group'];
				}
                $countVM = mysqli_num_rows($sqlCheck);

                if($countVM > 0) {
			if($script_id == null){ $script_id = $datascript_id; }
			if($script_name == null){ $script_name = $datascript_name;}
			if($script_comments == null){ $script_comments = $datascript_comments;}
			if($script_text == null){ $script_text = $datascript_text;}
		 	if($user_group == null){ $user_group = $datauser_group; }
			if($active == null){ $active =  $dataactive; }

			$queryVM ="UPDATE `vicidial_scripts` SET `script_id` = '".mysqli_escape_string($script_id)."',  `script_name` = '".mysqli_escape_string($script_name)."',  `script_comments` = '".mysqli_escape_string($script_comments)."',  `active` = '".mysqli_escape_string($active)."',  `script_text` = '".mysqli_escape_string($script_text)."' WHERE `script_id` = '".mysqli_escape_string($script_id)."';";


                        $rsltv1 = mysqli_query($link, $queryVM);
                        

					if($rsltv1 == false){
						$apiresults = array("result" => "Error: Try updating Script Again");
					} else {
						$apiresults = array("result" => "success");

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");

                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Voicemail box: $voicemail_id','UPDATE `vicidial_scripts` SET script_id = $script_id,  script_name = $script_name,  script_comments = $script_comments,  active = $active,  script_text =$script_text WHERE script_id = $script_id');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


					}

                                       
			} else {
				$apiresults = array("result" => "Error: Script doesn't exist");
				}
}}}
?>
