<?php
########################################################################################################
####  Name:             	go_reports.php                      	                            ####
####  Type:             	ci model - administrator                                            ####
####  Version:          	3.0                                                            	    ####
####  Build:            	1366344000                                                          ####
####  Copyright:        	GOAutoDial Inc. (c) 2011-2013 - GoAutoDial Open Source Community    ####
####			        <community@goautodial.com>            			   	    ####
####  Originated by:	        Rodolfo Januarius T. Manipol                                        ####
####  Written by:      		Christopher Lomuntad                                    	    ####
####  License:          	AGPLv2                                                              ####
########################################################################################################

class Go_reports extends Model {
	
   function __construct()
	{
	    parent::Model();
	    $this->reportsdb = $this->load->database('dialerdb', true);
	    $this->load->helper('download');
	    
	    $squery = $this->reportsdb->query("SELECT slave_db_server FROM system_settings");
	    if (!empty($squery->row()->slave_db_server)) {
	       $dsn = 'mysql://'.$this->reportsdb->username.':'.$this->reportsdb->password.'@'.$squery->row()->slave_db_server.'/'.$this->reportsdb->database.'?char_set=utf8&dbcollat=utf8_general_ci&db_debug=true';
	       $this->reportsdb = $this->load->database($dsn, true);
	    }
	}

	function go_search_user()
	{
	   $queryString = $this->uri->segment(3);
	   #$query_date =  date('Y-m-d');
	   $this->reportsdb->cache_on();
	   $query = $this->reportsdb->query("SELECT user FROM vicidial_users_view WHERE user rlike '$queryString' order by user asc LIMIT 10");	 
	   $datacount = $query->num_rows();
	   $dataval   = $query->result();
	   $return['datacount']=$datacount;
	   $return['dataval']  =$dataval;
	   return $return; 
	}
	
	
	function go_search_list()
	{
	   $queryString = $this->uri->segment(3);
	   #$query_date =  date('Y-m-d');
	   $this->reportsdb->cache_on();
	   $query = $this->reportsdb->query("SELECT fullname FROM vicidial_list_view WHERE fullname rlike '$queryString' order by fullname asc  LIMIT 10");	 
	   $datacount = $query->num_rows();
	   $dataval   = $query->result();
	   $return['datacount']=$datacount;
	   $return['dataval']  =$dataval;
	   return $return; 
	}	
	

	function go_search_phone()
	{
	   $queryString = $this->uri->segment(3); 
	   #$query_date =  date('Y-m-d');
	   $this->reportsdb->cache_on();
	   $query = $this->reportsdb->query("SELECT phone_number FROM vicidial_list_view WHERE phone_number rlike '$queryString' order by phone_number asc LIMIT 10");	 
	   $datacount = $query->num_rows();
	   $dataval   = $query->result();
	   $return['datacount']=$datacount;
	   $return['dataval']  =$dataval;
	   return $return; 
	}
	
	function go_search_clear()
	{
		$this->reportsdb->cache_delete_all();
	}
	
	//function go_check_balance()
	//{
	//	$userID = $this->session->userdata('user_name');
	//	$query = $this->a2db->query("select credit,currency from cc_card where username='$userID'");
	//	$resultsu = $query->row();
	//	$return['credit'] = number_format($resultsu->credit / $this->go_get_currency($resultsu->currency), 2);
	//	$return['currency'] = $resultsu->currency;
	//	return $return;
	//}
	
	//function go_check_info()
	//{
	//	$userID = $this->session->userdata('user_name');
	//	$query = $this->a2db->query("select firstname,lastname,email,phone,address,city,state,zipcode,country,useralias,company_name from cc_card where username='$userID'");
	//	$resultsu = $query->row();
	//	$return['accnt_num'] = $userID;
	//	$return['firstname'] = $resultsu->firstname;
	//	$return['lastname'] = $resultsu->lastname;
	//	$return['username'] = $resultsu->useralias;
	//	$return['company'] = $resultsu->company_name;
	//	$return['email'] = $resultsu->email;
	//	$return['phone'] = $resultsu->phone;
	//	$return['address'] = $resultsu->address;
	//	$return['city'] = $resultsu->city;
	//	$return['state'] = $resultsu->state;
	//	$return['zipcode'] = $resultsu->zipcode;
	//	$return['country'] = $resultsu->country;
	//	return $return;
	//}
	
	function go_get_campaigns()
	{
	    $groupId = $this->go_get_groupid();
	    if (!$this->commonhelper->checkIfTenant($groupId))
	    {
	       $ul='';
	    }
	    else
	    {  
	       $stringv = $this->go_getall_allowed_campaigns();
	       $ul = " and campaign_id IN ('$stringv') ";         
	    }
		
		$query = $this->reportsdb->query("SELECT campaign_id,campaign_name FROM vicidial_campaigns WHERE active='Y' $ul ORDER BY campaign_id");
		$return = $query->result();
	//	$return['campaign_id'] = $resultsu;
		return $return;
	}
	
	function go_get_num_seats()
	{
		$userID = $this->session->userdata('user_name');
		$query = $this->reportsdb->query("select num_seats from a2billing_wizard where account_num='$userID'");
		$resultsu = $query->row();
		$num_seats = $resultsu->num_seats;
		return $num_seats;
	}
	
	function go_get_logins()
	{
		$queryString = $this->uri->segment(3); 
		$userID = $this->session->userdata('user_name');
		$this->reportsdb->cache_on();
		if ($queryString=='list_agents')
		{
			$query = $this->reportsdb->query("SELECT user,pass FROM vicidial_users WHERE user_group='$userID' AND user_level='1' ORDER BY user");
		}
		else
		{
			$query = $this->reportsdb->query("SELECT login,phones.pass FROM phones,vicidial_users WHERE login=REPLACE(user,'_','') AND vicidial_users.user_group='$userID' AND user_level='1' ORDER BY login");
		}
		$datacount = $query->num_rows();
		$dataval   = $query->result();
		$return['datacount']=$datacount;
		$return['dataval']  =$dataval;
		return $return;
	}
	
