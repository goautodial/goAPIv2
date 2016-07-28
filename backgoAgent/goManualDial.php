<?php
####################################################
#### Name: goManualDial.php                     ####
#### Type: API for Agent UI                     ####
#### Version: 0.9                               ####
#### Copyright: GOAutoDial Inc. (c) 2011-2016   ####
#### Written by: Christopher P. Lomuntad        ####
#### License: AGPLv2                            ####
####################################################

$agent = get_settings('user', $astDB, $goUser);

$user = $agent->user;
$user_group = $agent->user_group;
$phone_login = (isset($phone_login)) ? $phone_login : $agent->phone_login;
$phone_pass = (isset($phone_pass)) ? $phone_pass : $agent->phone_pass;

### Check if the agent's phone_login is currently connected
$sipIsLoggedIn = check_sip_login($phone_login, $SIPserver);

if ($sipIsLoggedIn) {
    $lead_id = $_POST['lead_id'];

    $MT[0]='';
    $row='';   $rowx='';
    $override_dial_number='';
    $channel_live=1;
    $lead_id = preg_replace("/[^0-9]/","",$lead_id);
    if ( (strlen($conf_exten)<1) || (strlen($campaign)<1)  || (strlen($ext_context)<1) )
        {
        $channel_live=0;
        echo "HOPPER EMPTY\n";
        echo "Conf Exten $conf_exten or campaign $campaign or ext_context $ext_context is not valid\n";
        exit;
        }
    else
    {
    ##### grab number of calls today in this campaign and increment
    $eac_phone='';
    $stmt="SELECT calls_today,extension FROM vicidial_live_agents WHERE user='$user' and campaign_id='$campaign';";
    $rslt=mysqli_query($link, $stmt);
        if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00015',$user,$server_ip,$session_name,$one_mysql_log);}
    if ($DB) {echo "$stmt\n";}
    $vla_cc_ct = mysqli_num_rows($rslt);
    if ($vla_cc_ct > 0)
        {
        $row=mysqli_fetch_row($rslt);
        $calls_today =	$row[0];
        $eac_phone =	$row[1];
        }
    else
        {$calls_today ='0';}
    $calls_today++;
    
    $script_recording_delay=0;
    ##### find if script contains recording fields
    $stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_campaigns vc WHERE campaign_id='$campaign' and vs.script_id=vc.campaign_script and script_text LIKE \"%--A--recording_%\";";
    $rslt=mysqli_query($link, $stmt);
        if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00257',$user,$server_ip,$session_name,$one_mysql_log);}
    if ($DB) {echo "$stmt\n";}
    $vs_vc_ct = mysqli_num_rows($rslt);
    if ($vs_vc_ct > 0)
        {
        $row=mysqli_fetch_row($rslt);
        $script_recording_delay = $row[0];
        }
    
    ### check if this is a callback, if it is, skip the grabbing of a new lead and mark the callback as INACTIVE
    if ( (strlen($callback_id)>0) and (strlen($lead_id)>0) )
        {
        $affected_rows=1;
        $CBleadIDset=1;
    
        $stmt = "UPDATE vicidial_callbacks set status='INACTIVE' where callback_id='$callback_id';";
        if ($DB) {echo "$stmt\n";}
        $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00016',$user,$server_ip,$session_name,$one_mysql_log);}
        }
    ### check if this is a specific lead call, if it is, skip the grabbing of a new lead
    elseif (strlen($lead_id)>0)
        {
        $affected_rows=1;
        $CBleadIDset=1;
    
        if (strlen($phone_number) > 5)
            {$override_dial_number = $phone_number;}
        }
    else
        {
        if (strlen($phone_number)>3)
            {
            if (preg_match("/ENABLED/",$manual_dial_call_time_check))
                {
                $secX = date("U");
                $hour = date("H");
                $min = date("i");
                $sec = date("s");
                $mon = date("m");
                $mday = date("d");
                $year = date("Y");
                $isdst = date("I");
                $Shour = date("H");
                $Smin = date("i");
                $Ssec = date("s");
                $Smon = date("m");
                $Smday = date("d");
                $Syear = date("Y");
                $pulldate0 = "$year-$mon-$mday $hour:$min:$sec";
                $inSD = $pulldate0;
                $dsec = ( ( ($hour * 3600) + ($min * 60) ) + $sec );
    
                $postalgmt='';
                $postal_code='';
                $state='';
                if (strlen($phone_code)<1)
                    {$phone_code='1';}
    
                $local_call_time='24hours';
                ##### gather local call time setting from campaign
                $stmt="SELECT local_call_time FROM vicidial_campaigns where campaign_id='$campaign';";
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00353',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $camp_lct_ct = mysqli_num_rows($rslt);
                if ($camp_lct_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $local_call_time =			$row[0];
                    }
    
                ### get current gmt_offset of the phone_number
                $USarea = substr($phone_number, 0, 3);
                $gmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code);
    
                $dialable = dialable_gmt($DB,$link,$local_call_time,$gmt_offset,$state);
                
                if ($dialable < 1)
                    {
                    ### purge from the dial queue and api
                    $stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00354',$user,$server_ip,$session_name,$one_mysql_log);}
                    $VMDQaffected_rows = mysqli_affected_rows($link);
    
                    $stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00355',$user,$server_ip,$session_name,$one_mysql_log);}
                    $VLAEDaffected_rows = mysqli_affected_rows($link);
    
                    echo "OUTSIDE OF LOCAL CALL TIME   $VMDQaffected_rows|$VLAEDaffected_rows\n";
                    exit;
                    }
                }
    
            if (preg_match("/DNC/",$manual_dial_filter))
                {
                if (preg_match("/AREACODE/",$use_internal_dnc))
                    {
                    $phone_number_areacode = substr($phone_number, 0, 3);
                    $phone_number_areacode .= "XXXXXXX";
                    $stmt="SELECT count(*) from vicidial_dnc where phone_number IN('$phone_number','$phone_number_areacode');";
                    }
                else
                    {$stmt="SELECT count(*) FROM vicidial_dnc where phone_number='$phone_number';";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00017',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $row=mysqli_fetch_row($rslt);
                if ($row[0] > 0)
                    {
                    ### purge from the dial queue and api
                    $stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00356',$user,$server_ip,$session_name,$one_mysql_log);}
                    $VMDQaffected_rows = mysqli_affected_rows($link);
    
                    $stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00357',$user,$server_ip,$session_name,$one_mysql_log);}
                    $VLAEDaffected_rows = mysqli_affected_rows($link);
    
                    echo "DNC NUMBER\n";
                    exit;
                    }
                if ( (preg_match("/Y/",$use_campaign_dnc)) or (preg_match("/AREACODE/",$use_campaign_dnc)) )
                    {
                    $stmt="SELECT use_other_campaign_dnc from vicidial_campaigns where campaign_id='$campaign';";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00445',$user,$server_ip,$session_name,$one_mysql_log);}
                    $row=mysqli_fetch_row($rslt);
                    $use_other_campaign_dnc =	$row[0];
                    $temp_campaign_id = $campaign;
                    if (strlen($use_other_campaign_dnc) > 0) {$temp_campaign_id = $use_other_campaign_dnc;}
    
                    if (preg_match("/AREACODE/",$use_campaign_dnc))
                        {
                        $phone_number_areacode = substr($phone_number, 0, 3);
                        $phone_number_areacode .= "XXXXXXX";
                        $stmt="SELECT count(*) from vicidial_campaign_dnc where phone_number IN('$phone_number','$phone_number_areacode') and campaign_id='$temp_campaign_id';";
                        }
                    else
                        {$stmt="SELECT count(*) FROM vicidial_campaign_dnc where phone_number='$phone_number' and campaign_id='$temp_campaign_id';";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00018',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $row=mysqli_fetch_row($rslt);
                    if ($row[0] > 0)
                        {
                        ### purge from the dial queue and api
                        $stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00358',$user,$server_ip,$session_name,$one_mysql_log);}
                        $VMDQaffected_rows = mysqli_affected_rows($link);
    
                        $stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00359',$user,$server_ip,$session_name,$one_mysql_log);}
                        $VLAEDaffected_rows = mysqli_affected_rows($link);
    
                        echo "DNC NUMBER\n";
                        exit;
                        }
                    }
                }
            if (preg_match("/CAMPLISTS/",$manual_dial_filter))
                {
                $stmt="SELECT list_id,active from vicidial_lists where campaign_id='$campaign'";
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00019',$user,$server_ip,$session_name,$one_mysql_log);}
                $lists_to_parse = mysqli_num_rows($rslt);
                $camp_lists='';
                $o=0;
                while ($lists_to_parse > $o) 
                    {
                    $rowx=mysqli_fetch_row($rslt);
                    if (preg_match("/Y/", $rowx[1])) {$active_lists++;   $camp_lists .= "'$rowx[0]',";}
                    if (preg_match("/ALL/",$manual_dial_filter))
                        {
                        if (preg_match("/N/", $rowx[1])) 
                            {$inactive_lists++; $camp_lists .= "'$rowx[0]',";}
                        }
                    else
                        {
                        if (preg_match("/N/", $rowx[1])) 
                            {$inactive_lists++;}
                        }
                    $o++;
                    }
                $camp_lists = preg_replace("/.$/i","",$camp_lists);
    
                $stmt="SELECT count(*) FROM vicidial_list where phone_number='$phone_number' and list_id IN($camp_lists);";
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00020',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $row=mysqli_fetch_row($rslt);
                
                if ($row[0] < 1)
                    {
                    ### purge from the dial queue and api
                    $stmt = "DELETE from vicidial_manual_dial_queue where phone_number='$phone_number' and user='$user';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00360',$user,$server_ip,$session_name,$one_mysql_log);}
                    $VMDQaffected_rows = mysqli_affected_rows($link);
    
                    $stmt = "UPDATE vicidial_live_agents set external_dial='' where user='$user';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00361',$user,$server_ip,$session_name,$one_mysql_log);}
                    $VLAEDaffected_rows = mysqli_affected_rows($link);
    
                    echo "NUMBER NOT IN CAMPLISTS\n";
                    exit;
                    }
                }
            if ($stage=='lookup')
                {
                if (strlen($vendor_lead_code)>0)
                    {
                    $stmt="SELECT lead_id FROM vicidial_list where vendor_lead_code='$vendor_lead_code' order by modify_date desc LIMIT 1;";
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00021',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $man_leadID_ct = mysqli_num_rows($rslt);
                    if ( ($man_leadID_ct > 0) and (strlen($phone_number) > 5) )
                        {$override_phone++;}
                    }
                else
                {
                    // Added a script to fetch the tenant id and it's allowed campaigns -- Chris Lomuntad <chris@goautodial.com>
                    $stmt="SELECT TRIM(TRIM(TRAILING '-' FROM allowed_campaigns)) FROM vicidial_user_groups AS vug, vicidial_users AS vu WHERE vu.user='$user' AND vug.user_group=vu.user_group;";
                    $rslt=mysqli_query($link, $stmt);
                    $Xrow=mysqli_fetch_row($rslt);
                    $allowed_campaigns = str_replace(" ","','",$Xrow[0]);
                    
                    // Get allowed campaigns and list ids for the tenant
                    $stmt="SELECT list_id FROM vicidial_lists AS vl, vicidial_campaigns AS vc WHERE vc.campaign_id IN ('$allowed_campaigns') AND vl.campaign_id=vc.campaign_id;";
                    $rslt=mysqli_query($link, $stmt);
                    $Xct=mysqli_num_rows($rslt);
                    
                    if ($Xct > 0) {
                        for ($i=0;$i<$Xct;$i++)
                        {
                            $Xrow = mysql_fetch_row($rslt);
                            $list_ids[$i] = $Xrow[0];
                        }
                        $list_ids = implode("','",$list_ids);
                        $list_idSQL = "AND list_id IN ('$list_ids')";
                    }
                    
                    $stmt="SELECT lead_id FROM vicidial_list where phone_number='$phone_number' $list_idSQL order by modify_date desc LIMIT 1;";
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00362',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $man_leadID_ct = mysqli_num_rows($rslt);
                    }
                if ($man_leadID_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $affected_rows=1;
                    $lead_id =$row[0];
                    $CBleadIDset=1;
                    }
                else
                    {
                    ### insert a new lead in the system with this phone number
                    $stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME',vendor_lead_code='$vendor_lead_code';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00022',$user,$server_ip,$session_name,$one_mysql_log);}
                    $affected_rows = mysqli_affected_rows($link);
                    $lead_id = mysqli_insert_id($link);
                    $CBleadIDset=1;
                    }
                }
            else
                {
                ### insert a new lead in the system with this phone number
                $stmt = "INSERT INTO vicidial_list SET phone_code='$phone_code',phone_number='$phone_number',list_id='$list_id',status='QUEUE',user='$user',called_since_last_reset='Y',entry_date='$ENTRYdate',last_local_call_time='$NOW_TIME',vendor_lead_code='$vendor_lead_code';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00023',$user,$server_ip,$session_name,$one_mysql_log);}
                $affected_rows = mysqli_affected_rows($link);
                $lead_id = mysqli_insert_id($link);
                $CBleadIDset=1;
                }
            }
        else
            {
            ##### gather no hopper dialing settings from campaign
            $stmt="SELECT no_hopper_dialing,agent_dial_owner_only,local_call_time,dial_statuses,drop_lockout_time,lead_filter_id,lead_order,lead_order_randomize,lead_order_secondary,call_count_limit FROM vicidial_campaigns where campaign_id='$campaign';";
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00236',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $camp_nohopper_ct = mysqli_num_rows($rslt);
            if ($camp_nohopper_ct > 0)
                {
                $row=mysqli_fetch_row($rslt);
                $no_hopper_dialing =		$row[0];
                $agent_dial_owner_only =	$row[1];
                $local_call_time =			$row[2];
                $dial_statuses =			$row[3];
                $drop_lockout_time =		$row[4];
                $lead_filter_id =			$row[5];
                $lead_order =				$row[6];
                $lead_order_randomize =		$row[7];
                $lead_order_secondary =		$row[8];
                $call_count_limit =			$row[9];
                }
            if (preg_match("/N/i",$no_hopper_dialing))
                {
                ### grab the next lead in the hopper for this campaign and reserve it for the user
                $stmt = "UPDATE vicidial_hopper set status='QUEUE', user='$user' where campaign_id='$campaign' and status='READY' order by priority desc,hopper_id LIMIT 1";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00024',$user,$server_ip,$session_name,$one_mysql_log);}
                $affected_rows = mysqli_affected_rows($link);
                }
            else
                {
                ### figure out what the next lead that should be dialed is
    
                ##########################################################
                ### BEGIN find the next lead to dial without looking in the hopper
                ##########################################################
            #	$DB=1;
                if (strlen($dial_statuses)>2)
                    {
                    $g=0;
                    $p='13';
                    $GMT_gmt[0] = '';
                    $GMT_hour[0] = '';
                    $GMT_day[0] = '';
                    while ($p > -13)
                        {
                        $pzone=3600 * $p;
                        $pmin=(gmdate("i", time() + $pzone));
                        $phour=( (gmdate("G", time() + $pzone)) * 100);
                        $pday=gmdate("w", time() + $pzone);
                        $tz = sprintf("%.2f", $p);	
                        $GMT_gmt[$g] = "$tz";
                        $GMT_day[$g] = "$pday";
                        $GMT_hour[$g] = ($phour + $pmin);
                        $p = ($p - 0.25);
                        $g++;
                        }
    
                    $stmt="SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times FROM vicidial_call_times where call_time_id='$local_call_time';";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00237',$user,$server_ip,$session_name,$one_mysql_log);}
                    $rowx=mysqli_fetch_row($rslt);
                    $Gct_default_start =	$rowx[3];
                    $Gct_default_stop =		$rowx[4];
                    $Gct_sunday_start =		$rowx[5];
                    $Gct_sunday_stop =		$rowx[6];
                    $Gct_monday_start =		$rowx[7];
                    $Gct_monday_stop =		$rowx[8];
                    $Gct_tuesday_start =	$rowx[9];
                    $Gct_tuesday_stop =		$rowx[10];
                    $Gct_wednesday_start =	$rowx[11];
                    $Gct_wednesday_stop =	$rowx[12];
                    $Gct_thursday_start =	$rowx[13];
                    $Gct_thursday_stop =	$rowx[14];
                    $Gct_friday_start =		$rowx[15];
                    $Gct_friday_stop =		$rowx[16];
                    $Gct_saturday_start =	$rowx[17];
                    $Gct_saturday_stop =	$rowx[18];
                    $Gct_state_call_times = $rowx[19];
    
                    $ct_states = '';
                    $ct_state_gmt_SQL = '';
                    $ct_srs=0;
                    $b=0;
                    if (strlen($Gct_state_call_times)>2)
                        {
                        $state_rules = explode('|',$Gct_state_call_times);
                        $ct_srs = ((count($state_rules)) - 2);
                        }
                    while($ct_srs >= $b)
                        {
                        if (strlen($state_rules[$b])>1)
                            {
                            $stmt="SELECT state_call_time_id,state_call_time_state,state_call_time_name,state_call_time_comments,sct_default_start,sct_default_stop,sct_sunday_start,sct_sunday_stop,sct_monday_start,sct_monday_stop,sct_tuesday_start,sct_tuesday_stop,sct_wednesday_start,sct_wednesday_stop,sct_thursday_start,sct_thursday_stop,sct_friday_start,sct_friday_stop,sct_saturday_start,sct_saturday_stop from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
                            $rslt=mysqli_query($link, $stmt);
                            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00238',$user,$server_ip,$session_name,$one_mysql_log);}
                            $row=mysqli_fetch_row($rslt);
                            $Gstate_call_time_id =		$row[0];
                            $Gstate_call_time_state =	$row[1];
                            $Gsct_default_start =		$row[4];
                            $Gsct_default_stop =		$row[5];
                            $Gsct_sunday_start =		$row[6];
                            $Gsct_sunday_stop =			$row[7];
                            $Gsct_monday_start =		$row[8];
                            $Gsct_monday_stop =			$row[9];
                            $Gsct_tuesday_start =		$row[10];
                            $Gsct_tuesday_stop =		$row[11];
                            $Gsct_wednesday_start =		$row[12];
                            $Gsct_wednesday_stop =		$row[13];
                            $Gsct_thursday_start =		$row[14];
                            $Gsct_thursday_stop =		$row[15];
                            $Gsct_friday_start =		$row[16];
                            $Gsct_friday_stop =			$row[17];
                            $Gsct_saturday_start =		$row[18];
                            $Gsct_saturday_stop =		$row[19];
    
                            $ct_states .="'$Gstate_call_time_state',";
    
                            $r=0;
                            $state_gmt='';
                            while($r < $g)
                                {
                                if ($GMT_day[$r]==0)	#### Sunday local time
                                    {
                                    if (($Gsct_sunday_start==0) and ($Gsct_sunday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_sunday_start) and ($GMT_hour[$r]<$Gsct_sunday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                if ($GMT_day[$r]==1)	#### Monday local time
                                    {
                                    if (($Gsct_monday_start==0) and ($Gsct_monday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_monday_start) and ($GMT_hour[$r]<$Gsct_monday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                if ($GMT_day[$r]==2)	#### Tuesday local time
                                    {
                                    if (($Gsct_tuesday_start==0) and ($Gsct_tuesday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_tuesday_start) and ($GMT_hour[$r]<$Gsct_tuesday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                if ($GMT_day[$r]==3)	#### Wednesday local time
                                    {
                                    if (($Gsct_wednesday_start==0) and ($Gsct_wednesday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_wednesday_start) and ($GMT_hour[$r]<$Gsct_wednesday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                if ($GMT_day[$r]==4)	#### Thursday local time
                                    {
                                    if (($Gsct_thursday_start==0) and ($Gsct_thursday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_thursday_start) and ($GMT_hour[$r]<$Gsct_thursday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                if ($GMT_day[$r]==5)	#### Friday local time
                                    {
                                    if (($Gsct_friday_start==0) and ($Gsct_friday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_friday_start) and ($GMT_hour[$r]<$Gsct_friday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                if ($GMT_day[$r]==6)	#### Saturday local time
                                    {
                                    if (($Gsct_saturday_start==0) and ($Gsct_saturday_stop==0))
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_default_start) and ($GMT_hour[$r]<$Gsct_default_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    else
                                        {
                                        if ( ($GMT_hour[$r]>=$Gsct_saturday_start) and ($GMT_hour[$r]<$Gsct_saturday_stop) )
                                            {$state_gmt.="'$GMT_gmt[$r]',";}
                                        }
                                    }
                                $r++;
                                }
                            $state_gmt = "$state_gmt'99'";
                            $ct_state_gmt_SQL .= "or (state='$Gstate_call_time_state' and gmt_offset_now IN($state_gmt)) ";
                            }
    
                        $b++;
                        }
                    if (strlen($ct_states)>2)
                        {
                        $ct_states = preg_replace("/,$/i",'',$ct_states);
                        $ct_statesSQL = "and state NOT IN($ct_states)";
                        }
                    else
                        {
                        $ct_statesSQL = "";
                        }
    
                    $r=0;
                    $default_gmt='';
                    while($r < $g)
                        {
                        if ($GMT_day[$r]==0)	#### Sunday local time
                            {
                            if (($Gct_sunday_start==0) and ($Gct_sunday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_sunday_start) and ($GMT_hour[$r]<$Gct_sunday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        if ($GMT_day[$r]==1)	#### Monday local time
                            {
                            if (($Gct_monday_start==0) and ($Gct_monday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_monday_start) and ($GMT_hour[$r]<$Gct_monday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        if ($GMT_day[$r]==2)	#### Tuesday local time
                            {
                            if (($Gct_tuesday_start==0) and ($Gct_tuesday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_tuesday_start) and ($GMT_hour[$r]<$Gct_tuesday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        if ($GMT_day[$r]==3)	#### Wednesday local time
                            {
                            if (($Gct_wednesday_start==0) and ($Gct_wednesday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_wednesday_start) and ($GMT_hour[$r]<$Gct_wednesday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        if ($GMT_day[$r]==4)	#### Thursday local time
                            {
                            if (($Gct_thursday_start==0) and ($Gct_thursday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_thursday_start) and ($GMT_hour[$r]<$Gct_thursday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        if ($GMT_day[$r]==5)	#### Friday local time
                            {
                            if (($Gct_friday_start==0) and ($Gct_friday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_friday_start) and ($GMT_hour[$r]<$Gct_friday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        if ($GMT_day[$r]==6)	#### Saturday local time
                            {
                            if (($Gct_saturday_start==0) and ($Gct_saturday_stop==0))
                                {
                                if ( ($GMT_hour[$r]>=$Gct_default_start) and ($GMT_hour[$r]<$Gct_default_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            else
                                {
                                if ( ($GMT_hour[$r]>=$Gct_saturday_start) and ($GMT_hour[$r]<$Gct_saturday_stop) )
                                    {$default_gmt.="'$GMT_gmt[$r]',";}
                                }
                            }
                        $r++;
                        }
    
                    $default_gmt = "$default_gmt'99'";
                    $all_gmtSQL = "(gmt_offset_now IN($default_gmt) $ct_statesSQL) $ct_state_gmt_SQL";
    
                    $dial_statuses = preg_replace("/ -$/","",$dial_statuses);
                    $Dstatuses = explode(" ", $dial_statuses);
                    $Ds_to_print = (count($Dstatuses) - 0);
                    $Dsql = '';
                    $o=0;
                    while ($Ds_to_print > $o) 
                        {
                        $o++;
                        $Dsql .= "'$Dstatuses[$o]',";
                        }
                    $Dsql = preg_replace("/,$/","",$Dsql);
                    if (strlen($Dsql) < 2) {$Dsql = "''";}
    
                    $DLTsql='';
                    if ($drop_lockout_time > 0)
                        {
                        $DLseconds = ($drop_lockout_time * 3600);
                        $DLseconds = floor($DLseconds);
                        $DLseconds = intval("$DLseconds");
                        $DLTsql = "and ( ( (status IN('DROP','XDROP')) and (last_local_call_time < CONCAT(DATE_ADD(NOW(), INTERVAL -$DLseconds SECOND),' ',CURTIME()) ) ) or (status NOT IN('DROP','XDROP')) )";
                        }
    
                    $CCLsql='';
                    if ($call_count_limit > 0)
                        {
                        $CCLsql = "and (called_count < $call_count_limit)";
                        }
    
                    $stmt="SELECT lead_filter_sql FROM vicidial_lead_filters where lead_filter_id='$lead_filter_id';";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00239',$user,$server_ip,$session_name,$one_mysql_log);}
                    $filtersql_ct = mysqli_num_rows($rslt);
                    if ($DB) {echo "$filtersql_ct|$stmt\n";}
                    if ($filtersql_ct > 0)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $fSQL = "and ($row[0])";
                        $fSQL = preg_replace('/\\\\/','',$fSQL);
                        }
    
                    $stmt="SELECT list_id FROM vicidial_lists where campaign_id='$campaign' and active='Y';";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00240',$user,$server_ip,$session_name,$one_mysql_log);}
                    $camplists_ct = mysqli_num_rows($rslt);
                    if ($DB) {echo "$camplists_ct|$stmt\n";}
                    $k=0;
                    $camp_lists='';
                    while ($camplists_ct > $k)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $camp_lists .=	"'$row[0]',";
                        $k++;
                        }
                    $camp_lists = preg_replace("/.$/i","",$camp_lists);
                    if (strlen($camp_lists) < 4) {$camp_lists="''";}
    
                    $stmt="SELECT user_group,territory FROM vicidial_users where user='$user';";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00241',$user,$server_ip,$session_name,$one_mysql_log);}
                    $userterr_ct = mysqli_num_rows($rslt);
                    if ($DB) {echo "$userterr_ct|$stmt\n";}
                    if ($userterr_ct > 0)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $user_group =	$row[0];
                        $territory =	$row[1];
                        }
    
                    $adooSQL = '';
                    if (preg_match("/TERRITORY/i",$agent_dial_owner_only)) 
                        {
                        $agent_territories='';
                        $agent_choose_territories=0;
                        $stmt="SELECT agent_choose_territories from vicidial_users where user='$user';";
                        $rslt=mysqli_query($link, $stmt);
                            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00406',$user,$server_ip,$session_name,$one_mysql_log);}
                        $Uterrs_to_parse = mysqli_num_rows($rslt);
                        if ($Uterrs_to_parse > 0) 
                            {
                            $rowx=mysqli_fetch_row($rslt);
                            $agent_choose_territories = $rowx[0];
                            }
    
                        if ($agent_choose_territories < 1)
                            {
                            $stmt="SELECT territory from vicidial_user_territories where user='$user';";
                            $rslt=mysqli_query($link, $stmt);
                                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00407',$user,$server_ip,$session_name,$one_mysql_log);}
                            $vuts_to_parse = mysqli_num_rows($rslt);
                            $o=0;
                            while ($vuts_to_parse > $o) 
                                {
                                $rowx=mysqli_fetch_row($rslt);
                                $agent_territories .= "'$rowx[0]',";
                                $o++;
                                }
                            $agent_territories = preg_replace("/\,$/",'',$agent_territories);
                            $searchownerSQL=" and owner IN($agent_territories)";
                            if ($vuts_to_parse < 1)
                                {$searchownerSQL=" and lead_id < 0";}
                            }
                        else
                            {
                            $stmt="SELECT agent_territories from vicidial_live_agents where user='$user';";
                            $rslt=mysqli_query($link, $stmt);
                                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00408',$user,$server_ip,$session_name,$one_mysql_log);}
                            $terrs_to_parse = mysqli_num_rows($rslt);
                            if ($terrs_to_parse > 0) 
                                {
                                $rowx=mysqli_fetch_row($rslt);
                                $agent_territories = $rowx[0];
                                $agent_territories = preg_replace("/ -$|^ /",'',$agent_territories);
                                $agent_territories = preg_replace("/ /","','",$agent_territories);
                                $searchownerSQL=" and owner IN('$agent_territories')";
                                }
                            }
    
                        $adooSQL = $searchownerSQL;
                        }
                    if (preg_match("/USER/i",$agent_dial_owner_only)) {$adooSQL = "and owner='$user'";}
                    if (preg_match("/USER_GROUP/i",$agent_dial_owner_only)) {$adooSQL = "and owner='$user_group'";}
                    if (preg_match("/_BLANK/",$agent_dial_owner_only))
                        {
                        $adooSQLa = preg_replace("/^and /",'',$adooSQL);
                        $blankSQL = "and ( ($adooSQLa) or (owner='') or (owner is NULL) )";
                        $adooSQL = $blankSQL;
                        }
    
                    if ($lead_order_randomize == 'Y') {$last_order = "RAND()";}
                    else 
                        {
                        $last_order = "lead_id asc";
                        if ($lead_order_secondary == 'LEAD_ASCEND') {$last_order = "lead_id asc";}
                        if ($lead_order_secondary == 'LEAD_DESCEND') {$last_order = "lead_id desc";}
                        if ($lead_order_secondary == 'CALLTIME_ASCEND') {$last_order = "last_local_call_time asc";}
                        if ($lead_order_secondary == 'CALLTIME_DESCEND') {$last_order = "last_local_call_time desc";}
                        }
    
                    $order_stmt = '';
                    if (preg_match("/DOWN/i",$lead_order)){$order_stmt = 'order by lead_id asc';}
                    if (preg_match("/UP/i",$lead_order)){$order_stmt = 'order by lead_id desc';}
                    if (preg_match("/UP LAST NAME/i",$lead_order)){$order_stmt = "order by last_name desc, $last_order";}
                    if (preg_match("/DOWN LAST NAME/i",$lead_order)){$order_stmt = "order by last_name, $last_order";}
                    if (preg_match("/UP PHONE/i",$lead_order)){$order_stmt = "order by phone_number desc, $last_order";}
                    if (preg_match("/DOWN PHONE/i",$lead_order)){$order_stmt = "order by phone_number, $last_order";}
                    if (preg_match("/UP COUNT/i",$lead_order)){$order_stmt = "order by called_count desc, $last_order";}
                    if (preg_match("/DOWN COUNT/i",$lead_order)){$order_stmt = "order by called_count, $last_order";}
                    if (preg_match("/UP LAST CALL TIME/i",$lead_order)){$order_stmt = "order by last_local_call_time desc, $last_order";}
                    if (preg_match("/DOWN LAST CALL TIME/i",$lead_order)){$order_stmt = "order by last_local_call_time, $last_order";}
                    if (preg_match("/RANDOM/i",$lead_order)){$order_stmt = "order by RAND()";}
                    if (preg_match("/UP RANK/i",$lead_order)){$order_stmt = "order by rank desc, $last_order";}
                    if (preg_match("/DOWN RANK/i",$lead_order)){$order_stmt = "order by rank, $last_order";}
                    if (preg_match("/UP OWNER/i",$lead_order)){$order_stmt = "order by owner desc, $last_order";}
                    if (preg_match("/DOWN OWNER/i",$lead_order)){$order_stmt = "order by owner, $last_order";}
                    if (preg_match("/UP TIMEZONE/i",$lead_order)){$order_stmt = "order by gmt_offset_now desc, $last_order";}
                    if (preg_match("/DOWN TIMEZONE/i",$lead_order)){$order_stmt = "order by gmt_offset_now, $last_order";}
    
                    $stmt="UPDATE vicidial_list SET user='QUEUE$user' where called_since_last_reset='N' and user NOT LIKE \"QUEUE%\" and status IN($Dsql) and list_id IN($camp_lists) and ($all_gmtSQL) $CCLsql $DLTsql $fSQL $adooSQL $order_stmt LIMIT 1;";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysql_query($stmt, $link);
                    if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00242',$user,$server_ip,$session_name,$one_mysql_log);}
                    $affected_rows = mysql_affected_rows($link);
    
                #	$fp = fopen ("./DNNdebug_log.txt", "a");
                #	fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$agent_dialed_number|$user|M|$MqueryCID||$province|$affected_rows|$stmt|\n");
                #	fclose($fp);
    
                    if ($affected_rows > 0)
                        {
                        $stmt="SELECT lead_id,list_id,gmt_offset_now,state,entry_list_id,vendor_lead_code FROM vicidial_list where user='QUEUE$user' order by modify_date desc LIMIT 1;";
                        $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00243',$user,$server_ip,$session_name,$one_mysql_log);}
                        if ($DB) {echo "$stmt\n";}
                        $leadpick_ct = mysqli_num_rows($rslt);
                        if ($leadpick_ct > 0)
                            {
                            $row=mysqli_fetch_row($rslt);
                            $lead_id =			$row[0];
                            $list_id =			$row[1];
                            $gmt_offset_now =	$row[2];
                            $state =			$row[3];
                            $entry_list_id =	$row[4];
                            $vendor_lead_code = $row[5];
    
                            $stmt = "INSERT INTO vicidial_hopper SET lead_id='$lead_id',campaign_id='$campaign',status='QUEUE',list_id='$list_id',gmt_offset_now='$gmt_offset_now',state='$state',alt_dial='MAIN',user='$user',priority='0',source='Q',vendor_lead_code='$vendor_lead_code';";
                            if ($DB) {echo "$stmt\n";}
                            $rslt=mysqli_query($link, $stmt);
                            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00244',$user,$server_ip,$session_name,$one_mysql_log);}
                            }
                        }
                    }
                ##########################################################
                ### END  find the next lead to dial without looking in the hopper
                ##########################################################
            #	$DB=0;
                }
            }
        }
    
    if ($affected_rows > 0)
        {
        if (!$CBleadIDset)
            {
            ##### grab the lead_id of the reserved user in vicidial_hopper
            $stmt="SELECT lead_id FROM vicidial_hopper where campaign_id='$campaign' and status='QUEUE' and user='$user' LIMIT 1;";
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00025',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $hopper_leadID_ct = mysqli_num_rows($rslt);
            if ($hopper_leadID_ct > 0)
                {
                $row=mysqli_fetch_row($rslt);
                $lead_id =$row[0];
                }
            }
    
            ##### grab the data from vicidial_list for the lead_id
            $stmt="SELECT lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id FROM vicidial_list where lead_id='$lead_id' LIMIT 1;";
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00026',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $list_lead_ct = mysqli_num_rows($rslt);
            if ($list_lead_ct > 0)
                {
                $row=mysql_fetch_row($rslt);
            #	$lead_id		= trim("$row[0]");
                $dispo			= trim("$row[3]");
                $tsr			= trim("$row[4]");
                $vendor_id		= trim("$row[5]");
                $source_id		= trim("$row[6]");
                $list_id		= trim("$row[7]");
                $gmt_offset_now	= trim("$row[8]");
                $called_since_last_reset = trim("$row[9]");
                $phone_code		= trim("$row[10]");
                if ($override_phone < 1)
                    {$phone_number	= trim("$row[11]");}
                $title			= trim("$row[12]");
                $first_name		= trim("$row[13]");
                $middle_initial	= trim("$row[14]");
                $last_name		= trim("$row[15]");
                $address1		= trim("$row[16]");
                $address2		= trim("$row[17]");
                $address3		= trim("$row[18]");
                $city			= trim("$row[19]");
                $state			= trim("$row[20]");
                $province		= trim("$row[21]");
                $postal_code	= trim("$row[22]");
                $country_code	= trim("$row[23]");
                $gender			= trim("$row[24]");
                $date_of_birth	= trim("$row[25]");
                $alt_phone		= trim("$row[26]");
                $email			= trim("$row[27]");
                $security		= trim("$row[28]");
                $comments		= stripslashes(trim("$row[29]"));
                $called_count	= trim("$row[30]");
                $rank			= trim("$row[32]");
                $owner			= trim("$row[33]");
                $entry_list_id	= trim("$row[34]");
                    if ($entry_list_id < 100) {$entry_list_id = $list_id;}
                }
            if ($qc_features_active > 0)
                {
                //Added by Poundteam for Audited Comments
                ##### if list has audited comments, grab the audited comments
                require_once('audit_comments.php');
                $ACcount =		'';
                $ACcomments =		'';
                $audit_comments_active=audit_comments_active($list_id,$format,$user,$mel,$NOW_TIME,$link,$server_ip,$session_name,$one_mysql_log);
                if ($audit_comments_active)
                    {
                    get_audited_comments($lead_id,$format,$user,$mel,$NOW_TIME,$link,$server_ip,$session_name,$one_mysql_log);
                    }
                $ACcomments = strip_tags(htmlentities($ACcomments));
                $ACcomments = preg_replace("/\r/i",'',$ACcomments);
                $ACcomments = preg_replace("/\n/i",'!N',$ACcomments);
                //END Added by Poundteam for Audited Comments
                }
    
            $called_count++;
    
            if ( (strlen($agent_dialed_type) < 3) or (strlen($agent_dialed_number) < 6) )
                {
                $agent_dialed_number = $phone_number;
                if (strlen($agent_dialed_type) < 3)
                    {$agent_dialed_type = 'MAIN';}
                }
            if ( (strlen($callback_id)>0) and (strlen($lead_id)>0) )
                {
                if ($agent_dialed_type=='ALT')
                    {$agent_dialed_number = $alt_phone;}
                if ($agent_dialed_type=='ADDR3')
                    {$agent_dialed_number = $address3;}
                }
    
    
            ##### BEGIN check for postal_code and phone time zones if alert enabled
            $post_phone_time_diff_alert_message='';
            $stmt="SELECT post_phone_time_diff_alert,local_call_time,owner_populate FROM vicidial_campaigns where campaign_id='$campaign';";
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00414',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $camp_pptda_ct = mysqli_num_rows($rslt);
            if ($camp_pptda_ct > 0)
                {
                $row=mysqli_fetch_row($rslt);
                $post_phone_time_diff_alert =	$row[0];
                $local_call_time =				$row[1];
                $owner_populate =				$row[2];
                }
            if ( ($post_phone_time_diff_alert == 'ENABLED') or (preg_match("/OUTSIDE_CALLTIME/",$post_phone_time_diff_alert)) )
                {
                ### get current gmt_offset of the phone_number
                $postalgmtNOW = '';
                $USarea = substr($agent_dialed_number, 0, 3);
                $PHONEgmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmtNOW,$postal_code);
                $PHONEdialable = dialable_gmt($DB,$link,$local_call_time,$PHONEgmt_offset,$state);
    
                $postalgmtNOW = 'POSTAL';
                $POSTgmt_offset = lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmtNOW,$postal_code);
                $POSTdialable = dialable_gmt($DB,$link,$local_call_time,$POSTgmt_offset,$state);
    
            #	$post_phone_time_diff_alert_message = "$POSTgmt_offset|$POSTdialable   ---   $PHONEgmt_offset|$PHONEdialable|$USarea";
                $post_phone_time_diff_alert_message = '';
    
                if ($PHONEgmt_offset != $POSTgmt_offset)
                    {
                    $post_phone_time_diff_alert_message .= "Phone and Post Code Time Zone Mismatch! ";
    
                    if ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_ONLY')
                        {
                        $post_phone_time_diff_alert_message='';
                        if ($PHONEdialable < 1)
                            {$post_phone_time_diff_alert_message .= " Phone Area Code Outside Dialable Zone $PHONEgmt_offset ";}
                        if ($POSTdialable < 1)
                            {$post_phone_time_diff_alert_message .= " Postal Code Outside Dialable Zone $POSTgmt_offset";}
                        }
                    }
                if ( ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_PHONE') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_POSTAL') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_BOTH') )
                    {$post_phone_time_diff_alert_message = '';}
    
                if ( ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_PHONE') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_BOTH') )
                    {
                    if ($PHONEdialable < 1)
                        {$post_phone_time_diff_alert_message .= " Phone Area Code Outside Dialable Zone $PHONEgmt_offset ";}
                    }
                if ( ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_POSTAL') or ($post_phone_time_diff_alert == 'OUTSIDE_CALLTIME_BOTH') )
                    {
                    if ($POSTdialable < 1)
                        {$post_phone_time_diff_alert_message .= " Postal Code Outside Dialable Zone $POSTgmt_offset ";}
                    }
                }
            ##### END check for postal_code and phone time zones if alert enabled
    
    
            ##### if lead is a callback, grab the callback comments
            $CBentry_time =		'';
            $CBcallback_time =	'';
            $CBuser =			'';
            $CBcomments =		'';
            $CBstatus =			0;
    
            $stmt="SELECT count(*) FROM vicidial_statuses where status='$dispo' and scheduled_callback='Y';";
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00366',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $cb_record_ct = mysqli_num_rows($rslt);
            if ($cb_record_ct > 0)
                {
                $row=mysqli_fetch_row($rslt);
                $CBstatus =		$row[0];
                }
            if ($CBstatus < 1)
                {
                $stmt="SELECT count(*) FROM vicidial_campaign_statuses where status='$dispo' and scheduled_callback='Y';";
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00367',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $cb_record_ct = mysqli_num_rows($rslt);
                if ($cb_record_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $CBstatus =		$row[0];
                    }
                }
            if ( ($CBstatus > 0) or ($dispo == 'CBHOLD') )
                {
                $stmt="SELECT entry_time,callback_time,user,comments FROM vicidial_callbacks where lead_id='$lead_id' order by callback_id desc LIMIT 1;";
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00028',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $cb_record_ct = mysqli_num_rows($rslt);
                if ($cb_record_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $CBentry_time =		trim("$row[0]");
                    $CBcallback_time =	trim("$row[1]");
                    $CBuser =			trim("$row[2]");
                    $CBcomments =		trim("$row[3]");
                    }
                }
    
            $stmt = "SELECT local_gmt FROM servers where active='Y' limit 1;";
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00029',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $server_ct = mysqli_num_rows($rslt);
            if ($server_ct > 0)
                {
                $row=mysqli_fetch_row($rslt);
                $local_gmt =	$row[0];
                $isdst = date("I");
                if ($isdst) {$local_gmt++;}
                }
            $LLCT_DATE_offset = ($local_gmt - $gmt_offset_now);
            $LLCT_DATE = date("Y-m-d H:i:s", mktime(date("H")-$LLCT_DATE_offset,date("i"),date("s"),date("m"),date("d"),date("Y")));
    
            if (preg_match('/Y/',$called_since_last_reset))
                {
                $called_since_last_reset = preg_replace('/Y/','',$called_since_last_reset);
                if (strlen($called_since_last_reset) < 1) {$called_since_last_reset = 0;}
                $called_since_last_reset++;
                $called_since_last_reset = "Y$called_since_last_reset";
                }
            else {$called_since_last_reset = 'Y';}
            $ownerSQL='';
            if ( ($owner_populate=='ENABLED') and ( (strlen($owner) < 1) or ($owner=='NULL') ) )
                {
                $ownerSQL = ",owner='$user'";
                $owner=$user;
                }
            ### flag the lead as called and change it's status to INCALL
            $stmt = "UPDATE vicidial_list set status='INCALL', called_since_last_reset='$called_since_last_reset', called_count='$called_count',user='$user',last_local_call_time='$LLCT_DATE'$ownerSQL where lead_id='$lead_id';";
            if ($DB) {echo "$stmt\n";}
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00030',$user,$server_ip,$session_name,$one_mysql_log);}
    
            if (!$CBleadIDset)
                {
                ### delete the lead from the hopper
                $stmt = "DELETE FROM vicidial_hopper where lead_id='$lead_id';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00031',$user,$server_ip,$session_name,$one_mysql_log);}
                }
    
            $stmt="UPDATE vicidial_agent_log set lead_id='$lead_id',comments='MANUAL' where agent_log_id='$agent_log_id';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00032',$user,$server_ip,$session_name,$one_mysql_log);}
    
            $stmt="UPDATE vicidial_lists set list_lastcalldate=NOW() where list_id='$list_id';";
                if ($format=='debug') {echo "\n<!-- $stmt -->";}
            $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00439',$user,$server_ip,$session_name,$one_mysql_log);}
    
            $campaign_cid_override='';
            $LISTweb_form_address='';
            $LISTweb_form_address_two='';
            ### check if there is a list_id override
            if (strlen($list_id) > 1)
                {
                $stmt = "SELECT campaign_cid_override,web_form_address,web_form_address_two FROM vicidial_lists where list_id='$list_id';";
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00245',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $lio_ct = mysqli_num_rows($rslt);
                if ($lio_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $campaign_cid_override =	$row[0];
                    $LISTweb_form_address =		$row[1];
                    $LISTweb_form_address_two =	$row[2];
                    }
                }
    
            ### if preview dialing, do not send the call	
            if ( (strlen($preview)<1) or ($preview == 'NO') or (strlen($dial_ingroup) > 1) )
                {
                ### prepare variables to place manual call from VICIDiaL
                $CCID_on=0;   $CCID='';
                $local_DEF = 'Local/';
                $local_AMP = '@';
                $Local_out_prefix = '9';
                $Local_dial_timeout = '60';
            #	$Local_persist = '/n';
                                $Local_persist = '';
                if ($dial_timeout > 4) {$Local_dial_timeout = $dial_timeout;}
                $Local_dial_timeout = ($Local_dial_timeout * 1000);
                if (strlen($dial_prefix) > 0) {$Local_out_prefix = "$dial_prefix";}
                if (strlen($campaign_cid) > 6) {$CCID = "$campaign_cid";   $CCID_on++;}
                if (strlen($campaign_cid_override) > 6) {$CCID = "$campaign_cid_override";   $CCID_on++;}
                ### check for custom cid use
                $use_custom_cid=0;
                $stmt = "SELECT use_custom_cid FROM vicidial_campaigns where campaign_id='$campaign';";
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00313',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $uccid_ct = mysqli_num_rows($rslt);
                if ($uccid_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $use_custom_cid =	$row[0];
                    if ($use_custom_cid == 'AREACODE')
                        {
                        $temp_ac = substr("$agent_dialed_number", 0, 3);
                        $stmt = "SELECT outbound_cid FROM vicidial_campaign_cid_areacodes where campaign_id='$campaign' and areacode='$temp_ac' and active='Y' order by call_count_today limit 1;";
                        $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00426',$user,$server_ip,$session_name,$one_mysql_log);}
                        if ($DB) {echo "$stmt\n";}
                        $vcca_ct = mysqli_num_rows($rslt);
                        if ($vcca_ct > 0)
                            {
                            $row=mysqli_fetch_row($rslt);
                            $temp_vcca =	$row[0];
    
                            $stmt="UPDATE vicidial_campaign_cid_areacodes set call_count_today=(call_count_today + 1) where campaign_id='$campaign' and areacode='$temp_ac' and outbound_cid='$temp_vcca';";
                                if ($format=='debug') {echo "\n<!-- $stmt -->";}
                            $rslt=mysqli_query($stmt, $link);
                            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00427',$user,$server_ip,$session_name,$one_mysql_log);}
                            }
                        $temp_CID = preg_replace("/\D/",'',$temp_vcca);
                        }
                    if ($use_custom_cid == 'Y')
                        {$temp_CID = preg_replace("/\D/",'',$security);}
                    if (strlen($temp_CID) > 6) 
                        {$CCID = "$temp_CID";   $CCID_on++;}
                    }
    
                if (preg_match("/x/i",$dial_prefix)) {$Local_out_prefix = '';}
    
                $PADlead_id = sprintf("%010s", $lead_id);
                    while (strlen($PADlead_id) > 10) {$PADlead_id = substr("$PADlead_id", 1);}
    
                ### check for extension append in campaign
                $use_eac=0;
                $stmt = "SELECT count(*) FROM vicidial_campaigns where extension_appended_cidname='Y' and campaign_id='$campaign';";
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00322',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $eacid_ct = mysqli_num_rows($rslt);
                if ($eacid_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $use_eac =	$row[0];
                    }
    
                # Create unique calleridname to track the call: MmddhhmmssLLLLLLLLLL
                $MqueryCID = "M$CIDdate$PADlead_id";
                $EAC='';
                if ($use_eac > 0)
                    {
                    $eac_extension = preg_replace("/SIP\/|IAX2\/|Zap\/|DAHDI\/|Local\//",'',$eac_phone);
                    $EAC=" $eac_extension";
                    }
    
                ### whether to omit phone_code or not
                if (preg_match('/Y/i',$omit_phone_code)) 
                    {$Ndialstring = "$Local_out_prefix$agent_dialed_number";}
                else
                    {$Ndialstring = "$Local_out_prefix$phone_code$agent_dialed_number";}
    
                if ( ($usegroupalias > 0) and (strlen($account)>1) )
                    {
                    $RAWaccount = $account;
                    $account = "Account: $account";
                    $variable = "Variable: usegroupalias=1";
                    }
                else
                    {$account='';   $variable='';}
    
                $dial_channel = "$local_DEF$conf_exten$local_AMP$ext_context$Local_persist";
    
                $preset_name='';
                if (strlen($dial_ingroup) > 1)
                    {
                    ### look for a dial-ingroup cid
                    $dial_ingroup_cid='';
                    $stmt = "SELECT dial_ingroup_cid FROM vicidial_inbound_groups where group_id='$dial_ingroup';";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00440',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $digcid_ct = mysqli_num_rows($rslt);
                    if ($digcid_ct > 0)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $dial_ingroup_cid =	$row[0];
                        }
                    if (strlen($dial_ingroup_cid) > 6) {$CCID = "$dial_ingroup_cid";   $CCID_on++;}
    
                    $preset_name='DIG';
                    $MqueryCID = "Y$CIDdate$PADlead_id";
                    
                    $loop_ingroup_dial_prefix = '8305888888888888';
                    $dial_wait_seconds = '4';	# 1 digit only
                    if ($nocall_dial_flag == 'ENABLED')
                        {
                        $Ndialstring = "$loop_ingroup_dial_prefix$dial_wait_seconds" . "999";
                        $preset_name='DIG_NODIAL';
                        }
                    else
                        {$Ndialstring = "$loop_ingroup_dial_prefix$dial_wait_seconds$Ndialstring";}
    
    #				$dial_ingroup_dialstring = "90009*$dial_ingroup" . "**$lead_id" . "**$agent_dialed_number" . "*$user" . "*$user" . "**1*$conf_exten";
    #				$dial_channel = "$local_DEF$dial_ingroup_dialstring$local_AMP$ext_context$Local_persist";
    
                    $dial_channel = "$local_DEF$Ndialstring$local_AMP$ext_context$Local_persist";
    
                    $dial_wait_seconds = '0';	# 1 digit only
                    $dial_ingroup_dialstring = "90009*$dial_ingroup" . "**$lead_id" . "**$agent_dialed_number" . "*$user" . "*$user" . "**1*$conf_exten";
                    $Ndialstring = "$loop_ingroup_dial_prefix$dial_wait_seconds$dial_ingroup_dialstring";
                    }
    
                if ($CCID_on) {$CIDstring = "\"$MqueryCID$EAC\" <$CCID>";}
                else {$CIDstring = "$MqueryCID$EAC";}
    
                ### insert the call action into the vicidial_manager table to initiate the call
                #	$stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $conf_exten','Context: $ext_context','Channel: $local_DEF$Local_out_prefix$phone_code$phone_number$local_AMP$ext_context','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','','','','');";
                $stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$MqueryCID','Exten: $Ndialstring','Context: $ext_context','Channel: $dial_channel','Priority: 1','Callerid: $CIDstring','Timeout: $Local_dial_timeout','$account','$variable','','');";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00033',$user,$server_ip,$session_name,$one_mysql_log);}
    
                ### log outbound call in the dial log
                $stmt = "INSERT INTO vicidial_dial_log SET caller_code='$MqueryCID',lead_id='$lead_id',server_ip='$server_ip',call_date='$NOW_TIME',extension='$Ndialstring',channel='$dial_channel', timeout='$Local_dial_timeout',outbound_cid='$CIDstring',context='$ext_context';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00442',$user,$server_ip,$session_name,$one_mysql_log);}
    
                ### Skip logging and list overrides if dial in-group is used
                if (strlen($dial_ingroup) < 1)
                    {
                    $stmt = "INSERT INTO vicidial_auto_calls (server_ip,campaign_id,status,lead_id,callerid,phone_code,phone_number,call_time,call_type) values('$server_ip','$campaign','XFER','$lead_id','$MqueryCID','$phone_code','$agent_dialed_number','$NOW_TIME','OUT')";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00034',$user,$server_ip,$session_name,$one_mysql_log);}
                    }
    
                ### update the agent status to INCALL in vicidial_live_agents
                $stmt = "UPDATE vicidial_live_agents set status='INCALL',last_call_time='$NOW_TIME',callerid='$MqueryCID',lead_id='$lead_id',comments='MANUAL',calls_today='$calls_today',external_hangup=0,external_status='',external_pause='',external_dial='',last_state_change='$NOW_TIME' where user='$user' and server_ip='$server_ip';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00035',$user,$server_ip,$session_name,$one_mysql_log);}
    
                ### update calls_today count in vicidial_campaign_agents
                $stmt = "UPDATE vicidial_campaign_agents set calls_today='$calls_today' where user='$user' and campaign_id='$campaign';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
            if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00036',$user,$server_ip,$session_name,$one_mysql_log);}
    
                if ($agent_dialed_number > 0)
                    {
                    $stmt = "INSERT INTO user_call_log (user,call_date,call_type,server_ip,phone_number,number_dialed,lead_id,callerid,group_alias_id,preset_name) values('$user','$NOW_TIME','$agent_dialed_type','$server_ip','$agent_dialed_number','$Ndialstring','$lead_id','$CCID','$RAWaccount','$preset_name')";
                    if ($DB) {echo "$stmt\n";}
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00191',$user,$server_ip,$session_name,$one_mysql_log);}
                    }
    
                ### Skip logging and list overrides if dial in-group is used
                if (strlen($dial_ingroup) < 1)
                    {
                    $val_pause_epoch=0;
                    $val_pause_sec=0;
                    $stmt = "SELECT pause_epoch FROM vicidial_agent_log where agent_log_id='$agent_log_id';";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00323',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $vald_ct = mysqli_num_rows($rslt);
                    if ($vald_ct > 0)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $val_pause_epoch =	$row[0];
                        $val_pause_sec = ($StarTtime - $val_pause_epoch);
                        }
    
                    $stmt="UPDATE vicidial_agent_log set pause_sec='$val_pause_sec',wait_epoch='$StarTtime' where agent_log_id='$agent_log_id';";
                        if ($format=='debug') {echo "\n<!-- $stmt -->";}
                    $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00324',$user,$server_ip,$session_name,$one_mysql_log);}
    
                    #############################################
                    ##### START QUEUEMETRICS LOGGING LOOKUP #####
                    $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,queuemetrics_pe_phone_append,queuemetrics_socket,queuemetrics_socket_url FROM system_settings;";
                    $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00037',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $qm_conf_ct = mysqli_num_rows($rslt);
                    if ($qm_conf_ct > 0)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $enable_queuemetrics_logging =	$row[0];
                        $queuemetrics_server_ip	=		$row[1];
                        $queuemetrics_dbname =			$row[2];
                        $queuemetrics_login	=			$row[3];
                        $queuemetrics_pass =			$row[4];
                        $queuemetrics_log_id =			$row[5];
                        $queuemetrics_pe_phone_append = $row[6];
                        $queuemetrics_socket =			$row[7];
                        $queuemetrics_socket_url =		$row[8];
                        }
                    ##### END QUEUEMETRICS LOGGING LOOKUP #####
                    ###########################################
    
                    if ($enable_queuemetrics_logging > 0)
                        {
                        $data4SQL='';
                        $data4SS='';
                        $stmt="SELECT queuemetrics_phone_environment FROM vicidial_campaigns where campaign_id='$campaign' and queuemetrics_phone_environment!='';";
                        $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00389',$user,$server_ip,$session_name,$one_mysql_log);}
                        if ($DB) {echo "$stmt\n";}
                        $cqpe_ct = mysqli_num_rows($rslt);
                        if ($cqpe_ct > 0)
                            {
                            $row=mysqli_fetch_row($rslt);
                            $pe_append='';
                            if ( ($queuemetrics_pe_phone_append > 0) and (strlen($row[0])>0) )
                                {$pe_append = "-$qm_extension";}
                            $data4SQL = ",data4='$row[0]$pe_append'";
                            $data4SS = "&data4=$row[0]$pe_append";
                            }
    
                        $linkB=mysqli_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                        mysqli_select_db("$queuemetrics_dbname", $linkB);
    
                        # UNPAUSEALL
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='NONE',queue='NONE',agent='Agent/$user',verb='UNPAUSEALL',serverid='$queuemetrics_log_id' $data4SQL;";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysqli_query($linkB, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00038',$user,$server_ip,$session_name,$one_mysql_log);}
                        $affected_rows = mysqli_affected_rows($linkB);
    
                        # CALLOUTBOUND (formerly ENTERQUEUE)
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MqueryCID',queue='$campaign',agent='NONE',verb='CALLOUTBOUND',data2='$agent_dialed_number',serverid='$queuemetrics_log_id' $data4SQL;";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysqli_query($linkB, $stmt);
                if ($mel > 0) {mysql_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00039',$user,$server_ip,$session_name,$one_mysql_log);}
                        $affected_rows = mysqli_affected_rows($linkB);
    
                        # CONNECT
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtime',call_id='$MqueryCID',queue='$campaign',agent='Agent/$user',verb='CONNECT',data1='0',serverid='$queuemetrics_log_id' $data4SQL;";
                        if ($DB) {echo "$stmt\n";}
                        $rslt=mysqli_query($linkB, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$linkB,$mel,$stmt,'00040',$user,$server_ip,$session_name,$one_mysql_log);}
                        $affected_rows = mysqli_affected_rows($linkB);
    
                        mysqli_close($linkB);
    
                        if ( ($queuemetrics_socket == 'CONNECT_COMPLETE') and (strlen($queuemetrics_socket_url) > 10) )
                            {
                            $socket_send_data_begin='?';
                            $socket_send_data = "time_id=$StarTtime&call_id=$MqueryCID&queue=$campaign&agent=Agent/$user&verb=CONNECT&data1=0$data4SS";
                            if (preg_match("/\?/",$queuemetrics_socket_url))
                                {$socket_send_data_begin='&';}
                            ### send queue_log data to the queuemetrics_socket_url ###
                            if ($DB > 0) {echo "$queuemetrics_socket_url$socket_send_data_begin$socket_send_data<BR>\n";}
                            $SCUfile = file("$queuemetrics_socket_url$socket_send_data_begin$socket_send_data");
                            if ($DB > 0) {echo "$SCUfile[0]<BR>\n";}
                            }
                        }
    
                    }
    
                ### Check for List ID override settings
                $VDCL_xferconf_a_number='';
                $VDCL_xferconf_b_number='';
                $VDCL_xferconf_c_number='';
                $VDCL_xferconf_d_number='';
                $VDCL_xferconf_e_number='';
                $stmt = "SELECT xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_campaigns where campaign_id='$campaign';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00277',$user,$server_ip,$session_name,$one_mysql_log);}
                $VC_preset_ct = mysqli_num_rows($rslt);
                if ($VC_preset_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $VDCL_xferconf_a_number =	$row[0];
                    $VDCL_xferconf_b_number =	$row[1];
                    $VDCL_xferconf_c_number =	$row[2];
                    $VDCL_xferconf_d_number =	$row[3];
                    $VDCL_xferconf_e_number =	$row[4];
                    }
    
                ##### check if system is set to generate logfile for transfers
                $stmt="SELECT enable_agc_xfer_log FROM system_settings;";
                $rslt=mysqil_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00027',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $enable_agc_xfer_log_ct = mysqli_num_rows($rslt);
                if ($enable_agc_xfer_log_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $enable_agc_xfer_log =$row[0];
                    }
                if ( ($WeBRooTWritablE > 0) and ($enable_agc_xfer_log > 0) )
                    {
                    #	DATETIME|campaign|lead_id|phone_number|user|type
                    #	2007-08-22 11:11:11|TESTCAMP|65432|3125551212|1234|M
                    $fp = fopen ("./xfer_log.txt", "a");
                    fwrite ($fp, "$NOW_TIME|$campaign|$lead_id|$agent_dialed_number|$user|M|$MqueryCID||$province\n");
                    fclose($fp);
                    }
                }
    
    
            ##### find if script contains recording fields
            $stmt="SELECT count(*) FROM vicidial_lists WHERE list_id='$list_id' and agent_script_override!='' and agent_script_override IS NOT NULL and agent_script_override!='NONE';";
            $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00259',$user,$server_ip,$session_name,$one_mysql_log);}
            if ($DB) {echo "$stmt\n";}
            $vls_vc_ct = mysqli_num_rows($rslt);
            if ($vls_vc_ct > 0)
                {
                $row=mysqli_fetch_row($rslt);
                if ($row[0] > 0)
                    {
                    $script_recording_delay=0;
                    ##### find if script contains recording fields
                    $stmt="SELECT count(*) FROM vicidial_scripts vs,vicidial_lists vls WHERE list_id='$list_id' and vs.script_id=vls.agent_script_override and script_text LIKE \"%--A--recording_%\";";
                    $rslt=mysqli_query($link, $stmt);
                        if ($mel > 0) {mysql_error_logging($NOW_TIME,$link,$mel,$stmt,'00260',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $vs_vc_ct = mysqli_num_rows($rslt);
                    if ($vs_vc_ct > 0)
                        {
                        $row=mysqli_fetch_row($rslt);
                        $script_recording_delay = $row[0];
                        }
                    }
                }
    
            if (strlen($list_id)>0)
                {
                $stmt = "SELECT xferconf_a_number,xferconf_b_number,xferconf_c_number,xferconf_d_number,xferconf_e_number from vicidial_lists where list_id='$list_id';";
                if ($DB) {echo "$stmt\n";}
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00278',$user,$server_ip,$session_name,$one_mysql_log);}
                $VDIG_preset_ct = mysqli_num_rows($rslt);
                if ($VDIG_preset_ct > 0)
                    {
                    $row=mysqli_fetch_row($rslt);
                    if (strlen($row[0]) > 0)
                        {$VDCL_xferconf_a_number =	$row[0];}
                    if (strlen($row[1]) > 0)
                        {$VDCL_xferconf_b_number =	$row[1];}
                    if (strlen($row[2]) > 0)
                        {$VDCL_xferconf_c_number =	$row[2];}
                    if (strlen($row[3]) > 0)
                        {$VDCL_xferconf_d_number =	$row[3];}
                    if (strlen($row[4]) > 0)
                        {$VDCL_xferconf_e_number =	$row[4];}
                    }
                
                $custom_field_names='|';
                $custom_field_names_SQL='';
                $custom_field_values='----------';
                $custom_field_types='|';
                ### find the names of all custom fields, if any
                $stmt = "SELECT field_label,field_type FROM vicidial_lists_fields where list_id='$entry_list_id' and field_type NOT IN('SCRIPT','DISPLAY') and field_label NOT IN('vendor_lead_code','source_id','list_id','gmt_offset_now','called_since_last_reset','phone_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','gender','date_of_birth','alt_phone','email','security_phrase','comments','called_count','last_local_call_time','rank','owner');";
                $rslt=mysqli_query($link, $stmt);
                if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00334',$user,$server_ip,$session_name,$one_mysql_log);}
                if ($DB) {echo "$stmt\n";}
                $cffn_ct = mysqli_num_rows($rslt);
                $d=0;
                while ($cffn_ct > $d)
                    {
                    $row=mysqli_fetch_row($rslt);
                    $custom_field_names .=	"$row[0]|";
                    $custom_field_names_SQL .=	"$row[0],";
                    $custom_field_types .=	"$row[1]|";
                    $custom_field_values .=	"----------";
                    $d++;
                    }
                if ($cffn_ct > 0)
                    {
                    $custom_field_names_SQL = preg_replace("/.$/i","",$custom_field_names_SQL);
                    ### find the values of the named custom fields
                    $stmt = "SELECT $custom_field_names_SQL FROM custom_$entry_list_id where lead_id='$lead_id' limit 1;";
                    $rslt=mysqli_query($link, $stmt);
                    if ($mel > 0) {mysqli_error_logging($NOW_TIME,$link,$mel,$stmt,'00335',$user,$server_ip,$session_name,$one_mysql_log);}
                    if ($DB) {echo "$stmt\n";}
                    $cffv_ct = mysqli_num_rows($rslt);
                    if ($cffv_ct > 0)
                        {
                        $custom_field_values='----------';
                        $row=mysqli_fetch_row($rslt);
                        $d=0;
                        while ($cffn_ct > $d)
                            {
                            $custom_field_values .=	"$row[$d]----------";
                            $d++;
                            }
                        $custom_field_values = preg_replace("/\n/"," ",$custom_field_values);
                        $custom_field_values = preg_replace("/\r/","",$custom_field_values);
                        }
                    }
                }
    
    
            $comments = preg_replace("/\r/i",'',$comments);
            $comments = preg_replace("/\n/i",'!N',$comments);
    
            $LeaD_InfO =	$MqueryCID . "\n";
            $LeaD_InfO .=	$lead_id . "\n";
            $LeaD_InfO .=	$dispo . "\n";
            $LeaD_InfO .=	$tsr . "\n";
            $LeaD_InfO .=	$vendor_id . "\n";
            $LeaD_InfO .=	$list_id . "\n";
            $LeaD_InfO .=	$gmt_offset_now . "\n";
            $LeaD_InfO .=	$phone_code . "\n";
            $LeaD_InfO .=	$phone_number . "\n";
            $LeaD_InfO .=	$title . "\n";
            $LeaD_InfO .=	$first_name . "\n";
            $LeaD_InfO .=	$middle_initial . "\n";
            $LeaD_InfO .=	$last_name . "\n";
            $LeaD_InfO .=	$address1 . "\n";
            $LeaD_InfO .=	$address2 . "\n";
            $LeaD_InfO .=	$address3 . "\n";
            $LeaD_InfO .=	$city . "\n";
            $LeaD_InfO .=	$state . "\n";
            $LeaD_InfO .=	$province . "\n";
            $LeaD_InfO .=	$postal_code . "\n";
            $LeaD_InfO .=	$country_code . "\n";
            $LeaD_InfO .=	$gender . "\n";
            $LeaD_InfO .=	$date_of_birth . "\n";
            $LeaD_InfO .=	$alt_phone . "\n";
            $LeaD_InfO .=	$email . "\n";
            $LeaD_InfO .=	$security . "\n";
            $LeaD_InfO .=	$comments . "\n";
            $LeaD_InfO .=	$called_count . "\n";
            $LeaD_InfO .=	$CBentry_time . "\n";
            $LeaD_InfO .=	$CBcallback_time . "\n";
            $LeaD_InfO .=	$CBuser . "\n";
            $LeaD_InfO .=	$CBcomments . "\n";
            $LeaD_InfO .=	$agent_dialed_number . "\n";
            $LeaD_InfO .=	$agent_dialed_type . "\n";
            $LeaD_InfO .=	$source_id . "\n";
            $LeaD_InfO .=	$rank . "\n";
            $LeaD_InfO .=	$owner . "\n";
            $LeaD_InfO .=	"\n";
            $LeaD_InfO .=	$script_recording_delay . "\n";
            $LeaD_InfO .=	$VDCL_xferconf_a_number . "\n";
            $LeaD_InfO .=	$VDCL_xferconf_b_number . "\n";
            $LeaD_InfO .=	$VDCL_xferconf_c_number . "\n";
            $LeaD_InfO .=	$VDCL_xferconf_d_number . "\n";
            $LeaD_InfO .=	$VDCL_xferconf_e_number . "\n";
            $LeaD_InfO .=	$entry_list_id . "\n";
            $LeaD_InfO .=	$custom_field_names . "\n";
            $LeaD_InfO .=	$custom_field_values . "\n";
            $LeaD_InfO .=	$custom_field_types . "\n";
            $LeaD_InfO .=	$LISTweb_form_address . "\n";
            $LeaD_InfO .=	$LISTweb_form_address_two . "\n";
            $LeaD_InfO .=	$post_phone_time_diff_alert_message . "\n";
            $LeaD_InfO .=   $ACcount . "\n";
            $LeaD_InfO .=   $ACcomments . "\n";
    
            echo $LeaD_InfO;
            }
        else
            {
            echo "HOPPER EMPTY\n";
            }
        }
} else {
    $APIResult = array( "result" => "error", "message" => "SIP exten '{$phone_login}' is NOT connected" );
}
?>