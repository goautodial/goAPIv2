<?php
    #######################################################
    #### Name:  goGetAllAgentRank.php	               ####
    #### Description: API to get all agent assign      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2016      ####
    #### Written by: Jerico James F. Milo 	           ####
    #### License: AGPLv2                               ####
    #######################################################
    
    include_once ("goFunctions.php");
    
    $goUser 	= $_REQUEST['user_id'];
    $goVarLimit = $_REQUEST['goVarLimit'];
    $goGroupID 	= $_REQUEST['group_id'];
    
    if($goUser == null ) {  $apiresults = array(  "result" => "Error: Set a value for user_id");
	}elseif($goGroupID == null ) { $apiresults = array(  "result" => "Error: Set a value for group_id"); 
    } else {
	
		if($goVarLimit < 1){ $goVarLimit = ""; 	} else { $goVarLimit = "limit $goVarLimit"; }
		if (checkIfTenant($goUser)) { $addedSQL = "AND user_group='$user_group'"; }
		if (!is_null($find_user)) {	$findSQL = "AND user RLIKE '$find_user'"; }
	
		$query = "SELECT user,full_name,closer_campaigns,user_group from vicidial_users where user NOT IN ('VDAD','VDCL') and user_level != '4' $addedSQL $findSQL order by user $goVarLimit";
		$rsltv = mysqli_query($link, $query);
		$countResult = mysqli_num_rows($rsltv);
	
		if($countResult > 0) {
	
			while($fresults = mysqli_fetch_assoc($rsltv)){
			$isChecked = '';
				if (preg_match("/ $goGroupID /",$fresults['closer_campaigns'])) {$isChecked = ' CHECKED';}
	
			$stmtx="SELECT group_rank,group_grade,calls_today from vicidial_inbound_group_agents where group_id='$goGroupID' and user='{$fresults['user']}';";
			$rsltx = mysqli_query($link, $stmtx);
			$viga_to_print = mysqli_num_rows($rsltx);
	
				if ($viga_to_print > 0) {
						while($rowx = mysqli_fetch_assoc($rsltx)){
	
							$ARIG_rank  = $rowx['group_rank'];
							$ARIG_grade = $rowx['group_grade'];
							$ARIG_calls = $rowx['calls_today'];
	
							if($ARIG_calls==null){ $ARIG_calls="0";	}
	
						}
	
				} else {
	
					$stmtD="INSERT INTO vicidial_inbound_group_agents set calls_today='0',group_rank='0',group_weight='0',user='{$fresults['user']}',group_id='$goGroupID';";
					$rsltxy = mysqli_query($link, $stmtD);
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
