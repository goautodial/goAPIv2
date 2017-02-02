<?php
   ####################################################
   #### Name: goEditList.php                       ####
   #### Description: API to edit specific List     ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables
         $list_id = mysqli_real_escape_string($link, $_REQUEST['list_id']);
         $list_name = mysqli_real_escape_string($link, $_REQUEST['list_name']);
         $list_description = mysqli_real_escape_string($link, $_REQUEST['list_description']);
         $campaign_id = mysqli_real_escape_string($link, $_REQUEST['campaign_id']);
         $active = mysqli_real_escape_string($link, strtoupper($_REQUEST['active']));
         $reset_time = mysqli_real_escape_string($link, $_REQUEST['reset_time']);
         $xferconf_a_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_a_number']);
         $xferconf_b_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_b_number']);
         $xferconf_c_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_c_number']);
         $xferconf_d_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_d_number']);
         $xferconf_e_number = mysqli_real_escape_string($link, $_REQUEST['xferconf_e_number']);
         $agent_script_override = mysqli_real_escape_string($link, $_REQUEST['agent_script_override']);
         $drop_inbound_group_override = mysqli_real_escape_string($link, $_REQUEST['drop_inbound_group_override']);
         $campaign_cid_override = mysqli_real_escape_string($link, $_REQUEST['campaign_cid_override']);
         $web_form_address = mysqli_real_escape_string($link, $_REQUEST['web_form_address']);
         $reset_list = mysqli_real_escape_string($link, strtoupper($_REQUEST['reset_list']));
    	// $values = $_REQUEST['items'];
	 $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
	 $goUser = mysqli_real_escape_string($link, $_REQUEST['goUser']);
	 
	 $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	 $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
   
   
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
        $groupId = go_get_groupid($goUser);
        if (!checkIfTenant($groupId)) {
		$ul = "WHERE campaign_id='$campaign_id'";
                $ulList = "WHERE list_id='$list_id'";
        } else {
		$ul = "WHERE campaign_id='$campaign_id' AND user_group='$groupId'";
                $ulList = "WHERE list_id='$list_id' AND user_group='$groupId'";
        }
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
		$countResult = 0;
		if($campaign_id != null){
                $query = "SELECT campaign_id,campaign_name FROM vicidial_campaigns $ul ORDER BY campaign_id LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
                $countResult = mysqli_num_rows($rsltv);
		}

                $queryList = "SELECT list_id,list_name,list_description,(SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally,active,list_lastcalldate,campaign_id,reset_time from vicidial_lists $ulList order by list_id LIMIT 1";
                $rsltvList = mysqli_query($link, $queryList);
                        while($fresults = mysqli_fetch_array($rsltvList, MYSQLI_ASSOC)){
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

	                $countList = mysqli_num_rows($rsltvList);

			if($reset_list == "Y") {

                		if($countResult > 0) {
                
		           		$queryreset = "UPDATE vicidial_list set called_since_last_reset='N' where list_id='$listid_data';";
					$rsltvreset = mysqli_query($link, $queryreset);
                           		$hopperreset = "DELETE from vicidial_hopper where list_id='$listid_data' and campaign_id='$campaign_id';";
					$rsltvhopper = mysqli_query($link, $hopperreset);
						$apiresults = array("result" => "success");
				} else {
                        		$apiresults = array("result" => "Error: Campaign doesn't exist.");
				}
			} else {
                		if($countList > 0) {

                              /*  $items = $values;
                                foreach (explode("&",$items) as $item)
                                {
                                        list($var,$val) = explode("=",$item,2);
                                        if (strlen($val) > 0)
                                        {

						if ($var=="reset_list"){
							$reset_get = "$val";
						} else {
                                                if ($var!="list_id")
                                                        $itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";

                                                if ($var=="list_id")
                                                        $listid_data="$val";
						}
                                        }
                                }
                                $itemSQL = rtrim($itemSQL,', ');
			    */
		                if($countResult > 0) {
                
				//	$query = "UPDATE vicidial_lists set $itemSQL WHERE list_id='$listid_data';";
				//	$resultQuery = mysql_query($query, $link);

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
					$query = "UPDATE vicidial_lists set list_name = '$list_name', list_description = '$list_description', campaign_id = '$campaign_id', active = '$active', xferconf_a_number = '$xferconf_a_number', xferconf_b_number = '$xferconf_b_number', xferconf_c_number = '$xferconf_c_number', xferconf_d_number = '$xferconf_d_number', xferconf_e_number = '$xferconf_e_number',  agent_script_override = '$agent_script_override', drop_inbound_group_override = '$drop_inbound_group_override', campaign_cid_override = '$campaign_cid_override', web_form_address = '$web_form_address' WHERE list_id='$listid_data';";
					$resultQuery = mysqli_query($link, $query);

	### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY List: $list_id','UPDATE vicidial_lists SET list_id=$list_id,list_name=$list_name,list_description=$list_description,campaign_id=$campaign_id,active=$active,reset_time=$reset_time, xferconf_a_number=$xferconf_a_number,xferconf_b_number=$xferconf_b_number,xferconf_c_number=$xferconf_c_number,xferconf_d_number=$xferconf_d_number,xferconf_e_number=$xferconf_e_number,agent_script_override=$agent_script_override,drop_inbound_group_override=$drop_inbound_group_override,campaign_cid_override=$campaign_cid_override,web_form_address=$web_form_address');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
					$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified List ID: $list_id", $log_group, $query);


					if($resultQuery == false){
						$apiresults = array("result" => "Error: Update failed, check your details.");
					} else {
						$SQLdate = date("Y-m-d H:i:s");
                                		$querydate="UPDATE vicidial_lists SET list_changedate='$SQLdate' WHERE list_id='$listid_data';";
						$resultQueryDate = mysqli_query($link, $querydate);
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
	} }}}}}}}}}}}}
?>
