<?php
    #######################################################
    #### Name: getAllDispositions.php 	               ####
    #### Description: API to get all Dispositions      ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Jeremiah Sebastian V. Samatra     ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");

                $selectSQL = "";
		$select = $_REQUEST['select'];
		$camp = $_REQUEST['campaign_id'];
                if ($select=="Y")
                        $selectSQL = "WHERE selectable='Y'";
		$campSQL = "";
                if (!is_null($camp))
                        $campSQL = "AND campaign_id='$camp'";

                if (is_null($camp)) {
                        $camps = go_getall_allowed_campaigns($goUser);
                        if ($select=="N")
                                $campSQL = "WHERE campaign_id IN ('$camps')";
                        else
                                $campSQL = "AND campaign_id IN ('$camps')";
                }

                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
                        $ul = "AND user_group='$groupId'";
                   $addedSQL = "WHERE user_group='$groupId'";
		}

                $query = "SELECT status,status_name,campaign_id FROM vicidial_campaign_statuses $selectSQL  ORDER BY campaign_id";
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
			$dataCampID[] = $fresult['campaign_id'];			

		//$query = "SELECT campaign_name,campaign_id FROM vicidial_campaigns $ul $addedSQL  ORDER BY campaign_id";
          //      $rsltv2 = mysqli_query($link, $query);
			//while($fresults = mysqli_fetch_array($rsltv2, MYSQLI_ASSOC)){
			//$dataCampName[] = $fresults['campaign_name'];
			//$dataStatName[] = $fresults['status_name'];
			//$dataCampID[] = $fresults['campaign_id'];
			//$dataStat[] = $fresults['status'];

 	  		//$apiresults = array("result" => "success", "campaign_name" => $dataCampName, "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
		$apiresults = array("result" => "success", "query" => $query, "campaign_id" => $dataCampID, "status_name" => $dataStatName, "status" => $dataStat);
			//}
		}




?>

