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
$postfields["goAction"] = "goGetClusterStatus"; #action performed by the [[API:Functions]]


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
 
 if ($results["result"]=="success") {
   # Result was OK!
   //var_dump($results); #to see the returned arrays.

	echo $results["server_id"]."<br />";
	echo $results["server_ip"]."<br />";
	echo $results["active"]."<br />";
	echo $results["channels_total"]."<br />";
	echo $results["s_time"]."<br />";
	echo $results["sysload"]." - ". $results['cpu']."%"."<br />";
	echo $results["disk_usage"]."%";
	echo "<br /><br />";
	echo $results["db_time"]."<br />";
	echo $results["php_time"]."<br />";
 } else {
   # An error occured
   echo "The following error occured: ".$results["message"];
   var_dump($data);
 }
 
?>
