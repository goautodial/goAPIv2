<?php
   ####################################################
   #### Name: goEditPauseCode.php                  ####
   #### Description: API to edit specific Pause Code####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include "goFunctions.php";
 
    ### POST or GET Variables
        $camp = $_REQUEST['pauseCampID'];
        $pause_code = $_REQUEST['pause_code'];
        $pause_code_name = $_REQUEST['pause_code_name'];
        $billable = strtoupper($_REQUEST['billable']);
	
    ### Default values 
    $defBill = array('NO','YES','HALF');

    ### ERROR CHECKING ...
        if($camp == null || strlen($camp) < 3) {
                $apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_co)){
                $apiresults = array("result" => "Error: Special characters found in pause code and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $pause_codname)){
                $apiresults = array("result" => "Error: Special characters found in pause code name and must not be empty");
        } else {

                if(!in_array($billable,$defBill) && $billable != null) {
                        $apiresults = array("result" => "Error: Default value for billable is No, Yes or half only.");
                } else {

		
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                        $queryCheck = "SELECT * FROM vicidial_pause_codes WHERE campaign_id='$camp'";
                        $sqlCheck = mysqli_query($link,$queryCheck);
                        $countCheck1 = mysqli_num_rows($sqlCheck);
                                if($countCheck1 > 0){

                        $queryCheck = "SELECT pause_code,pause_code_name,campaign_id,billable FROM vicidial_pause_codes WHERE campaign_id='$camp' AND pause_code = '$pause_code';";
                        $sqlCheck = mysqli_query($link,$queryCheck);
                        $countCheck = mysqli_num_rows($sqlCheck);
                                if($countCheck <= 0){

                		while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
					$dataPC = $fresults['pause_code'];
					$dataPCN = $fresults['pause_code_name'];
					$dataCampID = $fresults['campaign_id'];
					$dataBill = $fresults['billable'];				  
				}
				}
                $countVM = mysqli_num_rows($sqlCheck);

                if($countVM > 0) {
		
			if($pause_code == null){$pause_code = $dataPC;}
			if($pause_code_name == null){$pause_code_name = $dataPCN;}
			if($camp == null){$camp = $dataCampID;}
			if($billable == null){$billable = $dataBill;}

			$queryVM ="UPDATE vicidial_pause_codes SET  pause_code_name='$pause_code_name',  billable='$billable' WHERE pause_code='$pause_code'";
                        $rsltv1 = mysqli_query($link,$queryVM);
                        

					if($rsltv1 == false){
						$apiresults = array("result" => "Error: Try updating Pause Code Again");
					} else {
						$apiresults = array("result" => "success");

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Pause Code: $pause_code','UPDATE vicidial_pause_codes SET pasue_code=$pause_code,  pause_code_name=$pause_code_name,  campaign_id=$camp,  billable=$billable WHERE pause_code=$pause_code');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


					}
		
                                       
			} else {
				$apiresults = array("result" => "Error: Pause code doesn't exist");
				}
                                } else {
                                          $apiresults = array("result" => "Error: Add failed, Campaign ID does not exist!");

                                }

}}}}
?>
