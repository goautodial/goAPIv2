<?php
    #######################################################
    #### Name: goEmergencyLogout.php                   ####
    #### Description: API to logout specific agent     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jerico James Milo                 ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("../goFunctions.php");
    
    ### POST or GET Variables
    $user_name = $_REQUEST['goUserAgent'];
	
    ### Check if user_name or user_email
    if(!empty($user_name)) 
    {
		$NOW_TIME = date("Y-m-d H:i:s");
		$thedate = date('U');
		$inactive_epoch = ($thedate - 60);

		$query = "SELECT user,campaign_id,UNIX_TIMESTAMP(last_update_time)as last_update_time FROM vicidial_live_agents WHERE user='$user_name';";
		$Vliveagent = mysqli_query($link, $query);
		$Vliveagent =  mysqli_fetch_array($Vliveagent, MYSQLI_ASSOC);

        if(!empty($Vliveagent))
		{
		#the result is
		$query2 = "SELECT agent_log_id,user,server_ip,event_time,lead_id,campaign_id,pause_epoch,pause_sec,wait_epoch,wait_sec,talk_epoch,talk_sec,dispo_epoch,dispo_sec,status,user_group,comments,sub_status,dead_epoch,dead_sec FROM vicidial_agent_log WHERE user='$user_name' ORDER BY agent_log_id DESC LIMIT 1;";
		$agentlog = mysqli_query($link, $query2);
		$agents =  mysqli_fetch_array($agentlog, MYSQLI_ASSOC);
		
			if($agentlog->num_rows > 0)
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
							$query3 = "UPDATE vicidial_list SET status='PU' WHERE lead_id='".$agents['lead_id']."'";
							$rslt3  = mysqli_query($link, $query3);
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
			
				$query4 = "UPDATE vicidial_agent_log SET ".rtrim($updatefieldsc, ",")." WHERE agent_log_id='".$agents['agent_log_id']."' LIMIT 1;";
				$rslt4  = mysqli_query($link, $query4);

            }

			$query5 = "DELETE FROM vicidial_live_agents WHERE user='".$agents['user']."' LIMIT 1;";
			$rslt5  = mysqli_query($link, $query5);
			$query6 = "DELETE FROM go_agent_sessions WHERE sess_agent_user='".$agents['user']."' LIMIT 1;";
			$rslt6  = mysqli_query($link, $query6);
                     
			$apiresults = array("result" => "success");
		
		} else {
			$query7 = "SELECT * FROM go_agent_sessions WHERE sess_agent_user='$user_name';";
			$VliveagentSess = mysqli_query($link, $query7);
			$VliveagentSess = mysqli_fetch_array($VliveagentSess, MYSQLI_ASSOC);
			
			if (!empty($VliveagentSess))
			{
				$query8 = "DELETE FROM go_agent_sessions WHERE sess_agent_user='$user_name';";
				$rslt8  = mysqli_query($link, $query8);
				$apiresults = array("result" => "success");
			} else {
				$apiresults = array("result" => "Error: Agent $user_name is not logged in");
			}
		}

    } else {
		$apiresults = array("result" => "Error: Set parameter goUserAgent");
	}

?>
