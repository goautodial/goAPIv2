<?php
/**
 * @file        goGetAllLists.php
 * @brief       API to get all lists
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Jeremiah Sebastian Samatra
 * @author      Alexander Jim Abenoja
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

	$campaigns 											= allowed_campaigns($log_group, $goDB, $astDB);
	
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
	} elseif (empty($campaigns) || is_null($campaigns)) {
		$err_msg 										= error_handle("40001");
        $apiresults 									= array(
			"code" 											=> "40001",
			"result" 										=> $err_msg
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
			if (is_array($campaigns)) {
				//$astDB->where("vicidial_lists.campaign_id", $campaigns, "IN");
				$campaign_ids							= implode("','", $campaigns);
				//$campaign_ids							= str_replace(",", "','", $campaign_ids);
								
				// set tenant value to 1 if tenant - saves on calling the checkIfTenantf function
				// every time we need to filter out requests
				$tenant									= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
                
                $astDB->where('user_group', $log_group);
                $allowed_camps                          = $astDB->getOne('vicidial_user_groups', 'allowed_campaigns');
                $allowed_campaigns                      = $allowed_camps['allowed_campaigns'];
                if (!preg_match("/ALL-CAMPAIGN/", $allowed_campaigns)) {
					$campaign_ids                       = explode(" ", trim($allowed_campaigns));
                    $campaign_ids						= implode("','", $campaign_ids);
                }
				
				if ($tenant) {
					$ul									= "WHERE vicidial_lists.campaign_id IN ('$campaign_ids')";
				} else {
					$ul									= "";
					
					if (strtoupper($log_group) != 'ADMIN') {
						//if ($userlevel > 8) {
							$ul							= "WHERE vicidial_lists.campaign_id IN ('$campaign_ids')";
						//}
					}					
				}
				
				$query 									= "SELECT vicidial_lists.list_id, vicidial_lists.list_name, vicidial_lists.list_description, (SELECT count(*) as tally FROM vicidial_list WHERE list_id = vicidial_lists.list_id) as tally, (SELECT count(*) as counter FROM vicidial_lists_fields WHERE list_id = vicidial_lists.list_id) as cf_count, vicidial_lists.active, vicidial_lists.list_lastcalldate, vicidial_lists.campaign_id, vicidial_lists.reset_time, vicidial_campaigns.campaign_name FROM vicidial_lists LEFT JOIN vicidial_campaigns ON vicidial_lists.campaign_id=vicidial_campaigns.campaign_id $ul ORDER by list_id;";			
				$rsltv 									= $astDB->rawQuery($query);
                //$testSQL                                = $query;
				
				foreach ($rsltv as $fresults) {
					$dataListId[] 						= $fresults['list_id'];
					$dataListName[] 					= $fresults['list_name'];
					$dataActive[] 						= $fresults['active'];
					$dataListLastcallDate[] 			= $fresults['list_lastcalldate'];
					$dataTally[] 						= $fresults['tally'];
					$dataCFCount[] 						= $fresults['cf_count'];
					$dataCampaignId[] 					= $fresults['campaign_id'];
					$dataCampaignName[] 				= $fresults['campaign_name'];
				}
				
				#get next list id
				//$query2 = "SELECT list_id from vicidial_lists WHERE list_id NOT IN ('999', '998') order by list_id;";
				$astDB->where('list_id', array('999','998'), 'not in');
				$astDB->orderBy('list_id', 'desc');
				$rsltv2 								= $astDB->get('vicidial_lists', null, 'list_id');
				
				foreach ($rsltv2 as $fetch_lists){
					$lists[] 							=  $fetch_lists['list_id'];
				}
				
				$max_list 								= max($lists);
				$min_list 								= min($lists);
				
				if ($max_list >= 99999999) {
					for($i=1;$i < $max_list;$i++){
						if(!in_array($i, $lists['list_id'])){
							$next_list 					= $i;
							$i 							= $max_list;
						}
					}
				} else {
					$next_list 							= $max_list + 1;
				}
				
				$apiresults 							= array(
					"result" 								=> "success",
					"list_id" 								=> $dataListId,
					"list_name" 							=> $dataListName,
					"active" 								=> $dataActive, 
					"list_lastcalldate" 					=> $dataListLastcallDate,
					"tally" 								=> $dataTally,
					"cf_count"								=> $dataCFCount,
					"campaign_id" 							=> $dataCampaignId, 
					"next_listID" 							=> $next_list, 
					"campaign_name" 						=> $dataCampaignName,
                    //"test_SQL"                              => $testSQL
				);
			} else {
				$err_msg 								= error_handle("40001");
				$apiresults 							= array(
					"code" 									=> "40001", 
					"result" 								=> $err_msg
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
