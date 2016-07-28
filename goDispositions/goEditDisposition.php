<?php
   ####################################################
   #### Name: goEditDisposition.php                ####
   #### Description: API to edit specific Disposition####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $status = $_REQUEST['status'];
        $campaign_id = $_REQUEST['campaign_id'];
        $status_name = $_REQUEST['status_name'];
        $selectable = $_REQUEST['selectable'];
        $human_answered = $_REQUEST['human_answered'];
        $sale = $_REQUEST['sale'];
        $dnc = $_REQUEST['dnc'];
        $customer_contact = $_REQUEST['customer_contact'];
        $not_interested = $_REQUEST['not_interested'];
        $unworkable = $_REQUEST['unworkable'];
        $scheduled_callback = $_REQUEST['scheduled_callback'];
        $ip_address = $_REQUEST['hostname'];
        $goUser = $_REQUEST['goUser'];


    ### Default values
    $defVal = array("Y","N");


    ### ERROR CHECKING
        if($campaign_id == null) {
                $apiresults = array("result" => "Error: Set a value for Campaign ID.");
        } else {


        if($status == null) {
                $apiresults = array("result" => "Error: Set a value for status.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status_name) && $status_name != null){
                $apiresults = array("result" => "Error: Special characters found in status name and must not be empty");
        } else {


                if(!in_array($scheduled_callback,$defVal) && $scheduled_callback != NULL) {
                        $apiresults = array("result" => "Error: Default value for scheduled_callback is Y or N only.");
                } else {
                if(!in_array($unworkable,$defVal) && $unworkable != NULL) {
                        $apiresults = array("result" => "Error: Default value for unworkable is Y or N only.");
                } else {
                if(!in_array($selectable,$defVal) && $selectable != NULL) {
                        $apiresults = array("result" => "Error: Default value for selectable is Y or N only.");
                } else {
                if(!in_array($human_answered,$defVal) && $human_answered != NULL) {
                        $apiresults = array("result" => "Error: Default value for human_answered is Y or N only.");
                } else {
                if(!in_array($sale,$defVal) && $sale != NULL) {
                        $apiresults = array("result" => "Error: Default value for sale is Y or N only.");
                } else {
                if(!in_array($dnc,$defVal) && $dnc != NULL) {
                        $apiresults = array("result" => "Error: Default value for dnc is Y or N only.");
                } else {
                if(!in_array($customer_contact,$defVal) && $customer_contact != NULL) {
                        $apiresults = array("result" => "Error: Default value for customer_contact is Y or N only.");
                } else {
                if(!in_array($not_interested,$defVal) && $not_interested != NULL) {
                        $apiresults = array("result" => "Error: Default value for not_interested is Y or N only.");
                } else {


		
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                  // $addedSQL = "A user_group='$groupId'";
                }

                        $queryCheck = "SELECT status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback  FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id'
						AND status='$status' $ul ;";
                        $sqlCheck = mysqli_query($link, $queryCheck);

                				while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
					$dataStat = $fresults['status'];
					$dataStatName = $fresults['status_name'];
					$dataSel = $fresults['selectable'];
					$dataCamp = $fresults['campaign_id'];
					$dataHumAns = $fresults['human_answered'];
					$dataCat = $fresults['category'];
					$dataSale = $fresults['sale'];
					$dataDNC = $fresults['dnc'];
					$dataCusCon = $fresults['customer_contact'];
					$dataNotInt = $fresults['not_interested'];
					$dataUnwork = $fresults['unworkable'];
					$dataSched = $fresults['scheduled_callback'];
				  
				}
                $countVM = mysqli_num_rows($sqlCheck);

                if($countVM > 0) {
		
			if($status_name == null){$status_name = $dataStatName;}
			if($selectable == null){$selectable = $dataSel;}
			if($human_answered == null){$human_answered = $dataHumAns;}
			if($sale == null){$sale = $dataSale;}
			if($dnc == null){$dnc = $dataDNC;}
			if($customer_contact == null){$customer_contact = $dataCusCon;}
			if($not_interested == null){$not_interested = $dataNotInt;}
			if($unworkable == null){$unworkable = $dataUnwork;}
			if($scheduled_callback == null){$scheduled_callback = $dataSched;}



/*
                $campaign_id = $this->uri->segment(3);
                $str = $this->go_unserialize($this->uri->segment(5));
                $is_exist = 0;
*/

                $queryDispo= "UPDATE vicidial_campaign_statuses SET status_name='$status_name',selectable='$selectable',human_answered='$human_answered',
				category='UNDEFINED', sale='$sale',dnc='$dnc',customer_contact='$customer_contact',not_interested='$not_interested',unworkable='$unworkable',
				scheduled_callback='$scheduled_callback' WHERE status='$status' AND campaign_id='$campaign_id';";

                        $rsltv1 = mysqli_query($link, $queryDispo);
                        

					if($rsltv1 == false){
						$apiresults = array("result" => "Error: Try updating Disposition Again");
					} else {
						$apiresults = array("result" => "success");

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Voicemail box: $voicemail_id','UPDATE vicidial_voicemail SET pass=$pass,  fullname=$fullname,  email=$email,  active=$active,  delete_vm_after_email=$delete_vm_after_email WHERE voicemail_id=$voicemail_id');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);


					}

                                       
			} else {
				$apiresults = array("result" => "Error: Campaign Status doesn't exist");
				}
}}}}
}}}}}}}
?>
