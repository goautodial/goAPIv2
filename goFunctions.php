<?php
/**
 * @file        goFunctions.php
 * @brief       General Functions
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Flores Milo  <jericojames@goautodial.com>
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

	if (isset($_GET["session_user"])) { $session_user = $_GET["session_user"];
    } elseif (isset($_POST["session_user"])) { $session_user = $_POST["session_user"]; }
	//else $apiresults = array("result" => "Error: Session User Not Defined");

    // Conversion with precision from GAD v3 app
    function go_sec_convert($sec,$precision){
		$sec = round($sec,0);
	
		if ($sec < 1){
			return "0:00";
		}else{
			if ($sec < 3600) {$precision='M';}
	
			if ($precision == 'H'){
				$Fhours_H =	($sec / 3600);
				$Fhours_H_int = floor($Fhours_H);
				$Fhours_H_int = intval("$Fhours_H_int");
				$Fhours_M = ($Fhours_H - $Fhours_H_int);
				$Fhours_M = ($Fhours_M * 60);
				$Fhours_M_int = floor($Fhours_M);
				$Fhours_M_int = intval("$Fhours_M_int");
				$Fhours_S = ($Fhours_M - $Fhours_M_int);
				$Fhours_S = ($Fhours_S * 60);
				$Fhours_S = round($Fhours_S, 0);
				if ($Fhours_S < 10) {$Fhours_S = "0$Fhours_S";}
				if ($Fhours_M_int < 10) {$Fhours_M_int = "0$Fhours_M_int";}
				$Ftime = "$Fhours_H_int:$Fhours_M_int:$Fhours_S";
			}
			if ($precision == 'M'){
				$Fminutes_M = ($sec / 60);
				$Fminutes_M_int = floor($Fminutes_M);
				$Fminutes_M_int = intval("$Fminutes_M_int");
				$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
				$Fminutes_S = ($Fminutes_S * 60);
				$Fminutes_S = round($Fminutes_S, 0);
				if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
				$Ftime = "$Fminutes_M_int:$Fminutes_S";
			}
			if ($precision == 'S'){
				$Ftime = $sec;
			}
			return "$Ftime";
		}
	}

    // CONVERT SECONDS TO DAY, HOUR:MINUTE,SEC //
    function convert($n) {
        /*$days=floor($secs/86400);
        $hours=floor($secs/3600);
        $minutes=floor($secs/60);
        if($secs>=86400){$secs=$secs%86400;$r=$days.'d ';}
        if($secs>=3600){$secs=$secs%3600;$r.=sprintf('%02d', $hours).':';}if($hours <= 0)$r.='00:';
        if($secs>=60){$secs=$secs%60;$r.=sprintf('%02d', $minutes).':';}if($minutes <= 0)$r.='00:';
        $r.=sprintf('%02d', $secs);
        return $r;*/
	$day = floor($n / (24 * 3600)); 
  
    	$n = ($n % (24 * 3600)); 
    	$hour = $n / 3600; 
  
    	$n %= 3600; 
    	$minutes = $n / 60 ; 
  
    	$n %= 60; 
    	$seconds = $n;

	$r = '';
	if($day > 0 )$r .= $day.'d ';
	if($hour > 0)$r .= sprintf('%02d', $hour).':';else $r.= '00:';
	if($minutes > 0)$r .= sprintf('%02d', $minutes).':';else $r.='00:';
	if($seconds > 0)$r .= sprintf('%02d', $seconds);else $r.='00';
	return $r;
    }
	
    ##### get usergroup #########
    function go_get_groupid($goUser, $dbase){
        //$query_userv = "SELECT user_group FROM vicidial_users WHERE user='$goUser'";
		$dbase->where('user', $goUser);
        $rsltv = $dbase->getOne('vicidial_users', 'user_group');
		$check_resultv = $dbase->getRowCount();

        if ($check_resultv > 0) {
            $goUser_group = $rsltv["user_group"];
            return $goUser_group;
        } else {
			return false;
		}
        
    }
    
    ##### checkiftenant ######
    function checkIfTenant($groupId, $dbase){
        //$query_tenant = "SELECT * FROM go_multi_tenant WHERE tenant_id='$groupId'";
		$dbase->where('tenant_id', $groupId);
        $rslt_tenant = $dbase->get('go_multi_tenant');
		$check_result_tenant = $dbase->getRowCount();
		
        if ($check_result_tenant > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    
    function go_getall_allowed_users($groupId) {
        include("goDBasterisk.php");
		$allowed_users = "";
		
        if ($groupId=='ADMIN' || $groupId=='admin') {
                   $query = "select user as userg from vicidial_users";
                   //$rsltv = mysqli_query($link,$query);

		   $result = $astDB->rawQuery($query);
        } else {
                   $query = "select user as userg from vicidial_users where user_group='$groupId'";
                   //$rsltv = mysqli_query($link,$query);
	
		   $result = $astDB->rawQuery($query);
        }
        
        /*while($info = mysqli_fetch_array( $rsltv )) {
            $users[] = $info['userg'];
        }*/

        if($astDB->count > 0){
                foreach($result as $fresults){
                        $users[] = $fresults['userg'];
                }
		
		if(!empty($users)){
			$imploded = implode("','", $users);
			$allowed_users = "'".$imploded."'";
		}
	}
        return $allowed_users;
    }
    
    
    function go_total_agents_callv($groupId) {
        include("goDBasterisk.php");
        if (!checkIfTenant($groupId)) { 
           $query = "select count(*) as qresult from vicidial_users";
           $rsltv = mysqli_query($link,$query);
        } else {
           $query = "select count(*) as qresult from vicidial_users where user_group='$groupId'";
           $rsltv = mysqli_query($link,$query);
        }
                
        $fresults = mysqli_fetch_assoc($rsltv);
        $fresults = $fresults['qresult'];
                
        if ($fresults == NULL) {
            $fresults = 0;
        }
        
        return $fresults;
    }
	
    function go_getall_allowed_campaigns($groupId, $dbase)
    {
		/*$groupId = $this->go_get_groupid();
			if (!is_null($tenant)) {
					$groupId = $tenant;
			}*/
		$query_date =  date('Y-m-d');
		//$query = "select trim(allowed_campaigns) as qresult from vicidial_user_groups where user_group='$groupId'";
		$dbase->where('user_group', $groupId);
		$resultsu = $dbase->getOne('vicidial_user_groups', 'TRIM(allowed_campaigns) AS qresult');
		
		if($dbase->getRowCount() > 0){
			$fresults = $resultsu['qresult'];
			$allowedCampaigns = explode(",",str_replace("",',',rtrim(ltrim(str_replace('-','',$fresults)))));
			
			$allAllowedCampaigns = implode("",$allowedCampaigns);
			//$allAllowedCampaigns = "'".str_replace(" ", "','",$allAllowedCampaigns)."'";
		}else{
			$allAllowedCampaigns = '';
		}
		
		return $allAllowedCampaigns;
    }

    function allowed_campaigns($log_group, $goDB, $astDB, $type=null) {
		if (checkIfTenant($log_group, $goDB)) {
			$astDB->where("user_group", $log_group);
			//$astDB->orWhere('user_group', "---ALL---");
		} else {
			if(strtoupper($log_group) !== "ADMIN"){
				$astDB->where('user_group', $log_group);
				//$astDB->orWhere('user_group', "---ALL---");
			}
		}    

		$cols 									= array(
			"campaign_id",
			"campaign_name",
			"dial_method",
			"active"
		);
		
		$astDB->orderBy('campaign_id', 'desc');
		$result 								= $astDB->get('vicidial_campaigns', NULL, $cols);
		$campaigns								= array();
		
		if ($astDB->count > 0) {
			foreach($result as $fresults){
				$dataCampID[] 					= $fresults['campaign_id'];
				$dataCampName[] 				= $fresults['campaign_name'];// .$fresults['dial_method'].$fresults['active'];
				$dataDialMethod[] 				= $fresults['dial_method'];
				$dataActive[] 					= $fresults['active'];
			}
			
		}
		
        switch ($type) {
            case "all":		
				$campaigns	 					= array(
					"campaign_id" 					=> $dataCampID, 
					"campaign_name" 				=> $dataCampName, 
					"dial_method" 					=> $dataDialMethod, 
					"active" 						=> $dataActive
				);
				
                break;
                
            case "status":		
				$campaigns	 					= array(
					"campaign_id" 					=> $dataCampID, 
					"active" 						=> $dataActive
				);
				
                break;                
            
            default: 
				$campaigns	 					= $dataCampID;
		}
		
		return $campaigns;
    }

	function go_getall_closer_campaigns($campaign_id, $astDB){
		if(strtoupper($campaign_id) === "ALL"){
			//ALL CAMPAIGNS
                        $SELECTQuery = $astDB->get("vicidial_campaigns", NULL, "campaign_id");

                        foreach($SELECTQuery as $camp_val){
                                $array_camp[] = $camp_val["campaign_id"];
                        }
                        $imploded_camp = "'".implode("','", $array_camp)."'";

			// Inbound Sales //
                        $inbound_query = "
                                SELECT closer_campaigns FROM vicidial_campaigns
                                WHERE campaign_id IN ($imploded_camp)
                                ORDER BY campaign_id
                        ";
                        $row1 = $astDB->rawQuery($inbound_query);

                        foreach($row1 as $data){
                                if(!empty($data['closer_campaigns'])){
                                $trimmed_cc = rtrim($data['closer_campaigns'], " - ");
                                $closer_camp[] = $trimmed_cc;
                                }//not null
                        }

                        //iterate thru array closer_camp to separate merged closer campaignsi
                        $imploded = implode(" ", $closer_camp);
                        $exploded = explode(" ", $imploded);
			$allCloserCampaigns = "'".implode("','",$exploded)."'";
		}else{
			$resultsu = $astDB
				->where("campaign_id", $campaign_id)
				->orderBy("campaign_id")
				->getValue("vicidial_campaigns", "trim(closer_campaigns)");
		
			if (count($resultsu) > 0) {
			//$fresults = $resultsu['qresult'];
				$closerCampaigns = explode(",",str_replace(" ",',',rtrim(ltrim(str_replace('-','',$resultsu)))));
				$allCloserCampaigns = "'".implode("','",$closerCampaigns)."'";
			} else {
				$allCloserCampaigns = '';
			}
		}// if campaign_id is equal to ALL
		return $allCloserCampaigns;
	}
    
	function go_get_calltimes($campaign_id, $astDB){		
		/*$query = "SELECT local_call_time AS call_time FROM vicidial_campaigns WHERE campaign_id='$campaign_id'";
		$query_result = mysqli_query($link, $query);
		$fetch_result = mysqli_fetch_array($query_result);
		$call_time = $fetch_result['call_time'];*/
		
		$call_time = $astDB
			->where("campaign_id", $campaign_id)
			->getValue("vicidial_campaigns", "local_call_time");		

		if (strlen($call_time) > 0){
			/*$query = "SELECT ct_default_start, ct_default_stop FROM vicidial_call_times WHERE call_time_id='$call_time'";
			$result_query = mysqli_query($link, $query);
			$fetch_result = mysqli_fetch_array($result_query);
			$result = $fetch_result['ct_default_start']. "-" . $fetch_result['ct_default_stop'];*/
			
			$fetch_result = $astDB
				->where("call_time_id", $call_time)
				->getOne("vicidial_call_times", "ct_default_start, ct_default_stop");
				
			$result = $fetch_result['ct_default_start']. "-" . $fetch_result['ct_default_stop'];
				
		}

		return $result;
	}
	
    #### Jerico James Flores Milo ####
    #### My APIxmlOuput           #### 
    function apiXMLOutput($val, $lastk = "") {
    	foreach ($val as $k => $v) {
    
    		if (is_array( $v )) {
    			if (is_numeric( $k )) {
    				echo "<{$lastk}>\n";
    			}
    			else {
    				if (( !is_numeric( key( $v ) ) && count( $v ) )) {
    					echo "<{$k}>\n";
    				}
    			}
    
    			apiXMLOutput( $v, $k );
    
    			if (is_numeric( $k )) {
    				echo "</{$lastk}>\n";
    				continue;
    			}
    
    
    			if (( !is_numeric( key( $v ) ) && count( $v ) )) {
    				echo "</{$k}>\n";
    				continue;
    			}
    
    			continue;
    		}
    
    		$v = html_entity_decode( $v );
    
    		if (( strpos( $v, "<![CDATA[" ) === false && htmlspecialchars( $v ) != $v )) {
    			$v = ( "<![CDATA[" . $v . "]" ) . "]>";
    		}
    
    		echo "<{$k}>{$v}</{$k}>\n";
    	}
    
    }

    function get_settings($type=null, $dbase, $param1=null, $param2=null) {
        switch ($type) {
            case "user":
                //User Settings
                $dbase->where('user', $param1);
                $return = $dbase->getOne('vicidial_users' , 'user_id,user,pass,pass_hash,phone_login,phone_pass,full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,closer_default_blended,user_group,vicidial_recording_override,alter_custphone_override,alert_enabled,agent_shift_enforcement_override,shift_override_flag,allow_alerts,closer_campaigns,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,agent_call_log_view_override,agent_choose_blended,agent_lead_search_override,preset_contact_search');
                break;
            
            case "campaign":
				$nextdial_secondsSQL = '';
				$nextResult = $dbase->rawQuery("SHOW COLUMNS FROM `vicidial_campaigns` LIKE 'nextdial_seconds'");
				if ($dbase->getRowCount() > 0) {
					$nextdial_secondsSQL = ",nextdial_seconds";
				}
				
                //Campaign Settings
                $dbase->where('campaign_id', $param1);
                $return = $dbase->getOne('vicidial_campaigns', "campaign_id,campaign_name,park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf AS Call_XC_a_DTMF,xferconf_a_number AS Call_XC_a_Number,xferconf_b_dtmf AS Call_XC_b_DTMF,xferconf_b_number AS Call_XC_b_Number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy AS Copy_to_Clipboard,use_campaign_dnc,three_way_call_cid,dial_method,three_way_dial_prefix,web_form_target,vtiger_screen_login,agent_allow_group_alias,default_group_alias,quick_transfer_button,prepopulate_transfer_preset,view_calls_in_queue,view_calls_in_queue_launch,call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,agent_select_territories,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number AS Call_XC_c_Number,xferconf_d_number AS Call_XC_d_Number,xferconf_e_number AS Call_XC_e_Number,use_custom_cid,scheduled_callbacks_alert,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds,customer_3way_hangup_action,ivr_park_call,manual_preview_dial,api_manual_dial,manual_dial_call_time_check,my_callback_option,per_call_notes,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code,auto_resume_precall,manual_dial_cid,custom_3way_button_transfer,callback_days_limit,disable_dispo_screen,disable_dispo_status,screen_labels,status_display_fields,pllb_grouping,pllb_grouping_limit,in_group_dial,in_group_dial_select,pause_after_next_call,owner_populate{$nextdial_secondsSQL}");
                break;
            
            case "hotkeys":
                //Campaign HotKeys
                $dbase->where('selectable', 'Y');
                $dbase->where('status', 'NEW', '!=');
                $dbase->where('campaign_id', $param1);
                $dbase->orderBy('hotkey', 'asc');
                $return = $dbase->get('vicidial_campaign_hotkeys', 9, 'hotkey,status,status_name');
                break;
            
            case "phone":
                //Phone Settings
                //Removed columns: DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,ASTmgrUSERNAME,ASTmgrSECRET,
                $dbase->where('login', $param1);
                //$dbase->where('pass', $param2);
                $dbase->where('active', 'Y');
                $return = $dbase->getOne('phones', 'extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,phone_ring_timeout,on_hook_agent,webphone_auto_answer');
                break;
            
            case "usergroup":
                //User Group Settings
                $dbase->where('user_group', $param1);
                $return = $dbase->getOne('vicidial_user_groups', 'forced_timeclock_login,shift_enforcement,group_shifts,agent_status_viewable_groups,agent_status_view_time,agent_call_log_view,agent_xfer_consultative,agent_xfer_dial_override,agent_xfer_vm_transfer,agent_xfer_blind_transfer,agent_xfer_dial_with_customer,agent_xfer_park_customer_dial,agent_fullscreen,webphone_url_override,webphone_dialpad_override,webphone_systemkey_override');
                break;
            
            case "labels":
                //Field Labels
                $return = $dbase->getOne('system_settings', 'label_title,label_first_name,label_middle_initial,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_postal_code,label_vendor_lead_code,label_gender,label_phone_number,label_phone_code,label_alt_phone,label_security_phrase,label_email,label_comments');
                break;
            
            case "queuemetrics":
                //Queue Metrics
                $return = $dbase->getOne('system_settings', 'enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,allow_sipsak_messages,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url');
                break;
            
            default:
                //System Settings
                $return = $dbase->getOne('system_settings', 'use_non_latin,vdc_header_date_format,vdc_customer_date_format,vdc_header_phone_format,webroot_writable,timeclock_end_of_day,vtiger_url,enable_vtiger_integration,outbound_autodial_active,enable_second_webform,user_territories_active,static_agent_url,custom_fields_enabled,pllb_grouping_limit,qc_features_active,allow_emails,default_language,vicidial_agent_disable,pass_hash_enabled,agentonly_callback_campaign_lock');
        }
        
        return json_decode(json_encode($return), FALSE);
    }
    
    function check_sip_login($dbase, $exten, $type='kamailio', $webrtc=true) {
        if (!$webrtc) {
            if ($type == 'kamailio') {
                //$cwd = getcwd();
                //$lastLine = exec('/usr/share/goautodial/goautodialc.pl "sudo /usr/sbin/kamctl ul show --brief"', $kamctlVars);
                //foreach ($kamctlVars as $var) {
                //    if (preg_match("/AOR/", trim($var))) {
                //        $newVar = trim(preg_replace("/AOR::/","", $var));
                //        if ($exten == $newVar) {
                //            return true;
                //        }
                //    }
                //}
				$dbase->where('username', $exten);
				$dbase->getOne('location');
				$return = ($dbase->getRowCount() > 0) ? true : false;
                return $return;
            } else {
                $lastLine = exec('/usr/share/goautodial/goautodialc.pl "sudo /usr/sbin/asterisk -rx \"sip show peer '.$exten.'\""', $asteriskVars);
                if (strlen($asteriskVars[1]) < 1) {
                    foreach ($asteriskVars as $vars) {
                        $list = explode(':', $vars);
                        if (trim($list[0]) == "Status" && preg_match('/^OK/', trim($list[1]))) {
                            return true;
                        }
                    }
                }
                return false;
            }
        } else {
            return true;
        }
    }
    
    function check_agent_login($aDB, $agent) {
        $user = get_settings('user', $aDB, $agent);
        $phone = get_settings('phone', $aDB, $user->phone_login, $user->phone_pass);
        
        $aDB->where('user', $agent);
        $aDB->where('server_ip', $phone->server_ip);
        $aDB->getOne('vicidial_live_agents');
        
        $return = ($aDB->getRowCount() > 0) ? 1 : 0;
        return $return;
    }

    function lookup_gmt($aDB, $phone_code, $USarea, $state, $LOCAL_GMT_OFF_STD, $Shour, $Smin, $Ssec, $Smon, $Smday, $Syear, $postalgmt, $postal_code) {
        $postalgmt_found = 0;
        if ( (preg_match("/POSTAL/i", $postalgmt)) && (strlen($postal_code) > 4) ) {
            if (preg_match('/^1$/', $phone_code)) {
                //$stmt="select postal_code,state,GMT_offset,DST,DST_range,country,country_code from vicidial_postal_codes where country_code='$phone_code' and postal_code LIKE \"$postal_code%\";";
                $aDB->where('country_code', $phone_code);
                $aDB->where('postal_code', $postal_code."%", 'LIKE');
                $rslt = $aDB->get('vicidial_postal_codes', null, 'postal_code,state,GMT_offset,DST,DST_range,country,country_code');
                $pc_recs = $aDB->getRowCount();
                if ($pc_recs > 0) {
                    $row = $rslt[0];
                    $gmt_offset =	$rslt['GMT_offset'];
                    $gmt_offset =   preg_replace("/\+/i", "", $gmt_offset);
                    $dst =			$row['DST'];
                    $dst_range =	$row['DST_range'];
                    $PC_processed++;
                    $postalgmt_found++;
                    $post++;
                }
            }
        }

        if ($postalgmt_found < 1) {
            $PC_processed=0;
            ### UNITED STATES ###
            if ($phone_code =='1') {
                //$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
                $aDB->where('country_code', $phone_code);
                $aDB->where('areacode', $USarea);
                $rslt = $aDB->get('vicidial_phone_codes', null, 'country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description');
                $pc_recs = $aDB->getRowCount();
                if ($pc_recs > 0) {
                    $row = $rslt[0];
                    $gmt_offset =	$row['GMT_offset'];
                    $gmt_offset =   preg_replace("/\+/i", "", $gmt_offset);
                    $dst =			$row['DST'];
                    $dst_range =	$row['DST_range'];
                    $PC_processed++;
				}
			}

            ### MEXICO ###
            if ($phone_code =='52') {
                //$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and areacode='$USarea';";
                $aDB->where('country_code', $phone_code);
                $aDB->where('areacode', $USarea);
                $rslt = $aDB->get('vicidial_phone_codes', null, 'country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description');
                $pc_recs = $aDB->getRowCount();
                if ($pc_recs > 0) {
                    $row = $rslt[0];
                    $gmt_offset =	$row['GMT_offset'];
                    $gmt_offset =   preg_replace("/\+/i", "", $gmt_offset);
                    $dst =			$row['DST'];
                    $dst_range =	$row['DST_range'];
                    $PC_processed++;
				}
			}

            ### AUSTRALIA ###
            if ($phone_code =='61') {
                //$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code' and state='$state';";
                $aDB->where('country_code', $phone_code);
                $aDB->where('state', $state);
                $rslt = $aDB->get('vicidial_phone_codes', null, 'country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description');
                $pc_recs = $aDB->getRowCount();
                if ($pc_recs > 0) {
                    $row = $rslt[0];
                    $gmt_offset =	$row['GMT_offset'];
                    $gmt_offset =   preg_replace("/\+/i", "", $gmt_offset);
                    $dst =			$row['DST'];
                    $dst_range =	$row['DST_range'];
                    $PC_processed++;
				}
			}

            ### ALL OTHER COUNTRY CODES ###
            if (!$PC_processed) {
                $PC_processed++;
                //$stmt="select country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description from vicidial_phone_codes where country_code='$phone_code';";
                $aDB->where('country_code', $phone_code);
                $rslt = $aDB->get('vicidial_phone_codes', null, 'country_code,country,areacode,state,GMT_offset,DST,DST_range,geographic_description');
                $pc_recs = $aDB->getRowCount();
                if ($pc_recs > 0) {
                    $row = $rslt[0];
                    $gmt_offset =	$row['GMT_offset'];
                    $gmt_offset =   preg_replace("/\+/i", "", $gmt_offset);
                    $dst =			$row['DST'];
                    $dst_range =	$row['DST_range'];
                    $PC_processed++;
				}
			}
		}

        ### Find out if DST to raise the gmt offset ###
        $AC_GMT_diff = ($gmt_offset - $LOCAL_GMT_OFF_STD);
        $AC_localtime = mktime(($Shour + $AC_GMT_diff), $Smin, $Ssec, $Smon, $Smday, $Syear);
        $hour = date("H", $AC_localtime);
        $min = date("i", $AC_localtime);
        $sec = date("s", $AC_localtime);
        $mon = date("m", $AC_localtime);
        $mday = date("d", $AC_localtime);
        $wday = date("w", $AC_localtime);
        $year = date("Y", $AC_localtime);
        $dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );

        $AC_processed = 0;
        if ( (!$AC_processed) and ($dst_range == 'SSM-FSN') ) {
            #**********************************************************************
            # SSM-FSN
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on Second Sunday March to First Sunday November at 2 am.
            #     INPUTS:
            #       mm              INTEGER       Month.
            #       dd              INTEGER       Day of the month.
            #       ns              INTEGER       Seconds into the day.
            #       dow             INTEGER       Day of week (0=Sunday, to 6=Saturday)
            #     OPTIONAL INPUT:
            #       timezone        INTEGER       hour difference UTC - local standard time
            #                                      (DEFAULT is blank)
            #                                     make calculations based on UTC time, 
            #                                     which means shift at 10:00 UTC in April
            #                                     and 9:00 UTC in October
            #     OUTPUT: 
            #                       INTEGER       1 = DST, 0 = not DST
            #
            # S  M  T  W  T  F  S
            # 1  2  3  4  5  6  7
            # 8  9 10 11 12 13 14
            #15 16 17 18 19 20 21
            #22 23 24 25 26 27 28
            #29 30 31
            # 
            # S  M  T  W  T  F  S
            #    1  2  3  4  5  6
            # 7  8  9 10 11 12 13
            #14 15 16 17 18 19 20
            #21 22 23 24 25 26 27
            #28 29 30 31
            # 
            #**********************************************************************

			$USACAN_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 11) {
                $USACAN_DST = 0;   
			} else if ($mm >= 4 and $mm <= 10) {
                $USACAN_DST = 1;   
			} else if ($mm == 3) {
                if ($dd > 13) {
                    $USACAN_DST = 1;   
                } else if ($dd >= ($dow+8)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $USACAN_DST = 0;   
                        } else {
                            $USACAN_DST = 1;   
                        }
                    } else {
                        if ($dow == 0 and $ns < 7200) {
                            $USACAN_DST = 0;   
                        } else {
                            $USACAN_DST = 1;   
                        }
                    }
                } else {
                    $USACAN_DST = 0;   
                }
			} else if ($mm == 11) {
                if ($dd > 7) {
                    $USACAN_DST = 0;   
                } else if ($dd < ($dow+1)) {
                    $USACAN_DST = 1;   
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (7200+($timezone-1)*3600)) {
                            $USACAN_DST = 1;   
                        } else {
                            $USACAN_DST = 0;   
                        }
                    } else { # local time calculations
                        if ($ns < 7200) {
                            $USACAN_DST = 1;   
                        } else {
                            $USACAN_DST = 0;   
                        }
                    }
                } else {
                    $USACAN_DST = 0;   
                }
			} # end of month checks
			
            if ($USACAN_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSA-LSO') ) {
            #**********************************************************************
            # FSA-LSO
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in April and last Sunday in October at 2 am.
            #**********************************************************************
			
			$USA_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 4 || $mm > 10) {
                $USA_DST = 0;
			} else if ($mm >= 5 and $mm <= 9) {
                $USA_DST = 1;
			} else if ($mm == 4) {
                if ($dd > 7) {
                    $USA_DST = 1;
                } else if ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $USA_DST = 0;
                        } else {
                            $USA_DST = 1;
                        }
                    } else {
                        if ($dow == 0 and $ns < 7200) {
                            $USA_DST = 0;
                        } else {
                            $USA_DST = 1;
                        }
                    }
                } else {
                    $USA_DST = 0;
                }
			} else if ($mm == 10) {
                if ($dd < 25) {
                    $USA_DST = 1;
                } else if ($dd < ($dow+25)) {
                    $USA_DST = 1;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (7200+($timezone-1)*3600)) {
                            $USA_DST = 1;
                        } else {
                            $USA_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 7200) {
                            $USA_DST = 1;
                        } else {
                            $USA_DST = 0;
                        }
                    }
                } else {
                    $USA_DST = 0;
                }
			} # end of month checks

            if ($USA_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'LSM-LSO') ) {
            #**********************************************************************
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on last Sunday in March and last Sunday in October at 1 am.
            #**********************************************************************
			
			$GBR_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
                $GBR_DST = 0;
			} else if ($mm >= 4 and $mm <= 9) {
                $GBR_DST = 1;
			} else if ($mm == 3) {
                if ($dd < 25) {
                    $GBR_DST = 0;
                } else if ($dd < ($dow+25)) {
                    $GBR_DST = 0;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $GBR_DST = 0;
                        } else {
                            $GBR_DST = 1;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $GBR_DST = 0;
                        } else {
                            $GBR_DST = 1;
                        }
                    }
                } else {
                    $GBR_DST = 1;
                }
			} else if ($mm == 10) {
                if ($dd < 25) {
                    $GBR_DST = 1;
                } else if ($dd < ($dow+25)) {
                    $GBR_DST = 1;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $GBR_DST = 1;
                        } else {
                            $GBR_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $GBR_DST = 1;
                        } else {
                            $GBR_DST = 0;
                        }
                    }
                } else {
                    $GBR_DST = 0;
                }
			} # end of month checks
			
            if ($GBR_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'LSO-LSM') ) {
            #**********************************************************************
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on last Sunday in October and last Sunday in March at 1 am.
            #**********************************************************************
			
			$AUS_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
                $AUS_DST = 1;
			} else if ($mm >= 4 and $mm <= 9) {
                $AUS_DST = 0;
			} else if ($mm == 3) {
                if ($dd < 25) {
                    $AUS_DST = 1;
                } else if ($dd < ($dow+25)) {
                    $AUS_DST = 1;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $AUS_DST = 1;
                        } else {
                            $AUS_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $AUS_DST = 1;
                        } else {
                            $AUS_DST = 0;
                        }
                    }
                } else {
                    $AUS_DST = 0;
                }
			} elseif ($mm == 10) {
                if ($dd < 25) {
                    $AUS_DST = 0;
                } else if ($dd < ($dow+25)) {
                    $AUS_DST = 0;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $AUS_DST = 0;
                        } else {
                            $AUS_DST = 1;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $AUS_DST = 0;
                        } else {
                            $AUS_DST = 1;
                        }
                    }
                } else {
                    $AUS_DST = 1;
                }
			} # end of month checks
			
            if ($AUS_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSO-LSM') ) {
            #**********************************************************************
            #   TASMANIA ONLY
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in October and last Sunday in March at 1 am.
            #**********************************************************************
			
			$AUST_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
                $AUST_DST = 1;
			} else if ($mm >= 4 and $mm <= 9) {
                $AUST_DST = 0;
			} else if ($mm == 3) {
                if ($dd < 25) {
                    $AUST_DST = 1;
                } else if ($dd < ($dow+25)) {
                    $AUST_DST = 1;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $AUST_DST = 1;
                        } else {
                            $AUST_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $AUST_DST = 1;
                        } else {
                            $AUST_DST = 0;
                        }
                    }
                } else {
                    $AUST_DST = 0;
                }
			} else if ($mm == 10) {
                if ($dd > 7) {
                    $AUST_DST = 1;
                } else if ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $AUST_DST = 0;
                        } else {
                            $AUST_DST = 1;
                        }
                    } else {
                        if ($dow == 0 and $ns < 3600) {
                            $AUST_DST = 0;
                        } else {
                            $AUST_DST = 1;
                        }
                    }
                } else {
                    $AUST_DST = 0;
                }
			} # end of month checks
			
            if ($AUST_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSO-FSA') ) {
            #**********************************************************************
            # FSO-FSA
            #   2008+ AUSTRALIA ONLY (country code 61)
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in October and first Sunday in April at 1 am.
            #**********************************************************************
		
            $AUSE_DST = 0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;
    
            if ($mm < 4 or $mm > 10) {
                $AUSE_DST = 1;   
            } else if ($mm >= 5 and $mm <= 9) {
                $AUSE_DST = 0;   
            } else if ($mm == 4) {
                if ($dd > 7) {
                    $AUSE_DST = 0;   
                } else if ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (3600+$timezone*3600)) {
                            $AUSE_DST = 1;   
                        } else {
                            $AUSE_DST = 0;   
                        }
                    } else {
                        if ($dow == 0 and $ns < 7200) {
                            $AUSE_DST = 1;   
                        } else {
                            $AUSE_DST = 0;   
                        }
                    }
                } else {
                    $AUSE_DST = 1;   
                }
            } else if ($mm == 10) {
                if ($dd >= 8) {
                    $AUSE_DST = 1;   
                } else if ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $AUSE_DST = 0;   
                        } else {
                            $AUSE_DST = 1;   
                        }
                    } else {
                        if ($dow == 0 and $ns < 3600) {
                            $AUSE_DST = 0;   
                        } else {
                            $AUSE_DST = 1;   
                        }
                    }
                } else {
                    $AUSE_DST = 0;   
                }
            } # end of month checks
            
            if ($AUSE_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'FSO-TSM') ) {
            #**********************************************************************
            #     This is s 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on first Sunday in October and third Sunday in March at 1 am.
            #**********************************************************************
			
			$NZL_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 3 || $mm > 10) {
                $NZL_DST = 1;
			} else if ($mm >= 4 and $mm <= 9) {
                $NZL_DST = 0;
			} else if ($mm == 3) {
                if ($dd < 14) {
                    $NZL_DST = 1;
                } else if ($dd < ($dow+14)) {
                    $NZL_DST = 1;
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $NZL_DST = 1;
                        } else {
                            $NZL_DST = 0;
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $NZL_DST = 1;
                        } else {
                            $NZL_DST = 0;
                        }
                    }
                } else {
                    $NZL_DST = 0;
                }
			} else if ($mm == 10) {
                if ($dd > 7) {
                    $NZL_DST = 1;
                } else if ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 and $ns < (7200+$timezone*3600)) {
                            $NZL_DST = 0;
                        } else {
                            $NZL_DST = 1;
                        }
                    } else {
                        if ($dow == 0 and $ns < 3600) {
                            $NZL_DST = 0;
                        } else {
                            $NZL_DST = 1;
                        }
                    }
                } else {
                    $NZL_DST = 0;
                }
			} # end of month checks
			
            if ($NZL_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'LSS-FSA') ) {
            #**********************************************************************
            # LSS-FSA
            #   2007+ NEW ZEALAND (country code 64)
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect.
            #     Based on last Sunday in September and first Sunday in April at 1 am.
            #**********************************************************************
            
            $NZLN_DST = 0;
            $mm = $mon;
            $dd = $mday;
            $ns = $dsec;
            $dow= $wday;
    
            if ($mm < 4 || $mm > 9) {
                $NZLN_DST = 1;   
            } else if ($mm >= 5 && $mm <= 9) {
                $NZLN_DST = 0;   
            } else if ($mm == 4) {
                if ($dd > 7) {
                    $NZLN_DST = 0;   
                } else if ($dd >= ($dow+1)) {
                    if ($timezone) {
                        if ($dow == 0 && $ns < (3600+$timezone*3600)) {
                            $NZLN_DST = 1;   
                        } else {
                            $NZLN_DST = 0;   
                        }
                    } else {
                        if ($dow == 0 && $ns < 7200) {
                            $NZLN_DST = 1;   
                        } else {
                            $NZLN_DST = 0;   
                        }
                    }
                } else {
                    $NZLN_DST = 1;   
                }
            } else if ($mm == 9) {
                if ($dd < 25) {
                    $NZLN_DST = 0;   
                } else if ($dd < ($dow+25)) {
                    $NZLN_DST = 0;   
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $NZLN_DST = 0;   
                        } else {
                            $NZLN_DST = 1;   
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $NZLN_DST = 0;   
                        } else {
                            $NZLN_DST = 1;   
                        }
                    }
                } else {
                    $NZLN_DST = 1;   
                }
            } # end of month checks
            
            if ($NZLN_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if ( (!$AC_processed) and ($dst_range == 'TSO-LSF') ) {
            #**********************************************************************
            # TSO-LSF
            #     This is returns 1 if Daylight Savings Time is in effect and 0 if 
            #       Standard time is in effect. Brazil
            #     Based on Third Sunday October to Last Sunday February at 1 am.
            #**********************************************************************
			
			$BZL_DST = 0;
			$mm = $mon;
			$dd = $mday;
			$ns = $dsec;
			$dow= $wday;

			if ($mm < 2 || $mm > 10) {
                $BZL_DST = 1;   
			} else if ($mm >= 3 and $mm <= 9) {
                $BZL_DST = 0;   
			} else if ($mm == 2) {
                if ($dd < 22) {
                    $BZL_DST = 1;   
                } else if ($dd < ($dow+22)) {
                    $BZL_DST = 1;   
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $BZL_DST = 1;   
                        } else {
                            $BZL_DST = 0;   
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $BZL_DST = 1;   
                        } else {
                            $BZL_DST = 0;   
                        }
                    }
                } else {
                    $BZL_DST = 0;   
                }
			} else if ($mm == 10) {
                if ($dd < 22) {
                    $BZL_DST = 0;   
                } else if ($dd < ($dow+22)) {
                    $BZL_DST = 0;   
                } else if ($dow == 0) {
                    if ($timezone) { # UTC calculations
                        if ($ns < (3600+($timezone-1)*3600)) {
                            $BZL_DST = 0;   
                        } else {
                            $BZL_DST = 1;   
                        }
                    } else { # local time calculations
                        if ($ns < 3600) {
                            $BZL_DST = 0;   
                        } else {
                            $BZL_DST = 1;   
                        }
                    }
                } else {
                    $BZL_DST = 1;   
                }
			} # end of month checks
			
            if ($BZL_DST) {$gmt_offset++;}
            $AC_processed++;
		}

        if (!$AC_processed) {
            //if ($DBX) {print "     No DST Method Found\n";}
            //if ($DBX) {print "     DST: 0\n";}
            $AC_processed++;
		}

        return $gmt_offset;
	}

    ##### DETERMINE IF LEAD IS DIALABLE #####
    function dialable_gmt($aDB, $local_call_time, $gmt_offset, $state) {
        $dialable=0;
    
        $pzone = 3600 * $gmt_offset;
        $pmin = (gmdate("i", time() + $pzone));
        $phour = ( (gmdate("G", time() + $pzone)) * 100);
        $pday = gmdate("w", time() + $pzone);
        $tz = sprintf("%.2f", $p);	
        $GMT_gmt = "$tz";
        $GMT_day = "$pday";
        $GMT_hour = ($phour + $pmin);
    
        //$stmt="SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times FROM vicidial_call_times where call_time_id='$local_call_time';";
        $aDB->where('call_time_id', $local_call_time);
        $rowx = $aDB->get('vicidial_call_times', null, 'call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times');
        $Gct_default_start =	$rowx['ct_default_start'];
        $Gct_default_stop =		$rowx['ct_default_stop'];
        $Gct_sunday_start =		$rowx['ct_sunday_start'];
        $Gct_sunday_stop =		$rowx['ct_sunday_stop'];
        $Gct_monday_start =		$rowx['ct_monday_start'];
        $Gct_monday_stop =		$rowx['ct_monday_stop'];
        $Gct_tuesday_start =	$rowx['ct_tuesday_start'];
        $Gct_tuesday_stop =		$rowx['ct_tuesday_stop'];
        $Gct_wednesday_start =	$rowx['ct_wednesday_start'];
        $Gct_wednesday_stop =	$rowx['ct_wednesday_stop'];
        $Gct_thursday_start =	$rowx['ct_thursday_start'];
        $Gct_thursday_stop =	$rowx['ct_thursday_stop'];
        $Gct_friday_start =		$rowx['ct_friday_start'];
        $Gct_friday_stop =		$rowx['ct_friday_stop'];
        $Gct_saturday_start =	$rowx['ct_saturday_start'];
        $Gct_saturday_stop =	$rowx['ct_saturday_stop'];
        $Gct_state_call_times = $rowx['ct_state_call_times'];
    
        if ($GMT_day == 0) {	#### Sunday local time{
            if (($Gct_sunday_start == 0) and ($Gct_sunday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_sunday_start) and ($GMT_hour < $Gct_sunday_stop) )
                    {$dialable = 1;}
            }
        }
        if ($GMT_day == 1) {	#### Monday local time
            if (($Gct_monday_start == 0) and ($Gct_monday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_monday_start) and ($GMT_hour < $Gct_monday_stop) )
                    {$dialable = 1;}
            }
        }
        if ($GMT_day == 2) {	#### Tuesday local time
            if (($Gct_tuesday_start == 0) and ($Gct_tuesday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_tuesday_start) and ($GMT_hour < $Gct_tuesday_stop) )
                    {$dialable = 1;}
            }
        }
        if ($GMT_day == 3) {	#### Wednesday local time
            if (($Gct_wednesday_start == 0) and ($Gct_wednesday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_wednesday_start) and ($GMT_hour < $Gct_wednesday_stop) )
                    {$dialable = 1;}
            }
        }
        if ($GMT_day == 4) {	#### Thursday local time
            if (($Gct_thursday_start == 0) and ($Gct_thursday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_thursday_start) and ($GMT_hour < $Gct_thursday_stop) )
                    {$dialable = 1;}
            }
        }
        if ($GMT_day == 5) {	#### Friday local time
            if (($Gct_friday_start == 0) and ($Gct_friday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_friday_start) and ($GMT_hour < $Gct_friday_stop) )
                    {$dialable = 1;}
            }
        }
        if ($GMT_day == 6) {	#### Saturday local time
            if (($Gct_saturday_start == 0) and ($Gct_saturday_stop == 0)) {
                if ( ($GMT_hour >= $Gct_default_start) and ($GMT_hour < $Gct_default_stop) )
                    {$dialable = 1;}
            } else {
                if ( ($GMT_hour >= $Gct_saturday_start) and ($GMT_hour < $Gct_saturday_stop) )
                    {$dialable = 1;}
            }
        }
    
        return $dialable;
	}

    // Audit Comments
    function audit_comments($aDB, $lead_id, $list_id, $format, $user, $NOW_TIME, $server_ip, $session_name, $campaign) {
        $audit_comments_active = audit_comments_active($aDB, $list_id, $format, $user, $NOW_TIME, $server_ip, $session_name);
        if ($audit_comments_active) {
            //Get comment from list
            //$stmt="select comments from vicidial_list where lead_id='$lead_id' limit 1;";
            $aDB->where('lead_id', $lead_id);
            $rslt = $aDB->getOne('vicidial_list', 'comments');
            if (strlen($rslt['comments']) > 0) {
                $comment = $rslt['comments'];
                //Put comment in comment table
                //$stmt="INSERT INTO vicidial_comments (lead_id,user_id,list_id,campaign_id,comment) VALUES ('$lead_id','$user','$list_id','$campaign','$comment');";
                $insertData = array(
                    'lead_id' => $lead_id,
                    'user_id' => $user,
                    'list_id' => $list_id,
                    'campaign_id' => $campaign,
                    'comment' => $comment
                );
                $rslt = $aDB->insert('vicidial_comments', $insertData);
                $affected = $aDB->getRowCount();
                if ($affected > 0) {
                    //$stmt="UPDATE vicidial_list set comments='' where lead_id='$lead_id';";
                    $aDB->where('lead_id', $lead_id);
                    $rslt = $aDB->update('vicidial_list', array('comments' => ''));
                } else {
                    //mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00142-AuditCommentsERROR-Comment not moved',$user,$server_ip,$session_name,$one_mysql_log);
                    echo "00142-AuditCommentsERROR-Comment not moved";
                }
            }
        }
    }
    
    function audit_comments_active($aDB, $list_id, $format, $user, $NOW_TIME, $server_ip, $session_name) {
        //$stmt="select count(audit_comments) from vicidial_lists_custom where list_id='$list_id' and audit_comments='1' limit 1;";
        $aDB->where('list_id', $list_id);
        $aDB->where('audit_comments', '1');
        $rslt = $aDB->getOne('vicidial_lists_custom', 'count(audit_comments) AS audit_comments');
        if ($rslt['audit_comments'] == '1') {
            return true;
        } else {
            return false;
        }
    }
    
    function get_audited_comments($aDB, $lead_id, $format, $user, $NOW_TIME, $server_ip, $session_name) {
        global $ACcount;
        global $ACcomments;
        //$stmt="select user_id,comment from vicidial_comments where lead_id='$lead_id';";
        $aDB->where('lead_id', $lead_id);
        $rslt = $aDB->get('vicidial_comments', null, 'user_id,comment');
        $ACcount = $aDB->getRowCount();
    
        if($ACcount > 0) {
            $x = 0;
            while ($ACcount > $x) {
                $row = $rslt[$x];
                $ACcomments .=	"UserID: {$row['user_id']}\n";
                $ACcomments .=	$row['comment'];
                $ACcomments .=	"\n----------------------------------\n";
                $x++;
            }
            return true;
        } else {
            return false;
        }
    }

    ##### BEGIN validate user login credentials, check for failed lock out #####
    function user_authorization($aDB, $user, $pass, $user_option, $user_update, $bcrypt, $return_hash) {
        #############################################
        ##### START SYSTEM_SETTINGS LOOKUP #####
        //$stmt = "SELECT use_non_latin,webroot_writable,pass_hash_enabled,pass_key,pass_cost,hosted_settings FROM system_settings;";
        $rslt = $aDB->getOne('system_settings', 'use_non_latin,webroot_writable,pass_hash_enabled,pass_key,pass_cost,hosted_settings');
        $qm_conf_ct = $aDB->getRowCount();
        if ($qm_conf_ct > 0) {
            $non_latin =            $rslt['use_non_latin'];
            $SSwebroot_writable =   $rslt['webroot_writable'];
            $SSpass_hash_enabled =  $rslt['pass_hash_enabled'];
            $SSpass_key =           $rslt['pass_key'];
            $SSpass_cost =          $rslt['pass_cost'];
            $SShosted_settings =    $rslt['hosted_settings'];
        }
        ##### END SETTINGS LOOKUP #####
        ###########################################

        $STARTtime = date("U");
        $TODAY = date("Y-m-d");
        $NOW_TIME = date("Y-m-d H:i:s");
        $ip = getenv("REMOTE_ADDR");
        $browser = getenv("HTTP_USER_AGENT");
        $LOCK_over = ($STARTtime - 900); # failed login lockout time is 15 minutes(900 seconds)
        $LOCK_trigger_attempts = 10;
        $pass_hash = '';
        $cwd = $_SERVER['DOCUMENT_ROOT'];
        $auth = 0;

        $user = preg_replace("/\'|\"|\\\\|;| /", "", $user);
        $pass = preg_replace("/\'|\"|\\\\|;| /", "", $pass);

        //$passSQL = "pass='$pass'";

        $aDB->where('user', $user);
        if ($SSpass_hash_enabled > 0) {
            if ($bcrypt < 1) {
                //$pass_hash = exec("{$cwd}/bin/bp.pl --pass=$pass");
                //$pass_hash = preg_replace("/PHASH: |\n|\r|\t| /",'',$pass_hash);
				$pass_options = [
					'cost' => $SSpass_cost,
					'salt' => base64_encode($SSpass_key)
				];
				$pass_hash = password_hash($pass, PASSWORD_BCRYPT, $pass_options);
				$pass_hash = substr($pass_hash, 29, 31);				
            } else {$pass_hash = $pass;}
            //$passSQL = "pass_hash='$pass_hash'";
            $aDB->where('pass_hash', $pass_hash);
        } else {
            $aDB->where('pass', $pass);
        }
        
        if ($user_option == 'MGR') {
            $aDB->where('manager_shift_enforcement_override', '1');
        } else {
            $aDB->where('user_level', 0, '>');
        }
        $aDB->where('active', 'Y');
        $rslt = $aDB->get('vicidial_users', null, 'failed_login_count,UNIX_TIMESTAMP(last_login_date) AS last_login_date');
        $userExist = $aDB->getRowCount();
        $stmt = $aDB->getLastQuery();

        if ( $userExist > 0 AND ( ($rslt[0]['failed_login_count'] < $LOCK_trigger_attempts) OR ($rslt[0]['last_login_date'] < $LOCK_over) ) ) {
            $auth++;
        }

        if ($auth < 1) {
            $auth_key = "BAD|{$stmt}";
            //$stmt="SELECT failed_login_count,UNIX_TIMESTAMP(last_login_date) from vicidial_users where user='$user';";
            $aDB->where('user', $user);
            $rslt = $aDB->getOne('vicidial_users', 'failed_login_count,UNIX_TIMESTAMP(last_login_date) AS last_login_date');
            $cl_user_ct = $aDB->getRowCount();
            if ($cl_user_ct > 0) {
                $failed_login_count =   $rslt['failed_login_count'];
                $last_login_date =      $rslt['last_login_date'];

                if ($failed_login_count < $LOCK_trigger_attempts) {
                    //$stmt="UPDATE vicidial_users set failed_login_count=(failed_login_count+1),last_ip='$ip' where user='$user';";
                    $failed_login_count++;
                    $aDB->where('user', $user);
                    $rslt = $aDB->update('vicidial_users', array('failed_login_count'=>$failed_login_count, 'last_ip'=>$ip));
                } else {
                    if ($LOCK_over > $last_login_date) {
                        //$stmt="UPDATE vicidial_users set last_login_date=NOW(),failed_login_count=1,last_ip='$ip' where user='$user';";
                        $aDB->where('user', $user);
                        $rslt = $aDB->update('vicidial_users', array('last_login_date'=>'NOW()', 'failed_login_count'=>1, 'last_ip'=>$ip));
                    } else {$auth_key='LOCK';}
                }
            }
        } else {
            $login_problem = 0;
            $aas_total = 0;
            $ap_total = 0;
            $vla_total = 0;
            $mvla_total = 0;
            $vla_set = 0;
            $vla_on = 0;

            //$stmt = "SELECT count(*) FROM servers where active='Y' and active_asterisk_server='Y';";
            $aDB->where('active', 'Y');
            $aDB->where('active_asterisk_server', 'Y');
            $rslt = $aDB->get('servers');
            $aas_ct = $aDB->getRowCount();
            if ($aas_ct > 0) {
                $aas_total = $aas_ct;
            }

            //$stmt = "SELECT count(*) FROM vicidial_live_agents where user!='$user';";
            $aDB->where('user', $user, '!=');
            $rslt = $aDB->get('vicidial_live_agents');
            $vla_ct = $aDB->getRowCount();
            if ($vla_ct > 0) {
                $vla_total = $vla_ct;
            }

            //$stmt = "SELECT count(*) FROM vicidial_live_agents where user='$user';";
            $aDB->where('user', $user);
            $rslt = $aDB->get('vicidial_live_agents');
            $mvla_ct = $aDB->getRowCount();
            if ($mvla_ct > 0) {
                $mvla_total = $mvla_ct;
            }

            if ( (preg_match("/MXAG/", $SShosted_settings)) and ($mvla_total < 1) ) {
                $vla_set = $SShosted_settings;
                $vla_set = preg_replace("/.*MXAG|_BUILD_|DRA| /", '', $vla_set);
                $vla_set = preg_replace('/[^0-9]/', '', $vla_set);
                if (strlen($vla_set) > 0)
                    {$vla_on++;}
            }

            if ($aas_total < 1) {
                $auth_key = 'ERRSERVERS';
                $login_problem++;
            }
    //       if ($ap_total < 1) {
    //           $auth_key = 'ERRPHONES';
    //           $login_problem++;
    //       }
            if ( ($vla_total >= $vla_set) and ($vla_on > 0) ) {
                $auth_key = 'ERRAGENTS';
                $login_problem++;
            }
            //if ($mvla_total > 0) {
            //    $auth_key = 'ERRDUPLICATE';
            //    $login_problem++;
            //}

            if ($login_problem < 1) {
                if ($user_update > 0) {
                        //$stmt="UPDATE vicidial_users set last_login_date=NOW(),last_ip='$ip',failed_login_count=0 where user='$user';";
                        $aDB->where('user', $user);
                        $rslt = $aDB->update('vicidial_users', array('last_login_date'=>'NOW()','last_ip'=>$ip,'failed_login_count'=>0));
                    }
                $auth_key = 'GOOD';
                if ( ($return_hash == '1') and ($SSpass_hash_enabled > 0) and (strlen($pass_hash) > 12) )
                    {$auth_key .= "|$pass_hash";}
            }
        }
        return $auth_key;
    }
    ##### END validate user login credentials, check for failed lock out #####
    
	##### ACTION LOGS #####
	function log_action($link, $action, $user, $ip, $details, $user_group, $db_query = '') {
		$action = $link->escape(strtoupper($action));
		$event_date = date("Y-m-d H:i:s");
		$user = $link->escape($user);
		$ip = $link->escape($ip);
		$user_group = $link->escape($user_group);
		$details = $link->escape($details);
		$db_query = $link->escape($db_query);
		
		if ((!is_null($user) && strlen($user) > 0) && (!is_null($ip) && strlen($ip) > 0) && $link) {
			//$logSQL = "INSERT INTO go_action_logs (user, ip_address, event_date, action, details, db_query, user_group) VALUES ('$user', '$ip', '$event_date', '$action', '$details', '$db_query', '$user_group');";
			$insertData = array(
				'user' => $user,
				'ip_address' => $ip,
				'event_date' => $event_date,
				'action' => $action,
				'details' => $details,
				'db_query' => $db_query,
				'user_group' => $user_group
			);
			$result = $link->insert('go_action_logs', $insertData);
		}
		
		if ($result) {
			$log_id = $link->getInsertId();
			return $log_id;
		} else {
			return false;
		}
	}
	##### END ACTION LOGS #####
    
    function hangup_cause_description($code) {
        global $hangup_cause_dictionary;
        if ( array_key_exists($code, $hangup_cause_dictionary)  ) { return $hangup_cause_dictionary[$code]; }
        else { return "Unidentified Hangup Cause Code."; }
	}
    

    ##### SIP Hangup Cause Description Map  #####
    function sip_hangup_cause_description($sip_code) {
        global $sip_hangup_cause_dictionary;
        if ( array_key_exists($sip_code,$sip_hangup_cause_dictionary)  ) { return $sip_hangup_cause_dictionary[$sip_code]; }
        else { return "Unidentified SIP Hangup Cause Code."; }
    }
    
    ##### Hangup Cause Dictionary #####
    $hangup_cause_dictionary = array(
        0 => "Unspecified. No other cause codes applicable.",
        1 => "Unallocated (unassigned) number.",
        2 => "No route to specified transit network (national use).",
        3 => "No route to destination.",
        6 => "Channel unacceptable.",
        7 => "Call awarded, being delivered in an established channel.",
        16 => "Normal call clearing.",
        17 => "User busy.",
        18 => "No user responding.",
        19 => "No answer from user (user alerted).",
        20 => "Subscriber absent.",
        21 => "Call rejected.",
        22 => "Number changed.",
        23 => "Redirection to new destination.",
        25 => "Exchange routing error.",
        27 => "Destination out of order.",
        28 => "Invalid number format (address incomplete).",
        29 => "Facilities rejected.",
        30 => "Response to STATUS INQUIRY.",
        31 => "Normal, unspecified.",
        34 => "No circuit/channel available.",
        38 => "Network out of order.",
        41 => "Temporary failure.",
        42 => "Switching equipment congestion.",
        43 => "Access information discarded.",
        44 => "Requested circuit/channel not available.",
        50 => "Requested facility not subscribed.",
        52 => "Outgoing calls barred.",
        54 => "Incoming calls barred.",
        57 => "Bearer capability not authorized.",
        58 => "Bearer capability not presently available.",
        63 => "Service or option not available, unspecified.",
        65 => "Bearer capability not implemented.",
        66 => "Channel type not implemented.",
        69 => "Requested facility not implemented.",
        79 => "Service or option not implemented, unspecified.",
        81 => "Invalid call reference value.",
        88 => "Incompatible destination.",
        95 => "Invalid message, unspecified.",
        96 => "Mandatory information element is missing.",
        97 => "Message type non-existent or not implemented.",
        98 => "Message not compatible with call state or message type non-existent or not implemented.",
        99 => "Information element / parameter non-existent or not implemented.",
        100 => "Invalid information element contents.",
        101 => "Message not compatible with call state.",
        102 => "Recovery on timer expiry.",
        103 => "Parameter non-existent or not implemented - passed on (national use).",
        111 => "Protocol error, unspecified.",
        127 => "Interworking, unspecified."
    );
    
    ##### SIP Hangup Cause Dictionary #####
    $sip_hangup_cause_dictionary = array(
        400 => "Bad Request.",
        401 => "Unauthorized.",
        402 => "Payment Required.",
        403 => "Forbidden.",
        404 => "Not Found.",
        405 => "Method Not Allowed.",
        406 => "Not Acceptable.",
        407 => "Proxy Authentication Required.",
        408 => "Request Timeout.",
        409 => "Conflict.",
        410 => "Gone.",
        411 => "Length Required.",
        412 => "Conditional Request Failed.",
        413 => "Request Entity Too Large.",
        414 => "Request-URI Too Long.",
        415 => "Unsupported Media Type.",
        416 => "Unsupported URI Scheme.",
        417 => "Unknown Resource-Priority.",
        420 => "Bad Extension.",
        421 => "Extension Required.",
        422 => "Session Interval Too Small.",
        423 => "Interval Too Brief.",
        424 => "Bad Location Information.",
        428 => "Use Identity Header.",
        429 => "Provide Referrer Identity.",
        433 => "Anonymity Disallowed.",
        436 => "Bad Identity-Info.",
        437 => "Unsupported Certificate.",
        438 => "Invalid Identity Header.",
        470 => "Consent Needed.",
        480 => "Temporarily Unavailable.",
        481 => "Call/Transaction Does Not Exist.",
        482 => "Loop Detected..",
        483 => "Too Many Hops.",
        484 => "Address Incomplete.",
        485 => "Ambiguous.",
        486 => "Busy Here.",
        487 => "Request Terminated.",
        488 => "Not Acceptable Here.",
        489 => "Bad Event.",
        491 => "Request Pending.",
        493 => "Undecipherable.",
        494 => "Security Agreement Required.",
        500 => "Server Internal Error.",
        501 => "Not Implemented.",
        502 => "Bad Gateway.",
        503 => "Service Unavailable.",
        504 => "Server Time-out.",
        505 => "Version Not Supported.",
        513 => "Message Too Large.",
        580 => "Precondition Failure.",
        600 => "Busy Everywhere.",
        603 => "Decline.",
        604 => "Does Not Exist Anywhere.",
        606 => "Not Acceptable."
    );
	
	#get file sizes
	function formatSizeUnits($bytes){
        if ($bytes >= 1073741824)
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        elseif ($bytes >= 1048576)
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        elseif ($bytes >= 1024)
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        elseif ($bytes > 1)
            $bytes = $bytes . ' bytes';
        elseif ($bytes == 1)
            $bytes = $bytes . ' byte';
        else
            $bytes = '0 bytes';
		
        return $bytes;
	}
	
	// API Response Codes
	function error_handle($code, $param = NULL){
		$err_msg = "";
			
		switch($code){
			case "10001";
				$err_msg = "Error: You do not have permission to view this page ";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10003";
				$err_msg = "Error: Mismatch of given parameters and accepted parameters";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10004";
				$err_msg = "Error: No such resource or access is restricted ";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10005";
				$err_msg = "Error: No such method for the choosen resouce";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10006";
				$err_msg = "Error: Missing parameters";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10007";
				$err_msg = "Error: Can't connect to database";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10008";
				$err_msg = "Error: File upload failed! Possible mismatch file type ";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10009";
				$err_msg = "Error: Method not allowed";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10010";
				$err_msg = "Error: SQL Query error or not allowed query	";
				if(!empty($param))
				$err_msg .= " in ".$param;
			break;
			case "10011";
				$err_msg = "Error: The provided JSON string is not valid ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10107";
				$err_msg = "Error: No list id provided ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10109";
				$err_msg = "Error: Campaign definition with this ID already exists ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10110";
				$err_msg = "Error: Campaign definition with this code already exists ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10111";
				$err_msg = "Error: Campaign definition with this description already exists ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10112";
				$err_msg = "Error: Location with this name already exists ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10113";
				$err_msg = "Error: Agent username already in use ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10114";
				$err_msg = "Error: Invalid location ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10115";
				$err_msg = "Error: while updating the location ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10116";
				$err_msg = "Error: Phone already exists ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "10117";
				$err_msg = "Error: Invalid phone number ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;			
			
			case "40001";
				$err_msg = "Error: Missing required parameters ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "40002";
				$err_msg = "Error: Missing required parameter needed for update ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "41001";
				$err_msg = "Required field ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "41002";
				$err_msg = "Invalid value. Integer expected ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "41003";
				$err_msg = "Invalid email ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "41004";
				$err_msg = "Invalid value ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "41005";
				$err_msg = "Mismatched parameter keys ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
			case "41006";
				$err_msg = "Value does not match required criteria ";
				if(!empty($param))
				$err_msg .= "in ".$param;
			break;
		}
		
		return $err_msg;
	}

    // check location
    function go_check_location($id, $usergroup){
        $result = 0; // if result is returned 1, then the usergroup exists within the location
        include("goDBgoautodial.php");
        $query = "SELECT user_group FROM locations WHERE id='$id'";
        $rsltv = mysqli_query($linkgo, $query) or die(mysql_error($linkgo));
        $check_resultv = mysqli_num_rows($rsltv);

        if ($check_resultv > 0) {
            $rowc=mysqli_fetch_array($rsltv);
            $goUser_group = $rowc["user_group"];

            $user_group_array = explode(",",$goUser_group);

            if(in_array($usergroup, $user_group_array)){
                $result = 1;
            }
            return $result;
        }else{
            return $result;
        }
    }

    // check if user location exists
    function go_check_user_location($user, $id){
        $result = 0; // if result is returned 1, then the location feature exists
        include("goDBgoautodial.php");
        $query = "SELECT location_id FROM users WHERE name='$user' OR userid='$id';";
        $rsltv = mysqli_query($linkgo, $query);
        $check_resultv = mysqli_num_rows($rsltv);

        if ($check_resultv > 0) {
            $rowc=mysqli_fetch_array($rsltv);
            $goLocation = $rowc["location_id"];

            $result = $goLocation;

            return $result;
        }else{
            return $result;
        }
    }
    
    // check usergroup in-reference to campaign
    function go_check_usergroup_campaign($link, $usergroup = NULL, $campaign_id = NULL){
        $result = 0; // if result is returned 1, then the usergroup is matched with the campaign

        if(empty($usergroup) || empty($campaign_id)){
            //$query = "SELECT user_group FROM vicidial_campaigns WHERE campaign_id = '$campaign_id' AND user_group='$usergroup'";
			$link->where('campaign_id', $campaign_id);
			$link->where('user_group', $usergroup);
            $rsltv = $link->get('vicidial_campaigns');
            $check_resultv = $link->getRowCount();

            if ($check_resultv > 0) {
                $result = 1;
            }
        }
        if($usergroup === "ADMIN") {
            $result = 1;
        }
        return $result;
    }

    function rebuildconfQuery($dbase, $server_ip = NULL){
        //"UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
        $data = Array("rebuild_conf_files" => "Y");
        $dbase->where('generate_vicidial_conf', "Y");
        $dbase->where('active_asterisk_server', "Y");
        
        if(!empty($server_ip))
        $dbase->where('server_ip', $server_ip);

        $result = $dbase->update("servers", $data);
        
        if ($result) {
            return "Success!";
        } else {
            return "Failed to rebuild conf. Mysql_Error: ".$dbase->getLastError();
        }
    }
	
	function encrypt_passwd($pass, $cost, $salt = null) {
		$pass_options = [
			'cost' => $cost,
			'salt' => base64_encode($salt)
		];
		$pass_hash = password_hash($pass, PASSWORD_BCRYPT, $pass_options);
		return substr($pass_hash, 29, 31);
	}
	
	function nl($string) {
		if(isset($_SERVER['SHELL'])) return preg_replace('/\<br(\s*)?\/?\>/i', PHP_EOL, $string);
		return nl2br($string);
	}
	
	// escape existing special characters already in the database
	function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
		$escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c", "	");
		$replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b", " ");
		$result = str_replace($escapers, $replacements, $value);

		return $result;
	}
	
    function remove_empty($array) {
    	return array_filter($array, '_remove_empty_internal');
    }

    function _remove_empty_internal($value) {
		return !empty($value) || $value === 0;
    }	
?>
