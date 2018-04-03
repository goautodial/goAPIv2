<?php
 /**
 * @file 		goGetLeadsOfList.php
 * @brief 		API for Uploading Leads
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author		Jericho James Milo  <james@goautodial.com>
 * @author     	Chris Lomuntad  <chris@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    //$thefile = $_FILES['goFileMe']['tmp_name'];
    $theList = $astDB->escape($_REQUEST["goListId"]);

	//$query = "SELECT phone_number FROM vicidial_list WHERE list_id='$theList';";
	$astDB->where('list_id', $theList);
	$rsltv = $astDB->get('vicidial_list', null, 'phone_number');
	$countResult = $astDB->getRowCount();

/*    if($countResult > 0) {
        while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
                $dataPhoneNumber[] = $fresults['phone_number'];
		}
	}
		
	$apiresults = array("result" => "success", "returnPnumbers" => $dataPhoneNumber);
*/
 
	if($countResult > 0) {
        
			$dataPhoneNumbers = array();
            
			foreach ($rsltv as $fresults){
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