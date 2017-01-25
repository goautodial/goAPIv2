<?php
    include_once("../goFunctions.php");
    
	$search = mysqli_real_escape_string($link,$_REQUEST['search']);
	
    //$query = "SELECT phone_number, campaign_id from vicidial_campaign_dnc WHERE phone_number LIKE '$search%' OR campaign_id LIKE '$search%' LIMIT 1000;";
	$query = "SELECT * FROM (SELECT a.phone_number AS phone_number, '' AS campaign_id FROM vicidial_dnc a UNION SELECT b.phone_number AS phone_number, b.campaign_id AS campaign_id FROM vicidial_campaign_dnc b) searchdnc where phone_number LIKE '$search%' OR campaign_id LIKE '$search%' LIMIT 1000;";
    $rsltv = mysqli_query($link, $query);
	$countResult = mysqli_num_rows($rsltv);
    
    if($countResult > 0) {
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$dataPhoneNumber[]       = $fresults['phone_number'];
			$dataCampaign[]       = $fresults['campaign_id'];
		}
		
		$apiresults = array(
			"result"            => "success",
			"phone_number"      => $dataPhoneNumber,
			"campaign"			=> $dataCampaign
		);
    }else{
        $apiresults = array("result" => "Error: No record found.");
    }
?>