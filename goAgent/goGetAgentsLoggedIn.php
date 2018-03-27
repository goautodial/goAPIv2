<?php
 /**
 * @file 		goGetAgentsLoggedIn.php
 * @brief 		API for Agent UI
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Chris Lomuntad <chris@goautodial.com>
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

$is_logged_in = check_agent_login($astDB, $goUser);

$agent = get_settings('user', $astDB, $goUser);
$user_group = $agent->user_group;

if (isset($_GET['goServerIP'])) { $server_ip = $astDB->escape($_GET['goServerIP']); }
    else if (isset($_POST['goServerIP'])) { $server_ip = $astDB->escape($_POST['goServerIP']); }
if (isset($_GET['goSessionName'])) { $session_name = $astDB->escape($_GET['goSessionName']); }
    else if (isset($_POST['goSessionName'])) { $session_name = $astDB->escape($_POST['goSessionName']); }
if (isset($_GET['goConfExten'])) { $conf_exten = $astDB->escape($_GET['goConfExten']); }
    else if (isset($_POST['goConfExten'])) { $conf_exten = $astDB->escape($_POST['goConfExten']); }
if (isset($_GET['goExtension'])) { $extension = $astDB->escape($_GET['goExtension']); }
    else if (isset($_POST['goExtension'])) { $extension = $astDB->escape($_POST['goExtension']); }
if (isset($_GET['goProtocol'])) { $protocol = $astDB->escape($_GET['goProtocol']); }
    else if (isset($_POST['goProtocol'])) { $protocol = $astDB->escape($_POST['goProtocol']); }
if (isset($_GET['goStage'])) { $stage = $astDB->escape($_GET['goStage']); }
    else if (isset($_POST['goStage'])) { $stage = $astDB->escape($_POST['goStage']); }
if (isset($_GET['goComments'])) { $comments = $astDB->escape($_GET['goComments']); }
    else if (isset($_POST['goComments'])) { $comments = $astDB->escape($_POST['goComments']); }
if (isset($_GET['goUserGroup'])) { $user_group = $astDB->escape($_GET['goUserGroup']); }
    else if (isset($_POST['goUserGroup'])) { $user_group = $astDB->escape($_POST['goUserGroup']); }
if (isset($_GET['goUserRole'])) { $user_role = $astDB->escape($_GET['goUserRole']); }
    else if (isset($_POST['goUserRole'])) { $user_role = $astDB->escape($_POST['goUserRole']); }

if ($is_logged_in || (strlen($campaign) < 1 && $user_role < 2 && $user_role != '')) {
	$agent_status_viewable_groupsSQL = '';
	### Gather timeclock and shift enforcement restriction settings
	//$stmt="SELECT agent_status_viewable_groups,agent_status_view_time from vicidial_user_groups where user_group='$VU_user_group';";
    $astDB->where('user_group', $user_group);
    $rslt = $astDB->getOne('vicidial_user_groups', 'agent_status_viewable_groups,agent_status_view_time');
	$agent_status_viewable_groups = $rslt['agent_status_viewable_groups'];
	//$agent_status_viewable_groupsSQL = preg_replace('/\s\s/i','',$agent_status_viewable_groups);
	//$agent_status_viewable_groupsSQL = preg_replace('/\s/i',"','",$agent_status_viewable_groupsSQL);
	//$agent_status_viewable_groupsSQL = "user_group IN('$agent_status_viewable_groupsSQL')";
    $viewable_groups = trim($agent_status_viewable_groups);
    $viewable_groups = preg_split('/\s/i', $viewable_groups);
	$agent_status_view = 0;
	if (strlen($agent_status_viewable_groups) > 2)
		{$agent_status_view = 1;}
	$agent_status_view_time = 0;
	if ($rslt['agent_status_view_time'] == 'Y')
		{$agent_status_view_time = 1;}
	$andSQL = '';
	if (!preg_match("/ALL-GROUPS/", $agent_status_viewable_groups) && strlen($campaign) > 0) {
		//$AGENTviewSQL = "($agent_status_viewable_groupsSQL)";
		//
		//if (preg_match("/CAMPAIGN-AGENTS/",$agent_status_viewable_groups))
		//	{$AGENTviewSQL = "($AGENTviewSQL or (campaign_id='$campaign'))";}
		//$AGENTviewSQL = "and $AGENTviewSQL";
        $astDB->where('user_group', $viewable_groups, 'in');
        $astDB->orWhere('campaign_id', $campaign);
	}
	if ($comments == 'AgentXferViewSelect') {
        //$AGENTviewSQL .= " and (vla.closer_campaigns LIKE \"%AGENTDIRECT%\")";
        $astDB->where('vla.closer_campaigns', '%AGENTDIRECT%', 'LIKE');
    }


	### Gather agents data and statuses
	$agentviewlistSQL = '';
	$j = 0;
	//$stmt="SELECT vla.user,vla.status,vu.full_name,UNIX_TIMESTAMP(last_call_time),UNIX_TIMESTAMP(last_call_finish) from vicidial_live_agents vla,vicidial_users vu where vla.user=vu.user $AGENTviewSQL order by vu.full_name;";
    $astDB->orderBy('vu.full_name', 'desc');
    $astDB->join('vicidial_users vu', 'vla.user=vu.user', 'left');
    $rslt = $astDB->get('vicidial_live_agents vla', null, 'vla.user,vla.status,vu.full_name,UNIX_TIMESTAMP(last_call_time) AS call_time,UNIX_TIMESTAMP(last_call_finish) AS call_finish');
	$agents_count = $astDB->getRowCount();
	$loop_count = 0;
    $agentviewlist = array();
    $agentViewLogin = array();
    $agentViewLogout = array();
    $agentViewXFER = array();
	while ($agents_count > $loop_count) {
		$row = $rslt[$loop_count];
		$user =			$row['user'];
		$status =		$row['status'];
		$full_name =	$row['full_name'];
		$call_start =	$row['call_time'];
		$call_finish =	$row['call_finish'];
		//$agentviewlistSQL .= "'$user',";
        $agentviewlist[] = $user;

		if ( ($status == 'READY') or ($status == 'CLOSER') ) {
			$statuscolor = '#ADD8E6';
			$textcolor = '#222';
			$call_time = ($StarTtimE - $call_finish);
		}
		if ( ($status == 'QUEUE') or ($status == 'INCALL') ) {
			$statuscolor = '#D8BFD8';
			$textcolor = '#222';
			$call_time = ($StarTtimE - $call_start);
		}
		if ($status == 'PAUSED') {
			$statuscolor = '#F0E68C';
			$textcolor = '#222';
			$call_time = ($StarTtimE - $call_finish);
		}

		if ($call_time < 1) {
			$call_time = "00:00";
		} else {
			$Fminutes_M = ($call_time / 60);
			$Fminutes_M_int = floor($Fminutes_M);
			$Fminutes_M_int = intval("$Fminutes_M_int");
			$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
			$Fminutes_S = ($Fminutes_S * 60);
			$Fminutes_S = round($Fminutes_S, 0);
			if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
			if ($Fminutes_M_int < 10) {$Fminutes_M_int = "0$Fminutes_M_int";}
			$call_time = "$Fminutes_M_int:$Fminutes_S";
		}

		if ($comments == 'AgentXferViewSelect')  {
			$AXVSuserORDER[$j] =	"$full_name$US$j";
			$AXVSuser[$j] =			$user;
			$AXVSfull_name[$j] =	$full_name;
			$AXVScall_time[$j] =	$call_time;
            $AXVSstatus[$j] =       $status;
			$AXVSstatuscolor[$j] =	$statuscolor;
			$AXVStextcolor[$j] =	$textcolor;
			$j++;
		} else {
            $agentViewLogin[$user]['full_name'] = $full_name;
            $agentViewLogin[$user]['status'] = $status;
            $agentViewLogin[$user]['call_time'] = $call_time;
            $agentViewLogin[$user]['statcolor'] = $statuscolor;
            $agentViewLogin[$user]['textcolor'] = $textcolor;
		}
		$loop_count++;
	}
    
    $agentsList = array(
        'logged_in' => $agentViewLogin
    );
    
	//$agentviewlistSQL = preg_replace("/.$/i","",$agentviewlistSQL);
	//if (strlen($agentviewlistSQL)<3)
	//	{$agentviewlistSQL = "''";}
	
	if (preg_match("/NOT-LOGGED-IN-AGENTS/", $agent_status_viewable_groups)) {
		//$stmt="SELECT user,full_name from vicidial_users where user NOT IN($agentviewlistSQL) order by full_name;";
        $astDB->where('user', $agentviewlist, 'in');
        $astDB->orderBy('full_name', 'desc');
        $rslt = $astDB->get('vicidial_users', null, 'user,full_name');
		$loop_count = 0;
		while ($agents_count > $loop_count) {
			$row = $rslt[$loop_count];
			$user =			$row['user'];
			$full_name =	$row['full_name'];
	
			if ($comments == 'AgentXferViewSelect')  {
				$AXVSuserORDER[$j] =	"$full_name$US$j";
				$AXVSuser[$j] =			$user;
				$AXVSfull_name[$j] =	$full_name;
				$AXVScall_time[$j] =	'00:00';
				$AXVSstatuscolor[$j] =	'transparent';
				$AXVStextcolor[$j] =	'#b8c7ce';
				$j++;
			} else {
                $agentViewLogout[$user]['full_name'] = $full_name;
                $agentViewLogout[$user]['call_time'] = "00:00";
                $agentViewLogout[$user]['statcolor'] = "transparent";
				$agentViewLogout[$user]['textcolor'] = "#b8c7ce";
			}
			$loop_count++;
        }
        $agentsList = array_merge($agentsList, array( "logged_out" => $agentViewLogout ));
    }
	
	### BEGIN Display the agent transfer select view ###
	$k = 0;
	if ($comments == 'AgentXferViewSelect') {
		$AXVSrecords = 100;
		$AXVScolumns = 1;
		$AXVSfontsize = '12px';
		if ($j > 30) {$AXVScolumns++;}
		if ($j > 60) {
            $AXVScolumns++;
            $AXVSfontsize = '11px';
        }
		if ($j > 90) {
            $AXVScolumns++;
            $AXVSfontsize = '10px';
        }
		if ($j > 120) {
            $AXVScolumns++;
            $AXVSfontsize = '9px';
        }
		$AXVSrecords = ($j / $AXVScolumns);
		$AXVSrecords = round($AXVSrecords, 0);
		$m = 0;
	
		sort($AXVSuserORDER);
		while ($j > $k) {
			$order_split = explode("_", $AXVSuserORDER[$k]);
			$i = $order_split[1];
	
			//echo "<TR BGCOLOR=\"$AXVSstatuscolor[$i]\"><TD><font style=\"font-size: $AXVSfontsize; font-family: sans-serif;\"> &nbsp; <a href=\"#\" onclick=\"AgentsXferSelect('$AXVSuser[$i]','AgentXferViewSelect');return false;\">$AXVSuser[$i] - $AXVSfull_name[$i]</a>&nbsp;</font></TD>";
			//if ($agent_status_view_time > 0)
				//{echo "<TD><font style=\"font-size: $AXVSfontsize;  font-family: sans-serif;\">&nbsp; $AXVScall_time[$i] &nbsp;</font></TD>";}
			//echo "</TR>";
            $xferUser = $AXVSuser[$i];
            $agentViewXFER[$xferUser]['full_name'] = $AXVSfull_name[$i];
            $agentViewXFER[$xferUser]['status'] = $AXVSstatus[$i];
            $agentViewXFER[$xferUser]['call_time'] = $AXVScall_time[$i];
            $agentViewXFER[$xferUser]['statcolor'] = $AXVSstatuscolor[$i];
            $agentViewXFER[$xferUser]['textcolor'] = $AXVStextcolor[$i];
	
			$k++;
			$m++;
			if ($m >= $AXVSrecords) {
				//echo "</TABLE></TD><TD VALIGN=TOP> &nbsp; </TD>";
				//echo "<TD VALIGN=TOP><TABLE CELLPADDING=0 CELLSPACING=1>";
				$m = 0;
			}
		}
        $agentsList = array_merge($agentsList, array( "xfer" => $agentViewXFER ));
		//echo "</TD></TR></TABLE>";
	}
	
	//echo "</TABLE><BR>\n";
	//echo "<font style=\"font-size:10px;font-family:sans-serif;\"><font style=\"background-color:#ADD8E6;\"> &nbsp; &nbsp;</font>-READY &nbsp; <font style=\"background-color:#D8BFD8;\">&nbsp; &nbsp;</font>-INCALL &nbsp; <font style=\"background-color:#F0E68C;\"> &nbsp; &nbsp;</font>-PAUSED &nbsp;\n";
	//if (preg_match("/NOT-LOGGED-IN-AGENTS/",$agent_status_viewable_groups))
		//{echo "<font style=\"background-color:#FFFFFF;\"> &nbsp; &nbsp;</font>-LOGGED-OUT &nbsp;\n";}
	
	//echo "</font>\n";
    $APIResult = array( "result" => "success", "data" => array( "agents" => $agentsList ), "debug" => $debugging );
} else {
    $APIResult = array( "result" => "error", "message" => "Agent '$goUser' is currently NOT logged in" );
}
?>