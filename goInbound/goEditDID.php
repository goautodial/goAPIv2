<?php
/**
 * @file        goEditDID.php
 * @brief       API to edit DID Details 
 * @copyright   Copyright (c) 2018 GOautodial Inc.
 * @author      Jerico James F. Milo
 * @author      Alexander Jim Abenoja
 * @author		Demian Lizandro A. Biscocho 
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
 
    // POST or GET Variables
    $did_id 										= $astDB->escape($_REQUEST['did_id']);
	$did_pattern 									= $astDB->escape($_REQUEST['did_pattern']);
	$did_description 								= $astDB->escape($_REQUEST['did_description']);
	$did_active 									= $astDB->escape(strtoupper($_REQUEST['did_active']));
	$did_route 										= $astDB->escape(strtoupper($_REQUEST['did_route']));
	$filter_clean_cid_number 						= $astDB->escape($_REQUEST['filter_clean_cid_number']);
	$user 											= $astDB->escape($_REQUEST['user']);
	$user_unavailable_action 						= $astDB->escape(strtoupper($_REQUEST['user_unavailable_action']));
	$user_route_settings_ingroup 					= $astDB->escape($_REQUEST['user_route_settings_ingroup']);
	$group_id 										= $astDB->escape($_REQUEST['group_id']);
	$phone 											= $astDB->escape($_REQUEST['phone']);
	$server_ip 										= $astDB->escape($_REQUEST['server_ip']);
	$menu_id 										= $astDB->escape($_REQUEST['menu_id']);
	$voicemail_ext 									= $astDB->escape($_REQUEST['voicemail_ext']);
	$extension 										= $astDB->escape($_REQUEST['extension']);
	$exten_context 									= $astDB->escape($_REQUEST['exten_context']);
	$list_id     									= $astDB->escape($_REQUEST['list_id']);
	$call_handle_method 							= $astDB->escape($_REQUEST['call_handle_method']);
	$agent_search_method     						= $astDB->escape($_REQUEST['agent_search_method']);
   
    // Default values 
    $defUUA 										= array(
		'IN_GROUP',
		'EXTEN',
		'VOICEMAIL',
		'PHONE',
		'VMAIL_NO_INST'
	);
	
    $defRoute 										= array(
		'EXTEN',
		'VOICEMAIL',
		'AGENT',
		'PHONE',
		'IN_GROUP',
		'CALLMENU',
		'VMAIL_NO_INST'
	);
	
    $defRecordCall 									= array(
		'Y',
		'N',
		'Y_QUEUESTOP'
	);
	
    $defActive 										= array(
		"Y",
		"N"
	);

	if (empty($log_user) || is_null($log_user)) {
		$apiresults 								= array(
			"result" 									=> "Error: Session User Not Defined."
		);
	} elseif (empty($did_id) || is_null($did_id)) {
        $apiresults 								= array(
			"result" 									=> "Error: Set a value for DID ID."
		);
	} elseif (!empty($did_pattern) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_pattern)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in did_pattern"
		);
    } elseif (!is_null($did_description) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_description)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in did_description"
		);
    } elseif (!in_array($user_unavailable_action,$defUUA) && !is_null($user_unavailable_action)) {
		$apiresults 								= array(
			"result" 									=> "Error: Default value for user_unavailable_action is IN_GROUP','EXTEN','VOICEMAIL','PHONE', or 'VMAIL_NO_INST'."
		);
	} elseif (!in_array($active,$defActive) && !is_null($active)) {
		$apiresults 								= array(
			"result" 									=> "Error: Default value for active is Y or N only."
		);
	} elseif (!in_array($did_route,$defRoute) && !is_null($did_route)) {
		$apiresults 								= array(
			"result" 									=> "Error: Default value for did_route are EXTEN, VOICEMAIL, AGENT, PHONE, IN_GROUP, or CALLMENU  only."
		);
	} elseif (!in_array($record_call,$defRecordCall) && !is_null($record_call)) {
		$apiresults 								= array(
			"result" 									=> "Error: Default value for Record Call are Y, N and Y_QUEUESTOP  only."
		);
	} elseif (!is_null($group_id) && preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $group_id)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in group_id"
		);
    } elseif (!is_null($phone) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in phone"
		);
    } elseif (!is_null($server_ip) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in server_ip"
		);
    } elseif (!is_null($menu_id) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in menu_id"
		);
    } elseif (!is_null($voicemail_ext) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_ext)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in voicemail_ext"
		);
    } elseif (!is_null($extension) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in extension"
		);
    } elseif (!is_null($exten_context) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $exten_context)) {
        $apiresults 								= array(
			"result" 									=> "Error: Special characters found in exten_context"
		);
    } else {
		if (checkIfTenant($log_group, $goDB)) {
            $astDB->where("user_group", $log_group);
            $astDB->orWhere("user_group", "---ALL---");
		}
		
        $astDB->where("did_id", $did_id);
        $rsltv_check 								= $astDB->get("vicidial_inbound_dids");
		
        if ($astDB->count > 0) {
			foreach ($rsltv_check as $fresults) {
				$datadid_pattern 					= $fresults['did_pattern'];
				$datadid_description				= $fresults['did_description'];
				$datadid_active 					= $fresults['did_active'];
				$datadid_route 						= $fresults['did_route'];
				$datafilter_clean_cid_number		= $fresults['filter_clean_cid_number'];
				$datauser							= $fresults['user'];
				$datauser_unavailable_action 		= $fresults['user_unavailable_action'];
				$datauser_route_settings_ingroup	= $fresults['user_route_settings_ingroup'];
				$datagroup_id 						= $fresults['group_id'];
				$dataphone	 						= $fresults['phone'];
				$dataserver_ip 						= $fresults['server_ip'];
				$datamenu_id 						= $fresults['menu_id'];
				$datavoicemail_ext	 				= $fresults['voicemail_ext'];
				$dataextension	 					= $fresults['extension'];
				$dataexten_context	 				= $fresults['exten_context'];
                $datalist_id                        = $fresults['list_id'];
				$datacall_handle_method	 			= $fresults['call_handle_method'];
                $dataagent_search_method            = $fresults['agent_search_method'];
			}

			if (empty($did_pattern)) { 
				$did_pattern 						= $datadid_pattern;
			}
			
			if (empty($did_description)) { 
				$did_description 					= $datadid_description;
			}
			
			if (empty($did_active)) {
				$did_active 						= $datadid_active;
			}
			
			if (empty($did_route)) {
				$did_route 							= $datadid_route;
			}        

			if (empty($filter_clean_cid_number)) {
				$filter_clean_cid_number 			= $datafilter_clean_cid_number;
			}
			
			if (empty($user)) { 
				$user 								= $datauser;
			}
			
			if (empty($user_unavailable_action)) { 
				$user_unavailable_action 			= $datauser_unavailable_action;
			}
			
			if (empty($user_route_settings_ingroup)) {
				$user_route_settings_ingroup 		= $datauser_route_settings_ingroup;
			}
			
			if (empty($group_id)) {
				$group_id 							= $datagroup_id;
			} 		

			if (empty($phone)) {
				$phone 								= $dataphone;
			}
			
			if (empty($server_ip)) {
				$server_ip 							= $dataserver_ip;
			}        

			if (empty($menu_id)) { 
				$menu_id							= $datamenu_id;
			}
			
			if (empty($voicemail_ext)) { 
				$voicemail_ext			 			= $datavoicemail_ext;
			}
			
			if (empty($extension)) {
				$extension 							= $dataextension;
			}
			
			if (empty($exten_context)) {
				$exten_context 						= $dataexten_context;
			}

			if (empty($list_id)) { 
				$list_id 						    = $datalist_id;
			}
			
			if (empty($call_handle_method)) {
				$call_handle_method 				= $datacall_handle_method;
			}

			if (empty($agent_search_method)) { 
				$agent_search_method 				= $dataagent_search_method;
			}
			
            $astDB->where("did_pattern", $did_pattern);
            $astDB->where("did_id", $did_id, "!=");
            $astDB->getOne("vicidial_inbound_dids", "did_pattern");

            if ($astDB->count < 1) {				
                $data 								= array(
					"did_pattern"						=> $did_pattern,
					"did_description" 					=> $did_description,
					"did_active" 						=> $did_active,
					"did_route" 						=> $did_route,
					"filter_clean_cid_number"			=> $filter_clean_cid_number,				
					'user' 								=> $user,
					'user_unavailable_action' 			=> $user_unavailable_action,
					'user_route_settings_ingroup' 		=> $user_route_settings_ingroup,
					'group_id' 							=> $group_id,
					'phone' 							=> $phone,
					'server_ip' 						=> $server_ip,
					'menu_id' 							=> $menu_id,
					'voicemail_ext' 					=> $voicemail_ext,
					'extension' 						=> $extension,
					'exten_context' 					=> $exten_context,
                    'list_id'                           => $list_id,
                    'call_handle_method'                => $call_handle_method,
                    'agent_search_method'               => $agent_search_method
				);
				
				$astDB->where("did_id", $did_id);
				$astDB->update("vicidial_inbound_dids", $data);
									
				$log_id 							= log_action($goDB, 'MODIFY', $log_user, $log_ip, "Modified DID ID $did_id", $log_group, $astDB->getLastQuery());
             
				$apiresults 						= array(
					"result" 							=> "success"
				);             
            } else {
                $apiresults 						= array(
					"result" 							=> "Duplicate did_pattern, It must be unique!\n"
				);
            }
        } else {
			$apiresults 							= array(
				"result" 								=> "Error: DID doesn't exist."
			);        
        }
    }
    
?>

