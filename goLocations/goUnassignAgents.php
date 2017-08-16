<?php
/*
#######################################################
#### Name: goAssignedAgents.php	                   ####
#### Description: API to get all assigned agents   ####
#### Version: 0.9                                  ####
#### Copyright: GOAutoDial Inc. (c) 2011-2014      ####
#### Written by: Chris Lomuntad                    ####
#### License: AGPLv2                               ####
#######################################################
*/

if (isset($_GET['unassigned'])) { $unassigned = $_GET['unassigned']; }
    else if (isset($_POST['unassigned'])) { $unassigned = $_POST['unassigned']; }

if (isset($unassigned) && count($unassigned) > 0) {
	foreach ($unassigned as $unassign) {
		$astDB->where('id', $unassign);
		$astDB->delete('vicidial_campaign_agents');
	}
	
	$APIResult = array("result" => "success");
} else {
	$APIResult = array("result" => "error");
}
?>
