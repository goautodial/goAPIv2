<?php
 /**
 * @file        goAddDID.php
 * @brief       API to add new DID
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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

    include_once("goAPI.php");
	// POST or GET Variables
        $did_pattern = $_REQUEST['did_pattern'];
        $did_description = $_REQUEST['did_description'];
        $active = strtoupper($_REQUEST['did_active']);
        $did_route = strtoupper($_REQUEST['did_route']);
        $user_group = $_REQUEST['user_group'];
        $goUser = $_REQUEST['goUser'];
        $ip_address = $_REQUEST['hostname'];
        $record_call = "N"; //$_REQUEST['record_call'];

	// Agent
		$user = $_REQUEST['user'];
        $user_unavailable_action = strtoupper($_REQUEST['user_unavailable_action']);

	// Ingroup
		$group_id = $_REQUEST['group_id'];

	// Phone
		$phone = $_REQUEST['phone'];
		$server_ip = $_REQUEST['server_ip'];

	// IVR
		$menu_id = $_REQUEST['menu_id'];

	// Voicemail
		$voicemail_ext = $_REQUEST['voicemail_ext'];

	// Custon Extension
		$extension = $_REQUEST['extension'];
		$exten_context = $_REQUEST['exten_context'];

    // Default values 
    $defUUA = array('IN_GROUP','EXTEN','VOICEMAIL','PHONE','VMAIL_NO_INST');
    $defRoute = array('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU','VMAIL_NO_INST');
    $defRecordCall = array('Y','N','Y_QUEUESTOP');
    $defActive = array("Y","N");

    if(empty($did_pattern)) {
        $apiresults = array("result" => "Error: Set a value for DID pattern.");
	}else if($did_description == null) {
		$apiresults = array("result" => "Error: Set a value for did_description.");
	}else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_pattern)){
		$apiresults = array("result" => "Error: Special characters found in did_pattern");
	}else if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_description)){
		$apiresults = array("result" => "Error: Special characters found in did_description");
	}else if(!in_array($user_unavailable_action,$defUUA) && $user_unavailable_action != null) {
		$apiresults = array("result" => "Error: Default value for user_unavailable_action is IN_GROUP','EXTEN','VOICEMAIL','PHONE', or 'VMAIL_NO_INST'.");
	}else if(!in_array($active,$defActive) && $active != null) {
		$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	}else if(!in_array($did_route,$defRoute) && $did_route != null) {
		$apiresults = array("result" => "Error: Default value for did_route are EXTEN, VOICEMAIL, AGENT, PHONE, IN_GROUP, or CALLMENU  only.");
	}else{
		
		$groupId = go_get_groupid($session_user, $astDB);
		$log_user = $session_user;
		$log_group = $groupId;

		// if DID ROUTE == AGENT
		if($did_route === "AGENT" && empty($user)){
			$apiresults = array("result" => "Error: Set Value for user"); 
		}else if($did_route === "AGENT" && empty($user_unavailable_action)){
			$apiresults = array("result" => "Error: Set Value for user_unavailable_action");
		}

		// if DID ROUTE == IN GROUP
		if($did_route === "IN_GROUP" && empty($group_id)){
			$apiresults = array("result" => "Error: Set Value for group_id");
		}

		// if DID ROUTE == PHONE
		if($did_route == "PHONE" && empty($phone)){
			$apiresults = array("result" => "Error: Set Value for phone");
		}else if($did_route == "PHONE" && empty($server_ip)){
			$apiresults = array("result" => "Error: Set Value for server_ip");
		}else if(!empty($phone) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone)){
            $apiresults = array("result" => "Error: Special characters found in phone");
		}else if(!empty($server_ip) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
            $apiresults = array("result" => "Error: Special characters found in server_ip");
		}

		// if DID ROUTE == CALLMENU
		if($did_route == "CALLMENU" && empty($menu_id)){
			$apiresults = array("result" => "Error: Set Value for menu_id");
		}else if(!empty($menu_id) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)){
            $apiresults = array("result" => "Error: Special characters found in menu_id");
		}

		// if DID ROUTE == VOICEMAIL
		if($did_route == "VOICEMAIL" && empty($voicemail_ext)){
			$apiresults = array("result" => "Error: Set Value for voicemail_ext");
		}else if($voicemail_ext != null && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_ext)){
            $apiresults = array("result" => "Error: Special characters found in voicemail_ext");
		}

		// IF DID ROUTE == EXTEN
		if($did_route == "EXTEN" && empty($extension)){
			$apiresults = array("result" => "Error: Set Value for extension");
		}else if($did_route == "EXTEN" && empty($exten_context)){
			$apiresults = array("result" => "Error: Set Value for exten_context");
		}else if(!empty($extension) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)){
            $apiresults = array("result" => "Error: Special characters found in extension");
		}else if(!empty($exten_context) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $exten_context)){
            $apiresults = array("result" => "Error: Special characters found in exten_context");
		}
			
		if(!empty($group_id) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_id)){
            $apiresults = array("result" => "Error: Special characters found in group_id");
		}
			
		if (!checkIfTenant($groupId, $goDB)) {
			$astDB->where("user_group", $user_group);
			//$ul = "WHERE user_group='$user_group'";
		} else {
			$astDB->where("user_group", $user_group);
			$astDB->where("user_group", $groupId);
			//$ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
		}
		
		$astDB->getOne("vicidial_user_groups");
		//$queryUG = "SELECT user_group,group_name,forced_timeclock_login FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
		$countUG = $astDB->count;
		
		if($countUG > 0){	
			$astDB->where("did_pattern", $did_pattern);
			$astDB->getOne("vicidial_inbound_dids");
			//$stmtdf = "SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern';";
			$rowdf = $astDB->count;
				
			if ($rowdf > 0) {
				$apiresults = array("result" => "DID NOT ADDED - DID already exist.\n");
			} else {
				if($did_route == "AGENT"){
					$col = Array(
								"did_pattern" => $did_pattern,
								"did_description" => $did_description,
								"did_route" => $did_route,
								"record_call" => $record_call,
								"user_group" => $user_group,
								"user" => $user,
								"user_unavailable_action" => $user_unavailable_action,
								"user_route_settings_ingroup" => $user_route_settings_ingroup
							);
					$queryAgent = $astDB->insert("vicidial_inbound_dids", $col);

					$queryAgent = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, user, user_unavailable_action, user_route_settings_ingroup) values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$user', '$user_unavailable_action', '$user_route_settings_ingroup');";
					//$queryAgentResult = mysqli_query($link, $queryAgent);
				
				//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, user, user_unavailable_action, user_route_settings_ingroup) values('0000', 'Test', 'AGENT', 'N', 'ADMIN', '', 'VOICEMAIL', 'AGENTDIRECT');
					$log_query = $queryAgent;
				}
				
				if($did_route == "PHONE"){
					$col = Array(
								"did_pattern" => $did_pattern,
								"did_description" => $did_description,
								"did_route" => $did_route,
								"record_call" => $record_call,
								"user_group" => $user_group,
								"phone" => $phone,
								"server_ip" => $server_ip
							);
					$queryPhone = $astDB->insert("vicidial_inbound_dids", $col);

					$queryPhone = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, phone, server_ip) values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$phone', '$server_ip');";
					//$queryPhoneResult = mysqli_query($link, $queryPhone);
				//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, phone, server_ip) values('00000', 'Sample', 'PHONE', 'N', 'ADMIN', '', '');
				
					$log_query = $queryPhone;
				}
				
				if($did_route == "CALLMENU"){
					$col = Array(
								"did_pattern" => $did_pattern,
								"did_description" => $did_description,
								"did_route" => $did_route,
								"record_call" => $record_call,
								"user_group" => $user_group,
								"user" => $user,
								"menu_id" => $menu_id
							);
					$queryCallmenu = $astDB->insert("vicidial_inbound_dids", $col);

					$queryCallmenu = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, menu_id) values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$menu_id');";
					//$queryCMResult = mysqli_query($link, $queryCallmenu);
				//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, menu_id) values('000000', 'test call menu', 'CALLMENU', 'N', 'ADMIN', '0000');
				
					$log_query = $queryCallmenu;
				}
				
				
				if($did_route == "VOICEMAIL"){
					$col = Array(
								"did_pattern" => $did_pattern,
								"did_description" => $did_description,
								"did_route" => $did_route,
								"record_call" => $record_call,
								"user_group" => $user_group,
								"voicemail_ext" => $voicemail_ext
							);
					$queryVM = $astDB->insert("vicidial_inbound_dids", $col);
					
					$queryVM = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, voicemail_ext) values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$voicemail_ext');";
					//$queryVMResult = mysqli_query($link, $queryVM);
				//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, voicemail_ext) values('0000000', 'vm', 'VOICEMAIL', 'N', 'ADMIN', '0000000');
				
					$log_query = $queryVM;
				}
				
				if($did_route == "EXTEN"){
					$col = Array(
								"did_pattern" => $did_pattern,
								"did_description" => $did_description,
								"did_route" => $did_route,
								"record_call" => $record_call,
								"user_group" => $user_group,
								"user" => $user,
								"extension" => $extension,
								"exten_context" => $exten_context
							);
					$queryExten = $astDB->insert("vicidial_inbound_dids", $col);
					
					$queryExten = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, extension, exten_context) values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group', '$extension', '$exten_context');";
					//$queryExtenResult = mysqli_query($link, $queryExten);
				//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, extension, exten_context) values('000000000', 'ce', 'EXTEN', 'N', 'ADMIN', '9998811112', 'default');
				
					$log_query = $queryExten;
				}
				
				if($did_route == "IN_GROUP"){
					$col = Array(
								"did_pattern" => $did_pattern,
								"did_description" => $did_description,
								"did_route" => $did_route,
								"record_call" => $record_call,
								"user_group" => $user_group,
                                "group_id" => $group_id,
								"user" => $user,
								"menu_id" => $menu_id
							);
					$queryIG = $astDB->insert("vicidial_inbound_dids", $col);
					
					$queryIG = "INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, group_id) values('$did_pattern', '$did_description', '$did_route', '$record_call', '$user_group','$group_id');";
					//$queryIGResult = mysqli_query($link, $queryIG);
				//INSERT INTO vicidial_inbound_dids (did_pattern, did_description, did_route, record_call, user_group, extension, exten_context) values('000000000', 'ce', 'EXTEN', 'N', 'ADMIN', '9998811112', 'default');
				
					$log_query = $queryIG;
				}
				
				$astDB->where("did_pattern", $did_pattern);
				$astDB->getOne("vicidial_inbound_dids");
				//$queryCheck = "SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern';";
				//$result = mysqli_query($link, $queryCheck);
						
				if ($astDB->count > 0) {
					 $log_id = log_action($goDB, 'ADD', $log_user, $ip_address, "Added a New DID $did_pattern", $log_group, $log_query);
					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "DID NOT ADDED, Check your details");
				}
			}
		} else {
				$apiresults = array("result" => "Error: Invalid User Group");
		}
	}//end else	
?>
