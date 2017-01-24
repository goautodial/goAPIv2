<?php
    include_once("../goFunctions.php");
    
	$campaign_id = $_REQUEST['campaign_id'];
	$phone_numbers = $_REQUEST['phone_numbers'];
	$stage = $_REQUEST['stage'];
	$user_id = $_REQUEST['user_id'];
	$ip_address = $_REQUEST['hostname'];
	
	$usergroup = get_usergroup($user_id, $link);
	$allowed_campaigns = get_usergroup($usergroup, $link);
	
	if ($campaign_id == "INTERNAL"){
		$dnc_numbers = explode("\r\n",$phone_numbers);
		
		foreach ($dnc_numbers as $dnc){
			$query_dnc = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_dnc WHERE phone_number='$dnc'");
			$idnc_exist = mysqli_num_rows($query_dnc);
			$query_campaign_dnc = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_campaign_dnc WHERE phone_number='$dnc'");
			$cdnc_exist = mysqli_num_rows($query_campaign_dnc);
			
			if ($idnc_exist < 1 && $cdnc_exist < 1){
				if ($stage == "ADD" && $dnc != ''){
					if (count($allowed_campaigns) > 1 && !in_array($allowed_campaigns,"---ALL---")) {
						foreach ($allowed_campaigns as $camp) {
							$query = mysqli_query($link, "INSERT INTO vicidial_campaign_dnc VALUES('$dnc','$camp');");
							log_action ('ADD', $user_id, $ip_address, date('Y-m-d H:i:s'), "Added DNC Number $dnc to Internal DNC List", $usergroup, "INSERT INTO vicidial_campaign_dnc VALUES('$dnc','$camp');");
						}
					} else {
						$query = mysqli_query($link, "INSERT INTO vicidial_dnc VALUES('$dnc');");
						log_action ('ADD', $user_id, $ip_address, date('Y-m-d H:i:s'), "Added DNC Number $dnc to Internal DNC List", $usergroup, "INSERT INTO vicidial_dnc VALUES('$dnc');");
					}
					$cnt++;
				}
			} else {
				if ($stage == "DELETE" && $dnc != ''){
					if (count($allowed_campaigns) > 1 && !in_array($allowed_campaigns,"---ALL---")) {
						foreach ($allowed_campaigns as $camp) {
							$query = mysqli_query($link, "DELETE FROM vicidial_campaign_dnc WHERE phone_number='$dnc';");
							log_action ('DELETE', $user_id, $ip_address, date('Y-m-d H:i:s'), "Deleted DNC Number $dnc to Internal DNC List", $usergroup, "DELETE FROM vicidial_campaign_dnc WHERE phone_number='$dnc';");
						}
					} else {
						$query = mysqli_query($link, "DELETE FROM vicidial_dnc WHERE phone_number='$dnc';");
						log_action ('DELETE', $user_id, $ip_address, date('Y-m-d H:i:s'), "Deleted DNC Number $dnc to Internal DNC List", $usergroup, "DELETE FROM vicidial_dnc WHERE phone_number='$dnc';");
					}
					$cnt++;
				}
			}
		}

		if ($cnt){
			if ($stage == "DELETE")
				$msg = "added";
			else
				$msg = "deleted";
		} else {
			if ($stage == "ADD")
				$msg = "already exist";
			else
				$msg = "does not exist";
		}
    } else {
		$dnc_numbers = explode("\r\n",$phone_numbers);

		foreach ($dnc_numbers as $dnc){
			$query = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_campaign_dnc WHERE phone_number='$dnc' AND campaign_id='".$campaign_id."'");
			$cdnc_exist = mysqli_num_rows($query);
			$query2 = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_dnc WHERE phone_number='$dnc'");
			$idnc_exist = mysqli_num_rows($query2);
		
			if ($idnc_exist < 1 && $cdnc_exist < 1){
				if ($stage == "ADD"){
					$query = mysqli_query($link, "INSERT INTO vicidial_campaign_dnc VALUES('$dnc','".$campaign_id."')");
					log_action ('ADD', $user_id, $ip_address, date('Y-m-d H:i:s'), "Added DNC Number $dnc for Campaign ".$campaign_id , $usergroup, "INSERT INTO vicidial_campaign_dnc VALUES('$dnc','".$campaign_id."')");
					$cnt++;
				}
			} else {
				if ($stage == "DELETE"){
					$query = mysqli_query($link, "DELETE FROM vicidial_campaign_dnc WHERE phone_number='$dnc' AND campaign_id='".$campaign_id."'");
					log_action ('DELETE', $user_id, $ip_address, date('Y-m-d H:i:s'), "Deleted DNC Number $dnc from Campaign ".$campaign_id , $usergroup, "DELETE FROM vicidial_campaign_dnc WHERE phone_number='$dnc' AND campaign_id='".$campaign_id."';");
					$cnt++;
				}
			}
		}
				
		if ($cnt){
			if ($stage == "ADD")
				$msg = "added";
			else
				$msg = "deleted";
		} else {
			if ($stage == "DELETE")
				$msg = "already exist";
			else
				$msg = "does not exist";
		}
	}
    
	$apiresults = array(
		"result"   => "success",
		"msg"      => $msg
	);
	
	function get_usergroup($user_id, $link){
		$query = mysqli_query($link, "select user_group from vicidial_users where user_id='$user_id';");
		$resultsu = mysqli_fetch_array($query);
		$groupid = $resultsu['user_group'];
		return $groupid;
	}
	function get_allowed_campaigns($groupid, $link){
		$query2 = mysql_query($link, "select campaign_id from vicidial_campaigns where user_group = '$groupid'");
		$check = mysqli_num_rows($query2);
		if($check > 0){
			while($resultc = mysqli_fetch_array($query2)){
				$allowed_campaigns = $resultc['campaign_id'];
			}
		}else{
			$allowed_campaigns = array('---ALL---');
		}
		return $allowed_campaigns;
	}
?>