<?php
 ####################################################
 #### Name: goGetAllAgentsAPI.php                         ####
 #### Type: API for dashboard php encode         ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
 #### Written by: Jerico Jeremiah Sebastian Samatra       ####
 #### License: AGPLv2                            ####
 ####################################################

 
$url = "https://encrypted.goautodial.com/goAPI/goDashboard/goAPI.php"; #URL to GoAutoDial API. (required)
$postfields["goUser"] = "goautodial"; #Username goes here. (required)
$postfields["goPass"] = "JUs7g0P455W0rD11214";
$postfields["goAction"] = "getPerHourCall"; #action performed by the [[API:Functions]]


 $ch = curl_init();
 curl_setopt($ch, CURLOPT_URL, $url);
 curl_setopt($ch, CURLOPT_POST, 1);
 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
 $data = curl_exec($ch);
 curl_close($ch);
 
//var_dump($data); 
 $data = explode(";",$data);
 foreach ($data AS $temp) {
   $temp = explode("=",$temp);
   $results[$temp[0]] = $temp[1];
 }
//var_dump($data); 
 if ($results["result"]=="success") {
   # Result was OK!
   //var_dump($results); #to see the returned arrays.
				echo "Inbound Calls <br />";
                                echo $results["cdate"]."<br />";
                                echo $results["Hour9"]."<br />";
                                echo $results["Hour10"]."<br />";
                                echo $results["Hour11"]."<br />";
                                echo $results["Hour12"]."<br />";
                                echo $results["Hour13"]."<br />";
                                echo $results["Hour14"]."<br />";
                                echo $results["Hour15"]."<br />";
                                echo $results["Hour16"]."<br />";
                                echo $results["Hour17"]."<br />";
                                echo $results["Hour18"]."<br />";
                                echo $results["Hour19"]."<br />";
                                echo $results["Hour20"]."<br />";
                                echo $results["Hour21"]."<br /><br />";	
		
				echo "Dropped Calls <br />";
                                echo $results["cdated"]."<br />";
                                echo $results["Hour9d"]."<br />";
                                echo $results["Hour10d"]."<br />";
                                echo $results["Hour11d"]."<br />";
                                echo $results["Hour12d"]."<br />";
                                echo $results["Hour13d"]."<br />";
                                echo $results["Hour14d"]."<br />";
                                echo $results["Hour15d"]."<br />";
                                echo $results["Hour16d"]."<br />";
                                echo $results["Hour17d"]."<br />";
                                echo $results["Hour18d"]."<br />";
                                echo $results["Hour19d"]."<br />";
                                echo $results["Hour20d"]."<br />";
                                echo $results["Hour21d"]."<br />";

		
				echo "Outbound Calls <br />";
                                echo $results["cdateo"]."<br />";
                                echo $results["Hour9o"]."<br />";
                                echo $results["Hour10o"]."<br />";
                                echo $results["Hour11o"]."<br />";
                                echo $results["Hour12o"]."<br />";
                                echo $results["Hour13o"]."<br />";
                                echo $results["Hour14o"]."<br />";
                                echo $results["Hour15o"]."<br />";
                                echo $results["Hour16o"]."<br />";
                                echo $results["Hour17o"]."<br />";
                                echo $results["Hour18o"]."<br />";
                                echo $results["Hour19o"]."<br />";
                                echo $results["Hour20o"]."<br />";
                                echo $results["Hour21o"]."<br />";



 } else {
   # An error occured
   echo "The following error occured: ".$results["message"];
var_dump($data);
 }
 
?>
