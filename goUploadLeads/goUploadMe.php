<?php
 ####################################################
 #### Name: goUploadMe.php                       ####
 #### Description: API for Uploading Leads       ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jerico James Milo              ####
 ####             Warren Ipac Briones            ####
 #### License: AGPLv2                            ####
 ####################################################
    include_once("goFunctions.php");

    $thefile = $_FILES['goFileMe']['tmp_name'];
    $theList = $_REQUEST["goListId"];

	// path where your CSV file is located
	define('CSV_PATH','/tmp/');

	// Name of your CSV file
	//$csv_file = CSV_PATH . "$thefile"; 
	$csv_file = $thefile;

//die($theList."<br>".$thefile."<br>".$csv_file);
if (($handle = fopen($csv_file, "r")) !== FALSE) {
   fgetcsv($handle);   
   while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        for ($c=0; $c < $num; $c++) {
          $col[$c] = $data[$c];
        }

	$col1 = $theList;
	$col2 = $col[0];
	$col3 = $col[1];
        $col4 = $col[2];
        $col5 = $col[3];
        $col6 = $col[4];
        $col7 = $col[5];
        $col8 = $col[6];
        $col9 = $col[7]; 
        $col10 = $col[8];  
        $col11 = $col[9];
	// SQL Query to insert data into DataBase
	$query = "INSERT INTO vicidial_list(list_id,first_name,middle_initial,last_name,address1,city,state,province,postal_code,email,comments) VALUES('".$col1."','".$col2."','".$col3."','".$col4."','".$col5."','".$col6."','".$col7."','".$col8."','".$col9."','".$col10."','".$col11."')";
	$rsltv = mysqli_query($link, $query);

//echo $col1."<br>".$col2."<br>".$col3;
$apiresults = array("result" => "success", "column1" => $col1, "column2" => $col2, "column3" => $col3, "column4" => $col4, "column5" => $col5, "column6" => $col6, "column7" => $col7, "column8" => $col8, "column9" => $col9, "column10" => $col10, "column11" => $col11);
//$apiresults = array("result" => "success");

 }
    fclose($handle);
}

//echo "File data successfully imported to database!!";
?>
