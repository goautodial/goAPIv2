<?php
 ####################################################
 #### Name: sample.php                           ####
 #### Description: API for Uploading Leads       ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo              ####
 ####             Warren Ipac Briones            ####
 #### License: AGPLv2                            ####
 ####################################################
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head> 
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /> 
<title>Import a CSV File with PHP & MySQL</title> 
</head> 

<body> 

<?php if (!empty($_GET[success])) { echo "<b>Your file has been imported.</b><br><br>"; } //generic success notice ?> 

<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1"> 
<input type="hidden" name="list_id" value="999">


  Choose your file: <br /> 
  <input name="fileme" type="file" id="fileme" /> 
  <input type="submit" name="Submit" value="Submit" /> 
</form> 

</body> 
</html> 

<?php

if(isset($_POST["Submit"])){

//echo $_POST["fileme"];
echo $_POST["list_id"];
echo $_FILES[fileme][tmp_name];


uploadMeNow($_POST["list_id"],$_FILES[fileme][tmp_name]);

}


function uploadMeNow($list_id,$tmpsFile) {
 $url = "https://gadcs.goautodial.com/goAPI/goUploadLeads/goAPI.php"; #URL to GoAutoDial API. (required)
        $postfields["goUser"] = "admin"; #Username goes here. (required)
        $postfields["goPass"] = "kam0teque1234"; #Password goes here. (required)
        $postfields["goAction"] = "goUploadMe"; #action performed by the [[API:Functions]]. (required)
        $postfields["goFileMe"] = "$tmpsFile"; #action performed by the [[API:Functions]]. (required)
        $postfields["goListId"] = "$list_id"; #action performed by the [[API:Functions]]. (required)
        $postfields["responsetype"] = "json"; #json. (required)

         $ch = curl_init();
         curl_setopt($ch, CURLOPT_URL, $url);
         curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
         curl_setopt($ch, CURLOPT_POST, 1);
         curl_setopt($ch, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
         $data = curl_exec($ch);
         curl_close($ch);
         $output = json_decode($data);

        if ($output->result=="success") {
           # Result was OK!
		echo "<br>".$output->result."<br>";
		echo "<br>".$output->column1."<br>";
		echo "<br>".$output->column2."<br>";
		echo "<br>".$output->column3."<br>";
                echo "<br>".$output->column4."<br>";
                echo "<br>".$output->column5."<br>";
                echo "<br>".$output->column6."<br>";
                echo "<br>".$output->column7."<br>";
                echo "<br>".$output->column8."<br>";
                echo "<br>".$output->column9."<br>";
                echo "<br>".$output->column10."<br>";
                echo "<br>".$output->column11."<br>";
         } else {
           # An error occured
		//var_dump($data);
                echo "The following error occured: ".$output->result;
        }


}
?>
