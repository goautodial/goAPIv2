<?php
    #######################################################
    #### Name: goEmergencyLogout.php                   ####
    #### Description: API to logout specific agent     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jerico James Milo                 ####
    #### License: AGPLv2                               ####
    #######################################################
    
    ### POST or GET Variables
    $user_name = $astDB->escape($_REQUEST['goUserAgent']);
    $log_user = $astDB->escape($_REQUEST['log_user']);
    $log_group = $astDB->escape($_REQUEST['log_group']);
    $ip_address = $astDB->escape($_REQUEST['log_ip']);
	
    ### Check if user_name or user_email
    if(!empty($user_name))
    {
		$NOW_TIME = date("Y-m-d H:i:s");
		$thedate = date('U');
		$inactive_epoch = ($thedate - 60);

		//$query = "SELECT user,campaign_id,UNIX_TIMESTAMP(last_update_time)as last_update_time FROM vicidial_live_agents WHERE user='$user_name';";
		$astDB->where('user', $user_name);
		$Vliveagent = $astDB->get('vicidial_live_agents', null, 'user,campaign_id,UNIX_TIMESTAMP(last_update_time) AS last_update_time');
		//$Vliveagent =  mysqli_fetch_array($Vliveagent, MYSQLI_ASSOC);

        if(!empty($Vliveagent))
		{
			#the result is
			//$query2 = "SELECT agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status,dead_epoch,dead_sec FROM vicidial_agent_log WHERE user='$user_name' ORDER BY agent_log_id DESC LIMIT 1;";
			$astDB->where('user', $user_name);
			$astDB->orderBy('agent_log_id', 'desc');
			$agents = $astDB->getOne('vicidial_agent_log', 'agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status,dead_epoch,dead_sec');
			//$agents =  mysqli_fetch_array($agentlog, MYSQLI_ASSOC);
		
			if($astDB->getRowCount() > 0)
			{
				if($agents['wait_epoch'] < 1 || ($agents['status'] == 'PAUSE' && $agents['dispo_epoch'] < 1) )
				{
				
					$agents['pause_sec'] = (($thedate-$agents['pause_epoch'])+$agents['pause_sec']);
					$updatefields = array('wait_epoch'=>$thedate,'pause_sec'=>$agents['pause_sec']);
                
				} else {
                
				    if($agents['talk_epoch'] < 1)
					{
						$agents['wait_sec'] = (($thedate-$agents['wait_epoch']) + $agents['wait_sec']);
						$updatefields = array('talk_epoch'=>$thedate,'wait_sec'=>$agents['wait_sec']);
					
					} else {
					
						if(is_null($agents['status']) && $agents['lead_id'] > 0)
						{
							//$query3 = "UPDATE vicidial_list SET status='PU' WHERE lead_id='".$agents['lead_id']."'";
							$astDB->where('lead_id', $agents['lead_id']);
							$rslt3  = $astDB->update('vicidial_list', array('status' => 'PU'));
						}
						
						if($agents['dispo_epoch'] < 1)
						{
							$agents['talk_sec'] = ($thedate-$agents['talk_epoch']);
							$updatefields = array_merge(array('dispo_epoch'=>$thedate,'talk_sec'=>$agents['talk_sec']),$updatethis);
						
						} else {
						
							if($agents['dispo_epoch'] < 1)
							{
								$agents['dispo_sec'] = ($thedate-$agents['dispo_epoch']);
								$updatefields = array('dispo_sec'=>$agents['dispo_sec']);
							}
                        
						}
						
                    }
					
                }
				
				foreach($updatefields as $xkey => $xvalue) {
    				$updatefieldsc .= $xkey."="."'".$xvalue."',";
				}
			
				//$query4 = "UPDATE vicidial_agent_log SET ".rtrim($updatefieldsc, ",")." WHERE agent_log_id='".$agents['agent_log_id']."' LIMIT 1;";
				$astDB->where('agent_log_id', $agents['agent_log_id']);
				$rslt4  = $astDB->update('vicidial_agent_log', $updatefields, 1);

            }

            //$sql_getsessID = "SELECT agent_session_id FROM go_agent_sessions WHERE sess_agent_user='$user_name';";
			$astDB->where('sess_agent_user', $user_name);
			$fetch_sessID = $astDB->getOne('go_agent_sessions', 'agent_session_id');
			//$fetch_sessID = mysqli_fetch_array($query_sessID);

			//$query5 = "DELETE FROM vicidial_live_agents WHERE user='".$agents['user']."' LIMIT 1;";
			$astDB->where('user', $agents['user']);
			$rslt5  = $astDB->delete('vicidial_live_agents', 1);
			//$query6 = "DELETE FROM go_agent_sessions WHERE sess_agent_user='".$agents['user']."' LIMIT 1;";
			$astDB->where('sess_agent_user', $agents['user']);
			$rslt6  = $astDB->delete('go_agent_sessions', 1);

			//insert to user_log
			//$query7 = "INSERT INTO vicidial_user_log(user, event, campaign_id, event_date, user_group, server_ip, session_id) VALUE('".$agents['user']."', 'FORCE-LOGOUT', '".$agents['campaign_id']."', '".$NOW_TIME."', '".$agents['user_group']."', '".$ip_address."', '".$fetch_sessID['agent_session_id']."');";
			$insertData = array(
				'user' => $agents['user'],
				'event' => 'FORCE-LOGOUT',
				'campaign_id' => $agents['campaign_id'],
				'event_date' => $NOW_TIME,
				'user_group' => $agents['user_group'],
				'server_ip' => $ip_address,
				'session_id' => $fetch_sessID['agent_session_id']
			);
			$rslt7  = $astDB->insert('vicidial_user_log', $insertData);
			
			$log_id = log_action($goDB, 'LOGOUT', $log_user, $ip_address, "User $log_user used emergency log out on $user_name", $log_group);
                     
			$apiresults = array("result" => "success");
		
		} else {
			//$query7 = "SELECT * FROM go_agent_sessions WHERE sess_agent_user='$user_name';";
			$astDB->where('sess_agent_user', $user_name);
			$VliveagentSess = $astDB->getOne('go_agent_sessions');
			//$VliveagentSess = mysqli_fetch_array($VliveagentSess, MYSQLI_ASSOC);
			
			if (!empty($VliveagentSess))
			{
				//$query8 = "DELETE FROM go_agent_sessions WHERE sess_agent_user='$user_name';";
				$astDB->where('sess_agent_user', $user_name);
				$rslt8  = $astDB->delete('go_agent_sessions');
				
				//$query7 = "INSERT INTO vicidial_user_log(user, event, campaign_id, event_date, user_group, server_ip, session_id) VALUE('".$user_name."', 'FORCE-LOGOUT', '".$agents['campaign_id']."', '".$NOW_TIME."', '".$agents['user_group']."', '".$ip_address."', '".$VliveagentSess['agent_session_id']."');";
				$insertData = array(
					'user' => $user_name,
					'event' => 'FORCE-LOGOUT',
					'campaign_id' => $agents['campaign_id'],
					'event_date' => $NOW_TIME,
					'user_group' => $agents['user_group'],
					'server_ip' => $ip_address,
					'session_id' => $VliveagentSess['agent_session_id']
				);
				$rslt7  = $astDB->insert('vicidial_user_log', $insertData);

				$log_id = log_action($goDB, 'LOGOUT', $log_user, $ip_address, "User $log_user used emergency log out on $user_name", $log_group);
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Agent $user_name is not logged in");
			}
		}

    } else {
		$apiresults = array("result" => "Error: Set parameter goUserAgent");
	}

?>
