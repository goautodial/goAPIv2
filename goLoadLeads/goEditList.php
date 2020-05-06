<?php
 /**
 * @file 		goEditList.php
 * @brief 		API for Modifying Lists
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jeremiah Sebastian Samatra  <jeremiah@goautodial.com>
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
	include_once ("goAPI.php");

    ### POST or GET Variables
	$list_id = $astDB->escape($_REQUEST['list_id']);
	$list_name = $astDB->escape($_REQUEST['list_name']);
	$list_description = $astDB->escape($_REQUEST['list_description']);
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$active = $astDB->escape(strtoupper($_REQUEST['active']));
	$reset_time = $astDB->escape($_REQUEST['reset_time']);
	$xferconf_a_number = $astDB->escape($_REQUEST['xferconf_a_number']);
	$xferconf_b_number = $astDB->escape($_REQUEST['xferconf_b_number']);
	$xferconf_c_number = $astDB->escape($_REQUEST['xferconf_c_number']);
	$xferconf_d_number = $astDB->escape($_REQUEST['xferconf_d_number']);
	$xferconf_e_number = $astDB->escape($_REQUEST['xferconf_e_number']);
	$agent_script_override = $astDB->escape($_REQUEST['agent_script_override']);
	$drop_inbound_group_override = $astDB->escape($_REQUEST['drop_inbound_group_override']);
	$campaign_cid_override = $astDB->escape($_REQUEST['campaign_cid_override']);
	$web_form_address = $astDB->escape($_REQUEST['web_form_address']);
	$reset_list = $astDB->escape(strtoupper($_REQUEST['reset_list']));
	// $values = $_REQUEST['items'];
   
    ### Default values 
    $defActive = array("Y","N");

####################################
	if($list_id == null) {
		$apiresults = array("result" => "Error: Set a value for List ID.");
	} else {
        if(!in_array($active,$defActive) && $active != null) {
            $apiresults = array("result" => "Error: Default value for active is Y or N only.");
        } else {
			if(!in_array($reset_list,$defActive) && $reset_list != null) {
				$apiresults = array("result" => "Error: Default value for reset_list is Y or N only.");
			} else {
				if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_name)){
					$apiresults = array("result" => "Error: Special characters found in list_name");
				} else {
					if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_description)){
						$apiresults = array("result" => "Error: Special characters found in list_description");
					} else {
						if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $reset_time)){
							$apiresults = array("result" => "Error: Special characters found in reset_time");
						} else {
							if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_a_number)){
								$apiresults = array("result" => "Error: Special characters found in xferconf_a_number");
							} else {
								if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_b_number)){
									$apiresults = array("result" => "Error: Special characters found in xferconf_b_number");
								} else {
									if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_c_number)){
										$apiresults = array("result" => "Error: Special characters found in xferconf_c_number");
									} else {
										if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_d_number)){
											$apiresults = array("result" => "Error: Special characters found in xferconf_d_number");
										} else {
											if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_e_number)){
												$apiresults = array("result" => "Error: Special characters found in xferconf_e_number");
											} else {
												if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $agent_script_override)){
													$apiresults = array("result" => "Error: Special characters found in agent_script_override");
												} else {
													if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $drop_inbound_group_override)){
														$apiresults = array("result" => "Error: Special characters found in drop_inbound_group_override");
													} else {
														if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $campaign_cid_override)){
															$apiresults = array("result" => "Error: Special characters found in campaign_cid_override");
														} else {
															if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $web_form_address)){
																$apiresults = array("result" => "Error: Special characters found in web_form_address");
															} else {
																$groupId = go_get_groupid($goUser, $astDB);
																if (!checkIfTenant($groupId, $goDB)) {
																	//$ul = "WHERE campaign_id='$campaign_id'";
																	$ulList = "WHERE list_id='$list_id'";
																	$astDB->where('campaign_id', $campaign_id);
																} else {
																	//$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
																	$ulList = "WHERE list_id='$list_id' AND user_group='$groupId'";
																	$astDB->where('campaign_id', $campaign_id);
																	$astDB->where('user_group', $groupId);
																}
																$countResult = 0;
																if($campaign_id != null){
																	//$query = "SELECT campaign_id,campaign_name FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
																	$astDB->orderBy('campaign_id', 'desc');
																	$rsltv = $astDB->getOne('vicidial_campaigns', 'campaign_id,campaign_name');
																	$countResult = $astDB->getRowCount();
																}
																$queryList = "SELECT list_id,list_name,list_description,(SELECT count(*) AS tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) AS tally,active,list_lastcalldate,campaign_id,reset_time FROM vicidial_lists $ulList ORDER BY list_id LIMIT 1";
																$rsltvList = $astDB->rawQuery($queryList);
																foreach ($rsltvList as $fresults){
																	$listid_data = $fresults['list_id'];
																	$list_name_data = $fresults['list_name'];
																	$list_description_data = $fresults['list_description'];
																	$campaign_id_data = $fresults['campaign_id'];
																	$active_data = $fresults['active'];
																	$xferconf_a_number_data = $fresults['xferconf_a_number'];
																	$xferconf_b_number_data = $fresults['xferconf_b_number'];
																	$xferconf_c_number_data = $fresults['xferconf_c_number'];
																	$xferconf_d_number_data = $fresults['xferconf_d_number'];
																	$xferconf_e_number_data = $fresults['xferconf_e_number'];
																	$agent_script_override_data = $fresults['agent_script_override'];
																	$drop_inbound_group_override_data = $fresults['drop_inbound_group_override'];
																	$campaign_cid_override_data = $fresults['campaign_cid_override'];
																	$web_form_address_data = $fresults['web_form_address'];
																}
																$countList = $astDB->getRowCount();

																if($reset_list == "Y") {
											                		if($countResult > 0) {
																		//$queryreset = "UPDATE vicidial_list set called_since_last_reset='N' where list_id='$listid_data';";
																		$astDB->where('list_id', $listid_data);
																		$rsltvreset = $astDB->update('vicidial_list', array('called_since_last_reset' => 'N'));
																		//$hopperreset = "DELETE from vicidial_hopper where list_id='$listid_data' and campaign_id='$campaign_id';";
																		$astDB->where('list_id', $listid_data);
																		$astDB->where('campaign_id', $campaign_id);
																		$rsltvhopper = $astDB->delete('vicidial_hopper');
																		$apiresults = array("result" => "success");
																	} else {
																		$apiresults = array("result" => "Error: Campaign doesn't exist.");
																	}
																} else {
																	if($countList > 0) {
																		if($countResult > 0) {
																			if($list_name == null){ $list_name = $list_name_data;} else { $list_name = $list_name;}
																			if($list_description == null) {$list_description = $list_description_data; } else { $list_description = $list_description;}
																			if($campaign_id == null){ $campaign_id = $campaign_id_data;} else { $campaign_id = $campaign_id;}
																			if($active == null){$active = $active_data;} else { $active = $active;}
																			if($xferconf_a_number == null) { $xferconf_a_number = $xferconf_a_number_data;} else {$xferconf_a_number = $xferconf_a_number;}
																			if($xferconf_b_number == null){ $xferconf_b_number = $xferconf_b_number_data;} else { $xferconf_b_number = $xferconf_b_number;}
																			if($xferconf_c_number == null){ $xferconf_c_number = $xferconf_c_number_data;} else { $xferconf_c_number = $xferconf_c_number;}
																			if($xferconf_d_number == null) { $xferconf_d_number = $xferconf_d_number_data;} else { $xferconf_d_number = $xferconf_d_number;}
																			if($xferconf_e_number == null) { $xferconf_e_number = $xferconf_e_number_data;} else { $xferconf_e_number = $xferconf_e_number;}
																			if($agent_script_override == null) { $agent_script_override = $agent_script_override_data;} else { $agent_script_override = $agent_script_override;}
																			if($drop_inbound_group_override == null){ $drop_inbound_group_override = $drop_inbound_group_override_data;} else { $drop_inbound_group_override = $drop_inbound_group_override;}
																			if($campaign_cid_override == null){ $campaign_cid_override = $campaign_cid_override_data;} else { $campaign_cid_override = $campaign_cid_override;}
																			if($web_form_address == null){ $web_form_address = $web_form_address_data;} else { $web_form_address = $web_form_address;}
																			
																			//$query = "UPDATE vicidial_lists set list_name = '$list_name', list_description = '$list_description', campaign_id = '$campaign_id', active = '$active', xferconf_a_number = '$xferconf_a_number', xferconf_b_number = '$xferconf_b_number', xferconf_c_number = '$xferconf_c_number', xferconf_d_number = '$xferconf_d_number', xferconf_e_number = '$xferconf_e_number',  agent_script_override = '$agent_script_override', drop_inbound_group_override = '$drop_inbound_group_override', campaign_cid_override = '$campaign_cid_override', web_form_address = '$web_form_address' WHERE list_id='$listid_data';";
																			$updateData = array(
																				'list_name' => $list_name,
																				'list_description' => $list_description,
																				'campaign_id' => $campaign_id,
																				'active' => $active,
																				'xferconf_a_number' => $xferconf_a_number,
																				'xferconf_b_number' => $xferconf_b_number,
																				'xferconf_c_number' => $xferconf_c_number,
																				'xferconf_d_number' => $xferconf_d_number,
																				'xferconf_e_number' => $xferconf_e_number,
																				'agent_script_override' => $agent_script_override,
																				'drop_inbound_group_override' => $drop_inbound_group_override,
																				'campaign_cid_override' => $campaign_cid_override,
																				'web_form_address' => $web_form_address
																			);
																			$astDB->where('list_id', $listid_data);
																			$resultQuery = $astDB->update('vicidial_lists', $updateData);
																			$logQuery = $astDB->getLastQuery();
																			
																			$log_id = log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified List ID: $list_id", $log_group, $logQuery);

																			if($resultQuery == false) {
																				$apiresults = array("result" => "Error: Update failed, check your details.");
																			} else {
																				$SQLdate = date("Y-m-d H:i:s");
																				//$querydate="UPDATE vicidial_lists SET list_changedate='$SQLdate' WHERE list_id='$listid_data';";
																				$astDB->where('list_id', $listid_data);
																				$resultQueryDate = $astDB->update('vicidial_lists', array('list_changedate' => $SQLdate));
																				$apiresults = array("result" => "success");
																			}
																		} else {
																			$apiresults = array("result" => "Error: Campaign doesn't exist.");
																		}
																	}  else {
																		$apiresults = array("result" => "Error: List doesn't exist.");
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
					}
				}
			}
		}
	}
?>