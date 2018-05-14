<?php
/**
 * @file        goActionDNC.php
 * @brief       API to Add or Delete DNC
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
	$campaign_id = $astDB->escape($_REQUEST['campaign_id']);
	$phone_numbers = str_replace(" ", "\n", rawurldecode($astDB->escape($_REQUEST['phone_numbers'])));
	$stage = $astDB->escape($_REQUEST['stage']);
	$ip_address = $astDB->escape($_REQUEST['hostname']);
	
	$groupId = go_get_groupid($session_user, $astDB);
	$log_user = $session_user;
	$log_group = $groupId;
	$allowed_campaigns = get_allowed_campaigns($groupId, $astDB);
	$cnt = 0;
	
	if ($campaign_id == "INTERNAL"){
		$dnc_numbers = explode("\r\n",$phone_numbers);
		
		foreach ($dnc_numbers as $dnc){
			//$query_dnc = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_dnc WHERE phone_number='$dnc'");
			$astDB->where('phone_number', $dnc);
			$query_dnc = $astDB->get('vicidial_dnc', null, 'phone_number');
			$idnc_exist = $astDB->getRowCount();
			//$query_campaign_dnc = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_campaign_dnc WHERE phone_number='$dnc'");
			$astDB->where('phone_number', $dnc);
			$query_campaign_dnc = $astDB->get('vicidial_campaign_dnc', null, 'phone_number');
			$cdnc_exist = $astDB->getRowCount();
			
			if ($idnc_exist < 1 && $cdnc_exist < 1){
				if ($stage == "ADD" && $dnc != ''){
					if (count($allowed_campaigns) > 1 && !in_array($allowed_campaigns,"---ALL---")) {
						foreach ($allowed_campaigns as $camp) {
							//$query = mysqli_query($link, "INSERT INTO vicidial_campaign_dnc VALUES('$dnc','$camp');");
							$query = $astDB->insert('vicidial_campaign_dnc', array('phone_number' => $dnc, 'campaign_id' => $camp));
						}
					} else {
						//$query = mysqli_query($link, "INSERT INTO vicidial_dnc VALUES('$dnc');");
						$query = $astDB->insert('vicidial_dnc', array('phone_number' => $dnc));
					}
					$cnt++;
				}
			} else {
				if ($stage == "DELETE" && $dnc != ''){
					if (count($allowed_campaigns) > 1 && !in_array($allowed_campaigns,"---ALL---")) {
						foreach ($allowed_campaigns as $camp) {
							//$query = mysqli_query($link, "DELETE FROM vicidial_campaign_dnc WHERE phone_number='$dnc';");
							$astDB->where('phone_number', $dnc);
							$query = $astDB->delete('vicidial_campaign_dnc');
						}
					} else {
						//$query = mysqli_query($link, "DELETE FROM vicidial_dnc WHERE phone_number='$dnc';");
						$astDB->where('phone_number', $dnc);
						$query = $astDB->delete('vicidial_dnc');
					}
					
					if ($query) {
						$cnt++;
					}
				}
			}
		}

		if ($cnt){
			if ($stage == "DELETE")
				$msg = "deleted";
			else
				$msg = "added";
			
			$details = ucfirst($msg) . " {$cnt} numbers " . ($msg == 'added' ? 'to' : 'from') . " Internal DNC list";
			$log_id = log_action($goDB, $stage, $log_user, $ip_address, $details, $log_group);
		} else {
			if ($stage == "ADD")
				$msg = "already exist";
			else
				$msg = "does not exist";
		}
    } else {
		$dnc_numbers = explode("\r\n",$phone_numbers);

		foreach ($dnc_numbers as $dnc){
			//$query = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_campaign_dnc WHERE phone_number='$dnc' AND campaign_id='$campaign_id';");
			$astDB->where('phone_number', $dnc);
			$astDB->where('campaign_id', $campaign_id);
			$query = $astDB->get('vicidial_campaign_dnc', null, 'phone_number');
			$cdnc_exist = $astDB->getRowCount();
			//$query2 = mysqli_query($link, "SELECT phone_number AS cnt FROM vicidial_dnc WHERE phone_number='$dnc';");
			$astDB->where('phone_number', $dnc);
			$query2 = $astDB->get('vicidial_dnc', null, 'phone_number');
			$idnc_exist = $astDB->getRowCount();
		
			if ($idnc_exist < 1 && $cdnc_exist < 1){
				if ($stage == "ADD"){
					//$query = mysqli_query($link, "INSERT INTO vicidial_campaign_dnc VALUES('$dnc','$campaign_id');");
					$query = $astDB->insert('vicidial_campaign_dnc', array('phone_number' => $dnc, 'campaign_id' => $campaign_id));
					$cnt++;
				}
			} else {
				if ($stage == "DELETE"){
					if ($cdnc_exist > 0) {
						//$query = mysqli_query($link, "DELETE FROM vicidial_campaign_dnc WHERE phone_number='$dnc' AND campaign_id='$campaign_id';");
						$astDB->where('phone_number', $dnc);
						$astDB->where('campaign_id', $campaign_id);
						$query = $astDB->delete('vicidial_campaign_dnc');
						$cnt++;
					}
					
					if ($campaign_id === '' && $idnc_exist > 0) {
						//$query = mysqli_query($link, "DELETE FROM vicidial_dnc WHERE phone_number='$dnc';");
						$astDB->where('phone_number', $dnc);
						$query = $astDB->delete('vicidial_dnc');
						$cnt++;
					}
				}
			}
		}
				
		if ($cnt){
			if ($stage == "ADD")
				$msg = "added";
			else
				$msg = "deleted";
			
			$details = ucfirst($msg) . " {$cnt} numbers " . ($msg == 'added' ? 'to' : 'from') . " Campaign DNC list";
			$log_id = log_action($goDB, $stage, $log_user, $ip_address, $details, $log_group);
		} else {
			if ($stage == "ADD")
				$msg = "already exist";
			else
				$msg = "does not exist";
		}
	}
    
	$apiresults = array(
		"result"   => "success",
		"msg"      => $msg
	);
	
	function get_allowed_campaigns($groupId, $link){
		//$query2 = mysqli_query($link, "select campaign_id from vicidial_campaigns where user_group = '$groupId'");
		$link->where('user_group', $groupId);
		$query2 = $link->get('vicidial_campaigns', null, 'campaign_id');
		$check = $link->getRowCount();
		if($check > 0){
			$camp_array = array();
			foreach ($query2 as $camp){
				$camp_array[] = $camp['campaign_id'];
			}
			$allowed_campaigns = $camp_array;
		}else{
			$allowed_campaigns = array('---ALL---');
		}
		return $allowed_campaigns;
	}
?>