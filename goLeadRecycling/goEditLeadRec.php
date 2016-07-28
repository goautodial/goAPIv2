<?php
   ########################################################
   #### Name: goEditPauseCode.php                      ####
   #### Description: API to edit specific Pause Code   ####
   #### Version: 0.9                                   ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015       ####
   #### Written by: Jeremiah Sebastian V. Samatra      ####
   #### License: AGPLv2                                ####
   ########################################################

    include "goFunctions.php";

    ### POST or GET Variables
        $camp = $_REQUEST['leadRecCampID'];
        $status = $_REQUEST['status'];
        $attempt_delay = $_REQUEST['attempt_delay'];
        $attempt_maximum = $_REQUEST['attempt_maximum'];
        $active = strtoupper($_REQUEST['active']);

    ### Default values
    $defActive = array('N','Y');

    ### ERROR CHECKING ...
        if($camp == null || strlen($camp) < 3) {
                $apiresults = array("result" => "Error: Set a value for CAMP ID not less than 3 characters.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/'$status)){
                $apiresults = array("result" => "Error: Special characters found in Status and must not be empty");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/ $attempt_delay)){
                $apiresults = array("result" => "Error: Special characters found in Attempt Delay and must not be empty");
        } else {
	if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $atmpt_maximum)){
               $apiresults = array("result" => "Error: Special characters found in Attempt Maximum and must not be empty");
        } else {

                if(!in_array($active,$defActive) && $active != null) {
                        $apiresults = array("result" => "Error: Default value for active is N for No and Y for Yes only.");
                } else {


                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                        $queryCheck = "SELECT * FROM vicidial_lead_recycle WHERE campaign_id='$camp'";
                        $sqlCheck = mysqli_query($link,$queryCheck);
                        $countCheck1 = mysqli_num_rows($sqlCheck);
                                if($countCheck1 > 0){
                        $queryCheck = "SELECT status,attempt_delay,attempt_maximum,campaign_id,active FROM vicidial_lead_recycle WHERE campaign_id='$camp' AND status = '$status';";
                        $sqlCheck = mysqli_query($link,$queryCheck);
                        $countCheck = mysqli_num_rows($sqlCheck);
                                if($countCheck <= 0){

                                while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
                                        $dataStatus = $fresults['status'];
                                        $dataAttemptDelay = $fresults['attempt_delay'];
                                        $dataAttemptMaximum = $fresults['attempt_maximum'];
                                        $dataCampID = $fresults['campaign_id'];
                                        $dataActive = $fresults['active'];
                                }
                                }
                $countVM = mysqli_num_rows($sqlCheck);

                if($countVM > 0) {

                        if($status == null){$status = $dataStatus;}
                        if($attempt_delay == null){$attempt_delay = $dataAttemptDelay;}
                        if($attempt_maximum == null){$attempt_maximum = $dataAttemptMaximum;}
                        if($camp == null){$camp = $dataCampID;}
                        if($active == null){$active = $dataActive;}

                        $queryVM ="UPDATE vicidial_lead_recycle SET  attempt_delay='$attempt_delay',attempt_maximum='$attempt_maximum',  active='$active' WHERE status='$status'";
                        $rsltv1 = mysqli_query($link,$queryVM);


                                        if($rsltv1 == false){
                                                $apiresults = array("result" => "Error: Try updating Pause Code Again");
                                        } else {
                                                $apiresults = array("result" => "success");

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Lead Recycling: $status','UPDATE vicidial_lead_recycle SET status=$status,  attempt_delay=$attempt_delay, attempt_maximum=$attempt_maximum,  campaign_id=$camp,  active=$active WHERE status=$status');";
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
