<?php
/**
 * @file        goEditList.php
 * @brief       API to edit specific list
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Jeremiah Sebastian Samatra
 * @author      Alexander Jim Abenoja
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

	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);
	
	// POST or GET Variables
	$list_id 											= $astDB->escape($_REQUEST['list_id']);
	$list_name 											= $astDB->escape($_REQUEST['list_name']);
	$list_description 									= $astDB->escape($_REQUEST['list_description']);
	$campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);
	$active 											= $astDB->escape(strtoupper($_REQUEST['active']));
	$reset_time 										= $astDB->escape($_REQUEST['reset_time']);
	$xferconf_a_number 									= $astDB->escape($_REQUEST['xferconf_a_number']);
	$xferconf_b_number									= $astDB->escape($_REQUEST['xferconf_b_number']);
	$xferconf_c_number 									= $astDB->escape($_REQUEST['xferconf_c_number']);
	$xferconf_d_number 									= $astDB->escape($_REQUEST['xferconf_d_number']);
	$xferconf_e_number 									= $astDB->escape($_REQUEST['xferconf_e_number']);
	$agent_script_override 								= $astDB->escape($_REQUEST['agent_script_override']);
	$drop_inbound_group_override 						= $astDB->escape($_REQUEST['drop_inbound_group_override']);
	$campaign_cid_override 								= $astDB->escape($_REQUEST['campaign_cid_override']);
	$web_form_address 									= $astDB->escape($_REQUEST['web_form_address']);
	$reset_list 										= $astDB->escape(strtoupper($_REQUEST['reset_list']));
	// $values = $_REQUEST['items'];
	
	// Default values 
	$defActive 											= array("Y","N");

	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} elseif (empty($list_id) || is_null($list_id)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
		);
	} elseif (!in_array($active,$defActive) && $active != null) {
		$err_msg 										= error_handle("41006", "active");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} elseif (!in_array($reset_list,$defActive) && $reset_list != null) {
		$err_msg 										= error_handle("41006", "reset_list");
		$apiresults 									= array(
			"code" 											=> "41006", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Default value for reset_list is Y or N only.");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $list_name)) {
		$err_msg 										= error_handle("41004", "list_name");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in list_name");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬]/', $reset_time)) {
		$err_msg 										= error_handle("41004", "reset_time");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in reset_time");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_a_number)) {
		$err_msg 										= error_handle("41004", "xferconf_a_number");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in xferconf_a_number");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_b_number)) {
		$err_msg 										= error_handle("41004", "xferconf_b_number");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in xferconf_b_number");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_c_number)) {
		$err_msg 										= error_handle("41004", "xferconf_c_number");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in xferconf_c_number");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_d_number)) {
		$err_msg 										= error_handle("41004", "xferconf_d_number");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in xferconf_d_number");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $xferconf_e_number)) {
		$err_msg 										= error_handle("41004", "xferconf_e_number");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in xferconf_e_number");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $agent_script_override)) {
		$err_msg 										= error_handle("41004", "agent_script_override");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in agent_script_override");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $drop_inbound_group_override)) {
		$err_msg 										= error_handle("41004", "drop_inbound_group_override");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in drop_inbound_group_override");
	} elseif (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $campaign_cid_override)) {
		$err_msg 										= error_handle("41004", "campaign_cid_override");
		$apiresults 									= array(
			"code" 											=> "41004", 
			"result" 										=> $err_msg
		);
		//$apiresults = array("result" => "Error: Special characters found in campaign_cid_override");
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			$rsltv_check 								= $astDB
				->where("list_id", $list_id)
				//->where("campaign_id", $campaigns, "IN")
				->get("vicidial_lists");
		
			if ($astDB->count > 0) {
				foreach ($rsltv_check as $fresults) {
					$data_list_name 					= $fresults['list_name'];
					$data_list_description 				= $fresults['list_description'];
					$data_campaign_id 					= $fresults['campaign_id'];
					$data_active 						= $fresults['active'];
					$data_reset_time 					= $fresults['reset_time'];
					$data_xferconf_a_number 			= $fresults['xferconf_a_number'];
					$data_xferconf_b_number 			= $fresults['xferconf_b_number'];
					$data_xferconf_c_number 			= $fresults['xferconf_c_number'];
					$data_xferconf_d_number 			= $fresults['xferconf_d_number'];
					$data_xferconf_e_number 			= $fresults['xferconf_e_number'];
					$data_agent_script_override 		= $fresults['agent_script_override'];
					$data_drop_inbound_group_override 	= $fresults['drop_inbound_group_override'];
					$data_campaign_cid_override			= $fresults['campaign_cid_override'];
					$data_web_form_address 				= $fresults['web_form_address'];
					$data_reset_list 					= $fresults['reset_list'];
				}
				
				if (empty($list_name))
					$list_name 							= $data_list_name;
				if (empty($list_description))
					$list_description 					= $data_list_description;
				if (empty($campaign_id))
					$campaign_id 						= $data_campaign_id;
				if (empty($active))
					$active 							= $data_active;
				if (empty($reset_time))
					$reset_time 						= $data_reset_time;
				if (empty($xferconf_a_number))
					$xferconf_a_number 					= $data_xferconf_a_number;
				if (empty($xferconf_b_number))
					$xferconf_b_number 					= $data_xferconf_b_number;
				if (empty($xferconf_c_number))
					$xferconf_c_number 					= $data_xferconf_c_number;
				if (empty($xferconf_d_number))
					$xferconf_d_number 					= $data_xferconf_d_number;
				if (empty($xferconf_e_number))
					$xferconf_e_number 					= $data_xferconf_e_number;
				if (empty($agent_script_override))
					$agent_script_override 				= $data_agent_script_override;
				if (empty($drop_inbound_group_override))
					$drop_inbound_group_override 		= $data_drop_inbound_group_override;
				if (empty($campaign_cid_override))
					$campaign_cid_override 				= $data_campaign_cid_override;
				if (empty($web_form_address))
					$web_form_address 					= $data_web_form_address;
				if (empty($reset_list))
					$reset_list 						= $data_reset_list;
				
                if ($reset_list === 'Y') {
                    $astDB->where('list_id', $list_id);
                    $astDB->update('vicidial_list', array('called_since_last_reset' => 'N'));
                }
			
				$updateData 							= array(
					'list_name' 							=> $list_name,
					'list_description' 						=> $list_description,
					'campaign_id' 							=> $campaign_id,
					'active' 								=> $active,
					'reset_time' 							=> $reset_time,
					'xferconf_a_number' 					=> $xferconf_a_number,
					'xferconf_b_number' 					=> $xferconf_b_number,
					'xferconf_c_number' 					=> $xferconf_c_number,
					'xferconf_d_number' 					=> $xferconf_d_number,
					'xferconf_e_number' 					=> $xferconf_e_number,
					'agent_script_override' 				=> $agent_script_override,
					'drop_inbound_group_override' 			=> $drop_inbound_group_override,
					'campaign_cid_override' 				=> $campaign_cid_override,
					'web_form_address' 						=> $web_form_address
				);
				
				$astDB->where('list_id', $list_id);
				$resultQuery 							= $astDB->update('vicidial_lists', $updateData);
			
				if ($resultQuery == false ) {
					$err_msg 							= error_handle("10010");
					$apiresults 						= array(
						"code" 								=> "10010", 
						"result" 							=> $err_msg
					);
				} else {
					$SQLdate 							= date("Y-m-d H:i:s");
					
					//$querydate="UPDATE vicidial_lists SET list_changedate='$SQLdate' WHERE list_id='$listid_data';";
					$astDB->where('list_id', $list_id);
					$astDB->update('vicidial_lists', array('list_changedate' => $SQLdate));
					
					//$queryresetback = "UPDATE vicidial_list set called_since_last_reset='N' where list_id='$list_id';";
					$astDB->where('list_id', $list_id);
					$astDB->update('vicidial_list', array('called_since_last_reset', 'N'));
					
					$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified the List ID: $list_id", $log_group, $astDB->getLastQuery());
					
					$apiresults 						= array(
						"result" 							=> "success"
					);
				}				
			} else {
				$err_msg 								= error_handle("41004", "list_id. Doesn't exist");
				$apiresults 							= array(
					"code" 									=> "41004", 
					"result" 								=> $err_msg
				);
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}

?>
