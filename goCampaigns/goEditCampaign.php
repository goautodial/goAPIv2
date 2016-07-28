<?php
   ####################################################
   #### Name: goEditCampaign.php                   ####
   #### Description: API to edit specific campaign ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jerico James Milo              ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include "../goFunctions.php";
 
    ### POST or GET Variables
    $goUser = $_REQUEST['goUser'];
    $ip_address = $_REQUEST['hostname'];
    $campaign_id = $_REQUEST['campaign_id'];
    $campaign_name = $_REQUEST['campaign_name'];
    $active = strtoupper($_REQUEST['active']);
    $dial_method = strtoupper($_REQUEST['dial_method']);
    $local_call_time = $_REQUEST['local_call_time'];
    $campaign_recording = $_REQUEST['campaign_recording'];
    $auto_dial_level = $_REQUEST['auto_dial_level'];
    //$limit = $_REQUEST['limit'];
   
    ### Default values 
    $defActive = array("Y","N");
    $defDialMethod = array("MANUAL","RATIO","ADAPT_HARD_LIMIT","ADAPT_TAPERED","ADAPT_AVERAGE","INBOUND_MAN"); 
    
    ### Check campaign_id if its null or empty
	if($campaign_id == null) { 
		$apiresults = array("result" => "Error: Set a value for Campaign ID."); 
	} else {
    		### Check value compare to default values
		if(!in_array($active,$defActive) && $active != null) { 
			$apiresults = array("result" => "Error: Default value for active is Y or N only."); 
		} else {
			if(!in_array($dial_method,$defDialMethod) && $dial_method != null) { 
				$apiresults = array("result" => "Error: Default value for dial method are MANUAL,RATIO,ADAPT_HARD_LIMIT,ADAPT_TAPERED,ADAPT_AVERAGE,INBOUND_MAN only."); 
			} else {
 
    				$groupId = go_get_groupid($goUser);
   				if($limit < 1){ $limit = 20; } else { $limit = $limit; }

				if (!checkIfTenant($groupId)) {
        				$ul="WHERE campaign_id='$campaign_id'";
    				} else { 
					$ul = "WHERE campaign_id='$campaign_ and must not be emptyid AND user_group='$groupId'";  
				}

   				$query = "SELECT campaign_id,campaign_name,dial_method,active FROM vicidial_campaigns $ul LIMIT 1;";
   				$rsltv = mysqli_query($link, $query);

				while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
					$dataCampID = $fresults['campaign_id'];
       					$dataCampName = $fresults['campaign_name'];
                			$dataDialMethod = $fresults['dial_method'];
                			$dataActive = $fresults['active'];
				}
				
				if( $campaign_name == null ) { $uCampaignName = $dataCampName; } else { $uCampaignName = $campaign_name; }
				if( $dial_method == null ) { $uDialMethod = $dataDialMethod;  } else { $uDialMethod = $dial_method;  } 
				if( $active == null ) { $uActive = $dataActive; } else { $uActive = $active; }  

				if($dataCampID != null) {	
					$updateQuery = "UPDATE vicidial_campaigns SET campaign_name='$campaign_name', dial_method='$dial_method', active='$active',auto_dial_level='$auto_dial_level', local_call_time='$local_call_time',campaign_recording='$campaign_recording' WHERE campaign_id='$campaign_id' LIMIT 1;";
					//echo $updateQuery;
			   		$updateResult = mysqli_query($link, $updateQuery);

        ### Admin logs
                                        $SQLdate = date("Y-m-d H:i:s");
                                        $queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY NEW CAMPAIGN $campaign_id','UPDATE vicidial_campaigns SET campaign_name=$uCampaignName, dial_method=$uDialMethod, active=$uActive WHERE campaign_id=$dataCampID LIMIT 1;');";
                                        $rsltvLog = mysqli_query($linkgo, $queryLog);

					$apiresults = array("result" => "success");
				} else {
					$apiresults = array("result" => "Error: Campaign doens't exist.");
				}
			}
		}
	}//end




/* outbound
UPDATE vicidial_campaigns SET campaign_name='Outbound Campaign - 2015-06-03',  dial_method='MANUAL', campaign_description='', campaign_changedate='2015-06-03 21:03:31', auto_dial_level='0',  campaign_script='', campaign_cid='5164536886',  campaign_recording='NEVER',  web_form_address='', campaign_vdad_exten='8369',  local_call_time='9am-9pm', dial_prefix='8888goautodial', active='N' WHERE campaign_id='00000000'
*/

/*inbound
UPDATE vicidial_campaigns SET campaign_name='Test inbound me',  dial_method='RATIO', campaign_description='none', campaign_changedate='2015-06-03 21:08:40', auto_dial_level='1.0',  campaign_script='', campaign_cid='5164536886',  campaign_recording='ALLFORCE',  web_form_address='', campaign_vdad_exten='8368',  local_call_time='9am-9pm', dial_prefix='8888goautodial', active='Y' WHERE campaign_id='000AAA'

/*blended
UPDATE vicidial_campaigns SET campaign_name='Blended Campaign - test',  dial_method='RATIO', campaign_description='none', campaign_changedate='2015-06-03 21:11:56', auto_dial_level='1.0',  campaign_script='', campaign_cid='5164536886',  campaign_recording='ALLFORCE',  web_form_address='', campaign_vdad_exten='8369',  local_call_time='9am-9pm', dial_prefix='8888goautodial', active='Y' WHERE campaign_id='000AAA'

*/






?>
