<?php
/**
 * @file        goFunctions.php
 * @brief       General Functions
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Flores Milo  <jericojames@goautodial.com>
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

    function go_getUsergroup($goUser ,$link){
        
        $query_userv = "SELECT user_group FROM vicidial_users WHERE user='$goUser'";
        $rsltv = mysqli_query($link, $query_userv);
        $check_resultv = mysqli_num_rows($rsltv);
        
        if ($check_resultv > 0) {
            $rowc=mysqli_fetch_array($rsltv, MYSQLI_ASSOC);
            $goUser_group = $rowc["user_group"];
            return $goUser_group;
        }
       
    }

    // moved to goFunctions.php
    /*function remove_empty($array) {
	   return array_filter($array, '_remove_empty_internal');
    }

    function _remove_empty_internal($value) {
        return !empty($value) || $value === 0;
    }*/
    
    function go_get_dates($d1, $d2)
    {
            $diff = explode("|", go_get_date_diff($d1, $d2));
            $days = $diff[2];

            for ($i=0;$i<=$days;$i++)
            {
                    $dateARY[$i] = $d1;
                    $d1 = date("Y-m-d", strtotime(date("Y-m-d", strtotime($d1)) . " +1 day"));
            }
            return $dateARY;
    }

   
    function go_get_date_diff($d1, $d2)
    {
            $diff = abs(strtotime($d2) - strtotime($d1));

            $years = floor($diff / (365*60*60*24));
            $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
            $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

            //printf("%d years, %d months, %d days\n", $years, $months, $days);
            return "$years|$months|$days";
    }

    ##### reformat seconds into HH:MM:SS or MM:SS #####
    /*function go_sec_convert($sec,$precision){
		$sec = round($sec,0);
		if ($sec < 1){
			return "0:00";
		}
		else{
			if ($sec < 3600) {$precision='M';}
			if ($precision == 'H'){
				$Fhours_H = ($sec / 3600);
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
			if ($precision == 'M'){
				$Fminutes_M = ($sec / 60);
				$Fminutes_M_int = floor($Fminutes_M);
				$Fminutes_M_int = intval("$Fminutes_M_int");
				$Fminutes_S = ($Fminutes_M - $Fminutes_M_int);
				$Fminutes_S = ($Fminutes_S * 60);
				$Fminutes_S = round($Fminutes_S, 0);
				if ($Fminutes_S < 10) {$Fminutes_S = "0$Fminutes_S";}
				$Ftime = "$Fminutes_M_int:$Fminutes_S";
			}
			if ($precision == 'S'){
				$Ftime = $sec;
			}
			return "$Ftime";
		}
	}
				
	function inner_checkIfTenant($groupId, $linkgo){
        $query_tenant = "SELECT * FROM go_multi_tenant WHERE tenant_id='$groupId'";
        $rslt_tenant = mysqli_query($linkgo,$query_tenant);
        $check_result_tenant = mysqli_num_rows($rslt_tenant);
        

        if ($check_result_tenant > 0) {
            return true;
        } else {
            return false;
        }
    }*/
    
	/*function go_getall_closer_campaigns($campaignID, $link){
		$query_date =  date('Y-m-d');
		$query_text = "select trim(closer_campaigns) as qresult from vicidial_campaigns where campaign_id='$campaignID' order by campaign_id";
		$query = mysqli_query($link, $query_text);
		$resultsu = mysqli_fetch_array($query);
		
		if(count($resultsu) > 0){
			$fresults = $resultsu['qresult'];
			$closerCampaigns = explode(",",str_replace(" ",',',rtrim(ltrim(str_replace('-','',$fresults)))));
			$allCloserCampaigns = implode("','",$closerCampaigns);
		}else{
			  $allCloserCampaigns = '';
		}
		  
		return $allCloserCampaigns;
	}*/
	
	/*function go_get_calltimes($camp, $link){
		
		$query = "SELECT local_call_time AS call_time FROM vicidial_campaigns WHERE campaign_id='$camp'";
		$query_result = mysqli_query($link, $query);
		$fetch_result = mysqli_fetch_array($query_result);
		$call_time = $fetch_result['call_time'];

		if (strlen($call_time) > 0){
			$query = "SELECT ct_default_start, ct_default_stop FROM vicidial_call_times WHERE call_time_id='$call_time'";
			$result_query = mysqli_query($link, $query);
			$fetch_result = mysqli_fetch_array($result_query);
			$result = $fetch_result['ct_default_start']. "-" . $fetch_result['ct_default_stop'];
		}

		return $result;
	}*/
	
	function go_get_statuses($camp, $link){
	# grab names of global statuses and statuses in the selected campaign
	
		$query = mysqli_query($link, "SELECT status,status_name,selectable,human_answered from vicidial_statuses order by status");
		$statuses_to_print = mysqli_num_rows($query);
	
		$ns = 0;
		while($row = mysqli_fetch_array($query)){
			if ($row['status'] != 'NEW') {
				if (($row['selectable'] =='Y' && $row['human_answered'] =='Y') || ($row['status'] =='INCALL' || $row['status'] == 'CBHOLD')) {
							$system_statuses[$ns] = $row['status'];
				} else {
							$statuses_code[$ns] = $row['status'];
				}
				
				$statuses_name[$row['status']] = $row['status_name'];
			}
				
				$statuses[$ns]=$row['status'];
				$ns++;
		}

		$query = mysqli_query($link, "SELECT status,status_name,selectable,human_answered from vicidial_campaign_statuses where campaign_id='$camp' and selectable='Y' and human_answered='Y' order by status");
		
		$Cstatuses_to_print = mysqli_num_rows($query);
	
		$o = 0;
		while($row = mysqli_fetch_array($query)) {
			if ($row['status'] != 'NEW') {
				
				if (($row['selectable'] =='Y' && $row['human_answered'] =='Y') || ($row['status'] =='INCALL' || $row['status'] == 'CBHOLD')) {
					$campaign_statuses[$o] = $row['status'];
				} else {
					$statuses_code[$o] = $row['status'];
				}
				
				$statuses_name[$row['status']] = $row['status_name'];
			}
			
			$statuses[$o]=$row['status'];
			$o++;
		}

		$apiresults = array($statuses, $statuses_name, $system_statuses, $campaign_statuses, $statuses_code);
	
		return $apiresults;
	}
	
	function get_inbound_groups($userID, $link, $groupId) {
		if($groupId != NULL)
		$groupSQL = "where user_group='$groupId'";
		
		else
		$groupSQL = "";
		
		$stmt ="select group_id,group_name from vicidial_inbound_groups $groupSQL;";
		
		$query = mysqli_query($link, $stmt);
		$inboundgroups = mysqli_fetch_array($query);
		
		return $inboundgroups;
	}

 
?>
