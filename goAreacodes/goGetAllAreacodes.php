<?php
/**
 * @file 		goGetAllAreacodes.php
 * @brief 		API to get all areacodes
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Thom Bernarth Patacsil 
 * @author		Christopher Lomuntad
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

	include_once ("goAPI.php");
	$draw   = (isset($_REQUEST['draw']) ? $astDB->escape($_REQUEST['draw']) : 0);
	$start  = (isset($_REQUEST['start']) ? $astDB->escape($_REQUEST['start']) : 0);
	$length = (isset($_REQUEST['length']) ? $astDB->escape($_REQUEST['length']) : 10);
	$order  = (isset($_REQUEST['order']) ? $astDB->escape($_REQUEST['order']) : "campaign_id");
	$dir    = (isset($_REQUEST['dir']) ? $astDB->escape($_REQUEST['dir']) : "desc");
	$search = (isset($_REQUEST['search']) ? $astDB->escape($_REQUEST['search']) : "");
	$can_update = (isset($_REQUEST['can_update']) ? $astDB->escape($_REQUEST['can_update']) : "N");
	  
	// Error Checking
	if (empty($goUser) || is_null($goUser)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI User Not Defined."
		);
	} elseif (empty($goPass) || is_null($goPass)) {
		$apiresults 									= array(
			"result" 										=> "Error: goAPI Password Not Defined."
		);
	} elseif (empty($log_user) || is_null($log_user)) {
		$apiresults 									= array(
			"result" 										=> "Error: Session User Not Defined."
		);
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel										= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {	
			// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
			// every time we need to filter out requests
			$tenant										=  (checkIfTenant ($log_group, $goDB)) ? 1 : 0;
			
			$astDB->where('user_group', $log_group);
			$allowed_camps = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) !== 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					} else {
						$allowed_campaigns = $allowed_camps['allowed_campaigns'];
						if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
							$allowed_campaigns = explode(" ", trim($allowed_campaigns));
							$astDB->orWhere('campaign_id', $allowed_campaigns, 'in');
						}
					}
				}					
			}
			
			/*$dataCampID								= "";
			$dataCampName								= "";
			$dataAreacode								= "";
			$dataOutboundCID							= "";
			$dataActive									= "";
			$dataDescription							= "";	
			$dataCallCountToday							= "";*/
			$cols 										= array(
				"vcid.campaign_id",
				"vc.campaign_name",
				"vcid.areacode",
				"vcid.outbound_cid",
				"vcid.active",
				"vcid.cid_description",
				"vcid.call_count_today"
			);
			
			$astDB->orderBy($order, $dir);
			$astDB->join('vicidial_campaigns vc', 'vcid.campaign_id=vc.campaign_id', 'LEFT');
			if (isset($search) && strlen($search) > 0) {
				$astDB->where('vcid.campaign_id', $search)
					  ->orWhere('vc.campaign_name', $search)
					  ->orWhere('vcid.areacode', $search)
					  ->orWhere('vcid.outbound_cid', $search);
			}
			$result	= $astDB->get('vicidial_campaign_cid_areacodes vcid', array($start, $length), $cols, true);
			
			if ($astDB->count > 0) {
				foreach ($result as $fresults){
					if (!$draw) {
						$dataCampID[] 						= $fresults['campaign_id'];
						$dataCampName[]						= $fresults['campaign_name'];
						$dataAreacode[] 					= $fresults['areacode'];
						$dataOutboundCID[] 					= $fresults['outbound_cid'];
						$dataActive[] 						= $fresults['active'];
						$dataDescription[]					= $fresults['cid_description'];
						$dataCallCountToday					= $fresults['call_count_today'];
					} else {
						$avatar_link = "";
						$campaign_link = "";
						if ($can_update !== "N") {
							$avatar_link .= '<a class="view_areacode" data-toggle="modal" data-target="#modal_edit_areacode" data-camp="'.$fresults['campaign_id'].'" data-ac="'.$fresults['areacode'].'">';
							$campaign_link .= '<a class="view_areacode" data-toggle="modal" data-target="#modal_edit_areacode" data-camp="'.$fresults['campaign_id'].'" data-ac="'.$fresults['areacode'].'">';
						}
						$avatar_link .= '<avatar username="'.$fresults['campaign_name'].'" :size="32"></avatar>';
						$campaign_link .= '<strong>'.$fresults['campaign_id'].'</strong>';
						if ($can_update !== "N") {
							$avatar_link .= '</a>';
							$campaign_link .= '</a>';
						}
						
						$dataOutput[]						= array(
							"avatar"							=> $avatar_link,
							"campaign_id"						=> $campaign_link,
							"campaign_name"						=> $fresults['campaign_name'],
							"areacode"							=> $fresults['areacode'],
							"outbound_cid"						=> $fresults['outbound_cid'],
							"active"							=> $fresults['active'],
							"action"							=> "",
						);
					}
				}				
			
				if (!$draw) {
					$apiresults 							= array(
						"result" 								=> "success", 
						"campaign_id" 							=> $dataCampID,
						"campaign_name"							=> $dataCampName,
						"areacode" 								=> $dataAreacode, 
						"outbound_cid" 							=> $dataOutboundCID, 
						"active" 								=> $dataActive,
						"description"							=> $dataDescription,
						"call_count_today"						=> $dataCallCountToday
					);
				} else {
					$apiresults								= array(
						"draw"									=> $draw,
						"recordsTotal"							=> $astDB->getUnlimitedRowCount(),
						"recordsFiltered"						=> $astDB->getUnlimitedRowCount(),
						"data"									=> $dataOutput
					);
				}
			} else {
				$apiresults                             = array(
					"result"								=> "No data available in table",
					//"test"									=> $astDB->getLastError()
				);
			}
		} else {
			$err_msg 									= error_handle("10001");
			$apiresults 								= array(
				"code" 										=> "10001", 
				"result" 									=> $err_msg
			);		
		}
	}
	
?>
