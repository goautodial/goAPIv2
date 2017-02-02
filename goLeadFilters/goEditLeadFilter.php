<?php
   ####################################################
   #### Name: goEditLeadFilter.php                 ####
   #### Description: API to edit Lead Filter 	   ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("goFunctions.php");
 
    ### POST or GET Variables
        $lead_filter_id = $_REQUEST['lead_filter_id'];
        $lead_filter_name = $_REQUEST['lead_filter_name'];
        $lead_filter_comments = $_REQUEST['lead_filter_comments'];
        $lead_filter_sql = $_REQUEST['lead_filter_sql'];
        $user_group = $_REQUEST['user_group'];
		
        $ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
        $log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
        $log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);


    ### ERROR CHECKING ...
      if($lead_filter_id == null) { 
                $apiresults = array("result" => "Error: Set a value for Lead Filter ID."); 
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filter_name)){
                $apiresults = array("result" => "Error: Special characters found in lead filter name");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $lead_filr_comments)){
                $apiresults = array("result" => "Error: Special characters found in lead filter comments");
        } else {

	$lead_filter_id = mysqli_real_escape_string($link, $lead_filter_id);
		
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
                }

                        $queryCheck = "SELECT lead_filter_id, lead_filter_name, lead_filter_comments, lead_filter_sql, user_group from vicidial_lead_filters where lead_filter_id='$lead_filter_id' $ul $addedSQL;";
                        $sqlCheck = mysqli_query($link, $queryCheck);

                				while($fresults = mysqli_fetch_array($sqlCheck, MYSQLI_ASSOC)){
					$dataLF_id = $fresults['lead_filter_id'];
					$dataLF_name = $fresults['lead_filter_name'];
					$dataLF_comments = $fresults['lead_filter_comments'];
					$dataLF_sql = $fresults['lead_filter_sql'];
					$dataLF_ug = $fresults['user_group'];
				  
				}
                $countLF = mysqli_num_rows($sqlCheck);
/*
                                $fields = $this->db->list_fields('vicidial_list');
                                $fields_to_filter[''] = "--- SELECT A FIELD ---";
                                foreach ($fields as $field)
                                {
                                        if (preg_match("/entry_date|modify_date|gmt_offset_now|phone_number|state|phone_code|called_count/", $field))
                                        {
                                                switch ($field)
                                                {
                                                        case "entry_date":
                                                                $field_name = "Date Uploaded";
                                                                break;
                                                        case "modify_date":
                                                                $field_name = "Date Modified";
                                                                break;
                                                        case "gmt_offset_now":
                                                                $field_name = "Timezone";
                                                                break;
                                                        case "phone_number":
                                                                $field_name = "Area Code";
                                                                break;
                                                        case "state":
                                                                $field_name = "State";
                                                                break;
                                                        case "phone_code":
                                                                $field_name = "Country Code";
                                                                break;
                                                        case "called_count":
                                                                $field_name = "Called Count";
                                                                break;
                                                }
                                                $fields_to_filter[$field] = "$field_name";
                                        }
                                }

                                $filter_options = $this->go_campaign->go_get_filter_options();
                                $data['countrycodes'] = $filter_options['countrycodes'];
                                $data['areacodes'] = $filter_options['areacodes'];
                                $data['states'] = $filter_options['states'];
                                $data['fields_to_filter'] = $fields_to_filter;

                                $query = $this->db->query("SELECT user_group,group_name FROM vicidial_user_groups $addedSQL");
                                $user_groups['---ALL---'] = "--- ALL USER GROUPS ---";
                                foreach ($query->result() as $group)
                                {
                                        $user_groups[$group->user_group] = "{$group->user_group} - {$group->group_name}";
                                }
                                $data['user_groups'] = $user_groups;
*/

                if($countLF > 0) {
		
			if($lead_filter_id == null){$lead_filter_id = $dataLF_id;}
			if($lead_filter_name == null){$lead_filter_name = $dataLF_name;}
			if($lead_filter_comments == null){$lead_filter_comments = $dataLF_comments;}
			if($lead_filter_sql == null){$lead_filter_sql = $dataLF_sql;}
			if($user_group == null){$user_group = $dataLF_ug;}

			$queryVM ="UPDATE vicidial_lead_filters SET lead_filter_name='".mysqli_real_escape_string($link, $lead_filter_name)."',  lead_filter_comments='".mysqli_real_escape_string($link, $lead_filter_comments)."',  lead_filter_sql='".mysqli_real_escape_string($link, $lead_filter_sql)."',  user_group='".mysqli_real_escape_string($link, $user_group)."' WHERE lead_filter_id='".mysqli_real_escape_string($link, $lead_filter_id)."'";
                        $rsltv1 = mysqli_query($link, $queryVM);
                        

					if($rsltv1 == false){
						$apiresults = array("result" => "Error: Try updating Lead Filter Again");
					} else {
						$apiresults = array("result" => "success");

        ### Admin logs
                                        //$SQLdate = date("Y-m-d H:i:s");
                                        //$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','Modified Lead Filter: $lead_filter_id','UPDATE vicidial_lead_filters SET lead_filter_name=$lead_filter_name,  lead_filter_comments=$lead_filter_comments,  lead_filter_sql=$lead_filter_sql,  user_group=$user_group WHERE lead_filter_id=$lead_filter_id');";
                                        //$rsltvLog = mysqli_query($linkgo, $queryLog);
						$log_id = log_action($linkgo, 'MODIFY', $log_user, $ip_address, "Modified Lead Filter ID: $lead_filter_id", $log_group, $queryVM);


					}

                                       
			} else {
				$apiresults = array("result" => "Error: Lead Filter doesn't exist", "count" => $countLF);
				}
}}
}
?>
