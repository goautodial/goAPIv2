<?php
    include_once("../goFunctions.php");
    
    $sounds_web_directory = '../../sounds';
    $files = scandir($sounds_web_directory);
    
    if(!empty($files)){
        $apiresults = array("result" => "success", "data" => $files);
    }else{
        $apiresults = array("result" => "error", "data" => $files);
    }
?>