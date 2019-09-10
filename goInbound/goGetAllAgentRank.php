<?php
/**
 * @file        goGetAllAgentRank.php
 * @brief       API to all agent rank info
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author     	Alexander Jim H. Abenoja
 * @author      Jerico James F. Milo
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

    $limit 												= (isset($_REQUEST['limit']) ? $astDB->escape($_REQUEST['limit']) : 1000);
    $ingroup_id 										= $astDB->escape($_REQUEST['group_id']);
    $find_user 											= $astDB->escape($_REQUEST['findUser']);
    
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
	} elseif (empty($ingroup_id) || is_null($ingroup_id)) {
        $apiresults 									= array(
			"result" 										=> "Error: Set a value for Group ID."
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
			$tenant										= (checkIfTenant($log_group, $goDB)) ? 1 : 0;
			
			if ($tenant) {
				$astDB->where("user_group", $log_group);
				$astDB->orWhere("user_group", "---ALL---");
			} else {
				if (strtoupper($log_group) != 'ADMIN') {
					if ($userlevel > 8) {
						$astDB->where("user_group", $log_group);
						$astDB->orWhere("user_group", "---ALL---");
					}
				}					
			}
		
			if (!is_null($find_user)) {
				$astDB->where("user", $find_user, "RLIKE");
				//$findSQL = "AND user RLIKE '$find_user'";
			}
		
			$cols 										= array(
				"user", 
				"full_name", 
				"closer_campaigns", 
				"user_group"
			);
			
			$astDB->where("user", DEFAULT_USERS, "NOT IN");
			$astDB->where("user_level", 4, "!=");
			$query_vu 									= $astDB->get("vicidial_users", $limit, $cols);
			//$query = "SELECT user,full_name,closer_campaigns,user_group from vicidial_users where user NOT IN ('VDAD','VDCL') and user_level != '4' $addedSQL $findSQL order by user $goVarLimit";
			
			if ($astDB->count > 0) {
				foreach ($query_vu as $fresults) {
					$isChecked 							= '';
					$username							= $fresults['user'];
					$closer_campaigns					= $fresults['closer_campaigns'];
					
					if (preg_match("/ $ingroup_id /", $closer_campaigns)) {
						$isChecked 						= ' CHECKED';
					}
					
					$cols2 								= array(
						"group_rank", 
						"group_grade", 
						"calls_today"
					);
					
					$query_viga 						= $astDB
						->where("group_id", $ingroup_id)
						->where("user", $username)
						->get("vicidial_inbound_group_agents", null, $cols2);
						//$stmtx="SELECT group_rank,group_grade,calls_today from vicidial_inbound_group_agents where group_id='$ingroup_id' and user='{$fresults['user']}';";
					
					if ($astDB->count > 0) {
						foreach ($query_viga as $fresults_viga) {
							$group_rank  				= $fresults_viga['group_rank'];
							$group_grade 				= $fresults_viga['group_grade'];
							$calls_today 				= $fresults_viga['calls_today'];
							
							if ($calls_today == null) {
								$calls_today				= 0;	
							}
						}
					} else {
						$insertData 					= array(
							"calls_today" 					=> 0,
							"group_rank" 					=> 0,
							"group_weight" 					=> 0,
							"user" 							=> $username,
							"group_id" 						=> $ingroup_id
						);
						
						$astDB->insert("vicidial_inbound_group_agents", $insertData);
						//$stmtD="INSERT INTO vicidial_inbound_group_agents set calls_today='0',group_rank='0',group_weight='0',user='{$fresults['user']}',group_id='$ingroup_id';";
						//$rsltxy = mysqli_query($link, $stmtD);
						$group_rank 					= 0;
						$group_grade 					= 0;
						$calls_today 					= 0;
					}
		
					$checkbox_field 					= "CHECK_$username";
					$rank_field     					= "RANK_$username";
					$grade_field    					= "GRADE_$username";
					$checkbox_list 						.= "|$checkbox_field";
			
					// start return data 
					$dataUser[]      					= $fresults['user'];
					$dataFullName[]  					= $fresults['full_name'];
					$dataUserGroup[] 					= $fresults['user_group'];
			
					//checkbox values and names & id
					//$users_output .= "<input type=checkbox name=\"$checkbox_field\" id=\"$checkbox_field\" value=\"YES\"$isChecked>";
					$dataCheckboxField[]				= $checkbox_field;
					$dataIsChecked[]    				= $isChecked;
			
					//rank dropdown name or id,def value,values from db ::
					//-> CI $users_output .= form_dropdown("$rank_field",$rankArray,$group_rank,"style='font-size:10px;'");
					// <select name="$rank_field" id=rank_field"> <option value="$group_rank" selected>$group_rank</option> <option value="$rankArray">$rankArray</option>"
					$rankArray 		  					= array(
						'9'									=> '9',
						'8'									=> '8',
						'7'									=> '7',
						'6'									=> '6',
						'5'									=> '5',
						'4'									=> '4',
						'3'									=> '3',
						'2'									=> '2',
						'1'									=> '1',
						'0'									=> '0',
						'-1'								=> '-1',
						'-2'								=> '-2',
						'-3'								=> '-3',
						'-4'								=> '-4',
						'-5'								=> '-5',
						'-6'								=> '-6',
						'-7'								=> '-7',
						'-8'								=> '-8',
						'-9'								=> '-9'
					);
					
					$dataRankFields[] 					= $rank_field;
					$dataArigRank[]   					= $group_rank;
					$dataRankArray   					= $rankArray;
					//grade dropdown name or id, def value, values from db :: 
					//-> CI $users_output .= form_dropdown("$grade_field",$gradeArray,$group_grade,"style='font-size:10px;'");
					// <select name="$grade_field" id="$grade_field"> <option value="$group_grade" selected>$group_grade</option> <option value="$gradeArray">$gradeArray</option>"
					$gradeArray 						= array(
						'10'								=>'10',
						'9'									=> '9',
						'8'									=> '8',
						'7'									=> '7',
						'6'									=> '6',
						'5'									=> '5',
						'4'									=> '4',
						'3'									=> '3',
						'2'									=> '2',
						'1'									=> '1',
						'0'									=> '0'
					);
					
					$dataGradeField[] 					= $grade_field;
					$dataArigGrade[]  					= $group_grade;
					$dataArigCalls[] 					= $calls_today;
					$dataGradeArray[]  					= $gradeArray;				
				}	
				
				$apiresults 							= array(
					"result" 								=> "success", 
					"user" 									=> $dataUser, 
					"full_name" 							=> $dataFullName, 
					"user_group"							=> $dataUserGroup, 
					"checkbox_fields" 						=> $dataCheckboxField, 
					"checkbox_ischecked" 					=> $dataIsChecked,
					"rank_fields" 							=> $dataRankFields,
					"values_rank" 							=> $dataArigRank,
					"dropdown_rankdefvalues" 				=> $dataRankArray,
					"grade_fields" 							=> $dataGradeField,
					"values_grade" 							=> $dataArigGrade,
					"dropdown_gradedefvalues" 				=> $dataGradeArray,
					"call_today" 							=> $dataArigCalls
				);			
			}  else {
				$apiresults 							= array(
					"result" 								=> "Error: No data to show."
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
