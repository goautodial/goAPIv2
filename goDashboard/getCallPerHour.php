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




                $groupId = go_get_groupid($goUser);

                if (!checkIfTenant($groupId)) {
                        $ul = "";
                } else {
			$stringv = go_getall_allowed_campaigns($goUser);
			$ul = " and campaign_id IN ('$stringv') "; 
		}

           $query_date =  date('Y-m-d H:i:s');

		 $query = "select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21' FROM vicidial_closer_log WHERE date_format(call_date, '%Y-%m-%d') = '$query_date'  $ul group by cdate;";
		 
		

	  //  $query = "select date_format(call_date, '%Y-%m-%d') as cdate,sum(if(date_format(call_date,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(call_date,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(call_date,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(call_date,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(call_date,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(call_date,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(call_date,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(call_date,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(call_date,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(call_date,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(call_date,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(call_date,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(call_date,'%H') = 21, 1, 0)) as 'Hour21' from vicidial_closer_log;"; 

	   $rsltv = mysqli_query($link,$query);

		while($fresult = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$datacdate[] = $fresult['cdate'];			
			$dataHour9[] = $fresult['Hour9'];			
			$dataHour10[] = $fresult['Hour10'];			
			$dataHour11[] = $fresult['Hour11'];			
			$dataHour12[] = $fresult['Hour12'];			
			$dataHour13[] = $fresult['Hour13'];			
			$dataHour14[] = $fresult['Hour14'];			
			$dataHour15[] = $fresult['Hour15'];			
			$dataHour16[] = $fresult['Hour16'];			
			$dataHour17[] = $fresult['Hour17'];			
			$dataHour18[] = $fresult['Hour18'];			
			$dataHour19[] = $fresult['Hour19'];			
			$dataHour20[] = $fresult['Hour20'];			
			$dataHour21[] = $fresult['Hour21'];			

 	  		$apiresults = array("result" => "success", "cdate" => $datacdate, "Hour9" => $dataHour9, "Hour10" => $dataHour10, "Hour11" => $dataHour11, "Hour12" => $dataHour12, "Hour13" => $dataHour13, "Hour14" => $dataHour14, "Hour15" => $dataHour15, "Hour16" => $dataHour16, "Hour17" => $dataHour17, "Hour18" => $dataHour18, "Hour19" => $dataHour19, "Hour20" => $dataHour20, "Hour21" => $dataHour21);
			}


?>

