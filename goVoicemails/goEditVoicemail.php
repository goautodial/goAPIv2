<?php
   ####################################################
   #### Name: goEditVoicemails.php                 ####
   #### Description: API to edit specific Voicemail####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once("../goFunctions.php");
 
    ### POST or GET Variables
        $pass = mysqli_real_escape_string($link, $_REQUEST['pass']);
        $fullname = mysqli_real_escape_string($link, $_REQUEST['fullname']);
        $email = mysqli_real_escape_string($link, $_REQUEST['email']);
        $active = mysqli_real_escape_string($link, $_REQUEST['active']);
        $delete_vm_after_email = mysqli_real_escape_string($link, $_REQUEST['delete_vm_after_email']);
        $voicemail_id = mysqli_real_escape_string($link, $_REQUEST['voicemail_id']);
		
		$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
		$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
    ### Default values 
    $defActive = array("Y","N");
    $defDelVM = array("N","Y"); 

    ### ERROR CHECKING ...
      if($voicemail_id == null) { 
                $apiresults = array("result" => "Error: Set a value for VOICEMAIL ID."); 
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $fullname)){
                $apiresults = array("result" => "Error: Special characters found in fullname");
        } else {
                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is Y or N only.");
                } else {
                if(!in_array($delete_vm_after_email,$defDelVM) && $delete_vm_after_email != null) {
                        $apiresults = array("result" => "Error: Default value for delete_vm_after_email is Y or N only.");
                } else {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL) && $email != null) {
                        $apiresults = array("result" => "Error: Invalid email format.");
                } else {

		
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   //$addedSQL = "WHERE user_group='$groupId'";
                }

                        $queryCheck = "SELECT voicemail_id,pass,active,fullname,email,delete_vm_after_email from vicidial_voicemail where voicemail_id='$voicemail_id' $ul;";
                        $sqlCheck = mysqli_query($link, $queryCheck);

                				while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
					$dataVM_id = $fresults['voicemail_id'];
					$dataVM_pass = $fresults['pass'];
					$dataactive = $fresults['active'];
					$datafullname = $fresults['fullname'];
					$dataemail = $fresults['email'];
					$datadeleteVMemail = $fresults['delete_vm_after_email'];
				  
				}
                $countVM = mysqli_num_rows($sqlCheck);

                if($countVM > 0) {
		
			if($pass == null){$pass = $dataVM_pass;}
			if($active == null){$active = $dataactive;}
			if($fullname == null){$fullname = $datafullname;}
			if($email == null){$email = $dataemail;}
			if($delete_vm_after_email == null){$delete_vm_after_email = $datadeleteVMemail;}

			$queryVM ="UPDATE vicidial_voicemail SET pass='$pass',  fullname='$fullname',  email='$email',  active='$active',  delete_vm_after_email='$delete_vm_after_email' WHERE voicemail_id='$voicemail_id'";
                        $rsltv1 = mysqli_query($link, $queryVM);
                        

					if($rsltv1 == false){
						$apiresults = array("result" => "Error: Try updating Voicemail Again");
					} else {
						$apiresults = array("result" => "success");

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Voicemail box: $voicemail_id','UPDATE vicidial_voicemail SET pass=$pass,  fullname=$fullname,  email=$email,  active=$active,  delete_vm_after_email=$delete_vm_after_email WHERE voicemail_id=$voicemail_id');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
						$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Voicemail ID: $voicemail_id", $log_group, $queryVM);


					}

                                       
			} else {
				$apiresults = array("result" => "Error: Voicemail doesn't exist");
				}
}}}}
}
?>
