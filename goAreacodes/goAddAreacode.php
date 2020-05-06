<?php
/**
 * @file 		goAddAreacode.php
 * @brief 		API to add areacode
 * @copyright 	Copyright (c) 2019 GOautodial Inc.
 * @author		Thom Bernarth Patacsil 
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

	$campaign_id 										= $astDB->escape($_REQUEST['campaign_id']);
	$areacode 										= $astDB->escape($_REQUEST['areacode']);
	$outbound_cid 										= $astDB->escape($_REQUEST['outbound_cid']);
	$cid_description									= $astDB->escape($_REQUEST['cid_description']);	
	$active 										= 'Y';

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
	} elseif (empty($campaign_id) || is_null($campaign_id)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: Campaign ID not Defined."
                ); 
	} elseif (empty($areacode) || is_null($areacode)) {
                $apiresults                                                                     = array(
                        "result"                                                                                => "Error: Areacode not Defined."
                );
	} else {
		// check if goUser and goPass are valid
		$fresults										= $astDB
			->where("user", $goUser)
			->where("pass_hash", $goPass)
			->getOne("vicidial_users", "user,user_level");
		
		$goapiaccess									= $astDB->getRowCount();
		$userlevel									= $fresults["user_level"];
		
		if ($goapiaccess > 0 && $userlevel > 7) {				
		
			// check if already existing in whole system
			$astDB->where( "campaign_id", $campaign_id );
			$astDB->where( "areacode", $areacode );
			$astDB->getOne( "vicidial_campaign_cid_areacodes", "campaign_id, areacode" );
		    
			if ( $astDB->count > 0 ) {
				$err_msg 									= "Areacode is Existing in the Campaign";
				$apiresults 								= array(
					"result" 									=> $err_msg
				);
			} else {

					$data						= array(
						'campaign_id' 						=> $campaign_id, 
						'areacode' 						=> $areacode, 
						'outbound_cid' 						=> $outbound_cid,
						'active'						=> $active,
						'cid_description' 					=> $cid_description 
					);
					
					$insertdata 					= $astDB->insert( 'vicidial_campaign_cid_areacodes', $data );					
				if($insertdata){
					$log_id                                         = log_action( $goDB, 'ADD', $log_user, $log_ip, "Added a New ACCID: $areacode at Campaign ID: $campaign_id", $log_group, $astDB->getLastQuery() );
					$apiresults = array (
						"result"		=> 'success',
						"data"			=> $data
					);
				} else {
					$apiresults = array (
						"result"		=> $astDB->getLastError()
					);
				}
			}
		}
	}
	return $apiresults;
?>
