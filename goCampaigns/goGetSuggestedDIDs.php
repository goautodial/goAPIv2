<?php
    include_once("../goFunctions.php");
    
    $keyword = $_REQUEST['keyword'];
    
    $query = "SELECT did_pattern from vicidial_inbound_dids where did_pattern LIKE '$keyword%';";
   	$rsltv = mysqli_query($link, $query);
    $countResult = mysqli_num_rows($rsltv);
    
    if($countResult > 0) {
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
            $dids[] = $fresults['did_pattern'];
        }
        
        $dataDID = "[";
        foreach($dids as $did){
            $dataDID .= '"'.$did.'",';
        }
        $dataDID = rtrim($dataDID, ",");
        $dataDID .= "]";
        
        $apiresults = array("result" => "success", "data" => $dataDID);
    } else {
        $apiresults = array("result" => "error");
    }
    
?>