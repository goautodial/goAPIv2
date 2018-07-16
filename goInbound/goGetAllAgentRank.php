<?php
/**
 * @file        goGetAllAgentRank.php
 * @brief       API to all agent rank info
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James F. Milo  <jericojames@goautodial.com>
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
    
    include_once ("goAPI.php");
    
    $limit = $_REQUEST['limit'];
    $goGroupID 	= $_REQUEST['group_id'];
    $find_user 	= $_REQUEST['findUser'];
    
    if(empty($goGroupID) ) { 
		$apiresults = array(  "result" => "Error: Set a value for group_id"); 
    } else {
	
		if($limit < 1)
			$limit = 1;

		$groupId = go_get_groupid($goUser, $astDB);

		if (checkIfTenant($goUser, $goDB)) {
			$astDB->where("user_group", $groupId);
			//$addedSQL = "AND user_group='$user_group'";
		}
		if (!is_null($find_user)) {
			$astDB->where("user", $find_user, "RLIKE");
			//$findSQL = "AND user RLIKE '$find_user'";
		}
		
		$cols = Array("user", "full_name", "closer_campaigns", "user_group");
		$astDB->where("user", Array("VDAD", "VDCL"), "NOT IN");
		$astDB->where("user_level", "4", "!=");
		$selectQuery = $astDB->get("vicidial_users", $limit, $cols);
		//$query = "SELECT user,full_name,closer_campaigns,user_group from vicidial_users where user NOT IN ('VDAD','VDCL') and user_level != '4' $addedSQL $findSQL order by user $goVarLimit";
		
		if($astDB->count > 0) {
			foreach ($selectQuery as $fresults) {
				$isChecked = '';
				if (preg_match("/ $goGroupID /",$fresults['closer_campaigns'])) {$isChecked = ' CHECKED';}
				
				$cols2 = Array("group_rank", "group_grade", "calls_today");
				$astDB->where("group_id", $goGroupID);
				$astDB->where("user", "{$fresults['user']}");
				$selectQuery2 = $astDB->get("vicidial_inbound_group_agents", null, $cols2);
				//$stmtx="SELECT group_rank,group_grade,calls_today from vicidial_inbound_group_agents where group_id='$goGroupID' and user='{$fresults['user']}';";
				
				if ($astDB->count > 0) {
					foreach ($selectQuery2 as $rowx){
						$ARIG_rank  = $rowx['group_rank'];
						$ARIG_grade = $rowx['group_grade'];
						$ARIG_calls = $rowx['calls_today'];
						if($ARIG_calls==null){ $ARIG_calls="0";	}
					}
				} else {
					$insertData = Array(
									"calls_today" => 0,
									"group_rank" => 0,
									"group_weight" => 0,
									"user" => "{$fresults[user]}",
									"group_id" => $goGroupID
								);
					$astDB->insert("vicidial_inbound_group_agents");
					//$stmtD="INSERT INTO vicidial_inbound_group_agents set calls_today='0',group_rank='0',group_weight='0',user='{$fresults['user']}',group_id='$goGroupID';";
					//$rsltxy = mysqli_query($link, $stmtD);
					$ARIG_rank =        '0';
					$ARIG_grade =       '0';
					$ARIG_calls =       '0';
				}
	
			$checkbox_field = "CHECK_{$fresults['user']}";
			$rank_field     = "RANK_{$fresults['user']}";
			$grade_field    = "GRADE_{$fresults['user']}";
			$checkbox_list .= "|$checkbox_field";
	
			// start return data 
			$dataUser[]      = $fresults['user'];
			$dataFullName[]  = $fresults['full_name'];
			$dataUserGroup[] = $fresults['user_group'];
	
			//checkbox values and names & id
			//$users_output .= "<input type=checkbox name=\"$checkbox_field\" id=\"$checkbox_field\" value=\"YES\"$isChecked>";
			$dataCheckboxField[] = $checkbox_field;
			$dataIsChecked[]     = $isChecked;
	
			//rank dropdown name or id,def value,values from db ::
			//-> CI $users_output .= form_dropdown("$rank_field",$rankArray,$ARIG_rank,"style='font-size:10px;'");
			// <select name="$rank_field" id=rank_field"> <option value="$ARIG_rank" selected>$ARIG_rank</option> <option value="$rankArray">$rankArray</option>"
			$rankArray 		  	= array('9'=>'9','8'=>'8','7'=>'7','6'=>'6','5'=>'5','4'=>'4','3'=>'3','2'=>'2','1'=>'1','0'=>'0','-1'=>'-1','-2'=>'-2','-3'=>'-3','-4'=>'-4','-5'=>'-5','-6'=>'-6','-7'=>'-7','-8'=>'-8','-9'=>'-9');
			$dataRankFields[] 	= $rank_field;
			$dataArigRank[]   	= $ARIG_rank;
			$dataRankArray   	= $rankArray;
				  
			//grade dropdown name or id, def value, values from db :: 
			//-> CI $users_output .= form_dropdown("$grade_field",$gradeArray,$ARIG_grade,"style='font-size:10px;'");
			// <select name="$grade_field" id="$grade_field"> <option value="$ARIG_grade" selected>$ARIG_grade</option> <option value="$gradeArray">$gradeArray</option>"
			$gradeArray 		= array('10'=>'10','9'=>'9','8'=>'8','7'=>'7','6'=>'6','5'=>'5','4'=>'4','3'=>'3','2'=>'2','1'=>'1','0'=>'0');
			$dataGradeField[] 	= $grade_field;
			$dataArigGrade[]  	= $ARIG_grade;
			$dataArigCalls[] 	= $ARIG_calls;
			$dataGradeArray   	= $gradeArray;
			
			
			$apiresults = array("result" => "success", 
								"user" => $dataUser, 
								"full_name" => $dataFullName, 
								"user_group" => $dataUserGroup, 
								"checkbox_fields" => $dataCheckboxField, 
								"checkbox_ischecked" => $dataIsChecked,
								"rank_fields" => $dataRankFields,
								"values_rank" => $dataArigRank,
								"dropdown_rankdefvalues" => $dataRankArray,
								"grade_fields" => $dataGradeField,
								"values_grade" => $dataArigGrade,
								"dropdown_gradedefvalues" => $dataGradeArray,
								"call_today" => $dataArigCalls); 
			}
	
		}  else {
			$apiresults = array("result" => "Error: No data to show.");
	   }
   }
?>