	function go_get_reports()
	{
		$pageTitle = $this->uri->segment(3);
		$fromDate = $this->uri->segment(4);
		$toDate = $this->uri->segment(5);
		$campaignID = $this->uri->segment(6);
		$request = $this->uri->segment(7);
		$userID = $this->session->userdata('user_name');
		$userGroup = $this->session->userdata('user_group');
		#$query_date =  date('Y-m-d');
		
		if ($campaignID!='null' || $pageTitle == 'call_export_report')
		{
			$return['groupId'] = $this->go_get_groupid();
			$date_diff = $this->go_get_date_diff($fromDate, $toDate);
			$date_array = implode("','",$this->go_get_dates($fromDate, $toDate));
			$this->reportsdb->cache_on();
			$file_download = 1;
			if ($pageTitle!='inbound_report') {
//				$campaignID = '';
				$query = $this->reportsdb->query("select campaign_name from vicidial_campaigns where campaign_id='$campaignID'");
			} else {
				$query = $this->reportsdb->query("select group_name as campaign_name from vicidial_inbound_groups where uniqueid_status_prefix='".$return['groupId']."'");
			}
			$resultu = $query->row();
			$return['campaign_name'] = $resultu->campaign_name;
			$ul = "and campaign_id='$campaignID'";
			if (!isset($request) || $request=='') {
				$return['request'] = 'daily';
			} else {
				$return['request'] = $request;
			}
			
			$query = $this->reportsdb->query("SELECT status FROM vicidial_statuses WHERE sale='Y'");
			foreach ($query->result() as $Qstatus)
			{
			   $sstatuses[$Qstatus->status] = $Qstatus->status;
			   $sstatusRX	.= "{$Qstatus->status}|";
			}
			$sstatuses = implode("','",$sstatuses);
			
			$query = $this->reportsdb->query("SELECT status FROM vicidial_campaign_statuses WHERE sale='Y' AND campaign_id='$campaignID'");
			foreach ($query->result() as $Qstatus)
			{
			   $cstatuses[$Qstatus->status] = $Qstatus->status;
			   $cstatusRX	.= "{$Qstatus->status}|";
			}
			$cstatuses = implode("','",$cstatuses);
			if (strlen($sstatuses) > 0 && strlen($cstatuses) > 0)
			{
			   $statuses = "{$sstatuses}','{$cstatuses}";
			   $statusRX = "{$sstatusRX}{$cstatusRX}";
			} else {
			   $statuses = (strlen($sstatuses) > 0 && strlen($cstatuses) < 1) ? $sstatuses : $cstatuses;
			   $statusRX = (strlen($sstatusRX) > 0 && strlen($cstatusRX) < 1) ? $sstatusRX : $cstatusRX;
			}
			$statusRX = trim($statusRX, "|");
			
			// Agent Statistics
			if ($pageTitle=='stats')
			{
				if ($return['request']=='daily') {
					$stringv = $this->go_getall_closer_campaigns();
					$closerCampaigns = " and campaign_id IN ('$stringv') ";
					$vcloserCampaigns = " and vclog.campaign_id IN ('$stringv') ";
					$return['call_time'] = $this->go_get_calltimes($campaignID);

					if (strlen($stringv) > 0 && $stringv != '')
					{
						$MunionSQL = "UNION select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_closer_log where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d') between '$fromDate' and '$toDate' $closerCampaigns group by cdate";
						$rsltv = mysqli_query($link,$MunionSQL);

						$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d') between '$fromDate' and '$toDate' $closerCampaigns";
						$rsltv = mysqli_query($link,$TunionSQL);

						$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d') between '$fromDate' and '$toDate' $closerCampaigns group by status";
						$rsltv = mysqli_query($link,$DunionSQL);
					}
					
					// Total Calls Made
					//$query = $this->reportsdb->query("select * from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and call_date between '$fromDate 00:00:00' and '$toDate 23:59:59'");
					$query = $this->reportsdb->query("select cdate, sum(Hour0) as 'Hour0', sum(Hour1) as 'Hour1', sum(Hour2) as 'Hour2', sum(Hour3) as 'Hour3', sum(Hour4) as 'Hour4', sum(Hour5) as 'Hour5', sum(Hour6) as 'Hour6', sum(Hour7) as 'Hour7', sum(Hour8) as 'Hour8', sum(Hour9) as 'Hour9', sum(Hour10) as 'Hour10', sum(Hour11) as 'Hour11', sum(Hour12) as 'Hour12', sum(Hour13) as 'Hour13', sum(Hour14) as 'Hour14', sum(Hour15) as 'Hour15', sum(Hour16) as 'Hour16', sum(Hour17) as 'Hour17', sum(Hour18) as 'Hour18', sum(Hour19) as 'Hour19', sum(Hour20) as 'Hour20', sum(Hour21) as 'Hour21', sum(Hour22) as 'Hour22', sum(Hour23) as 'Hour23' from (select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 00, 1, 0)) as 'Hour0',sum(if(date_format(call_date,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(call_date,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(call_date,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(call_date,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(call_date,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(call_date,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(call_date,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(call_date,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(call_date,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(call_date,'%H') = 23, 1, 0)) as 'Hour23' from vicidial_log where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d') between '$fromDate' and '$toDate' $ul group by cdate $MunionSQL) t group by cdate;");
					$return['data_calls'] = $query->result();
					
					$query = $this->reportsdb->query("select phone_number from vicidial_log vl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d') between '$fromDate' and '$toDate' $ul $TunionSQL");
					$return['total_calls'] = $query->num_rows();
					
					// Total Number of Leads
					$query = $this->reportsdb->query("select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id");
					$return['total_leads'] = $query->num_rows();
					
					// Total Number of New Leads
					$query = $this->reportsdb->query("select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id and vl.status='NEW'");
					$return['total_new'] = $query->num_rows();
					
					// Total Agents Logged In
					$query = $this->reportsdb->query("select date_format(event_time, '%Y-%m-%d') as cdate,user as cuser from vicidial_agent_log where campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') between '$fromDate' and '$toDate' group by cuser");
					$return['total_agents'] = $query->num_rows();
					$return['data_agents'] = $query->result();
					
					// Disposition of Calls
					$query = $this->reportsdb->query("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by status $DunionSQL) t group by status;");
					$query = $this->reportsdb->query("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and date_format(call_date, '%Y-%m-%d') between '$fromDate' and '$toDate' $ul group by status $DunionSQL) t group by status;");
					$return['total_status'] = $query->num_rows();
					$return['data_status'] = $query->result();
				}
				
				if ($return['request']=='weekly') {
					$stringv = $this->go_getall_closer_campaigns();
					$closerCampaigns = " and campaign_id IN ('$stringv') ";
					$vcloserCampaigns = " and vclog.campaign_id IN ('$stringv') ";

					if (strlen($stringv) > 0 && $stringv != '')
					{
						$MunionSQL = "UNION select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_closer_log where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by weekno";
						$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns";
						$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log vcl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $closerCampaigns group by status";
					}
					
					// Total Calls Made
					//$query = $this->reportsdb->query("select * from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and call_date between '$fromDate 00:00:00' and '$toDate 23:59:59'");
					$query = $this->reportsdb->query("select weekno, sum(Day0) as 'Day0', sum(Day1) as 'Day1', sum(Day2) as 'Day2', sum(Day3) as 'Day3', sum(Day4) as 'Day4', sum(Day5) as 'Day5', sum(Day6) as 'Day6' from (select week(DATE_FORMAT( call_date, '%Y-%m-%d' )) as weekno, sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 0, 1, 0))  as 'Day0', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0))  as 'Day1', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0))  as 'Day2', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0))  as 'Day3', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0))  as 'Day4', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0))  as 'Day5', sum(if(weekday(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0))  as 'Day6' from vicidial_log where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $ul group by weekno $MunionSQL) t group by weekno;");
					$return['data_calls'] = $query->result();
					
					$query = $this->reportsdb->query("select phone_number from vicidial_log vl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $ul $TunionSQL");
					$return['total_calls'] = $query->num_rows();
					
					// Total Number of Leads
					$query = $this->reportsdb->query("select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id");
					$return['total_leads'] = $query->num_rows();
					
					// Total Number of New Leads
					$query = $this->reportsdb->query("select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id and vl.list_id='NEW'");
					$return['total_new'] = $query->num_rows();
					
					// Total Agents Logged In
					$query = $this->reportsdb->query("select date_format(event_time, '%Y-%m-%d') as cdate,user as cuser from vicidial_agent_log where campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') between '$fromDate' and '$toDate' group by cuser");
					$return['total_agents'] = $query->num_rows();
					$return['data_agents'] = $query->result();
					
					// Disposition of Calls
					$query = $this->reportsdb->query("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and week(DATE_FORMAT( call_date, '%Y-%m-%d' )) between week('$fromDate') and week('$toDate') $ul group by status $DunionSQL) t group by status;");
					$return['total_status'] = $query->num_rows();
					$return['data_status'] = $query->result();
				}
				
				if ($return['request']=='monthly') {
					$stringv = $this->go_getall_closer_campaigns();
					$closerCampaigns = " and campaign_id IN ('$stringv') ";
					$vcloserCampaigns = " and vclog.campaign_id IN ('$stringv') ";

					if (strlen($stringv) > 0 && $stringv != '')
					{
						$MunionSQL = "UNION select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_closer_log where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by monthname";
						$TunionSQL = "UNION ALL select phone_number from vicidial_closer_log vcl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns";
						$DunionSQL = "UNION select status,count(*) as ccount from vicidial_closer_log vcl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $closerCampaigns group by status";
					}

					// Total Calls Made
					$query = $this->reportsdb->query("select monthname, sum(Month1) as 'Month1', sum(Month2) as 'Month2', sum(Month3) as 'Month3', sum(Month4) as 'Month4', sum(Month5) as 'Month5', sum(Month6) as 'Month6', sum(Month7) as 'Month7', sum(Month8) as 'Month8', sum(Month9) as 'Month9', sum(Month10) as 'Month10', sum(Month11) as 'Month11', sum(Month12) as 'Month12' from (select MONTHNAME(DATE_FORMAT( call_date, '%Y-%m-%d' )) as monthname, sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 1, 1, 0)) as 'Month1', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 2, 1, 0)) as 'Month2', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 3, 1, 0)) as 'Month3', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 4, 1, 0)) as 'Month4', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 5, 1, 0)) as 'Month5', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 6, 1, 0)) as 'Month6', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 7, 1, 0)) as 'Month7', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 8, 1, 0)) as 'Month8', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 9, 1, 0)) as 'Month9', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 10, 1, 0)) as 'Month10', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 11, 1, 0)) as 'Month11', sum(if(MONTH(DATE_FORMAT( call_date, '%Y-%m-%d' )) = 12, 1, 0)) as 'Month12' from vicidial_log where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by monthname $MunionSQL) t group by monthname;");
					$return['data_calls'] = $query->result();
					
					$query = $this->reportsdb->query("select phone_number from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul $TunionSQL");
					$return['total_calls'] = $query->num_rows();
					
					// Total Number of Leads
					$query = $this->reportsdb->query("select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id");
					$return['total_leads'] = $query->num_rows();
					
					// Total Number of New Leads
					$query = $this->reportsdb->query("select * from vicidial_list as vl, vicidial_lists as vlo where vlo.campaign_id='$campaignID' and vl.list_id=vlo.list_id and vl.list_id='NEW'");
					$return['total_new'] = $query->num_rows();
					
					// Total Agents Logged In
					$query = $this->reportsdb->query("select date_format(event_time, '%Y-%m-%d') as cdate,user as cuser from vicidial_agent_log where campaign_id='$campaignID' and MONTH(event_time) between MONTH('$fromDate') and MONTH('$toDate') group by cuser");
					$return['total_agents'] = $query->num_rows();
					$return['data_agents'] = $query->result();
					
					// Disposition of Calls
					$query = $this->reportsdb->query("select status, sum(ccount) as ccount from (select status,count(*) as ccount from vicidial_log vl where length_in_sec>'0' and MONTH(call_date) between MONTH('$fromDate') and MONTH('$toDate') $ul group by status $DunionSQL) t group by status;");
					$return['total_status'] = $query->num_rows();
					$return['data_status'] = $query->result();
				}
			}
			
			// Agent Time Detail
			if ($pageTitle=="agent_detail") {
				### BEGIN gather user IDs and names for matching up later
				$query = $this->reportsdb->query("SELECT full_name,user FROM vicidial_users ORDER BY user LIMIT 100000");
				$user_ct = $query->num_rows();
	
				foreach ($query->result() as $i => $row)
					{
					$ULname[$i] =	$row->full_name;
					$ULuser[$i] =	$row->user;
					}
				### END gather user IDs and names for matching up later
			
				### BEGIN gather timeclock records per agent
				$query = $this->reportsdb->query("SELECT user,SUM(login_sec) AS login_sec FROM vicidial_timeclock_log WHERE event IN('LOGIN','START') AND date_format(event_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' GROUP BY user LIMIT 10000000");
				$timeclock_ct = $query->num_rows();
	
				foreach ($query->result() as $i => $row)
					{
					$TCuser[$i] =	$row->user;
					$TCtime[$i] =	$row->login_sec;
					}
				### END gather timeclock records per agent
			
				### BEGIN gather pause code information by user IDs
				$sub_statuses='-';
				$sub_statusesTXT='';
				$sub_statusesHEAD='';
				$sub_statusesHTML='';
				$sub_statusesFILE='';
				$sub_statusesTOP='';
				$sub_statusesARY=$MT;
				$sub_status_count=0;
				$PCusers='-';
				$PCusersARY=$MT;
				$PCuser_namesARY=$MT;
				$user_count=0;
				$i=0;
				$query = $this->reportsdb->query("SELECT user,SUM(pause_sec) AS pause_sec,sub_status FROM vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND pause_sec > 0 AND pause_sec < 65000 $ul GROUP BY user,sub_status ORDER BY user,sub_status DESC LIMIT 10000000");
				$pause_sec_ct = $query->num_rows();
	
				foreach ($query->result() as $i => $row)
					{
					$PCuser[$i] =		$row->user;
					$PCpause_sec[$i] =	$row->pause_sec;
					$sub_status[$i] =	$row->sub_status;
			
					if (!eregi("-$sub_status[$i]-", $sub_statuses))
						{
						$sub_statusesFILE .= ",$sub_status[$i]";
						$sub_statuses .= "$sub_status[$i]-";
						$sub_statusesARY[$sub_status_count] = $sub_status[$i];
						$sub_statusesTOP .= "<td><div align=\"center\" class=\"style4\" nowrap><strong> &nbsp;$sub_status[$i]&nbsp; </strong></div></td>";
						$sub_status_count++;
						}
					if (!eregi("-$PCuser[$i]-", $PCusers))
						{
						$PCusersARY[$user_count] = $PCuser[$i];
						$user_count++;
						}
			
					$i++;
					}
				### END gather pause code information by user IDs
			
				##### BEGIN Gather all agent time records and parse through them in PHP to save on DB load
				$query = $this->reportsdb->query("SELECT user,wait_sec,talk_sec,dispo_sec,pause_sec,lead_id,status,dead_sec FROM vicidial_agent_log WHERE date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' $ul LIMIT 10000000");
				$agent_time_ct = $query->num_rows();
				$j=0;
				$k=0;
				$uc=0;
				foreach ($query->result() as $i => $row)
					{
					$user =			$row->user;
					$wait =			$row->wait_sec;
					$talk =			$row->talk_sec;
					$dispo =		$row->dispo_sec;
					$pause =		$row->pause_sec;
					$lead =			$row->lead_id;
					$status =		$row->status;
					$dead =			$row->dead_sec;
					if ($wait > 65000) {$wait=0;}
					if ($talk > 65000) {$talk=0;}
					if ($dispo > 65000) {$dispo=0;}
					if ($pause > 65000) {$pause=0;}
					if ($dead > 65000) {$dead=0;}
					$customer =		($talk - $dead);
					if ($customer < 1)
						{$customer=0;}
					$TOTwait =	($TOTwait + $wait);
					$TOTtalk =	($TOTtalk + $talk);
					$TOTdispo =	($TOTdispo + $dispo);
					$TOTpause =	($TOTpause + $pause);
					$TOTdead =	($TOTdead + $dead);
					$TOTcustomer =	($TOTcustomer + $customer);
					$TOTALtime = ($TOTALtime + $pause + $dispo + $talk + $wait);
					if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$TOTcalls++;}
					
					$user_found=0;
					if ($uc < 1) 
						{
						$Suser[$uc] = $user;
						$uc++;
						}
					$m=0;
					while ( ($m < $uc) and ($m < 50000) )
						{
						if ($user == "$Suser[$m]")
							{
							$user_found++;
			
							$Swait[$m] =	($Swait[$m] + $wait);
							$Stalk[$m] =	($Stalk[$m] + $talk);
							$Sdispo[$m] =	($Sdispo[$m] + $dispo);
							$Spause[$m] =	($Spause[$m] + $pause);
							$Sdead[$m] =	($Sdead[$m] + $dead);
							$Scustomer[$m] =	($Scustomer[$m] + $customer);
							if ( ($lead > 0) and ((!eregi("NULL",$status)) and (strlen($status) > 0)) ) {$Scalls[$m]++;}
							}
						$m++;
						}
					if ($user_found < 1)
						{
						$Scalls[$uc] =	0;
						$Suser[$uc] =	$user;
						$Swait[$uc] =	$wait;
						$Stalk[$uc] =	$talk;
						$Sdispo[$uc] =	$dispo;
						$Spause[$uc] =	$pause;
						$Sdead[$uc] =	$dead;
						$Scustomer[$uc] =	$customer;
						if ($lead > 0) {$Scalls[$uc]++;}
						$uc++;
						}
	
					}
				if ($DB) {echo "{$this->lang->line("go_done_gathering")} $i {$this->lang->line("go_records_analyzing")}<BR>\n";}
				##### END Gather all agent time records and parse through them in PHP to save on DB load
			
				############################################################################
				##### END gathering information from the database section
				############################################################################
			
				##### BEGIN print the output to screen or put into file output variable
				if ($file_download > 0)
					{
					$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
					$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to")} $toDate\n\n";
					$file_output .= "{$this->lang->line("go_user_id_c_tc_at_w_t_d_p_w_c")},$sub_statusesFILE\n";
					}
				##### END print the output to screen or put into file output variable
			
				############################################################################
				##### BEGIN formatting data for output section
				############################################################################
			
				##### BEGIN loop through each user formatting data for output
				$AUTOLOGOUTflag=0;
				$m=0;
				while ( ($m < $uc) and ($m < 50000) )
					{
					$SstatusesHTML='';
					$SstatusesFILE='';
					$Stime[$m] = ($Swait[$m] + $Stalk[$m] + $Sdispo[$m] + $Spause[$m]);
					$RAWuser = $Suser[$m];
					$RAWcalls = $Scalls[$m];
					$RAWtimeSEC = $Stime[$m];
			
					$Swait[$m]=		$this->go_sec_convert($Swait[$m],'H'); 
					$Stalk[$m]=		$this->go_sec_convert($Stalk[$m],'H'); 
					$Sdispo[$m]=	$this->go_sec_convert($Sdispo[$m],'H'); 
					$Spause[$m]=	$this->go_sec_convert($Spause[$m],'H'); 
					$Sdead[$m]=		$this->go_sec_convert($Sdead[$m],'H'); 
					$Scustomer[$m]=	$this->go_sec_convert($Scustomer[$m],'H'); 
					$Stime[$m]=		$this->go_sec_convert($Stime[$m],'H'); 
			
					$RAWtime = $Stime[$m];
					$RAWwait = $Swait[$m];
					$RAWtalk = $Stalk[$m];
					$RAWdispo = $Sdispo[$m];
					$RAWpause = $Spause[$m];
					$RAWdead = $Sdead[$m];
					$RAWcustomer = $Scustomer[$m];
			
					$n=0;
					$user_name_found=0;
					while ($n < $user_ct)
						{
						//if (strtolower($Suser[$m]) == strtolower("$ULuser[$n]"))
						if ($Suser[$m] == "$ULuser[$n]")
							{
							$user_name_found++;
							$RAWname = $ULname[$n];
							$Sname[$m] = $ULname[$n];
							}
						$n++;
						}
					if ($user_name_found < 1)
						{
						$RAWname =		"{$this->lang->line("go_not_system")}";
						$Sname[$m] =	$RAWname;
						}
			
					$n=0;
					$punches_found=0;
					while ($n < $punches_to_print)
						{
						if ($Suser[$m] == "$TCuser[$n]")
							{
							$punches_found++;
							$RAWtimeTCsec =		$TCtime[$n];
							$TOTtimeTC =		($TOTtimeTC + $TCtime[$n]);
							$StimeTC[$m]=		$this->go_sec_convert($TCtime[$n],'H'); 
							$RAWtimeTC =		$StimeTC[$m];
							$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
							}
						$n++;
						}
					if ($punches_found < 1)
						{
						$RAWtimeTCsec =		"0";
						$StimeTC[$m]=		"0:00"; 
						$RAWtimeTC =		$StimeTC[$m];
						$StimeTC[$m] =		sprintf("%10s", $StimeTC[$m]);
						}
			
					### Check if the user had an AUTOLOGOUT timeclock event during the time period
					$TCuserAUTOLOGOUT = ' ';
					$query = $this->reportsdb->query("SELECT COUNT(*) as cnt FROM vicidial_timeclock_log WHERE event='AUTOLOGOUT' AND user='$Suser[$m]' AND date_format(event_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$timeclock_ct = $query->num_rows();
	
					if ($autologout_results > 0)
						{
						$row=$query->row();
						if ($row->cnt > 0)
							{
							$TCuserAUTOLOGOUT =	'*';
							$AUTOLOGOUTflag++;
							}
						}
			
					### BEGIN loop through each status ###
					$n=0;
					while ($n < $sub_status_count)
						{
						$Sstatus=$sub_statusesARY[$n];
						$SstatusTXT='';
						### BEGIN loop through each stat line ###
						$i=0; $status_found=0;
						while ( ($i < $pause_sec_ct) and ($status_found < 1) )
							{
							if ( ($Suser[$m]=="$PCuser[$i]") and ($Sstatus=="$sub_status[$i]") )
								{
								$USERcodePAUSE_MS =		$this->go_sec_convert($PCpause_sec[$i],'H');
								if (strlen($USERcodePAUSE_MS)<1) {$USERcodePAUSE_MS='0';}
								$pfUSERcodePAUSE_MS =	sprintf("%10s", $USERcodePAUSE_MS);
	
								$SstatusesFILE .= ",$USERcodePAUSE_MS";
								$Sstatuses[$m] .= "<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $USERcodePAUSE_MS &nbsp;</div></td>";
								$status_found++;
								}
							$i++;
							}
						if ($status_found < 1)
							{
							$SstatusesFILE .= ",0:00";
							$Sstatuses[$m] .= "<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; 0:00 &nbsp;</div></td>";
							}
						### END loop through each stat line ###
						$n++;
						}
					### END loop through each status ###
	
					if ($file_download > 0)
						{
						if (strlen($RAWtime)<1) {$RAWtime='0';}
						if (strlen($RAWwait)<1) {$RAWwait='0';}
						if (strlen($RAWtalk)<1) {$RAWtalk='0';}
						if (strlen($RAWdispo)<1) {$RAWdispo='0';}
						if (strlen($RAWpause)<1) {$RAWpause='0';}
						if (strlen($RAWdead)<1) {$RAWdead='0';}
						if (strlen($RAWcustomer)<1) {$RAWcustomer='0';}
						$fileToutput = "$RAWname,$RAWuser,$RAWcalls,$RAWtimeTC,$RAWtime,$RAWwait,$RAWtalk,$RAWdispo,$RAWpause,$RAWdead,$RAWcustomer,$SstatusesFILE\n";
						}
					$Scalls[$m] = ($Scalls[$m] > 0) ? $Scalls[$m] : 0;
					
					if ($x==0) {
						$bgcolor = "#E0F8E0";
						$x=1;
					} else {
						$bgcolor = "#EFFBEF";
						$x=0;
					}
			//				<td><div align=\"right\" class=\"style4\">&nbsp; $StimeTC[$m]$TCuserAUTOLOGOUT &nbsp;</div></td>
					$Toutput = "  <tr style=\"background-color:$bgcolor;\">
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\">&nbsp; $Sname[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\">&nbsp; $Suser[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Scalls[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Stime[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Swait[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Stalk[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Sdispo[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Spause[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Sdead[$m] &nbsp;</div></td>
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $Scustomer[$m] &nbsp;</div></td>
							</tr>";
			
					$Boutput = "  <tr style=\"background-color:$bgcolor;\">
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\">&nbsp; $Sname[$m] &nbsp;</div></td>
							$Sstatuses[$m]
							</tr>";
			
					$TOPsorted_output[$m] = $Toutput;
					$BOTsorted_output[$m] = $Boutput;
					$TOPsorted_outputFILE[$m] = $fileToutput;
			
					if (!ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage))
						if ($file_download > 0)
							{$file_output .= "$fileToutput";}
			
					if ($TOPsortMAX < $TOPsortTALLY[$m]) {$TOPsortMAX = $TOPsortTALLY[$m];}
			
			#		echo "$Suser[$m]|$Sname[$m]|$Swait[$m]|$Stalk[$m]|$Sdispo[$m]|$Spause[$m]|$Scalls[$m]\n";
					$m++;
					}
				##### END loop through each user formatting data for output
			
			
				$TOT_AGENTS = $m;
			// 	### BEGIN sort through output to display properly ###
				if ( ($TOT_AGENTS > 0) and (ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage)) )
					{
					if (ereg("ID",$stage))
						{sort($TOPsort, SORT_NUMERIC);}
					if (ereg("TIME|LEADS|TCLOCK",$stage))
						{rsort($TOPsort, SORT_NUMERIC);}
					if (ereg("NAME",$stage))
						{rsort($TOPsort, SORT_STRING);}
			
					$m=0;
					while ($m < $k)
						{
						$sort_split = explode("-----",$TOPsort[$m]);
						$i = $sort_split[1];
						$sort_order[$m] = "$i";
						if ($file_download > 0)
							{$file_output .= "$TOPsorted_outputFILE[$i]";}
						$m++;
						}
					}
				### END sort through output to display properly ###
			
				############################################################################
				##### END formatting data for output section
				############################################################################
			
			
			
			
				############################################################################
				##### BEGIN last line totals output section
				############################################################################
				$SUMstatusesHTML='';
				$SUMstatusesFILE='';
				$TOTtotPAUSE=0;
				$n=0;
				while ($n < $sub_status_count)
					{
					$Scalls=0;
					$Sstatus=$sub_statusesARY[$n];
					$SUMstatusTXT='';
					### BEGIN loop through each stat line ###
					$i=0; $status_found=0;
					while ($i < $pause_sec_ct)
						{
						if ($Sstatus=="$sub_status[$i]")
							{
							$Scalls =		($Scalls + $PCpause_sec[$i]);
							$status_found++;
							}
						$i++;
						}
					### END loop through each stat line ###
					if ($status_found < 1)
						{
						$SUMstatuses .= "<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; 0:00 &nbsp;</div></td>";
						}
					else
						{
						$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);
			
						$USERsumstatPAUSE_MS =		$this->go_sec_convert($Scalls,'H'); 
						$pfUSERsumstatPAUSE_MS =	sprintf("%11s", $USERsumstatPAUSE_MS);
	
						$SUMstatusesFILE .= ",$USERsumstatPAUSE_MS";
						$SUMstatuses .= "<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\">&nbsp; $USERsumstatPAUSE_MS &nbsp;</div></td>";
						}
					$n++;
					}
				### END loop through each status ###
			
