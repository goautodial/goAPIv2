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
    // GROUP BY gmt_offset_now,called_since_last_reset
    $query = "SELECT
				gmt_offset_now,
				called_since_last_reset,
				count(*) as counttlist
			FROM vicidial_list
			WHERE list_id='$list_id'
			GROUP BY gmt_offset_now 
			ORDER BY gmt_offset_now,called_since_last_reset;";
	$rsltv = mysqli_query($link, $query);
    
    while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
		$dataGMT[]                	=  $fresults['gmt_offset_now'];
        $dataCalledSinceLastReset[] =  $fresults['called_since_last_reset'];
        $dataCountTLists[]          =  $fresults['counttlist'];

		$apiresults = array(
			"result"                    => "success",
			"gmt_offset_now"            => $dataGMT,
            "called_since_last_reset"   => $dataCalledSinceLastReset,
            "counttlist"               	=> $dataCountTLists
		);
	}
?>