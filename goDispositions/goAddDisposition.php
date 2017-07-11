<?php
   ####################################################
   #### Name: goAddDisposition.php                 ####
   #### Description: API to add new Disposition    ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
        $category = "UNDEFINED"; //$_REQUEST['category'];
		$userid = mysqli_real_escape_string($link, $_REQUEST['userid']);
        $status = mysqli_real_escape_string($link, $_REQUEST['status']);
        $campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
        $status_name = mysqli_real_escape_string($link, $_REQUEST['status_name']);
        $selectable = mysqli_real_escape_string($link, $_REQUEST['selectable']);
        $human_answered = mysqli_real_escape_string($link, $_REQUEST['human_answered']);
        $sale = mysqli_real_escape_string($link, $_REQUEST['sale']);
        $dnc = mysqli_real_escape_string($link, $_REQUEST['dnc']);
        $customer_contact = mysqli_real_escape_string($link, $_REQUEST['customer_contact']);
        $not_interested = mysqli_real_escape_string($link, $_REQUEST['not_interested']);
        $unworkable = mysqli_real_escape_string($link, $_REQUEST['unworkable']);
        $scheduled_callback = mysqli_real_escape_string($link, $_REQUEST['scheduled_callback']);
		
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
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
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status_name) || $status_name == null){
                $apiresults = array("result" => "Error: Special characters found in status name and must not be empty");
        } else {
                if(!in_array($scheduled_callback,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for scheduled_callback is Y or N only.");
                } else {
                if(!in_array($unworkable,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for unworkable is Y or N only.");
                } else {
                if(!in_array($selectable,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for selectable is Y or N only.");
                } else {
                if(!in_array($human_answered,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for human_answered is Y or N only.");
                } else {
                if(!in_array($sale,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for sale is Y or N only.");
                } else {
                if(!in_array($dnc,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for dnc is Y or N only.");
                } else {
                if(!in_array($customer_contact,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for customer_contact is Y or N only.");
                } else {
                if(!in_array($not_interested,$defVal)) {
                        $apiresults = array("result" => "Error: Default value for not_interested is Y or N only.");
                } else {
					$groupId = go_get_groupid($goUser);
					if (!checkIfTenant($groupId)) {
							$ul = "";
					} else {
							$ul = "AND user_group='$groupId'";
					   $addedSQL = "WHERE user_group='$groupId'";
					}
					
					$query = "SELECT status FROM vicidial_statuses WHERE status='$status'; ";
					$rsltv = mysqli_query($link, $query);
					$countResult = mysqli_num_rows($rsltv);
					
					if($countResult <= 0) {
						$queryCheck = "SELECT campaign_id, status FROM vicidial_campaign_statuses WHERE status='$status' AND campaign_id='$campaign_id';";
						$sqlCheck = mysqli_query($link, $queryCheck);
						$countCheck = mysqli_num_rows($sqlCheck);
						
						if($countCheck <= 0){
							if($campaign_id == "ALL"){
								$getAllowedCampaigns_query = "SELECT vicidial_users.user_group, vicidial_user_groups.allowed_campaigns FROM vicidial_users, vicidial_user_groups WHERE vicidial_users.user_group = vicidial_user_groups.user_group AND vicidial_users.user_id ='$userid'";
								$allowedCampaigns_result = mysqli_query($link, $getAllowedCampaigns_query);
								$allowedCampaignsFetch = mysqli_fetch_array($allowedCampaigns_result, MYSQLI_ASSOC);
								$allowedCampaigns = $allowedCampaignsFetch['allowed_campaigns'];
								//if admin
								if(preg_match("/ALL-CAMPAIGNS/", $allowedCampaigns)){
									$queryx = "SELECT campaign_id FROM vicidial_campaigns;";
									$exec_queryx = mysqli_query($link, $queryx);
									
									while($row = mysqli_fetch_array($exec_queryx)){
										$campaign_id = $row['campaign_id'];
										$newQuery = "INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback) VALUES('$status','$status_name','$selectable','$campaign_id','$human_answered','$category','$sale','$dnc','$customer_contact','$not_interested','$unworkable','$scheduled_callback');";
										$rsltv = mysqli_query($link, $newQuery);
									}
								}else{
									$multiple_campaigns = explode("-", $allowedCampaigns);
									$allowedCampaignsx = $multiple_campaigns[0];
									$campsWithSpaces = explode(" ",$allowedCampaignsx);
									
									for($i=0; $i< count($campsWithSpaces); $i++){
										$campaign_id = $campsWithSpaces[$i];
										if($campaign_id != ''){
											$newQuery = "INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback) VALUES('$status','$status_name','$selectable','$campaign_id','$human_answered','$category','$sale','$dnc','$customer_contact','$not_interested','$unworkable','$scheduled_callback');";
											$rsltv = mysqli_query($link, $newQuery);
										}
									}
								}
							}else{
								$newQuery = "INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback) VALUES('$status','$status_name','$selectable','$campaign_id','$human_answered','$category','$sale','$dnc','$customer_contact','$not_interested','$unworkable','$scheduled_callback')";
								$rsltv = mysqli_query($link, $newQuery);
							}
								
							### Admin logs
							//$SQLdate = date("Y-m-d H:i:s");
							//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Voicemail: $voicemail_id','INSERT INTO vicidial_voicemail (voicemail_id,pass,fullname,active,email,user_group) VALUES ($voicemail_id,$pass,$fullname,$active,$email,$user_group)');";
							//$rsltvLog = mysqli_query($linkgo, $queryLog);
							$log_id = log_action($linkgo, 'ADD', $log_user, $ip_address, "Added a New Disposition $status on Campaign $campaign_id", $log_group, $newQuery);
							
							if($rsltv == false){
								$apiresults = array("result" => "Error: Add failed, check your details");
							} else {
								$apiresults = array("result" => "success");
							}
						}else {
							$apiresults = array("result" => "Error: Add failed, Campaign Status already already exist!");
						}
					} else {
						$apiresults = array("result" => $query);
					}
                }
				}
				}
				}
				}
				}
				}
				}
				}
				}
		}
?>