				### call function to calculate and print dialable leads
				$TOTwait = $this->go_sec_convert($TOTwait,'H');
				$TOTtalk = $this->go_sec_convert($TOTtalk,'H');
				$TOTdispo = $this->go_sec_convert($TOTdispo,'H');
				$TOTpause = $this->go_sec_convert($TOTpause,'H');
				$TOTdead = $this->go_sec_convert($TOTdead,'H');
				$TOTcustomer = $this->go_sec_convert($TOTcustomer,'H');
				$TOTALtime = $this->go_sec_convert($TOTALtime,'H');
				$TOTtimeTC = $this->go_sec_convert($TOTtimeTC,'H');
	
				if ($file_download > 0)
					{
					$file_output .= "{$this->lang->line("go_total_agents")}: $TOT_AGENTS,$TOTcalls,$TOTtimeTC,$TOTALtime,$TOTwait,$TOTtalk,$TOTdispo,$TOTpause,$TOTdead,$TOTcustomer,$SUMstatusesFILE\n";
					}
				############################################################################
				##### END formatting data for output section
				############################################################################
				
				$return['TOPsorted_output']		= $TOPsorted_output;
				$return['BOTsorted_output']		= $BOTsorted_output;
				$return['TOPsorted_outputFILE']	= $TOPsorted_outputFILE;
				$return['TOTwait']				= $TOTwait;
				$return['TOTtalk']				= $TOTtalk;
				$return['TOTdispo']				= $TOTdispo;
				$return['TOTpause']				= $TOTpause;
				$return['TOTdead']				= $TOTdead;
				$return['TOTcustomer']			= $TOTcustomer;
				$return['TOTALtime']			= $TOTALtime;
				$return['TOTtimeTC']			= $TOTtimeTC;
				$return['sub_statusesTOP']		= $sub_statusesTOP;
				$return['SUMstatuses']			= $SUMstatuses;
				$return['TOT_AGENTS']			= $TOT_AGENTS;
				$return['TOTcalls']				= $TOTcalls;
				$return['file_output']			= $file_output;
			}
			
			// Agent Performance Detail
			if ($pageTitle == "agent_pdetail") {
				$statusesFILE='';
				$statuses='-';
				$statusesARY[0]='';
				$j=0;
				$users='-';
				$usersARY[0]='';
				$user_namesARY[0]='';
				$k=0;
				if ($this->commonhelper->checkIfTenant($userGroup))
				    $userGroupSQL = "and vicidial_users.user_group='$userGroup'";
				
				$query = $this->reportsdb->query("select count(*) as calls,sum(talk_sec) as talk,full_name,vicidial_users.user as user,sum(pause_sec) as pause_sec,sum(wait_sec) as wait_sec,sum(dispo_sec) as dispo_sec,status,sum(dead_sec) as dead_sec from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user $userGroupSQL and campaign_id='$campaignID' and pause_sec<65000 and wait_sec<65000 and talk_sec<65000 and dispo_sec<65000 group by user,full_name,status order by full_name,user,status desc limit 500000");
				$rows_to_print = $query->num_rows();
	
				foreach($query->result() as $i => $row)
					{
					$calls[$i] =		$row->calls;
					$talk_sec[$i] =		$row->talk;
					$full_name[$i] =	$row->full_name;
					$user[$i] =		$row->user;
					$pause_sec[$i] =	$row->pause_sec;
					$wait_sec[$i] =		$row->wait_sec;
					$dispo_sec[$i] =	$row->dispo_sec;
					$status[$i] =		$row->status;
					$dead_sec[$i] =		$row->dead_sec;
					$customer_sec[$i] =	($talk_sec[$i] - $dead_sec[$i]);
					if ($customer_sec[$i] < 1)
						{$customer_sec[$i]=0;}
					if ( (!eregi("-$status[$i]-", $statuses)) and (strlen($status[$i])>0) )
						{
						$statusesFILE .= ",$status[$i]";
						$statuses .= "$status[$i]-";
						$SUMstatuses .= "$status[$i] ";
						$statusesARY[$j] = $status[$i];
						$SstatusesTOP .= "<td nowrap><div align=\"center\" class=\"style4\"><strong>&nbsp; $status[$i] &nbsp;</strong></div></td>";
						$j++;
						}
					if (!eregi("-$user[$i]-", $users))
						{
						$users .= "$user[$i]-";
						$usersARY[$k] = $user[$i];
						$user_namesARY[$k] = $full_name[$i];
						$k++;
						}
				
					$i++;
					}
				
				if ($file_download > 0)
					{
					$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
					$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
					$file_output .= "{$this->lang->line("go_user_name_id_c_at_p_a_w_t_t")}$statusesFILE\n";
					}
				
				### BEGIN loop through each user ###
				$m=0;
				while ($m < $k)
					{
					$Suser=$usersARY[$m];
					$Sfull_name=$user_namesARY[$m];
					$Stime=0;
					$Scalls=0;
					$Stalk_sec=0;
					$Spause_sec=0;
					$Swait_sec=0;
					$Sdispo_sec=0;
					$Sdead_sec=0;
					$Scustomer_sec=0;
					$SstatusesHTML='';
					$SstatusesFILE='';
				
					### BEGIN loop through each status ###
					$n=0;
					while ($n < $j)
						{
						$Sstatus=$statusesARY[$n];
						$SstatusTXT='';
						### BEGIN loop through each stat line ###
						$i=0; $status_found=0;
						while ($i < $rows_to_print)
							{
							if ( ($Suser=="$user[$i]") and ($Sstatus=="$status[$i]") )
								{
								$Scalls =		($Scalls + $calls[$i]);
								$Stalk_sec =	($Stalk_sec + $talk_sec[$i]);
								$Spause_sec =	($Spause_sec + $pause_sec[$i]);
								$Swait_sec =	($Swait_sec + $wait_sec[$i]);
								$Sdispo_sec =	($Sdispo_sec + $dispo_sec[$i]);
								$Sdead_sec =	($Sdead_sec + $dead_sec[$i]);
								$Scustomer_sec =	($Scustomer_sec + $customer_sec[$i]);
								$SstatusesFILE .= ",$calls[$i]";
								$SstatusesMID[$m] .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $calls[$i] &nbsp;</div></td>";
								$status_found++;
								}
							$i++;
							}
						if ($status_found < 1)
							{
							$SstatusesFILE .= ",0";
							$SstatusesMID[$m] .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; 0 &nbsp;</div></td>";
							}
						### END loop through each stat line ###
						$n++;
						}
					### END loop through each status ###
					$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
					$TOTcalls=($TOTcalls + $Scalls);
					$TOTtime=($TOTtime + $Stime);
					$TOTtotTALK=($TOTtotTALK + $Stalk_sec);
					$TOTtotWAIT=($TOTtotWAIT + $Swait_sec);
					$TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);
					$TOTtotDISPO=($TOTtotDISPO + $Sdispo_sec);
					$TOTtotDEAD=($TOTtotDEAD + $Sdead_sec);
					$TOTtotCUSTOMER=($TOTtotCUSTOMER + $Scustomer_sec);
					$Stime = ($Stalk_sec + $Spause_sec + $Swait_sec + $Sdispo_sec);
					if ( ($Scalls > 0) and ($Stalk_sec > 0) ) {$Stalk_avg = ($Stalk_sec/$Scalls);}
						else {$Stalk_avg=0;}
					if ( ($Scalls > 0) and ($Spause_sec > 0) ) {$Spause_avg = ($Spause_sec/$Scalls);}
						else {$Spause_avg=0;}
					if ( ($Scalls > 0) and ($Swait_sec > 0) ) {$Swait_avg = ($Swait_sec/$Scalls);}
						else {$Swait_avg=0;}
					if ( ($Scalls > 0) and ($Sdispo_sec > 0) ) {$Sdispo_avg = ($Sdispo_sec/$Scalls);}
						else {$Sdispo_avg=0;}
					if ( ($Scalls > 0) and ($Sdead_sec > 0) ) {$Sdead_avg = ($Sdead_sec/$Scalls);}
						else {$Sdead_avg=0;}
					if ( ($Scalls > 0) and ($Scustomer_sec > 0) ) {$Scustomer_avg = ($Scustomer_sec/$Scalls);}
						else {$Scustomer_avg=0;}
				
					$RAWuser = $Suser;
					$RAWcalls = $Scalls;
				
					$pfUSERtime_MS =		$this->go_sec_convert($Stime,'H'); 
					$pfUSERtotTALK_MS =		$this->go_sec_convert($Stalk_sec,'H'); 
					$pfUSERavgTALK_MS =		$this->go_sec_convert($Stalk_avg,'M'); 
					$pfUSERtotPAUSE_MS =	$this->go_sec_convert($Spause_sec,'H'); 
					$pfUSERavgPAUSE_MS =	$this->go_sec_convert($Spause_avg,'M'); 
					$pfUSERtotWAIT_MS =		$this->go_sec_convert($Swait_sec,'H'); 
					$pfUSERavgWAIT_MS =		$this->go_sec_convert($Swait_avg,'M'); 
					$pfUSERtotDISPO_MS =	$this->go_sec_convert($Sdispo_sec,'H'); 
					$pfUSERavgDISPO_MS =	$this->go_sec_convert($Sdispo_avg,'M'); 
					$pfUSERtotDEAD_MS =		$this->go_sec_convert($Sdead_sec,'H'); 
					$pfUSERavgDEAD_MS =		$this->go_sec_convert($Sdead_avg,'M'); 
					$pfUSERtotCUSTOMER_MS =	$this->go_sec_convert($Scustomer_sec,'H'); 
					$pfUSERavgCUSTOMER_MS =	$this->go_sec_convert($Scustomer_avg,'M'); 
				
					$PAUSEtotal[$m] = $pfUSERtotPAUSE_MS;
				
					if ($file_download > 0) {
						$fileToutput = "$Sfull_name,=\"$Suser\",$Scalls,$pfUSERtime_MS,$pfUSERtotPAUSE_MS,$pfUSERavgPAUSE_MS,$pfUSERtotWAIT_MS,$pfUSERavgWAIT_MS,$pfUSERtotTALK_MS,$pfUSERavgTALK_MS,$pfUSERtotDISPO_MS,$pfUSERavgDISPO_MS,$pfUSERtotDEAD_MS,$pfUSERavgDEAD_MS,$pfUSERtotCUSTOMER_MS,$pfUSERavgCUSTOMER_MS$SstatusesFILE\n";
					}
					
					if ($x==0) {
						$bgcolor = "#E0F8E0";
						$x=1;
					} else {
						$bgcolor = "#EFFBEF";
						$x=0;
					}
					
					$Toutput = "<tr style=\"background-color:$bgcolor;\">
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $Sfull_name &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $Suser &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $Scalls &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtime_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtotPAUSE_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERavgPAUSE_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtotWAIT_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERavgWAIT_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtotTALK_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERavgTALK_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtotDISPO_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERavgDISPO_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtotDEAD_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERavgDEAD_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERtotCUSTOMER_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $pfUSERavgCUSTOMER_MS &nbsp;</div></td>
							</tr>";
				
					$Moutput = "<tr style=\"background-color:$bgcolor;\">
							<td style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\" style=\"font-size: 10px;\">&nbsp; $Sfull_name &nbsp;</div></td>
							$SstatusesMID[$m]
							</tr>";
				
					$TOPsorted_output[$m] = $Toutput;
					$MIDsorted_output[$m] = $Moutput;
					$TOPsorted_outputFILE[$m] = $fileToutput;
				
					if (!ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage))
						if ($file_download > 0)
							{$file_output .= "$fileToutput";}
				
					$m++;
					}
				### END loop through each user ###
				
				### BEGIN sort through output to display properly ###
				if (ereg("ID|TIME|LEADS",$stage))
					{
					if (ereg("ID",$stage))
						{sort($TOPsort, SORT_NUMERIC);}
					if (ereg("TIME|LEADS",$stage))
						{rsort($TOPsort, SORT_NUMERIC);}
				
					$m=0;
					while ($m < $k)
						{
						$sort_split = explode("-----",$TOPsort[$m]);
						$i = $sort_split[1];
						$sort_order[$m] = "$i";
						if ($file_download > 0)
							{$file_output .= "$TOPsorted_outputFILE[$i]";}
						$m++;
						}
					}
				### END sort through output to display properly ###
				
				
				
				###### LAST LINE FORMATTING ##########
				### BEGIN loop through each status ###
				$SUMstatusesHTML='';
				$SUMstatusesFILE='';
				$n=0;
				while ($n < $j)
					{
					$Scalls=0;
					$Sstatus=$statusesARY[$n];
					$SUMstatusTXT='';
					### BEGIN loop through each stat line ###
					$i=0; $status_found=0;
					while ($i < $rows_to_print)
						{
						if ($Sstatus=="$status[$i]")
							{
							$Scalls =		($Scalls + $calls[$i]);
							$status_found++;
							}
						$i++;
						}
					### END loop through each stat line ###
					if ($status_found < 1)
						{
						$SUMstatusesFILE .= ",0";
						$SstatusesSUM .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; 0 &nbsp;</div></td>";
						}
					else
						{
						$SUMstatusesFILE .= ",$Scalls";
						$SstatusesSUM .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; $Scalls &nbsp;</div></td>";
						}
					$n++;
					}
				### END loop through each status ###
				$TOT_AGENTS = $m;
				
				if ($TOTtotTALK < 1) {$TOTavgTALK = '0';}
				else {$TOTavgTALK = ($TOTtotTALK / $TOTcalls);}
				if ($TOTtotDISPO < 1) {$TOTavgDISPO = '0';}
				else {$TOTavgDISPO = ($TOTtotDISPO / $TOTcalls);}
				if ($TOTtotDEAD < 1) {$TOTavgDEAD = '0';}
				else {$TOTavgDEAD = ($TOTtotDEAD / $TOTcalls);}
				if ($TOTtotPAUSE < 1) {$TOTavgPAUSE = '0';}
				else {$TOTavgPAUSE = ($TOTtotPAUSE / $TOTcalls);}
				if ($TOTtotWAIT < 1) {$TOTavgWAIT = '0';}
				else {$TOTavgWAIT = ($TOTtotWAIT / $TOTcalls);}
				if ($TOTtotCUSTOMER < 1) {$TOTavgCUSTOMER = '0';}
				else {$TOTavgCUSTOMER = ($TOTtotCUSTOMER / $TOTcalls);}
				
				$TOTtime_MS =		$this->go_sec_convert($TOTtime,'H'); 
				$TOTtotTALK_MS =	$this->go_sec_convert($TOTtotTALK,'H'); 
				$TOTtotDISPO_MS =	$this->go_sec_convert($TOTtotDISPO,'H'); 
				$TOTtotDEAD_MS =	$this->go_sec_convert($TOTtotDEAD,'H'); 
				$TOTtotPAUSE_MS =	$this->go_sec_convert($TOTtotPAUSE,'H'); 
				$TOTtotWAIT_MS =	$this->go_sec_convert($TOTtotWAIT,'H'); 
				$TOTtotCUSTOMER_MS =	$this->go_sec_convert($TOTtotCUSTOMER,'H'); 
				$TOTavgTALK_MS =	$this->go_sec_convert($TOTavgTALK,'M'); 
				$TOTavgDISPO_MS =	$this->go_sec_convert($TOTavgDISPO,'H'); 
				$TOTavgDEAD_MS =	$this->go_sec_convert($TOTavgDEAD,'H'); 
				$TOTavgPAUSE_MS =	$this->go_sec_convert($TOTavgPAUSE,'H'); 
				$TOTavgWAIT_MS =	$this->go_sec_convert($TOTavgWAIT,'H'); 
				$TOTavgCUSTOMER_MS =	$this->go_sec_convert($TOTavgCUSTOMER,'H'); 
				
				if ($file_download > 0)
					{
					$file_output .= "{$this->lang->line("go_total_agents")}: $TOT_AGENTS,$TOTcalls,$TOTtime_MS,$TOTtotPAUSE_MS,$TOTavgPAUSE_MS,$TOTtotWAIT_MS,$TOTavgWAIT_MS,$TOTtotTALK_MS,$TOTavgTALK_MS,$TOTtotDISPO_MS,$TOTavgDISPO_MS,$TOTtotDEAD_MS,$TOTavgDEAD_MS,$TOTtotCUSTOMER_MS,$TOTavgCUSTOMER_MS$SUMstatusesFILE\n";
					}
				
				$sub_statuses='-';
				$sub_statusesTXT='';
				$sub_statusesFILE='';
				$sub_statusesHEAD='';
				$sub_statusesHTML='';
				$sub_statusesARY=$MT;
				$j=0;
				$PCusers='-';
				$PCusersARY=$MT;
				$PCuser_namesARY=$MT;
				$k=0;
				$query = $this->reportsdb->query("select full_name,vicidial_users.user as user,sum(pause_sec) as pause_sec,sub_status,sum(wait_sec + talk_sec + dispo_sec) as non_pause_sec from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user $userGroupSQL and campaign_id='$campaignID' and pause_sec<65000 group by user,full_name,sub_status order by full_name,user,sub_status desc limit 100000");
				$subs_to_print = $query->num_rows();
	
				foreach ($query->result() as $i => $row)
					{
					$PCfull_name[$i] =	$row->full_name;
					$PCuser[$i] =		$row->user;
					$PCpause_sec[$i] =	$row->pause_sec;
					$sub_status[$i] =	$row->sub_status;
					$PCnon_pause_sec[$i] =	$row->non_pause_sec;
				
					if (!eregi("-$sub_status[$i]-", $sub_statuses))
						{
						$sub_statuses .= "$sub_status[$i]-";
						$sub_statusesFILE .= ",$sub_status[$i]";
						$sub_statusesARY[$j] = $sub_status[$i];
						$SstatusesBOT .= "<td nowrap><div align=\"center\" class=\"style4\"><strong>&nbsp; $sub_status[$i] &nbsp;</strong></div></td>";
						$j++;
						}
					if (!eregi("-$PCuser[$i]-", $PCusers))
						{
						$PCusers .= "$PCuser[$i]-";
						$PCusersARY[$k] = $PCuser[$i];
						$PCuser_namesARY[$k] = $PCfull_name[$i];
						$k++;
						}
				
					$i++;
					}
				
				if ($file_download > 0) {
					$file_output .= "\n\n{$this->lang->line("go_user_name_id_t_np_p")},$sub_statusesFILE\n";
				}
				
				### BEGIN loop through each user ###
				$m=0;
				$Suser_ct = count($usersARY);
				$TOTtotNONPAUSE = 0;
				$TOTtotTOTAL = 0;
				
				while ($m < $k)
					{
					$d=0;
					while ($d < $Suser_ct)
						{
						if ($usersARY[$d] === "$PCusersARY[$m]")
							{$pcPAUSEtotal = $PAUSEtotal[$d];}
						$d++;
						}
					$Suser=$PCusersARY[$m];
					$Sfull_name=$PCuser_namesARY[$m];
					$Spause_sec=0;
					$Snon_pause_sec=0;
					$Stotal_sec=0;
					$SstatusesHTML='';
					$Ssub_statusesFILE='';
				
					### BEGIN loop through each status ###
					$n=0;
					while ($n < $j)
						{
						$Sstatus=$sub_statusesARY[$n];
						$SstatusTXT='';
						### BEGIN loop through each stat line ###
						$i=0; $status_found=0;
						while ($i < $subs_to_print)
							{
							if ( ($Suser=="$PCuser[$i]") and ($Sstatus=="$sub_status[$i]") )
								{
								$Spause_sec =	($Spause_sec + $PCpause_sec[$i]);
								$Snon_pause_sec =	($Snon_pause_sec + $PCnon_pause_sec[$i]);
								$Stotal_sec =	($Stotal_sec + $PCnon_pause_sec[$i] + $PCpause_sec[$i]);
				
								$USERcodePAUSE_MS =		$this->go_sec_convert($PCpause_sec[$i],'H'); 
								$pfUSERcodePAUSE_MS =	sprintf("%6s", $USERcodePAUSE_MS);
				
								$Ssub_statusesFILE .= ",$USERcodePAUSE_MS";
								$SstatusesBOTR[$m] .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; $USERcodePAUSE_MS &nbsp;</div></td>";
								$status_found++;
								}
							$i++;
							}
						if ($status_found < 1)
							{
							$Ssub_statusesFILE .= ",0";
							$SstatusesBOTR[$m] .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; 0:00 &nbsp;</div></td>";
							}
						### END loop through each stat line ###
						$n++;
						}
					### END loop through each status ###
					$TOTtotPAUSE=($TOTtotPAUSE + $Spause_sec);
				
					$TOTtotNONPAUSE = ($TOTtotNONPAUSE + $Snon_pause_sec);
					$TOTtotTOTAL = ($TOTtotTOTAL + $Stotal_sec);
				
					$pfUSERtotPAUSE_MS =		$this->go_sec_convert($Spause_sec,'H'); 
					$pfUSERtotNONPAUSE_MS =		$this->go_sec_convert($Snon_pause_sec,'H'); 
					$pfUSERtotTOTAL_MS =		$this->go_sec_convert($Stotal_sec,'H'); 
				
					if ($file_download > 0) {
						$fileToutput = "$Sfull_name,=\"$Suser\",$pfUSERtotTOTAL_MS,$pfUSERtotNONPAUSE_MS,$pfUSERtotPAUSE_MS,$Ssub_statusesFILE\n";
					}
					
					if ($x==1) {
						$bgcolor = "#E0F8E0";
						$x=0;
					} else {
						$bgcolor = "#EFFBEF";
						$x=1;
					}
					
					$Boutput = "<tr style=\"background-color:$bgcolor;\">
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\" style=\"font-size:10px\">&nbsp; $Sfull_name &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"left\" class=\"style4\" style=\"font-size:10px\">&nbsp; $Suser &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; $pfUSERtotTOTAL_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; $pfUSERtotNONPAUSE_MS &nbsp;</div></td>
							<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; $pfUSERtotPAUSE_MS &nbsp;</div></td>
							</tr>";
				
					$BOTsorted_output[$m] = $Boutput;
				
					if (!ereg("NAME|ID|TIME|LEADS|TCLOCK",$stage))
						if ($file_download > 0)
							{$file_output .= "$fileToutput";}
				
					$m++;
					}
				### END loop through each user ###
				
				### BEGIN sort through output to display properly ###
				if (ereg("ID|TIME|LEADS",$stage))
					{
					$n=0;
					while ($n <= $m)
						{
						$i = $sort_order[$m];
						if ($file_download > 0)
							{$file_output .= "$TOPsorted_outputFILE[$i]";}
						$m--;
						}
					}
				### END sort through output to display properly ###
				
				###### LAST LINE FORMATTING ##########
				### BEGIN loop through each status ###
				$SUMstatusesHTML='';
				$SUMsub_statusesFILE='';
				$TOTtotPAUSE=0;
				$n=0;
				while ($n < $j)
					{
					$Scalls=0;
					$Sstatus=$sub_statusesARY[$n];
					$SUMstatusTXT='';
					### BEGIN loop through each stat line ###
					$i=0; $status_found=0;
					while ($i < $subs_to_print)
						{
						if ($Sstatus=="$sub_status[$i]")
							{
							$Scalls =		($Scalls + $PCpause_sec[$i]);
							$status_found++;
							}
						$i++;
						}
					### END loop through each stat line ###
					if ($status_found < 1)
						{
						$SUMsub_statusesFILE .= ",0";
						$SstatusesBSUM .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; 0:00 &nbsp;</div></td>";
						}
					else
						{
						$TOTtotPAUSE = ($TOTtotPAUSE + $Scalls);
				
						$USERsumstatPAUSE_MS =		$this->go_sec_convert($Scalls,'H'); 
				
						$SUMsub_statusesFILE .= ",$USERsumstatPAUSE_MS";
						$SstatusesBSUM .= "<td nowrap style=\"border-top:dashed 1px #D0D0D0;\"><div align=\"right\" class=\"style4\" style=\"font-size:10px\">&nbsp; $USERsumstatPAUSE_MS &nbsp;</div></td>";
						}
					$n++;
					}
				### END loop through each status ###
					$TOT_AGENTS = $m;
				
					$TOTtotPAUSEB_MS =		$this->go_sec_convert($TOTtotPAUSE,'H'); 
					$TOTtotNONPAUSE_MS =	$this->go_sec_convert($TOTtotNONPAUSE,'H'); 
					$TOTtotTOTAL_MS =		$this->go_sec_convert($TOTtotTOTAL,'H'); 
				
					if ($file_download > 0) {
						$file_output .= "{$this->lang->line("go_total_agents")}: $TOT_AGENTS,$TOTtotTOTAL_MS,$TOTtotNONPAUSE_MS,$TOTtotPAUSE_MS,$SUMsub_statusesFILE\n";
					}
					
				$return['TOPsorted_output']		= $TOPsorted_output;
				$return['BOTsorted_output']		= $BOTsorted_output;
				$return['TOPsorted_outputFILE']	= $TOPsorted_outputFILE;
				$return['TOTwait']				= $TOTwait;
				$return['TOTtalk']				= $TOTtalk;
				$return['TOTdispo']				= $TOTdispo;
				$return['TOTpause']				= $TOTpause;
				$return['TOTdead']				= $TOTdead;
				$return['TOTcustomer']			= $TOTcustomer;
				$return['TOTALtime']			= $TOTALtime;
				$return['TOTtimeTC']			= $TOTtimeTC;
				$return['sub_statusesTOP']		= $sub_statusesTOP;
				$return['SUMstatuses']			= $SUMstatuses;
				$return['TOT_AGENTS']			= $TOT_AGENTS;
				$return['TOTcalls']				= $TOTcalls;
				$return['TOTtime_MS']			= $TOTtime_MS; 
				$return['TOTtotTALK_MS']		= $TOTtotTALK_MS; 
				$return['TOTtotDISPO_MS']		= $TOTtotDISPO_MS; 
				$return['TOTtotDEAD_MS']		= $TOTtotDEAD_MS; 
				$return['TOTtotPAUSE_MS']		= $TOTtotPAUSE_MS; 
				$return['TOTtotWAIT_MS']		= $TOTtotWAIT_MS; 
				$return['TOTtotCUSTOMER_MS']	= $TOTtotCUSTOMER_MS; 
				$return['TOTavgTALK_MS']		= $TOTavgTALK_MS; 
				$return['TOTavgDISPO_MS']		= $TOTavgDISPO_MS; 
				$return['TOTavgDEAD_MS']		= $TOTavgDEAD_MS; 
				$return['TOTavgPAUSE_MS']		= $TOTavgPAUSE_MS; 
				$return['TOTavgWAIT_MS']		= $TOTavgWAIT_MS; 
				$return['TOTavgCUSTOMER_MS']	= $TOTavgCUSTOMER_MS; 
				$return['TOTtotTOTAL_MS']		= $TOTtotTOTAL_MS;
				$return['TOTtotNONPAUSE_MS']	= $TOTtotNONPAUSE_MS; 
				$return['TOTtotPAUSEB_MS']		= $TOTtotPAUSEB_MS; 
				$return['MIDsorted_output']		= $MIDsorted_output; 
				$return['SstatusesTOP']			= $SstatusesTOP; 
				$return['SstatusesSUM']			= $SstatusesSUM;
				$return['SstatusesBOT']			= $SstatusesBOT; 
				$return['SstatusesBOTR']		= $SstatusesBOTR;
				$return['SstatusesBSUM']		= $SstatusesBSUM;
				$return['file_output']			= $file_output;
			}
			
			if ($pageTitle=="dispo") {
				$list_ids[0] = "{$this->lang->line("go_all")}";
				$total_all=($list_ids[0] == "{$this->lang->line("go_all")}") ? ''.$this->lang->line("go_all_list_ids").' '.$campaignID : ''.$this->lang->line("go_list_ids").': '.implode(',',$list_ids);
				if (isset($list_ids) && $list_ids[0] == "{$this->lang->line("go_all")}") {
					$query = $this->reportsdb->query("SELECT list_id FROM vicidial_lists WHERE campaign_id='$campaignID' ORDER BY list_id");
	
					foreach ($query->result() as $i => $row) {
						$list_ids[$i]=$row->list_id;
					}
					$i++;
					$list_ids[$i] = "{$userGroup}0";
				}
		
				# grab names of global statuses and statuses in the selected campaign
				$query = $this->reportsdb->query("SELECT status,status_name from vicidial_statuses order by status");
				$statuses_to_print = $query->num_rows();
	
				foreach ($query->result() as $o => $row) 
					{
					$statuses_list[$row->status] = $row->status_name;
					}
		
				$query = $this->reportsdb->query("SELECT status,status_name from vicidial_campaign_statuses where campaign_id='$campaignID' order by status");
				$Cstatuses_to_print = $query->num_rows();
	
				foreach ($query->result() as $o => $row) 
					{
					$statuses_list[$row->status] = $row->status_name;
					}
				# end grab status names
		
				$leads_in_list = 0;
				$leads_in_list_N = 0;
				$leads_in_list_Y = 0;
				$list = "'".implode("','",$list_ids)."'";
				$query = $this->reportsdb->query("SELECT status, if(called_count >= 10, 10, called_count) as called_count, count(*) as count from vicidial_list where list_id IN('".implode("','",$list_ids)."') and status NOT IN('DC','DNCC','XDROP') group by status, if(called_count >= 10, 10, called_count) order by status,called_count");
				$status_called_to_print = $query->num_rows();
				
				$sts=0;
				$first_row=1;
				$all_called_first=1000;
				$all_called_last=0;
				foreach ($query->result() as $o => $row) 
					{
					$leads_in_list = ($leads_in_list + $row->count);
					$count_statuses[$o]			= $row->status;
					$count_called[$o]			= $row->called_count;
					$count_count[$o]			= $row->count;
					$all_called_count[$row->called_count] = ($all_called_count[$row->called_count] + $row->count);
		
					if ( (strlen($status[$sts]) < 1) or ($status[$sts] != $row->status) )
						{
						if ($first_row) {$first_row=0;}
						else {$sts++;}
						$status[$sts] = $row->status;
						$status_called_first[$sts] = $row->called_count;
						if ($status_called_first[$sts] < $all_called_first) {$all_called_first = $status_called_first[$sts];}
						}
					$leads_in_sts[$sts] = ($leads_in_sts[$sts] + $row->count);
					$status_called_last[$sts] = $row->called_count;
					if ($status_called_last[$sts] > $all_called_last) {$all_called_last = $status_called_last[$sts];}
					}
		
		
				$TOPsorted_output = "<center>\n";
				$TOPsorted_output .= "<TABLE align=center cellpadding=1 cellspacing=1 style=\"width:50%;border:#D0D0D0 solid 1px; -moz-border-radius:5px; -khtml-border-radius:5px; -webkit-border-radius:5px; border-radius:5px;\">\n";
				$TOPsorted_output .= "<tr style=\"background-color:#FFFFFF\"><td align=center class=\"style3\"><strong>{$this->lang->line("go_status")}</strong></td><td align=center class=\"style3\"><strong>{$this->lang->line("go_status_name")}</strong></td>";
				$first = $all_called_first;
				while ($first <= $all_called_last)
					{
					if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='style="background-color:#FFF"';} 
					else{$AB='style="background-color:#FFF"';}
					if ($first >= 10) {$Fplus="+";}
					else {$Fplus='';}
					$TOPsorted_output .= "<td align=center class=\"style3\" $AB><strong>&nbsp;$first$Fplus&nbsp;</strong></td>";
					$first++;
					}
				$TOPsorted_output .= "<td align=center class=\"style3\" nowrap><strong>&nbsp;{$this->lang->line("go_sub_total")}&nbsp;</strong></td></tr>\n";
		
				$sts=0;
				$statuses_called_to_print = count($status);
				while ($statuses_called_to_print > $sts) 
					{
					$Pstatus = $status[$sts];
					if (eregi("1$|3$|5$|7$|9$", $sts))
						{$bgcolor='style="background-color:#E0F8E0;border-top:#D0D0D0 dashed 1px;"';   $AB='style="background-color:#EFFBEF;border-top:#D0D0D0 dashed 1px;"';} 
					else
						{$bgcolor='style="background-color:#EFFBEF;border-top:#D0D0D0 dashed 1px;"';   $AB='style="background-color:#E0F8E0;border-top:#D0D0D0 dashed 1px;"';}
						
						$TOPsorted_output .= "<tr><td class=\"style3\" align=center $bgcolor>&nbsp;$Pstatus&nbsp;</td><td nowrap class=\"style3\" align=center $bgcolor>&nbsp;$statuses_list[$Pstatus]&nbsp;</td>";
			
						$first = $all_called_first;
						while ($first <= $all_called_last)
							{
							if (eregi("1$|3$|5$|7$|9$", $sts))
								{
								if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='style="background-color:#E0F8E0;border-top:#D0D0D0 dashed 1px;"';} 
								else{$AB='style="background-color:#E0F8E0;border-top:#D0D0D0 dashed 1px;"';}
								}
							else
								{
								if (eregi("0$|2$|4$|6$|8$", $first)) {$AB='style="background-color:#EFFBEF;border-top:#D0D0D0 dashed 1px;"';} 
								else{$AB='style="background-color:#EFFBEF;border-top:#D0D0D0 dashed 1px;"';}
								}
			
							$called_printed=0;
							$o=0;
							while ($status_called_to_print > $o) 
								{
								if ( ($count_statuses[$o] == "$Pstatus") and ($count_called[$o] == "$first") )
									{
									$called_printed++;
									$TOPsorted_output .= "<td $AB align=center>&nbsp;$count_count[$o]&nbsp;</td>";
									}
			
								$o++;
								}
							if (!$called_printed) 
								{$TOPsorted_output .= "<td $AB align=center>&nbsp;0&nbsp;</td>";}
							$first++;
							}
						$TOPsorted_output .= "<td class=\"style3\" align=center $AB>$leads_in_sts[$sts]</td></tr>\n\n";
			
						$sts++;
	//					}
					}
		
				$TOPsorted_output .= "<tr><td align=center colspan=2 align=center class=\"style3\" nowrap style=\"border-top:#D0D0D0 dashed 1px;\">&nbsp;<b>{$this->lang->line("go_total_for")} ".$total_all."&nbsp;</td>";
				$first = $all_called_first;
				while ($first <= $all_called_last)
					{
					if (eregi("1$|3$|5$|7$|9$", $first)) {$AB='style="background-color:#FFF;border-top:#D0D0D0 dashed 1px;"';} 
					else{$AB='style="background-color:#FFF;border-top:#D0D0D0 dashed 1px;"';}
					if ($all_called_count[$first]) {
						$TOPsorted_output .= "<td align=center $AB class=\"style3\"><b>$all_called_count[$first]</td>";
					} else {
						$TOPsorted_output .= "<td $AB class=\"style3\" align=center>&nbsp;0&nbsp;</td>";
					}
					$first++;
					}
				$TOPsorted_output .= "<td align=center class=\"style3\" style=\"border-top:#D0D0D0 dashed 1px;\"><b>$leads_in_list</td></tr>\n";
				$TOPsorted_output .= "</table><br /><small style='color:red;'>{$this->lang->line("go_note_sel_camp")}</small></center>\n";
				
				$return['TOPsorted_output']		= $TOPsorted_output;
				$return['SUMstatuses']			= $sts;
			}
			
			if ($pageTitle == "sales_agent") {
				$list_ids = "{$this->lang->line("go_all")}";
				$list_id_query=(isset($list_ids) && $list_ids != "{$this->lang->line("go_all")}") ? "and vlog.list_id IN ('".implode("','",$list_ids)."')" : "";
				
				// Outbound Sales
				$query = $this->reportsdb->query("SELECT us.full_name AS full_name, us.user AS user,
						SUM(IF(vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale
						from vicidial_users as us, vicidial_log as vlog, vicidial_list as vl 
						where us.user=vlog.user 
						and vl.phone_number=vlog.phone_number 
						and vl.lead_id=vlog.lead_id 
						and vlog.length_in_sec>'0'
						and vlog.status in ('$statuses') 
						and date_format(vlog.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
						and vlog.campaign_id='$campaignID'
						group by us.full_name");
				$numO = $query->num_rows();
				
				$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
				$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
				$file_output .= "{$this->lang->line("go_outbound_sales_an_aid_sc")}";
				if ($numO) {
					$total_sales=0;
					foreach($query->result() as $row) {
					
						if ($x==1) {
							$bgcolor = "#E0F8E0";
							$x=0;
						} else {
							$bgcolor = "#EFFBEF";
							$x=1;
						}
					
						$file_output .= $row->full_name.",".$row->user.",".$row->sale."\n";
						$TOPsorted_output .= "<tr style=\"background-color:$bgcolor;\">";
						$TOPsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div class=\"style4\">&nbsp;".$row->full_name."&nbsp;</div></td>";
						$TOPsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$row->user."&nbsp;</div></td>";
						$TOPsorted_output .= "<td nowrap width=\"120\" style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$row->sale."&nbsp;</div></td>";
						$TOPsorted_output .= "</tr>";
						$total_out_sales=$total_out_sales+$row->sale;
					}
				}
				if ($total_out_sales < 1) {
					$file_output .= "{$this->lang->line("go_no_records_found")}";
				} else {
					$file_output .= "{$this->lang->line("go_total")},,$total_out_sales\n\n";
				}
				
				// Inbound Sales
				$query = $this->reportsdb->query("SELECT closer_campaigns FROM vicidial_campaigns WHERE campaign_id='".$campaignID."' ORDER BY campaign_id");
				$row = $query->row();
				$closer_camp_array=explode(" ",$row->closer_campaigns);
				$num=count($closer_camp_array);
			
				$x=0;
				while($x<$num) {
					if ($closer_camp_array[$x]!="-") {
							$closer_campaigns[$x]=$closer_camp_array[$x];
					}
					$x++;
				}
				$campaign_inb_query="vlog.campaign_id IN ('".implode("','",$closer_campaigns)."')";
				
				$query = $this->reportsdb->query("SELECT us.full_name AS full_name, us.user AS user,
						SUM(IF(vlog.status REGEXP '^($statusRX)$', 1, 0)) AS sale
						from vicidial_users as us, vicidial_closer_log as vlog, vicidial_list as vl 
						where us.user=vlog.user 
						and vl.phone_number=vlog.phone_number 
						and vl.lead_id=vlog.lead_id 
						and vlog.length_in_sec>'0' 
						and vlog.status in ('$statuses') 
						and date_format(vlog.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
						and $campaign_inb_query
						group by us.full_name");
				$numI = $query->num_rows();
				
				$file_output .= "{$this->lang->line("go_inbound_sales_an_ai_sc")}";
				if ($numI) {
					$total_sales=0;
	
					foreach($query->result() as $row) {
					
						if ($x==1) {
							$bgcolor = "#E0F8E0";
							$x=0;
						} else {
							$bgcolor = "#EFFBEF";
							$x=1;
						}          
					
						$file_output .= $row->full_name.",".$row->user.",".$row->sale."\n";
						$BOTsorted_output .= "<tr style=\"background-color:$bgcolor;\">";
						$BOTsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div class=\"style4\">&nbsp;".$row->full_name."&nbsp;</div></td>";
						$BOTsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$row->user."&nbsp;</div></td>";
						$BOTsorted_output .= "<td nowrap width=\"120\" style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$row->sale."&nbsp;</div></td>";
						$BOTsorted_output .= "</tr>";
						$total_in_sales=$total_in_sales+$row->sale;
					}
				}
				if ($total_in_sales < 1) {
					$file_output .= "{$this->lang->line("go_no_records_found")}";
				} else {
					$file_output .= "{$this->lang->line("go_total")},,$total_in_sales";
				}
				
				$return['TOPsorted_output']		= $TOPsorted_output;
				$return['BOTsorted_output']		= $BOTsorted_output;
				$return['TOToutbound']			= $total_out_sales;
				$return['TOTinbound']			= $total_in_sales;
				$return['file_output']			= $file_output;
			}
			
			if ($pageTitle == "sales_tracker") {
				$list_ids = "{$this->lang->line("go_all")}";
				$list_id_query=(isset($list_ids) && $list_ids != "{$this->lang->line("go_all")}") ? "and vlo.list_id IN ('".implode("','",$list_ids)."')" : "";
				
				if ($return['request']=='outbound') {
					$query = $this->reportsdb->query("select distinct(vl.phone_number) as phone_number,vlo.call_date as call_date,us.full_name as agent,
							vl.first_name as first_name,vl.last_name as last_name,vl.address1 as address,vl.city as city,vl.state as state,
							vl.postal_code as postal,vl.email as email,vl.alt_phone as alt_phone,vl.comments as comments,vl.lead_id
							from vicidial_log as vlo, vicidial_list as vl, vicidial_users as us 
							where us.user=vlo.user 
							and vl.phone_number=vlo.phone_number 
							and vl.lead_id=vlo.lead_id 
							and vlo.length_in_sec>'0' 
							and vlo.status in ('$statuses') 
							and date_format(vlo.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
							and vlo.campaign_id='$campaignID' 
							$list_id_query
							order by vlo.call_date ASC limit 2000");
					$TOPsorted_output = $query->result();
					
					if ($file_download > 0) {
						$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
						$file_output .= "{$this->lang->line("go_date_range")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
						$file_output .= "{$this->lang->line("go_outbound_sales_cdt_a_pn_f_l_a_c_s_p_e_an_c")}";
						
						foreach ($TOPsorted_output as $row) {
							$file_output .=$row->call_date.",".$row->agent.",".$row->phone_number.",".$row->first_name.",".$row->last_name.",".$row->address.",".$row->city.",".$row->state.",".$row->postal.",".$row->email.",".$row->alt_phone.",".$row->comments."\n";
						}
					}
				}
			
				if ($return['request']=='inbound') {
					$query = $this->reportsdb->query("SELECT closer_campaigns FROM vicidial_campaigns WHERE campaign_id='$campaignID' ORDER BY campaign_id");
					$row = $query->row();
					$closer_camp_array=explode(" ",$row->closer_campaigns);
					$num=count($closer_camp_array);
				
					$x=0;
					while($x<$num) {
						if ($closer_camp_array[$x]!="-") {
							$closer_campaigns[$x]=$closer_camp_array[$x];
						}
						$x++;
					}
					$campaign_inb_query="vlo.campaign_id IN ('".implode("','",$closer_campaigns)."')";
				
					$query = $this->reportsdb->query("select distinct(vl.phone_number) as phone_number,vlo.call_date as call_date,us.full_name as agent,
							vl.first_name as first_name,vl.last_name as last_name,vl.address1 as address,vl.city as city,vl.state as state,
							vl.postal_code as postal,vl.email as email,vl.alt_phone as alt_phone,vl.comments as comments,vl.lead_id
							from vicidial_closer_log as vlo, vicidial_list as vl, vicidial_users as us 
							where us.user=vl.user 
							and vl.phone_number=vlo.phone_number 
							and vl.lead_id=vlo.lead_id 
							and vlo.length_in_sec>'0' 
							and date_format(vlo.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'
							and $campaign_inb_query 
							and vlo.status in ('$statuses')
							order by vlo.call_date ASC limit 2000");
					$TOPsorted_output = $query->result();
					
					if ($file_download > 0) {
						$file_output  = "{$this->lang->line("go_campaign")},$campaignID - ".$resultu->campaign_name."\n";
						$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
						$file_output .= "{$this->lang->line("go_outbound_sales_cdt_a_pn_f_l_a_c_s_p_e_an_c")}";
						
						foreach ($TOPsorted_output as $row) {
							$file_output .=$row->call_date.",".$row->agent.",".$row->phone_number.",".$row->first_name.",".$row->last_name.",".$row->address.",".$row->city.",".$row->state.",".$row->postal.",".$row->email.",".$row->alt_phone.",".$row->comments."\n";
						}
					}
				}
				
				$return['TOPsorted_output']		= $TOPsorted_output;
				$return['file_output']			= $file_output;
			}
			
			if ($pageTitle == "inbound_report") {
				$query = $this->reportsdb->query("SELECT * FROM vicidial_closer_log WHERE campaign_id = '$campaignID' AND date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
				$TOPsorted_output = $query->result();
				
				if ($file_download > 0) {
					$file_output  = "{$this->lang->line("go_inbound_camp")},$campaignID - ".$resultu->campaign_name."\n";
					$file_output .= "{$this->lang->line("go_date_range_caps")},$fromDate {$this->lang->line("go_to_caps")} $toDate\n\n";
					$file_output .= "{$this->lang->line("go_date_aid_pn_t_cd_d")}";
					
					foreach ($TOPsorted_output as $row) {
						list($ldate, $ltime) = split(' ',$row->call_date);
						$phone_number = ($row->phone_number != "") ? $row->phone_number : "{$this->lang->line("go_not_registered")}";
						
						$file_output .= "$ldate,".$row->user.",$phone_number,$ltime,".$row->length_in_sec.",".$row->status."\n";
					}
				}
				
				$return['TOPsorted_output']		= $TOPsorted_output;
				$return['file_output']			= $file_output;
			}
			
			if ($pageTitle == "call_export_report") {
				//$return['allowed_campaigns']	= $this->go_getall_allowed_campaigns();
			    $groupId = $this->go_get_groupid();
				if (!$this->commonhelper->checkIfTenant($groupId)) {
				  $ul = '';
				  $user_group_SQL = '';
				} else {
				  $ul = "WHERE user_group='".$this->session->userdata('user_group')."'";
				  $user_group_SQL = "and (CASE WHEN vl.user!='VDAD' THEN vl.user_group = '".$this->session->userdata('user_group')."' ELSE 1=1 END)";
				}
				$query = $this->reportsdb->query("SELECT campaign_id FROM vicidial_campaigns $ul");
				foreach ($query->result() as $campid)
				{
				    $allowed_campaigns[] = $campid->campaign_id;
				}
				$return['allowed_campaigns']	= implode(",",$allowed_campaigns);
				$return['inbound_groups']		= $this->go_get_inbound_groups();
				
				$filterSQL = ($this->commonhelper->checkIfTenant($groupId)) ? "WHERE campaign_id IN ('".implode("','",$allowed_campaigns)."')" : "";
				$query = $this->reportsdb->query("SELECT list_id FROM vicidial_lists $filterSQL");
				$return['lists_to_print']		= $query->result();

				$query = $this->reportsdb->query("select status,status_name from vicidial_statuses union select status,status_name from vicidial_campaign_statuses $filterSQL");
				$return['statuses_to_print'] = $query->result();
				
				$query = $this->reportsdb->query("select custom_fields_enabled from system_settings");
				$custom_fields_enabled = $query->row();
				
				if (strlen($campaignID) > 4) {
					//$query = $this->reportsdb->query("");
					list($header_row, $rec_fields, $custom_fields, $call_notes, $export_fields) = explode(",",$request);
					list($campaign, $group, $list_id, $status) = split(",", $campaignID);
					$campaign = explode("+",eregi_replace("\+$",'',$campaign));
					$group = explode("+",eregi_replace("\+$",'',$group));
					$list_id = explode("+",eregi_replace("\+$",'',$list_id));
					$status = explode("+",eregi_replace("\+$",'',$status));
					
					$campaign_ct = count($campaign);
					$group_ct = count($group);
					$user_group_ct = count($group);
					$list_ct = count($list_id);
					$status_ct = count($status);
					$campaign_string='|';
					$group_string='|';
					$user_group_string='|';
					$list_string='|';
					$status_string='|';
					$outbound_calls=0;
					$export_rows='';
				
					$i=0;
					while($i < $campaign_ct)
						{
						   if (strlen($campaign[$i]) > 0) {
						      $campaign_string .= "$campaign[$i]|";
						      $campaign_SQL .= "'$campaign[$i]',";
						   }
						$i++;
						}
					if ( (ereg("--{$this->lang->line("go_none")}--",$campaign_string) ) or (strlen($campaign_SQL) < 1) )
						{
						//$campaign_SQL = "campaign_id IN('')";
						$campaign_SQL = "";
						$RUNcampaign=1;
						}
					else
						{
						$campaign_SQL = eregi_replace(",$",'',$campaign_SQL);
						$campaign_SQL = "and vl.campaign_id IN($campaign_SQL)";
						$RUNcampaign++;
						}
				
					$i=0;
					while($i < $group_ct)
						{
						   if (strlen($group[$i]) > 0) {
						      $group_string .= "$group[$i]|";
						      $group_SQL .= "'$group[$i]',";
						   }
						$i++;
						}
					if ( (ereg("--{$this->lang->line("go_none")}--",$group_string) ) or ($group_ct < 1) )
						{
						//$group_SQL = "campaign_id IN('')";
						$group_SQL = "";
						$RUNgroup=0;
						}
					else
						{
						$group_SQL = eregi_replace(",$",'',$group_SQL);
						if($group_SQL!=NULL){
                                                $group_SQL = "and vl.campaign_id IN($group_SQL)";
						}
						else {
						$group_SQL = "and vl.campaign_id IN('$group_SQL')";
						}
						$RUNgroup++;
						}
						
					//$user_group_SQL = "and vl.user_group = '".$return['groupId']."'";
					//$user_group_SQL = '';
					
					$i=0;
					while($i < $list_ct)
						{
						$list_string .= "$list_id[$i]|";
						$list_SQL .= "'$list_id[$i]',";
						$i++;
						}
					if ( (ereg("--{$this->lang->line("go_all")}--",$list_string) ) or ($list_ct < 1) )
						{
						$list_SQL = "";
						}
					else
						{
						$list_SQL = eregi_replace(",$",'',$list_SQL);
						$list_SQL = "and vi.list_id IN($list_SQL)";
						}
				
					$i=0;
					while($i < $status_ct)
						{
						$status_string .= "$status[$i]|";
						$status_SQL .= "'$status[$i]',";
						$i++;
						}
					if ( (ereg("--{$this->lang->line("go_all")}--",$status_string) ) or ($status_ct < 1) )
						{
						$status_SQL = "";
						}
					else
						{
						$status_SQL = eregi_replace(",$",'',$status_SQL);
						$status_SQL = "and vl.status IN ($status_SQL)";
						}
					
					if ($export_fields == "{$this->lang->line("go_extended")}")
						{
						$export_fields_SQL = ",entry_date,vi.called_count,last_local_call_time,modify_date,called_since_last_reset";
						$EFheader = ",entry_date,called_count,last_local_call_time,modify_date,called_since_last_reset";
						}
	
					$k=1;
					if ($RUNcampaign > 0)
						{
						$query = $this->reportsdb->query("SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.phone_number,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.alt_dial,vi.rank,vi.owner,vi.lead_id,vl.uniqueid,vi.entry_list_id$export_fields_SQL from vicidial_users vu,vicidial_log vl,vicidial_list vi where date_format(vl.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and (CASE WHEN vl.user!='VDAD' THEN vu.user=vl.user ELSE vl.user='VDAD' END) and vi.lead_id=vl.lead_id $list_SQL $campaign_SQL $user_group_SQL $status_SQL group by vl.call_date order by vl.call_date limit 100000");
						$outbound_to_print = $query->num_rows();
						if ($outbound_to_print < 1)
							{
							$err_nooutbcalls = "{$this->lang->line("go_no_outbound_calls")}";
				// 			exit;
							}
						else
							{
							foreach ($query->result_array() as $row)
								{
								$row['comments'] = preg_replace("/\n|\r/",'!N',$row['comments']);
				
								$export_status[$k] =		$row['status'];
								$export_list_id[$k] =		$row['list_id'];
								$export_lead_id[$k] =		$row['lead_id'];
								$export_uniqueid[$k] =		$row['uniqueid'];
								$export_vicidial_id[$k] =	$row['uniqueid'];
								$export_entry_list_id[$k] =	$row['entry_list_id'];
								$export_fieldsDATA='';
								if ($export_fields == "{$this->lang->line("go_extended")}")
									{$export_fieldsDATA = $row['entry_date'].",".$row['called_count'].",".$row['last_local_call_time'].",".$row['modify_date'].",".$row['called_since_last_reset'].",";}
								$export_rows[$k] = $row['call_date'].",".$row['phone_number'].",".$row['status'].",".$row['user'].",\"".$row['full_name']."\",".$row['campaign_id'].",\"".$row['vendor_lead_code']."\",".$row['source_id'].",".$row['list_id'].",".$row['gmt_offset_now'].",\"".$row['phone_code']."\",\"".$row['phone_number']."\",\"".$row['title']."\",\"".$row['first_name']."\",\"".$row['middle_initial']."\",\"".$row['last_name']."\",\"".$row['address1']."\",\"".$row['address2']."\",\"".$row['address3']."\",\"".$row['city']."\",\"".$row['state']."\",\"".$row['province']."\",\"".$row['postal_code']."\",\"".$row['country_code']."\",\"".$row['gender']."\",\"".$row['date_of_birth']."\",\"".$row['alt_phone']."\",\"".$row['email']."\",\"".$row['security_phrase']."\",\"".$row['comments']."\",".$row['length_in_sec'].",\"".$row['user_group']."\",\"".$row['alt_dial']."\",\"".$row['rank']."\",\"".$row['owner']."\",".$row['lead_id'].",$export_fieldsDATA";
								$k++;
								$outbound_calls++;
								}
							}
						}
						
					if ($header_row=="{$this->lang->line("go_yes")}")
						{
						$RFheader = '';
						$NFheader = '';
						$CFheader = '';
						$EXheader = '';
						if ($rec_fields=="{$this->lang->line("go_id")}")
							{$RFheader = ",recording_id";}
						if ($rec_fields=="{$this->lang->line("go_filename")}")
							{$RFheader = ",recording_filename";}
						if ($rec_fields=="{$this->lang->line("go_location")}")
							{$RFheader = ",recording_location";}
						if ($rec_fields=="{$this->lang->line("go_all")}")
							{$RFheader = ",recording_id,recording_filename,recording_location";}
						if ($export_fields=="{$this->lang->line("go_extended")}")
							{$EXheader = ",uniqueid,caller_code,server_ip,hangup_cause,dialstatus,channel,dial_time,answered_time,cpd_result";}
						if ($call_notes=="{$this->lang->line("go_yes")}")
							{$NFheader = ",call_notes";}
						//if ( ($custom_fields_enabled > 0) and ($custom_fields=='YES') )
						//	{$CFheader = ",custom_fields";}
						if ( ($custom_fields_enabled > 0) and ($custom_fields=="{$this->lang->line("go_yes")}") )
						   {
						      $x = 1;
						      while ($k > $x) {
							 $CF_list_id = $export_list_id[$x];
							 if ($export_entry_list_id[$x] > 99)
								 {$CF_list_id = $export_entry_list_id[$x];}
							 $stmt="SHOW TABLES LIKE \"custom_$CF_list_id\";";
							 $query=$this->reportsdb->query($stmt);
							 $tablecount_to_print = $query->num_rows();
							 if ($tablecount_to_print > 0) 
								{
								$stmt = "describe custom_$CF_list_id;";
								$query=$this->reportsdb->query($stmt);
								foreach ($query->result() as $row)
								       {
									   if ($row->Field != "lead_id" && !in_array($row->Field,explode(",",$CFheader))) {
									      $CFheader .= ",".$row->Field;
									      $CFdata[$row->Field] = '';
									   }
								       }
								}
							 $x++;
						      }
						   }
			
						$export_rows[0] = "call_date,phone_number,status,user,full_name,campaign_id,vendor_lead_code,source_id,list_id,gmt_offset_now,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,length_in_sec,user_group,alt_dial,rank,owner,lead_id$EFheader,list_name,list_description,status_name$RFheader$EXheader$NFheader$CFheader";
						}
						
					  if ($RUNgroup > 0)
						{
						$query = $this->reportsdb->query("SELECT vl.call_date,vl.phone_number,vl.status,vl.user,vu.full_name,vl.campaign_id,vi.vendor_lead_code,vi.source_id,vi.list_id,vi.gmt_offset_now,vi.phone_code,vi.phone_number,vi.title,vi.first_name,vi.middle_initial,vi.last_name,vi.address1,vi.address2,vi.address3,vi.city,vi.state,vi.province,vi.postal_code,vi.country_code,vi.gender,vi.date_of_birth,vi.alt_phone,vi.email,vi.security_phrase,vi.comments,vl.length_in_sec,vl.user_group,vl.queue_seconds,vi.rank,vi.owner,vi.lead_id,vl.closecallid,vi.entry_list_id,vl.uniqueid$export_fields_SQL from vicidial_users vu,vicidial_closer_log vl,vicidial_list vi where date_format(vl.call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and vu.user=vl.user and vi.lead_id=vl.lead_id $list_SQL $group_SQL $user_group_SQL $status_SQL order by vl.call_date limit 100000");
						$inbound_to_print = $query->num_rows();
						if ( ($inbound_to_print < 1) and ($outbound_calls < 1) )
							{
							$err_noinbcalls = "{$this->lang->line("go_no_outbound_calls")}";
				// 			exit;
							}
						else
							{
							foreach ($query->result_array() as $row)
								{
								$row['comments'] = preg_replace("/\n|\r/",'!N',$row['comments']);
				
								$export_status[$k] =		$row['status'];
								$export_list_id[$k] =		$row['list_id'];
								$export_lead_id[$k] =		$row['lead_id'];
								$export_vicidial_id[$k] =	$row['closecallid'];
								$export_entry_list_id[$k] =	$row['entry_list_id'];
								$export_uniqueid[$k] =		$row['uniqueid'];
								$export_fieldsDATA='';
								if ($export_fields == "{$this->lang->line("go_extended")}")
									{$export_fieldsDATA = $row['entry_date'].",".$row['called_count'].",".$row['last_local_call_time'].",".$row['modify_date'].",".$row['called_since_last_reset'].",";}
								$export_rows[$k] = $row['call_date'].",\"".$row['phone_number']."\",\"".$row['status']."\",\"".$row['user']."\",\"".$row['full_name']."\",".$row['campaign_id'].",\"".$row['vendor_lead_code']."\",\"".$row['source_id']."\",".$row['list_id'].",".$row['gmt_offset_now'].",\"".$row['phone_code']."\",\"".$row['phone_number']."\",\"".$row['title']."\",\"".$row['first_name']."\",\"".$row['middle_initial']."\",\"".$row['last_name']."\",\"".$row['address1']."\",\"".$row['address2']."\",\"".$row['address3']."\",\"".$row['city']."\",\"".$row['state']."\",\"".$row['province']."\",\"".$row['postal_code']."\",\"".$row['country_code']."\",\"".$row['gender']."\",\"".$row['date_of_birth']."\",\"".$row['alt_phone']."\",\"".$row['email']."\",\"".$row['security_phrase']."\",\"".$row['comments']."\",".$row['length_in_sec'].",\"".$row['user_group']."\",".$row['queue_seconds'].",\"".$row['rank']."\",\"".$row['owner']."\",".$row['lead_id'].",$export_fieldsDATA";
								$k++;
								}
							}
						}
						
					$i=0;
					while ($k > $i)
						{
						$custom_data='';
						$ex_list_name='';
						$ex_list_description='';
						$query = $this->reportsdb->query("SELECT list_name,list_description FROM vicidial_lists where list_id='$export_list_id[$i]'");
						$ex_list_ct = $query->num_rows();
						if ($ex_list_ct > 0)
							{
							$row = $query->row();
							$ex_list_name =			$row->list_name;
							$ex_list_description =	$row->list_description;
							}
			
						$ex_status_name='';
						$query = $this->reportsdb->query("SELECT status_name FROM vicidial_statuses where status='$export_status[$i]'");
						$ex_list_ct = $query->num_rows();
						if ($ex_list_ct > 0)
							{
							$row = $query->row();
							$ex_status_name =			$row->status_name;
							}
						else
							{
							$query = $this->reportsdb->query("SELECT status_name FROM vicidial_campaign_statuses where status='$export_status[$i]'");
							$ex_list_ct = $query->num_rows();
							if ($ex_list_ct > 0)
								{
								$row = $query->row();
								$ex_status_name =			$row->status_name;
								}
							}
			
						$rec_data='';
						if ( (($rec_fields=="{$this->lang->line("go_id")}") or ($rec_fields=="{$this->lang->line("go_filename")}") or ($rec_fields=="{$this->lang->line("go_location")}") or ($rec_fields=="{$this->lang->line("go_all")}")) && $i > 0 )
							{
							$rec_id='';
							$rec_filename='';
							$rec_location='';
							$query = $this->reportsdb->query("SELECT recording_id,filename,location from recording_log where vicidial_id='$export_vicidial_id[$i]' order by recording_id desc LIMIT 10");
							$recordings_ct = $query->num_rows();
							$u=0;
							while ($recordings_ct > $u)
								{
								$row = $query->row();
								$rec_id .=			$row->recording_id;
								$rec_filename .=	$row->filename;
								$rec_location .=	$row->location;
			
								$u++;
								}
							//$rec_id = preg_replace("/.$/",'',$rec_id);
							//$rec_filename = preg_replace("/.$/",'',$rec_filename);
							//$rec_location = preg_replace("/.$/",'',$rec_location);
							if ($rec_fields=="{$this->lang->line("go_id")}")
								{$rec_data = ",$rec_id";}
							if ($rec_fields=="{$this->lang->line("go_filename")}")
								{$rec_data = ",$rec_filename";}
							if ($rec_fields=="{$this->lang->line("go_location")}")
								{$rec_data = ",$rec_location";}
							if ($rec_fields=="{$this->lang->line("go_all")}")
								{$rec_data = ",$rec_id,\"$rec_filename\",\"$rec_location\"";}
							}
			
						$extended_data_a='';
						$extended_data_b='';
						$extended_data_c='';
						if ($export_fields=="{$this->lang->line("go_extended")}")
							{
							$extended_data = ",$export_uniqueid[$i]";
							if (strlen($export_uniqueid[$i]) > 0)
								{
								$uniqueidTEST = $export_uniqueid[$i];
								$uniqueidTEST = preg_replace('/\..*$/','',$uniqueidTEST);
								$query = $this->reportsdb->query("SELECT caller_code,server_ip from vicidial_log_extended where uniqueid LIKE \"$uniqueidTEST%\" and lead_id='$export_lead_id[$i]' LIMIT 1");
								$vle_ct = $query->num_rows();
								if ($vle_ct > 0)
									{
									$row=$query->row();
									$extended_data_a =	",".$row->caller_code.",".$row->server_ip;
									$export_call_id[$i] = $row->caller_code;
									}
			
								$query = $this->reportsdb->query("SELECT hangup_cause,dialstatus,channel,dial_time,answered_time from vicidial_carrier_log where uniqueid LIKE \"$uniqueidTEST%\" and lead_id='$export_lead_id[$i]' LIMIT 1");
								$vcarl_ct = $query->num_rows();
								if ($vcarl_ct > 0)
									{
									$row=$query->row();
									$extended_data_b =	",\"".$row->hangup_cause."\",\"".$row->dialstatus."\",\"".$row->channel."\",\"".$row->dial_time."\",\"".$row->answered_time."\"";
									}
			
								$query = $this->reportsdb->query("SELECT result from vicidial_cpd_log where callerid='$export_call_id[$i]' LIMIT 1");
								$vcpdl_ct = $query->num_rows();
								if ($vcpdl_ct > 0)
									{
									$row=$query->row();
									$extended_data_c =	",\"".$row->result."\"";
									}
			
								}
							if (strlen($extended_data_a)<1)
								{$extended_data_a =	",,";}
							if (strlen($extended_data_b)<1)
								{$extended_data_b =	",,,,,";}
							if (strlen($extended_data_c)<1)
								{$extended_data_c =	",";}
							$extended_data .= "$extended_data_a$extended_data_b$extended_data_c";
							}
			
						$notes_data='';
						if ($call_notes=="{$this->lang->line("go_yes")}")
							{
							if (strlen($export_vicidial_id[$i]) > 0)
								{
								$query = $this->reportsdb->query("SELECT call_notes from vicidial_call_notes where vicidial_id='$export_vicidial_id[$i]' LIMIT 1");
								$notes_ct = $query->num_rows();
								if ($notes_ct > 0)
									{
									$row=$query->row;
									$notes_data =	$row->call_notes;
									}
								$notes_data = preg_replace("/\r\n/",' ',$notes_data);
								$notes_data = preg_replace("/\n/",' ',$notes_data);
								}
							$notes_data =	",\"$notes_data\"";
							}
			
						if ( ($custom_fields_enabled > 0) and ($custom_fields=="{$this->lang->line("go_yes")}") )
							{
							$CF_list_id = $export_list_id[$i];
							if ($export_entry_list_id[$i] > 99)
								{$CF_list_id = $export_entry_list_id[$i];}
							$query = $this->reportsdb->query("SHOW TABLES LIKE \"custom_$CF_list_id\"");
							$tablecount_to_print = $query->num_rows();
							if ($tablecount_to_print > 0) 
								{
								$query = $this->reportsdb->query("describe custom_$CF_list_id");
								$columns_ct = $query->num_rows();
								$u=0;
								foreach ($query->result() as $row)
									{
									//$row=$query->row();
									$column[$u] =	$row->Field;
									$u++;
									}
								if ($columns_ct > 1)
									{
									$query = $this->reportsdb->query("SELECT * from custom_$CF_list_id where lead_id='$export_lead_id[$i]' limit 1");
									$customfield_ct = $query->num_rows();
									if ($customfield_ct > 0)
										{
										$row=$query->row_array();
										$t=1;
										while ($columns_ct > $t) 
											{
											//$custom_data .= ",\"".$row[$column[$t]]."\"";
										        $CFdata[$column[$t]] = $row[$column[$t]];
											$t++;
											}
										}
									}
							        $custom_data = ",\"".implode('","',$CFdata)."\"";
								$custom_data = preg_replace("/\r\n/",'!N',$custom_data);
								$custom_data = preg_replace("/\n/",'!N',$custom_data);
							        
							        $CFdata = array_fill_keys(array_keys($CFdata), '');
								}
							}

						if ($i < 1)
						   $file_output .= $export_rows[$i]."\n";
						else
	  					   $file_output .= $export_rows[$i]."\"$ex_list_name\",\"$ex_list_description\",\"$ex_status_name\"$rec_data$extended_data$notes_data$custom_data\n";
						$i++;
						}
				
				}
				
				$return['custom_fields_enabled']= $custom_fields_enabled;
				$return['file_output']			= $file_output;
			}
			
			// Dashboard
			if ($pageTitle=="dashboard") {
				$sub_total = array();
				list($statuses, $statuses_name, $system_statuses, $campaign_statuses, $statuses_code) = $this->go_get_statuses($campaignID);
				
				// and (val.sub_status NOT LIKE 'LOGIN%' OR val.sub_status IS NULL) 
				$query = $this->reportsdb->query("select us.user,us.full_name,val.status,count(*) as calls from vicidial_users as us,vicidial_agent_log as val where date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and us.user=val.user and val.status<>'' and val.campaign_id='$campaignID' group by us.user,us.full_name,val.status order by us.full_name,us.user,val.status desc limit 500000");
				foreach ($query->result() as $row)
				{
					$agent[$row->user][$row->status] = $row->calls;
				}
	
				$query = $this->reportsdb->query("select val.status from vicidial_agent_log as val, vicidial_log as vl where val.status<>'' and date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and val.campaign_id='$campaignID' and val.uniqueid=vl.uniqueid group by val.status limit 500000");
				foreach ($query->result() as $i => $row)
				{
					$Dstatus[$row->status] = $row->status;
					$TOPsorted_output .= "<td nowrap style=\"text-transform:uppercase;\"><div align=\"center\" class=\"style4\">&nbsp;".$row->status."&nbsp;</div></td>";
				}
				$TOPsorted_output .= "<td nowrap><div align=\"center\" class=\"style3\"><strong>&nbsp;{$this->lang->line("go_sub_total_caps")}&nbsp;</strong></td></tr>";
	
				if (count($agent)>0)
				{
					$query = $this->reportsdb->query("select lower(us.user) as user,us.full_name from vicidial_users as us, vicidial_agent_log as val where date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and lower(us.user)=lower(val.user) and val.campaign_id='$campaignID' group by us.user limit 500000");
					
					foreach ($query->result() as $i => $user_info)
					{
						if ($c == 1) {
							$bgcolor = "#EFFBEF";
							$c = 0;
						} else {
							$bgcolor = "#E0F8E0";
							$c = 1;
						}
	
						$TOPsorted_output .= "<tr style=\"background-color:$bgcolor;\"><td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style3 toolTip\" title=\"".$user_info->user."\">&nbsp;<strong>".$user_info->full_name."</strong>&nbsp;</div></td>";
						
						$t = 0;
						foreach ($Dstatus as $s)
						{
							$call_cnt = ($agent[$user_info->user][$s]>0) ? $agent[$user_info->user][$s] : 0;
							$TOPsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$call_cnt."&nbsp;</div></td>";
							$sub_total[$user_info->user] = $sub_total[$user_info->user] + $agent[$user_info->user][$s];
							$t++;
						}
	
						$TOPsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style3\">&nbsp;".$sub_total[$user_info->user]."&nbsp;</div></td></tr>";
						$total_all = $total_all + $sub_total[$user_info->user];
					}
				}
				
	// 			$query = $this->reportsdb->query("select val.status from vicidial_agent_log as val, vicidial_log as vl where val.status<>'' and date_format(val.event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and val.campaign_id='$campaignID' and val.uniqueid=vl.uniqueid group by val.status limit 500000");
	// 			foreach ($query->result() as $row)
	// 			{
	// 				if ($c == 1) {
	// 					$bgcolor = "#EFFBEF";
	// 					$c = 0;
	// 				} else {
	// 					$bgcolor = "#E0F8E0";
	// 					$c = 1;
	// 				}
	// 				
	// 				$TOPsorted_output .= "<tr style=\"background-color:$bgcolor;\"><td nowrap style=\"border-top:#D0D0D0 dashed 1px;text-transform:uppercase;\"><div align=\"center\" class=\"style4\">&nbsp;".$statuses_name[$row->status]." (".$row->status.")&nbsp;</div></td>";
	// 				
	// 				foreach ($agent as $o => $user)
	// 				{
	// 					$TOPsorted_output .= "<td nowrap style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style4\">&nbsp;".$user[$row->status]."&nbsp;</div></td>";
	// 					$sub_total[$o][$row->status] = $sub_total[$o][$row->status] + $user[$row->status];
	// 				}
	// 			}

	//			$TOPsorted_output .= "<tr><td nowrap style=\"border-top:#D0D0D0 dashed 1px;\" colspan=\"".(1+$t)."\"><div align=\"right\" class=\"style3\"><strong>&nbsp;TOTAL:&nbsp;</strong></td><td style=\"border-top:#D0D0D0 dashed 1px;\"><div align=\"center\" class=\"style3\"><strong>&nbsp;$total_all&nbsp;</strong></td></tr>";
				
				if (count($system_statuses) > 0)
				{
					$statuses_codes = implode("','", $system_statuses);
				}
				
				if (count($campaign_statuses) > 0)
				{
					$statuses_codes .= implode("','", $campaign_statuses);
				}
				
				// TOTAL CALLS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status<>''");
				$total_calls = $query->num_rows();
				
				// TOTAL CONTACTS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status IN ('$statuses_codes')");
				$total_contacts = $query->num_rows();
			
				// TOTAL NON-CONTACTS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status NOT IN ('$statuses_codes')");
				$total_noncontacts = $query->num_rows();
	
				// TOTAL SALES ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status IN ('$statuses','XFER')");
				$total_sales = $query->num_rows();
			
				// TOTAL XFER ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status='XFER'");
				$total_xfer = $query->num_rows();
			
				// TOTAL NOT INTERESTED ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status='NI'");
				$total_notinterested = $query->num_rows();
			
				// TOTAL CALLBACKS ====  AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)
				$query = $this->reportsdb->query("SELECT * FROM vicidial_agent_log WHERE campaign_id='$campaignID' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND status='CALLBK'");
				$total_callbacks = $query->num_rows();
				
				$query = $this->reportsdb->query("select sum(talk_sec) talk_sec,sum(pause_sec) pause_sec,sum(wait_sec) wait_sec,sum(dispo_sec) dispo_sec,sum(dead_sec) dead_sec from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user and $dac_agents_query2 talk_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000 and campaign_id='$campaignID' limit 500000");
				$total_hours=$query->row();
				$total_talk_hours=$total_hours->talk_sec;
				$total_pause_hours=$total_hours->pause_sec;
				$total_wait_hours=$total_hours->wait_sec;
				$total_dispo_hours=$total_hours->dispo_sec;
				$total_dead_hours=$total_hours->dead_sec;
				$total_login_hours=($total_hours->talk_sec+$total_hours->pause_sec+$total_hours->wait_sec+$total_hours->dispo_sec+$total_hours->dead_sec);
				
				$inbound_campaigns = $this->go_get_inbound_groups();
				foreach ($inbound_campaigns as $i => $item)
				{
					$inb_camp[$i] = $item->group_id;
				}
	
				if (count($inb_camp)>0)
					$inbCamp = implode("','",$inb_camp);
	
				$total_dialer_calls=0;
// 				$total_dialer_calls_output[]='';
				$isGraph = false;
				$c=0;
				foreach ($statuses_code as $code) {
					$query = $this->reportsdb->query("select count(*) as cnt from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and status='$code' and date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$row_out[$code]=$query->row()->cnt;
					
					$query = $this->reportsdb->query("select count(*) as cnt from vicidial_closer_log where campaign_id IN ('$inbCamp') and length_in_sec>'0' and status='$code' and date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$row_in[$code]=$query->row()->cnt;
	//				var_dump("select * from vicidial_log where campaign_id='$campaignID' and length_in_sec>'0' and status='$code' and date_format(call_date, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate'");
					$subtotal[$code]=$row_out[$code]+$row_in[$code];
			
					if ($subtotal[$code]>0) {
						if ($c == 1) {
							$bgcolor = "#EFFBEF";
							$c = 0;
						} else {
							$bgcolor = "#E0F8E0";
							$c = 1;
						}
						
						if (!$isGraph)
						{
							$total_dialer_calls_output .= '<tr style="background-color:'.$bgcolor.';">
								<td style="border-top:#D0D0D0 dashed 1px;"><div align="center"><span class="style3">&nbsp;'.$code.'&nbsp;</span></div></td>
								<td style="border-top:#D0D0D0 dashed 1px;"><div align="center"><span class="style3">&nbsp;'.$statuses_name[$code].'&nbsp;</span></div></td>
								<td style="border-top:#D0D0D0 dashed 1px;"><div align="center"><span class="style3">&nbsp;'.$subtotal[$code].'&nbsp;</span></div></td>
							</tr>';
						} else {
							$total_dialer_calls_output[$code] = $subtotal[$code];
						}
					}
			
					$total_dialer_calls=$total_dialer_calls+$subtotal[$code];
				}
// 				$total_dialer_calls_output = json_encode($total_dialer_calls_output);
				
				// Graph
				foreach ($statuses as $status) {
					$query = $this->reportsdb->query("SELECT count(*) as cnt FROM vicidial_agent_log WHERE campaign_id='$campaignID' and status='$status' and date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' AND (sub_status NOT LIKE 'LOGIN%' OR sub_status IS NULL)");
					$SUMstatuses[$status]=$query->row()->cnt;
				}
	
				for($x=0;$x<count($statuses);$x++)
				{
					$SstatusesARY[$x] = $statuses_name[$statuses[$x]]." (".$statuses[$x].")";
				}
	
				$return['TOPsorted_output']	= $TOPsorted_output;
				$return['SstatusesTOP']		= $SstatusesARY;
				$return['SUMstatuses']		= $SUMstatuses;
				$return['total_calls']		= $total_calls;
				$return['total_contacts']	= $total_contacts;
				$return['total_noncontacts']= $total_noncontacts;
				$return['total_sales']		= $total_sales;
				$return['total_xfer']		= $total_xfer;
				$return['total_notinterested']= $total_notinterested;
				$return['total_callbacks']	= $total_callbacks;
				$return['total_talk_hours']	= $total_talk_hours;
				$return['total_pause_hours']= $total_pause_hours;
				$return['total_wait_hours']	= $total_wait_hours;
				$return['total_dispo_hours']= $total_dispo_hours;
				$return['total_dead_hours']	= $total_dead_hours;
				$return['total_login_hours']= $total_login_hours;
				$return['total_dialer_calls_output']= $total_dialer_calls_output;
				$return['total_dialer_calls']= $total_dialer_calls;
	//			var_dump($statuses_code);
	//			var_dump("select sum(talk_sec),sum(pause_sec),sum(wait_sec),sum(dispo_sec),sum(dead_sec) from vicidial_users,vicidial_agent_log where date_format(event_time, '%Y-%m-%d') BETWEEN '$fromDate' AND '$toDate' and vicidial_users.user=vicidial_agent_log.user and $dac_agents_query2 talk_sec<36000 and wait_sec<36000 and talk_sec<36000 and dispo_sec<36000 and campaign_id='$campaignID' limit 500000;");
			}
		}

		$query = $this->reportsdb->query("select status,status_name from vicidial_statuses union select status,status_name from vicidial_campaign_statuses");
		$return['statuses'] = $query->result();
		
		return $return;
	}

	function go_reports_analytics_stats()
	{
        $callfunc  = $this->go_get_hourly_stats();
		$querycount = $callfunc['datacount'];
		
        $groupIdfunc  = $this->go_get_groupId();
		$data['groupId'] = $groupIdfunc;
		
		if ($querycount==0){
			$callfunc = $this->go_get_weekly_stats();
			$querycount = $callfunc['datacount'];
			
			$queryval = $callfunc['dataval'];

			foreach($queryval as $item):		
			$cnt = $item->weekno;
			endforeach;
			
			if ($cnt==null){
				$querycount = 0;
			}

			if ($querycount==0){
				$callfunc = $this->go_get_cmonth_daily_stats();
				$queryval = $callfunc['dataval'];
				$data['get_cmonth_daily_stats'] = $queryval;
				$data['request'] = 'monthly';
				$this->load->view('go_reports_analytics_cmonth_daily', $data);
				

			}
			else{
				$queryval = $callfunc['dataval'];
				$data['get_weekly_stats'] = $queryval;
				$data['request'] = 'weekly';
				$this->load->view('go_reports_analytics_weekly', $data);
			}
		
		
		}
		else
		{
		$queryval = $callfunc['dataval'];
		$data['get_hourly_stats'] = $queryval;
		$data['request'] = 'daily';
		$this->load->view('go_reports_page', $data);
		}
	}
	
	function go_get_groupid()
	{
		$userID = $this->session->userdata('user_name');
	    $query = $this->reportsdb->query("select user_group from vicidial_users where user='$userID'"); 
	    $resultsu = $query->row();
	    $groupid = $resultsu->user_group;
	    return $groupid;
	}
	
	  function go_getall_allowed_campaigns()
	  {
		$groupId = $this->go_get_groupid();	 
		$query_date =  date('Y-m-d');
		$query = $this->reportsdb->query("select trim(allowed_campaigns) as qresult from vicidial_user_groups where user_group='$groupId'"); 
		$resultsu = $query->row();
	
			if(count($resultsu) > 0){
			   $fresults = $resultsu->qresult;
			   $allowedCampaigns = explode(",",str_replace(" ",',',rtrim(ltrim(str_replace('-','',$fresults)))));
			   sort($allowedCampaigns);
			   $allAllowedCampaigns = implode("','",$allowedCampaigns);
	
			}else{
			   $allAllowedCampaigns = '';
			}
		return $allAllowedCampaigns;
	  }
	  
      function go_getall_closer_campaigns()
      {
		$campaignID = $this->uri->segment(6);
	    $query_date =  date('Y-m-d');
	    $query = $this->reportsdb->query("select trim(closer_campaigns) as qresult from vicidial_campaigns where campaign_id='$campaignID' order by campaign_id"); 
	    $resultsu = $query->row();

            if(count($resultsu) > 0){
	        $fresults = $resultsu->qresult;
	        $closerCampaigns = explode(",",str_replace(" ",',',rtrim(ltrim(str_replace('-','',$fresults)))));
                
                $allCloserCampaigns = implode("','",$closerCampaigns);

            }else{
                $allCloserCampaigns = '';
            }
	    return $allCloserCampaigns;
      }
	  
	function go_get_inbound_groups()
	{
		$groupId = $this->go_get_groupid();
		$groupSQL = ($this->commonhelper->checkIfTenant($groupId)) ? "where user_group='$groupId'" : "";
		//$query = $this->reportsdb->query("select group_id,group_name from vicidial_inbound_groups where uniqueid_status_prefix='$groupId';");
		$stmt ="select group_id,group_name from vicidial_inbound_groups $groupSQL;";
		//$query = $this->reportsdb->query("select group_id,group_name from vicidial_inbound_groups where uniqueid_status_prefix='$groupId';"); 
		$query = $this->reportsdb->query($stmt); 
		$inboundgroups = $query->result();
		return $inboundgroups;	 
	}
      
	function go_get_userfulname()
	{
		$userID = $this->session->userdata('user_name');
		$query = $this->reportsdb->query("select full_name from vicidial_users where user='$userID';"); 
		$resultsu = $query->row();
		$userfulname = $resultsu->full_name;
		return $userfulname;	 
 	}
	
	##### reformat seconds into HH:MM:SS or MM:SS #####
	function go_sec_convert($sec,$precision)
		{
		$sec = round($sec,0);
	
		if ($sec < 1)
			{
			return "0:00";
			}
		else
			{
			if ($sec < 3600) {$precision='M';}
	
			if ($precision == 'H')
				{
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
			if ($precision == 'M')
				{
				$Fminutes_M = ($sec / 60);
				$Fminutes_M_int = floor($Fminutes_M);
				$Fminutes_M_int = intval("$Fminutes_M_int");
				$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
				$Fminutes_S = ($Fminutes_S * 60);
				$Fminutes_S = round($Fminutes_S, 0);
				if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
				$Ftime = "$Fminutes_M_int:$Fminutes_S";
				}
			if ($precision == 'S')
				{
				$Ftime = $sec;
				}
			return "$Ftime";
			}
		}
		
	function go_get_statuses($camp)
		{
		# grab names of global statuses and statuses in the selected campaign
		$query = $this->reportsdb->query("SELECT status,status_name,selectable,human_answered from vicidial_statuses order by status");
		$statuses_to_print = $query->num_rows();
		
		$ns=0;
		foreach ($query->result() as $o => $rowx)
			{
			if ($rowx->status!='NEW') {
				if (($rowx->selectable=='Y' && $rowx->human_answered=='Y') || ($rowx->status=='INCALL' || $rowx->status=='CBHOLD')) {
					$system_statuses[$o] = $rowx->status;
				} else {
					$statuses_code[$o] = $rowx->status;
				}
				$statuses_name[$rowx->status] = $rowx->status_name;
			}
			$statuses[$ns]=$rowx->status;
			$ns++;
			}
			
		$query = $this->reportsdb->query("SELECT status,status_name,selectable,human_answered from vicidial_campaign_statuses where campaign_id='$camp' and selectable='Y' and human_answered='Y' order by status");
		$Cstatuses_to_print = $query->num_rows();
		
		foreach ($query->result() as $o => $rowx)
			{
			if ($rowx->status!='NEW') {
				if (($rowx->selectable=='Y' && $rowx->human_answered=='Y') || ($rowx->status=='INCALL' || $rowx->status=='CBHOLD')) {
					$campaign_statuses[$o] = $rowx->status;
				} else {
					$statuses_code[$o] = $rowx->status;
				}
				$statuses_name[$rowx->status] = $rowx->status_name;
			}
			$statuses[$ns]=$rowx->status;
			$ns++;
			}

		$return = array($statuses, $statuses_name, $system_statuses, $campaign_statuses, $statuses_code);
		
		return $return;
		}

	function go_get_calltimes($camp)
	{
		$query = $this->reportsdb->query("SELECT local_call_time AS call_time FROM vicidial_campaigns WHERE campaign_id='$camp'");
		$call_time = $query->row()->call_time;

		if (strlen($call_time) > 0)
		{
			$query = $this->reportsdb->query("SELECT ct_default_start, ct_default_stop FROM vicidial_call_times WHERE call_time_id='$call_time'");
			$result = $query->row()->ct_default_start . "-" . $query->row()->ct_default_stop;
		}

		return $result;
	}

	function go_get_date_diff($d1, $d2)
	{
		$diff = abs(strtotime($d2) - strtotime($d1));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

// 		printf("%d years, %d months, %d days\n", $years, $months, $days);
		return "$years|$months|$days";
	}

	function go_get_dates($d1, $d2)
	{
		$diff = explode("|", $this->go_get_date_diff($d1, $d2));
		$days = $diff[2];

		for ($i=0;$i<=$days;$i++)
		{
			$dateARY[$i] = $d1;
			$d1 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($d1)) . " +1 day"));
		}
		return $dateARY;
	}


        function go_get_CDR($parameters){

                $client = $parameters['client'];
		$limit = "100";

                $startDate = explode("-",$parameters['dates'][0]); 
                $endDate = explode("-",$parameters['dates'][1]);
                $startDateStr =  date("D M d Y",mktime(0,0,0,$startDate[1],$startDate[2],$startDate[0]));
                $endDateStr =  date("D M d Y",mktime(0,0,0,$endDate[1],$endDate[2],$endDate[0]));

		$start_date = "00:00:00.000 GMT {$startDateStr}";
		$end_date = "23:59:59.000 GMT {$endDateStr}";

                $scalars = array(
                                 'i_account' => array($client,'int'),
                                 'limit' => array($limit,'int'),
                                 'start_date' => array($start_date,'string'),
                                 'end_date' => array($end_date,'string'),
                                 'type' => array('non_zero','string') 
                                );

                $sippyresult = $this->commonhelper->getsippy("getAccountCDRs",$scalars);

                return $sippyresult;

                
        }

}
