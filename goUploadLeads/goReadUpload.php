<?php
 /**
 * @file 	goReadUpload.php
 * @brief 	API for Uploading Leads with Lead Mapping Function
 * @copyright 	Copyright (C) GOautodial Inc.
 * @author     	Alexander Jim H. Abenoja  <alex@goautodial.com>
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
	include_once (__DIR__ . "/goAPI.php");

/** @var MySQLiDB $astDB */
/** @var MySQLiDB $goDB */
/** @var MySQLiDB $kamDB */
/** @var string $goUser */
/** @var string $goPass */
/** @var string $goAction */
/** @var string $goURL */
/** @var string $userResponseType */
/** @var string $session_user */
/** @var string $log_user */
/** @var string|false $log_group */
/** @var string $log_ip */


	ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '6000M');
	ini_set('post_max_size', '6000M');

	//ini_set('display_errors', 'on');
    	//error_reporting(E_ALL);

	if (empty($_FILES['goFileMe']['tmp_name']) || !is_uploaded_file($_FILES['goFileMe']['tmp_name'])) {
		$apiresults = [
			'result' => 'error',
			'message' => 'No upload file was provided.'
		];
		return;
	}

	$thefile = $_FILES['goFileMe']['tmp_name'];
	$theList = ($_REQUEST["goListId"] ?? '');
	$goDupcheck = ($_REQUEST["goDupcheck"] ?? '');
	$default_delimiter = ",";
	$delimiters = [];

	// path where your CSV file is located
	define('CSV_PATH','/tmp/');

	// Name of your CSV file
	$csv_file = $thefile;

	// REPLACE DELIMITER to SEMI-COLON -- CUSTOMIZATION!!!!!
        if(!empty($_REQUEST["custom_delimiter"]) && isset($_REQUEST["custom_delimiter"])){
           //$delimiters = ($_REQUEST["custom_delimiter"] ?? '');
           $delimiters = explode(" ", ($_REQUEST["custom_delimiter"] ?? ''));
           $str = file_get_contents($csv_file);
           if ($str !== false) {
               $str1 = str_replace($delimiters, $default_delimiter, $str);
               file_put_contents($csv_file, $str1);
           }
        }
	// REGEX to prevent weird characters from ending up in the fields
	$field_regx = "/['\"`\\;]/";
	$getCF = [];
	$field_regx = str_replace((array) $delimiters, "", $field_regx);

	// STANDARD FIELDS
	$getSF = ["Phone","VendorLeadCode","PhoneCode","Title","FirstName","MiddleInitial","LastName","Address1","Address2","Address3","City","State","Province","PostalCode","CountryCode","Gender","DateOfBirth","AltPhone","Email","SecurityPhrase","Comments"];

	// GET CUSTOM FIELDS OF LIST
	$astDB->where('list_id', $theList);
        $rsltv = $astDB->get('vicidial_lists_fields', null, 'field_label, field_name');

	if(!empty($rsltv)){

	foreach((is_array($rsltv) ? $rsltv : []) as $fresults){
		$getCF[] = $fresults['field_label'];
	}

	}else{
		$getCF[] = "";
	}

	if (($file = fopen($csv_file, "r")) !== FALSE) { //$handle = $file
		$getHeader = fgetcsv($file, 1000, $default_delimiter, escape: '\\');
		fclose($file);
		$apiresults = ["result" => "success", "data" => $getHeader, "standard_fields" => $getSF, "custom_fields" => $getCF];
	}else{
		$apiresults = ["result" => "csv read fail"];
	}

?>
