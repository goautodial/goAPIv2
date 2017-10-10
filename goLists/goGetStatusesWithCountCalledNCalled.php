<?php
    #######################################################
    #### Name: goGetListInfo.php	                   ####
    #### Description: API to get specific List	       ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2016-2017      ####
    #### Written by: NOEL UMANDAP                      ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once("../goFunctions.php");
    
    $list_id = $_REQUEST['list_id'];
    // GROUP BY vicidial_list.status,vicidial_list.called_since_last_reset
    // $query = "SELECT
    //             vicidial_list.status as stats,
    //             vicidial_list.called_since_last_reset,
    //             count(*) as countvlists,
    //             vicidial_statuses.status_name
    //         FROM vicidial_list
    //         LEFT JOIN vicidial_statuses
    //         ON vicidial_list.status=vicidial_statuses.status
    //         WHERE vicidial_list.list_id='$list_id'
    //         GROUP BY vicidial_list.status, substr(vicidial_list.called_since_last_reset,1,1) 
    //         ORDER BY vicidial_list.status,vicidial_list.called_since_last_reset;";
    $query = "SELECT
                vicidial_list.status as stats,
                SUM(IF(substr(vicidial_list.called_since_last_reset,1,1)='Y',1,0)) AS 'is_called',
                SUM(IF(substr(vicidial_list.called_since_last_reset,1,1)='N',1,0)) AS 'not_called',
                count(*) as countvlists,
                vicidial_statuses.status_name
            FROM vicidial_list
            LEFT JOIN vicidial_statuses
            ON vicidial_list.status=vicidial_statuses.status
            WHERE vicidial_list.list_id='$list_id' 
            GROUP BY vicidial_list.status 
            ORDER BY vicidial_list.status,vicidial_list.called_since_last_reset;";
	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataStats[]                =  $fresults['stats'];
        $dataIsCalled[] =  $fresults['is_called'];
        $dataNotCalled[] =  $fresults['not_called'];
        $dataCountVLists[]          =  $fresults['countvlists'];
        $dataStatName[]             =  $fresults['status_name'];

		$apiresults = array(
			"result"                    => "success",
			"stats"                     => $dataStats,
            "is_called"   => $dataIsCalled,
            "not_called"   => $dataNotCalled,
            "countvlists"               => $dataCountVLists,
            "status_name"               => $dataStatName
            // "query" => $query
		);
	}
?>