<?php
    ##############################################################
    #### Name: getAllDispositions.php 	               		  ####
    #### Description: API to get all custom Dispositions      ####
    #### Version: 0.9                                 		  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      		  ####
    #### Written by: Jeremiah Sebastian V. Samatra     		  ####
    #### License: AGPLv2                               		  ####
    ##############################################################
    include_once ("goFunctions.php");

        $selectSQL = "";
		$campSQL = "";
		$select = $_REQUEST['select'];
		$camp = $_REQUEST['campaign_id'];
		
                if ($select=="Y")
						$selectSQL = "WHERE selectable='Y'";
						
				if (!is_null($camp))
                        $campSQL = "AND campaign_id='$camp'";

                if (is_null($camp)){
                        $camps = go_getall_allowed_campaigns($goUser);
						
                        if ($select=="N"){
								 $campSQL = "WHERE campaign_id IN ('$camps')";
						}else{
								$campSQL = "AND campaign_id IN ('$camps')";
						}
                }

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
						$addedSQL = "WHERE user_group='$groupId'";
				}
		
		if($camp != NULL){
				$query = "SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses $selectSQL  ORDER BY campaign_id";
		}else{
				$query = "SELECT status, status_name FROM vicidial_campaign_statuses UNION  SELECT status, status_name FROM vicidial_statuses ORDER BY status;";
		}
                
				$rsltv = mysqli_query($link, $query);
/*	
		$check_result_status = mysql_num_rows($rslt);

        if ($check_result_status < 1){

		$queryOne = "SELECT status,status_name FROM vicidial_statuses $selectSQL ORDER BY status";
		$rsltv = mysql_query($queryOne, $link);
            
		}	
*/
		while($fresult = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataStat[] = $fresult['status'];			
			$dataStatName[] = $fresult['status_name'];
			
			if($camp != NULL)
				$dataCampID[] = $fresult['campaign_id'];			

				if($camp != NULL){
						$apiresults = array("result" => "success", "query" => $query, "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
				}else{
						$apiresults = array("result" => "success", "query" => $query, "status" => $dataStat, "status_name" => $dataStatName);
				}
				
				
		}




?>

