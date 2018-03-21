<?php
    ####################################################
    #### Name: goGetTicketsPerHour.php              ####
    #### Type: API to get hourly tickets            ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2016        ####
    #### Written by: Demian Lizandro Biscocho       ####
    #### License: AGPLv2                            ####
    ####################################################

    $groupId = go_get_groupid($goUser, $astDB);

    if (!checkIfTenant($groupId, $goDB)) {
        $ul = "";
    } else {
        $stringv = go_getall_allowed_campaigns($goUser, $astDB);
        $ul = " and campaign_id IN ('$stringv') ";
    }

    $query_date =  date('Y-m-d');

    //tickets
    $ostquery = "SELECT date_format(created, '%Y-%m-%d') as tdate,sum(if(date_format(created,'%H') = 01, 1, 0)) as 'Hour1',sum(if(date_format(created,'%H') = 02, 1, 0)) as 'Hour2',sum(if(date_format(created,'%H') = 03, 1, 0)) as 'Hour3',sum(if(date_format(created,'%H') = 04, 1, 0)) as 'Hour4',sum(if(date_format(created,'%H') = 05, 1, 0)) as 'Hour5',sum(if(date_format(created,'%H') = 06, 1, 0)) as 'Hour6',sum(if(date_format(created,'%H') = 07, 1, 0)) as 'Hour7',sum(if(date_format(created,'%H') = 08, 1, 0)) as 'Hour8',sum(if(date_format(created,'%H') = 09, 1, 0)) as 'Hour9',sum(if(date_format(created,'%H') = 10, 1, 0)) as 'Hour10',sum(if(date_format(created,'%H') = 11, 1, 0)) as 'Hour11',sum(if(date_format(created,'%H') = 12, 1, 0)) as 'Hour12',sum(if(date_format(created,'%H') = 13, 1, 0)) as 'Hour13',sum(if(date_format(created,'%H') = 14, 1, 0)) as 'Hour14',sum(if(date_format(created,'%H') = 15, 1, 0)) as 'Hour15',sum(if(date_format(created,'%H') = 16, 1, 0)) as 'Hour16',sum(if(date_format(created,'%H') = 17, 1, 0)) as 'Hour17',sum(if(date_format(created,'%H') = 18, 1, 0)) as 'Hour18',sum(if(date_format(created,'%H') = 19, 1, 0)) as 'Hour19',sum(if(date_format(created,'%H') = 20, 1, 0)) as 'Hour20',sum(if(date_format(created,'%H') = 21, 1, 0)) as 'Hour21',sum(if(date_format(created,'%H') = 22, 1, 0)) as 'Hour22',sum(if(date_format(created,'%H') = 23, 1, 0)) as 'Hour23',sum(if(date_format(created,'%H') = 24, 1, 0)) as 'Hour24' from ost_ticket WHERE date_format(created, '%Y-%m-%d') = CURDATE()";
    $fresults = $ostDB->rawQuery($ostquery);
    //$fresults = mysqli_fetch_assoc($rsltv);
	
    if ($fresults == NULL) {
        $fresults = array();
    }
    
    $apiresults = array_merge( array( "result" => "success" ), $fresults);
    
?>
