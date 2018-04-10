<?php
    $keyword = $_REQUEST['keyword'];

    $astDB->where('did_pattern', "$keyword%", 'like');
    $rsltv = $astDB->get('vicidial_inbound_dids', null, 'did_pattern')
    
    if($rsltv) {
        foreach($rsltv as $fresults){
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