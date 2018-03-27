<?php
 /**
 * @file 		goEditDisposition.php
 * @brief 		API for Dispositions
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

 
    ### POST or GET Variables
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
	$goUser = $astDB->escape($_REQUEST['goUser']);

	$log_user = $astDB->escape($_REQUEST['log_user']);
	$log_group = $astDB->escape($_REQUEST['log_group']);
	
	$priority = $astDB->escape($_REQUEST['priority']);
	$color = $astDB->escape($_REQUEST['color']);
	$edit_type = $astDB->escape($_REQUEST['type']);
	
	$type = (!in_array($edit_type, array('SYSTEM', 'CUSTOM'))) ? 'CUSTOM' : $edit_type;

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
												$groupId = go_get_groupid($goUser, $astDB);
								
												if (!checkIfTenant($groupId, $goDB)) {
													//$ul = "";
												} else {
													//$ul = "AND user_group='$groupId'";
													// $addedSQL = "A user_group='$groupId'";
													$astDB->where('user_group', $groupId);
												}
								
												//$queryCheck = "SELECT status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback  FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id'
												//AND status='$status' $ul ;";
												$astDB->where('campaign_id', $campaign_id);
												$astDB->where('status', $status);
												$sqlCheck = $astDB->get('vicidial_campaign_statuses', null, 'status,status_name,selectable,campaign_id,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback');
							
												foreach ($sqlCheck as $fresults){
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
												$countVM = $astDB->getRowCount();
								
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
										
										
													//$queryDispo= "UPDATE vicidial_campaign_statuses SET status_name='$status_name',selectable='$selectable',human_answered='$human_answered',
													//category='UNDEFINED', sale='$sale',dnc='$dnc',customer_contact='$customer_contact',not_interested='$not_interested',unworkable='$unworkable',
													//scheduled_callback='$scheduled_callback' WHERE status='$status' AND campaign_id='$campaign_id';";
													$updateData = array(
														'status_name' => $status_name,
														'selectable' => $selectable,
														'human_answered' => $human_answered,
														'category' => 'UNDEFINED',
														'sale' => $sale,
														'dnc' => $dnc,
														'customer_contact' => $customer_contact,
														'not_interested' => $not_interested,
														'unworkable' => $unworkable,
														'scheduled_callback' => $scheduled_callback
													);
													$astDB->where('status', $status);
													$astDB->where('campaign_id', $campaign_id);
													$rsltv1 = $astDB->update('vicidial_campaign_statuses', $updateData);
										
													if($rsltv1 == false){
														$apiresults = array("result" => "Error: Try updating Disposition Again");
													} else {
														$apiresults = array("result" => "success");
														
														$chkStatus = "SHOW TABLES LIKE 'go_statuses'";
														$statusRslt = $goDB->rawQuery($chkStatus);
														$statusExist = $goDB->getRowCount();
														
														if ($statusExist > 0) {
															//$chkStatus = "SELECT * FROM go_statuses WHERE status='$status' AND campaign_id='$campaign_id';";
															$goDB->where('status', $status);
															$goDB->where('campaign_id', $campaign_id);
															$statusRslt = $goDB->get('go_statuses');
															$statusCnt = $goDB->getRowCount();
															
															if ($statusCnt > 0) {
																//$statusQuery = "UPDATE go_statuses SET priority='$priority',color='$color',type='$type' WHERE status='$status' AND campaign_id='$campaign_id';";
																$updateData = array(
																	'priority' => $priority,
																	'color' => $color,
																	'type' => $type
																);
																$goDB->where('status', $status);
																$goDB->where('campaign_id', $campaign_id);
																$goDB->update('go_statuses', $updateData);
															} else {
																//$statusQuery = "INSERT INTO go_statuses (status, campaign_id, priority, color, type) VALUES ('$status', '$campaign_id', '$priority', '$color', '$type');";
																$insertData = array(
																	'status' => $status,
																	'campaign_id' => $campaign_id,
																	'priority' => $priority,
																	'color' => $color,
																	'type' => $type
																);
																$goDB->insert('go_statuses', $insertData);
															}
															$statusRslt = $goDB->getRowCount();
														}
								
										### Admin logs
														//$SQLdate = date("Y-m-d H:i:s");
														//$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Voicemail box: $voicemail_id','UPDATE vicidial_voicemail SET pass=$pass,  fullname=$fullname,  email=$email,  active=$active,  delete_vm_after_email=$delete_vm_after_email WHERE voicemail_id=$voicemail_id');";
														//$rsltvLog = mysqli_query($linkgo, $queryLog);
														$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified dispositions on campaign $campaign_id", $log_group, $queryDispo);
													}
												} else {
													$apiresults = array("result" => "Error: Campaign Status doesn't exist");
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
