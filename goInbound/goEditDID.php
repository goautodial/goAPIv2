<?php
/**
 * @file        goEditDID.php
 * @brief       API to edit DID Details 
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James F. Milo  <jerico@goautodial.com>
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
    
    include_once ("../goFunctions.php");
 
    // POST or GET Variables
        $did_pattern = $_REQUEST['did_pattern'];
        $did_description = $_REQUEST['did_description'];
        $active = strtoupper($_REQUEST['did_active']);
        $did_route = strtoupper($_REQUEST['did_route']);
        $ip_address = $_REQUEST['hostname'];
		$filter_clean_cid_number = $astDB->escape($_REQUEST['filter_clean_cid_number']);
		
		$log_user = $goUser;
		$log_group = $astDB->escape($_REQUEST['log_group']);

        $user = $_REQUEST['user'];
        $user_unavailable_action = strtoupper($_REQUEST['user_unavailable_action']);
		$user_route_settings_ingroup = $_REQUEST['user_route_settings_ingroup'];
        $group_id = $_REQUEST['group_id'];
        $phone = $_REQUEST['phone'];
        $server_ip = $_REQUEST['server_ip'];
        $menu_id = $_REQUEST['menu_id'];
        $voicemail_ext = $_REQUEST['voicemail_ext'];
        $extension = $_REQUEST['extension'];
        $exten_context = $_REQUEST['exten_context'];
        $did_id = $_REQUEST['did_id'];
   
    // Default values 
    $defUUA = array('IN_GROUP','EXTEN','VOICEMAIL','PHONE','VMAIL_NO_INST');
    $defRoute = array('EXTEN','VOICEMAIL','AGENT','PHONE','IN_GROUP','CALLMENU','VMAIL_NO_INST');
    $defRecordCall = array('Y','N','Y_QUEUESTOP');
    $defActive = array("Y","N");

    if(empty($did_id)) {
        $apiresults = array("result" => "Error: Set a value for DID ID.");
    } elseif(!empty($did_pattern) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_pattern)){
        $apiresults = array("result" => "Error: Special characters found in did_pattern");
    } elseif(!is_null($did_description) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $did_description)){
        $apiresults = array("result" => "Error: Special characters found in did_description");
    } elseif(!in_array($user_unavailable_action,$defUUA) && !is_null($user_unavailable_action)) {
		$apiresults = array("result" => "Error: Default value for user_unavailable_action is IN_GROUP','EXTEN','VOICEMAIL','PHONE', or 'VMAIL_NO_INST'.");
	} elseif(!in_array($active,$defActive) && !is_null($active)) {
		$apiresults = array("result" => "Error: Default value for active is Y or N only.");
	} elseif(!in_array($did_route,$defRoute) && !is_null($did_route)) {
		$apiresults = array("result" => "Error: Default value for did_route are EXTEN, VOICEMAIL, AGENT, PHONE, IN_GROUP, or CALLMENU  only.");
	} elseif(!in_array($record_call,$defRecordCall) && !is_null($record_call)) {
		$apiresults = array("result" => "Error: Default value for Record Call are Y, N and Y_QUEUESTOP  only.");
	} elseif(!is_null($group_id) && preg_match('/[\'^£$%&*()}{@#~?><>,|=+¬]/', $group_id)){
        $apiresults = array("result" => "Error: Special characters found in group_id");
    } elseif(!is_null($phone) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $phone)){
        $apiresults = array("result" => "Error: Special characters found in phone");
    } elseif(!is_null($server_ip) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $server_ip)){
        $apiresults = array("result" => "Error: Special characters found in server_ip");
    } elseif(!is_null($menu_id) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $menu_id)){
        $apiresults = array("result" => "Error: Special characters found in menu_id");
    } elseif(!is_null($voicemail_ext) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $voicemail_ext)){
        $apiresults = array("result" => "Error: Special characters found in voicemail_ext");
    } elseif(!is_null($extension) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $extension)){
        $apiresults = array("result" => "Error: Special characters found in extension");
    } elseif(!is_null($exten_context) && preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $exten_context)){
        $apiresults = array("result" => "Error: Special characters found in exten_context");
    } else {

        $astDB->where("did_id", $did_id);
        $fresults = $astDB->getOne("vicidial_inbound_dids");
        //$stmtdf="SELECT did_id, did_pattern from vicidial_inbound_dids where did_id='$did_id';";

        if ($astDB->count < 1) {
            $apiresults = array("result" => "DID not found.\n");
        } else {
            $dataID = $fresults['did_id'];
            $dataPattern = $fresults['did_pattern'];

            $astDB->where("did_pattern", $did_pattern);
            $astDB->where("did_id", $dataID);
            $rowr = $astDB->getValue("vicidial_inbound_dids", "count(*)");
            //$queryCheck="SELECT did_pattern from vicidial_inbound_dids where did_pattern='$did_pattern' AND did_id !='$dataID';";

            if ($rowr > 0) {
                $apiresults = array("result" => "Duplicate did_pattern, It must be unique!\n");
            }else{
                $agent_sql = "";
                $group_id_sql = "";
                $phone_sql = "";
                $menu_id_sql = "";
                $voicemail_ext_sql = "";
                $extension_sql = "";

                $data = Array(
                            "did_pattern" => $did_pattern,
                            "did_description" => $did_description,
                            "did_active" => $active,
                            "did_route" => $did_route,
                            "filter_clean_cid_number" => $filter_clean_cid_number
                        );
                // Agent
                    if(!is_null($user)){
                        array_push($data, 
                                "user" => $user,
                                "user_unavailable_action" => $user_unavailable_action,
                                "user_route_settings_ingroup" => $user_route_settings_ingroup    
                            );
                       //$agent_sql = ", user = '$user', user_unavailable_action = '$user_unavailable_action', user_route_settings_ingroup = '$user_route_settings_ingroup' ";
                    }

                // Ingroup
                    if(!is_null($group_id)){
                        array_push($data, 
                                "group_id" => $group_id
                            );
                        //$group_id_sql = ", group_id = '$group_id' ";    
                    }

                // Phone
                    if(!is_null($phone)){
                        array_push($data, 
                                "phone" => $phone,
                                "server_ip" => $server_ip
                            );
                        //$phone_sql = ", phone = '$phone', server_ip = '$server_ip' ";
                    }

                // IVR
                    if(!is_null($menu_id)){
                        array_push($data, 
                                "menu_id" => $menu_id
                            );
                        //$menu_id_sql = ", menu_id = '$menu_id' ";
                    }

                // Voicemail
                    if(!is_null($voicemail_ext)){
                        array_push($data, 
                                "voicemail_ext" => $voicemail_ext
                            );
                        //$voicemail_ext_sql = ", voicemail_ext = '$voicemail_ext' ";
                    }

                // Custon Extension
                    if(!is_null($extension)){
                        array_push($data, 
                                "extension" => $extension,
                                "exten_context" => $exten_context
                            );
                        //$extension_sql = ", extension = '$extension', exten_context = '$exten_context' ";
                    }

				if(!empty($dataID)){
    			    if(empty($did_pattern)){ $did_pattern = $dataPattern;}
    			    if(empty($did_description)) { $did_description = $datadid_description;} else {$did_description = $did_description;}
    			    if(empty($did_active)) {$did_active = $datadid_active;} else {$did_active = $did_active;}
    			    if(empty($did_route)) {$did_route = $datadid_route;} else { $did_route = $did_route;}
                    
                    $astDB->where("did_id", $did_id);
                    $updateQuery = $astDB->update("vicidial_inbound_dids", $data);
                    
                    $query = "UPDATE vicidial_inbound_dids
    				SET did_pattern = '$did_pattern', did_description = '$did_description', did_active = '$active',
    				did_route = '$did_route', filter_clean_cid_number = '$filter_clean_cid_number' $agent_sql $group_id_sql $phone_sql
    				,user_route_settings_ingroup='$user_route_settings_ingroup'
    				$menu_id_sql $voicemail_ext_sql $extension_sql 
    				WHERE did_id='$did_id';";
                    
    				$log_id = log_action($goDB, 'MODIFY', $log_user, $ip_address, "Modified DID ID $did_id", $log_group, $query);

                    if(){
                        $apiresults = array("result" => "success");
                    }else {
                        $apiresults = array("result" => "Error: Failed to modified the Group ID");
                    }
                }
            }
        }
    }
?>

