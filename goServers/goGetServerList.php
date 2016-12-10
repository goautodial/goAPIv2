<?php
    #######################################################
    #### Name: goGetServerList.php                     ####
    #### Description: API to get all servers           ####
    #### Version: 4.0                                  ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2016      ####
    #### Written by: Alexander Jim H. Abenoja          ####
    #### License: AGPLv2                               ####
    #######################################################
    include_once ("goFunctions.php");

        $query = "SELECT server_description, server_ip FROM servers ORDER BY server_ip;";
  		$rsltv = mysqli_query($link,$query);

		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataDesc[] = $fresults['server_description'];
			$dataServerIP[] = $fresults['server_ip'];
		}
		
		$apiresults = array("result" => "success", "server_description" => $dataDesc, "server_ip" => $dataServerIP);

?>
