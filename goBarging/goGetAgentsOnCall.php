<?php
    #######################################################
    #### Name: goGetAgentsOnCall.php 	               ####
    #### Description: API to get all Users Online      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2016      ####
    #### Written by: Jerico James F. Milo              ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");
    
	$query = "SELECT extension as 'station',vicidial_live_agents.user as 'user',vicidial_users.user_group as 'tenant_id',conf_exten as 'sessionid',status,comments,server_ip,call_server_ip,UNIX_TIMESTAMP(last_call_time) as 'last_call_time',UNIX_TIMESTAMP(last_call_finish) as last_call_finish,campaign_id as 'campaign',UNIX_TIMESTAMP(last_state_change) as 'last_state_change',lead_id,agent_log_id,vicidial_live_agents.callerid as 'callerid' FROM vicidial_live_agents,vicidial_users WHERE vicidial_live_agents.user=vicidial_users.user AND vicidial_live_agents.user_level != 4 ORDER BY status,last_call_time LIMIT 2000;";
    $rsltv = mysqli_query($link, $query);
    $countResult = mysqli_num_rows($rsltv);
    //var_dump($query);
    if($countResult > 0) {
    
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)) {
            
            $dataStation[]           = $fresults['station'];
            $dataUser[]              = $fresults['user'];
            $dataTenant_id[]         = $fresults['tenant_id'];
            $dataSession_id[]        = $fresults['sessionid'];
            $dataStatus[]            = $fresults['status'];
            $dataComments[]	         = $fresults['comments'];
            $dataServer_ip[]	     = $fresults['server_ip'];
            $dataCall_server_ip[]	 = $fresults['call_server_ip'];
            $dataLast_call_time[]	 = $fresults['last_call_time'];
            $dataLast_call_finish[]	 = $fresults['last_call_finish'];
            $dataCampaign[]	         = $fresults['campaign'];
            $dataLast_state_change[] = $fresults['last_state_change'];
            $dataLead_id[]           = $fresults['lead_id'];
            $dataAgent_log_id[]	     = $fresults['agent_log_id'];
            $dataCallerid[]	         = $fresults['callerid'];
                        
            $apiresults = array("result" => "success", "station" => $dataStation, "user" => $dataUser, "tenant_id" => $dataTenant_id, "session_id" => $dataSession_id, "status" => $dataStatus, "comments" => $dataComments, "server_ip" => $dataServer_ip, "call_server_ip" => $dataCall_server_ip, "last_call_time" => $dataLast_call_time, "last_call_finish" => $dataLast_call_finish, "campaign" => $dataCampaign, "last_state_change" => $dataLast_state_change, "lead_id" => $dataLead_id, "agent_log_id" => $dataAgent_log_id, "caller_id" => $dataCallerid);
    
        }
    
    } else {
    
            $apiresults = array("result" => "Error: No data to show.");
    
    }

?>
