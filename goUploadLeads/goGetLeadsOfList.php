<?php
 ####################################################
 #### Name: goGetLeadsOfList.php                 ####
 #### Description: API to get leads from list_id ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
 #### Written by: Jerico James Milo              ####
 #### License: AGPLv2                            ####
 ####################################################
    include_once("goFunctions.php");

    //$thefile = $_FILES['goFileMe']['tmp_name'];
    $theList = $_REQUEST["goListId"];

	$query = "SELECT phone_number FROM vicidial_list WHERE list_id='$theList';";
	$rsltv = mysqli_query($link, $query);
	$countResult = mysqli_num_rows($rsltv);

/*    if($countResult > 0) {
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataPhoneNumber[] = $fresults['phone_number'];
		}
	}
		
	$apiresults = array("result" => "success", "returnPnumbers" => $dataPhoneNumber);
*/
 
	if($countResult > 0) {
        
			$dataPhoneNumbers = array();
            
			while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                array_push($dataPhoneNumbers, $fresults);
				//$dataPhoneNumbers[] = 	$fresults['phone_number'];
            }
			 			
			$data = array_merge($dataPhoneNumbers);
			//echo count($data);
            $apiresults = array("result" => "success", "data" => $data);
    }
	

/*
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
*/
?>
