<?php
   ####################################################
   #### Name: goEditUserGroup.php                  ####
   #### Description: API to edit user group	   ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
   #### Written by: Jeremiah Sebastian V. Samatra  ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include_once ("../goFunctions.php");
 
    ### POST or GET Variables

    $ip_address = $_REQUEST['hostname'];
    $user_group = $_REQUEST['user_group'];
    $group_name = $_REQUEST['group_name'];
    $group_level = $_REQUEST['group_level'];
    $forced_timeclock_login = strtoupper($_REQUEST['forced_timeclock_login']);
    $shift_enforcement = strtoupper($_REQUEST['shift_enforcement']);
    $values = $_REQUEST['items'];
   //user_group, group_name, group_level, forced_timeclock_login, shift_enforcement
    ### Default values 
    $defFTL = array('Y','N','ADMIN_EXEMPT');
    $defSE = array('OFF','START','ALL');
########################
        if($user_group == null) {
                $apiresults = array("result" => "Error: Set a value for User Group.");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $user_group)){
                $apiresults = array("result" => "Error: Special characters found in user_group");
        } else {
        if(preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $group_name)){
                $apiresults = array("result" => "Error: Special characters found in group_name");
        } else {
                if($group_level < 1 && $group_level != null || $group_level > 9 && $group_level != null) {
                        $apiresults = array("result" => "Error: Group Level Value should be in between 1 and 9");
                } else {

                if(!in_array($forced_timeclock_login,$defFTL) && $forced_timeclock_login != null) {
                        $apiresults = array("result" => "Error: Default value for aforced_timeclock_login is Y, N or ADMIN_EXEMPT only.");
                } else {
                if(!in_array($shift_enforcement,$defSE) && $shift_enforcement != null) {
                        $apiresults = array("result" => "Error: Default value for shift_enforcement is OFF, START or ALL only.");
                } else {
                        #$items = explode("&",str_replace(";","",$this->input->post("items")));
                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "WHERE user_group='$user_group'";
                        $ulUser = "AND user='$user'";
                } else {
                        $ul = "WHERE user_group='$user_group' AND user_group='$groupId'";
                        $ulUser = "AND user='$user' AND user_group='$groupId'";
                }

                $query = "SELECT user_group, group_name, forced_timeclock_login, shift_enforcement FROM vicidial_user_groups $ul ORDER BY user_group LIMIT 1;";
                $rsltv = mysqli_query($link, $query);
				$countResult = mysqli_num_rows($rsltv);
					if($countResult > 0) {
						   
	/*
									$postItem = rtrim($values,"&");
									$items = explode("&",str_replace(";","",$postItem));
									foreach ($items as $item)
									{
											list($var,$val) = split("=",$item);
											if (strlen($val) > 0)
											{ 
					
							if($var=="group_level"){
								$group_level = "$val";
							} else {
													$var = str_replace('[]','',$var);
													if ($var!="user_group" && $var!="allowed_campaigns" && $var!="agent_status_viewable_groups" && $var!="allowed_reports" && $var!="admin_viewable_groups" && $var!="admin_viewable_call_times")
															$itemSQL .= "$var='".str_replace('+',' ',mysql_real_escape_string($val))."', ";
	
													if ($var=="user_group"){
															$group="$val";
															$user_group['user_group'] = $val;
													}
	
													if ($var=="allowed_campaigns")
															$allowed_campaignsSQL .= "$val ";
	
													if ($var=="agent_status_viewable_groups")
															$viewable_groupsSQL .= "$val ";
	
													if ($var=="allowed_reports")
															$allowed_reportsSQL .= str_replace('+',' ',$val).", ";
													if ($var=="admin_viewable_groups")
															$Aviewable_groupsSQL .= "$val ";
	
													if ($var=="admin_viewable_call_times")
															$viewable_calltimesSQL .= "$val ";
							}
											}
									}
									$itemSQL = rtrim($itemSQL,', ');
									$allowed_reportsSQL = substr($allowed_reportsSQL,0,(strlen($allowed_reportsSQL)-2));
									$otherSQL = ", allowed_campaigns=' $allowed_campaignsSQL-', agent_status_viewable_groups=' $viewable_groupsSQL', allowed_reports='$allowed_reportsSQL', admin_viewable_groups=' $Aviewable_groupsSQL', admin_viewable_call_times=' $viewable_calltimesSQL'";
									*/
									 //user_group, group_name, group_level, forced_timeclock_login, shift_enforcement
									/* if($group_name == null){$group_name = $datagroup_name;} if($forced_timeclock_login == null){$forced_timeclock_login = $dataforced_timeclock_login;} if($shift_enforcement == null){$shift_enforcement = $datashift_enforcement;}*/
						$query = "UPDATE vicidial_user_groups SET group_name='$group_name', forced_timeclock_login='$forced_timeclock_login', shift_enforcement='$shift_enforcement' WHERE user_group='$user_group';";
						$rsltvQuery = mysqli_query($link, $query);
		
						$queryGL = "UPDATE user_access_group SET group_level = '$group_level' WHERE user_group='$user_group';";
						$rsltvGL = mysqli_query($linkgo, $queryGL);
		
						if($rsltvQuery == false){
							$apiresults = array("result" => "Error: Failed Update, Check your details");
						} else {
								$SQLdate = date("Y-m-d H:i:s");
								$queryLog = "INSERT INTO go_action_logs (user,ip_address,event_date,action,details,db_query) values('$goUser','$ip_address','$SQLdate','MODIFY','MODIFY USER GROUP $group','UPDATE vicidial_user_groups SET group_level=$group_level,group_name=$group_name,forced_timeclock_login=$forced_timeclock_login,shift_enforcement=$shift_enforcement  WHERE user_group=$group;');";
								$rsltvLog = mysqli_query($linkgo, $queryLog);
		
							$apiresults = array("result" => "success", "query" => $query);
						}
					} else {
						$apiresults = array("result" => "Error: User Group doesn't exist".$query);
					}
		}}
		}}
#########################
                               /* if ($this->db->affected_rows())
                                {
                                    $this->commonhelper->auditadmin("MODIFY","MODIFY USER GROUP $group","UPDATE vicidial_user_groups SET $itemSQL$otherSQL WHERE user_group='$group';");
                                    $updated++;
                                }*/
				/*
                                $groupings = array_merge($user_group,array('permissions'=>$_POST['permiso'],'group_level'=>$_POST['group_level']));
                                if(!$this->go_access->go_check_access_exist($group)){
                                    $this->go_access->goautodialDB->where('user_group',$group);
                                    $this->go_access->goautodialDB->update('user_access_group',$groupings);
                                    if ($this->go_access->goautodialDB->affected_rows()) {
                                        $this->commonhelper->auditadmin("UPDATE","Updated new Group Access: $group");
                                        $updated++;
                                    }
                                }else{
                                    $this->go_access->goautodialDB->insert('user_access_group',$groupings);
                                    if ($this->go_access->goautodialDB->affected_rows()) {
                                        $this->commonhelper->auditadmin("ADD","Add new Group Access: $group");
                                        $updated++;
                                    }
                                }

                                if ($updated) {
                                    $return = "SUCCESS";
                                }*/
}}
?>
