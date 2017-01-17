<?php
    include_once("../goFunctions.php");
    
    $query = "SELECT phone_number from vicidial_dnc;";
    $rsltv = mysqli_query($link, $query);
	$countResult = mysqli_num_rows($rsltv);
    
    if($countResult > 0) {
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataPhoneNumber[]       = $fresults['phone_number'];
            }
            
            $apiresults = array(
                "result"            => "success",
                "phone_number"      => $dataPhoneNumber
            );
    }else{
        $apiresults = array("result" => "Error: No record found.");
    }
?>