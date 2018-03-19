<?php
   //////////////////////////////////#
   //# Name: goAddDisposition.php                 //#
   //# Description: API to add new Disposition    //#
   //# Version: 0.9                               //#
   //# Copyright: GOAutoDial Ltd. (c) 2011-2015   //#
   //# Written by: Jeremiah Sebastian V. Samatra  //#
   //# License: AGPLv2                            //#
   //////////////////////////////////#
    
 
    // POST or GET Variables
	$category = "UNDEFINED"; //$_REQUEST['category'];
	$userid = $astDB->escape($_REQUEST['userid']);
	$status = $astDB->escape($_REQUEST['status']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$status_name = $astDB->escape($_REQUEST['status_name']);
	$selectable = $astDB->escape($_REQUEST['selectable']);
	$human_answered = $astDB->escape($_REQUEST['human_answered']);
	$sale = $astDB->escape($_REQUEST['sale']);
	$dnc = $astDB->escape($_REQUEST['dnc']);
	$customer_contact = $astDB->escape($_REQUEST['customer_contact']);
	$not_interested = $astDB->escape($_REQUEST['not_interested']);
	$unworkable = $astDB->escape($_REQUEST['unworkable']);
	$scheduled_callback = $astDB->escape($_REQUEST['scheduled_callback']);
		
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	
	$color = $astDB->escape($_REQUEST['color']);
	$priority = $astDB->escape($_REQUEST['priority']);
	$type = $astDB->escape($_REQUEST['type']);
	$goUser = $astDB->escape($_REQUEST['goUser']);

    // Default values 
    $defVal = array("Y","N");
	
	if (!$color) { $color = "#b5b5b5"; }
	if (!$priority) { $priority = 1; }
	if (!$type) { $type = 'CUSTOM'; }


    // ERROR CHECKING 
	if($campaign_id == null) {
		$err_msg = error_handle("40001");
		$apiresults = array("code" => "40001", "result" => $err_msg);
		//$apiresults = array("result" => "Error: Set a value for Campaign ID.");
	} else {
        if($status == null) {
			$err_msg = error_handle("40001");
			$apiresults = array("code" => "40001", "result" => $err_msg);
            //$apiresults = array("result" => "Error: Set a value for status.");
        } else {
			if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $status_name) || $status_name == null){
				$err_msg = error_handle("10003", "status_name");
				$apiresults = array("code" => "10003", "result" => $err_msg);
				//$apiresults = array("result" => "Error: Special characters found in status name and must not be empty");
			} else {
				if(!in_array($scheduled_callback,$defVal)) {
					$err_msg = error_handle("10003", "scheduled_callback");
					$apiresults = array("code" => "10003", "result" => $err_msg);
					//$apiresults = array("result" => "Error: Default value for scheduled_callback is Y or N only.");
				} else {
					if(!in_array($unworkable,$defVal)) {
						$err_msg = error_handle("10003", "unworkable");
						$apiresults = array("code" => "10003", "result" => $err_msg);
						//$apiresults = array("result" => "Error: Default value for unworkable is Y or N only.");
					} else {
						if(!in_array($selectable,$defVal)) {
							$err_msg = error_handle("10003", "selectable");
							$apiresults = array("code" => "10003", "result" => $err_msg);
							//$apiresults = array("result" => "Error: Default value for selectable is Y or N only.");
						} else {
							if(!in_array($human_answered,$defVal)) {
								$err_msg = error_handle("10003", "human_answered");
								$apiresults = array("code" => "10003", "result" => $err_msg);
								//$apiresults = array("result" => "Error: Default value for human_answered is Y or N only.");
							} else {
								if(!in_array($sale,$defVal)) {
									$err_msg = error_handle("10003", "sale");
									$apiresults = array("code" => "10003", "result" => $err_msg);
									//$apiresults = array("result" => "Error: Default value for sale is Y or N only.");
								} else {
									if(!in_array($dnc,$defVal)) {
										$err_msg = error_handle("10003", "dnc");
										$apiresults = array("code" => "10003", "result" => $err_msg);
										//$apiresults = array("result" => "Error: Default value for dnc is Y or N only.");
									} else {
										if(!in_array($customer_contact,$defVal)) {
											$err_msg = error_handle("10003", "customer_contact");
											$apiresults = array("code" => "10003", "result" => $err_msg);
											//$apiresults = array("result" => "Error: Default value for customer_contact is Y or N only.");
										} else {
											if(!in_array($not_interested,$defVal)) {
												$err_msg = error_handle("10003", "not_interested");
												$apiresults = array("code" => "10003", "result" => $err_msg);
												//$apiresults = array("result" => "Error: Default value for not_interested is Y or N only.");
											} else {
												$groupId = go_get_groupid($goUser, $astDB);
												if (!checkIfTenant($groupId, $goDB)) {
													$ul = "";
												} else {
													$ul = "AND user_group='$groupId'";
													$addedSQL = "WHERE user_group='$groupId'";
												}
												
												//$query = "SELECT status FROM vicidial_statuses WHERE status='$status'; ";
												$astDB->where('status', $status);
												$rsltv = $astDB->get('vicidial_statuses', null, 'status');
												$countResult = $astDB->getRowCount();
												
												if($countResult <= 0) {
													//$queryCheck = "SELECT campaign_id, status FROM vicidial_campaign_statuses WHERE status='$status' AND campaign_id='$campaign_id';";
													$astDB->where('status', $status);
													$astDB->where('campaign_id', $campaign_id);
													$sqlCheck = $astDB->get('vicidial_campaign_statuses', null, 'campaign_id, status');
													$countCheck = $astDB->getRowCount();
													
													if($countCheck <= 0){
														if($campaign_id == "ALL"){
															$getAllowedCampaigns_query = "SELECT vicidial_users.user_group, vicidial_user_groups.allowed_campaigns FROM vicidial_users, vicidial_user_groups WHERE vicidial_users.user_group = vicidial_user_groups.user_group AND vicidial_users.user_id ='$userid'";
															$allowedCampaigns_result = $astDB->rawQuery($getAllowedCampaigns_query);
															$allowedCampaignsFetch = $allowedCampaigns_result[0];
															$allowedCampaigns = $allowedCampaignsFetch['allowed_campaigns'];
															//if admin
															if(preg_match("/ALL-CAMPAIGNS/", $allowedCampaigns)){
																//$queryx = "SELECT campaign_id FROM vicidial_campaigns;";
																$exec_queryx = $astDB->get('vicidial_campaigns', null, 'campaign_id');
																
																foreach ($exec_queryx as $row){
																	$campaign_id = $row['campaign_id'];
																	$newQuery = "INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback) VALUES('$status','$status_name','$selectable','$campaign_id','$human_answered','$category','$sale','$dnc','$customer_contact','$not_interested','$unworkable','$scheduled_callback');";
																	$rsltv = $astDB->rawQuery($newQuery);
																	
																	$tableQuery = "SHOW tables LIKE 'go_statuses';";
																	$checkTable = $goDB->rawQuery($tableQuery);
																	$tableExist = $goDB->getRowCount();
																	if ($tableExist > 0) {
																		$statusQuery = "INSERT INTO go_statuses (status, campaign_id, priority, color, type) VALUES ('$status', '$campaign_id', '$priority', '$color', '$type');";
																		$statusRslt = $goDB->rawQuery($statusQuery);
																	}
																}
															}else{
																$multiple_campaigns = explode("-", $allowedCampaigns);
																$allowedCampaignsx = $multiple_campaigns[0];
																$campsWithSpaces = explode(" ",$allowedCampaignsx);
																
																for($i=0; $i< count($campsWithSpaces); $i++){
																	$campaign_id = $campsWithSpaces[$i];
																	if($campaign_id != ''){
																		$newQuery = "INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback) VALUES('$status','$status_name','$selectable','$campaign_id','$human_answered','$category','$sale','$dnc','$customer_contact','$not_interested','$unworkable','$scheduled_callback');";
																		$rsltv = $astDB->rawQuery($newQuery);
																		
																		$tableQuery = "SHOW tables LIKE 'go_statuses';";
																		$checkTable = $goDB->rawQuery($tableQuery);
																		$tableExist = $goDB->getRowCount();
																		if ($tableExist > 0) {
																			$statusQuery = "INSERT INTO go_statuses (status, campaign_id, priority, color, type) VALUES ('$status', '$campaign_id', '$priority', '$color', '$type');";
																			$statusRslt = $goDB->rawQuery($statusQuery);
																		}
																	}
																}
															}
														}else{
															$newQuery = "INSERT INTO vicidial_campaign_statuses (status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback) VALUES('$status','$status_name','$selectable','$campaign_id','$human_answered','$category','$sale','$dnc','$customer_contact','$not_interested','$unworkable','$scheduled_callback')";
															$rsltv = $astDB->rawQuery($newQuery);
															
															$tableQuery = "SHOW tables LIKE 'go_statuses';";
															$checkTable = $goDB->rawQuery($tableQuery);
															$tableExist = $goDB->getRowCount();
															if ($tableExist > 0) {
																$statusQuery = "INSERT INTO go_statuses (status, campaign_id, priority, color, type) VALUES ('$status', '$campaign_id', '$priority', '$color', '$type');";
																$statusRslt = $goDB->rawQuery($statusQuery);
															}
														}
															
														// Admin logs
														//$SQLdate = date("Y-m-d H:i:s");
														//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','ADD','Added New Voicemail: $voicemail_id','INSERT INTO vicidial_voicemail (voicemail_id,pass,fullname,active,email,user_group) VALUES ($voicemail_id,$pass,$fullname,$active,$email,$user_group)');";
														//$rsltvLog = mysqli_query($linkgo, $queryLog);
														$log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New Disposition $status on Campaign $campaign_id", $log_group, $newQuery);
														
														if($rsltv == false){
															$err_msg = error_handle("10010");
															$apiresults = array("code" => "10010", "result" => $err_msg);
															//$apiresults = array("result" => "Error: Add failed, check your details");
														} else {
															$apiresults = array("result" => "success");
														}
													} else {
														$err_msg = error_handle("41004", "status. Campaign Status already exists");
														$apiresults = array("code" => "41004", "result" => $err_msg);
														//$apiresults = array("result" => "Error: Add failed, Campaign Status already already exist!");
													}
												} else {
													$err_msg = error_handle("41004", "status. Status already exists in the default statuses");
													$apiresults = array("code" => "41004", "result" => $err_msg);
													//$apiresults = array("result" => $query);
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
