<?php

	####################################################
	#### Name: goUploadMe.php                       ####
	#### Description: API for Uploading Leads       ####
	#### Version: 4                                 ####
	#### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
	#### Written by: Jerico James Milo              ####
	#### License: AGPLv2                            ####
	####################################################
	
	ini_set('memory_limit','1024M');
	ini_set('upload_max_filesize', '6000M');
	ini_set('post_max_size', '6000M');
	
	#ini_set('display_errors', 'on');
    #error_reporting(E_ALL);
	
	include_once("../goFunctions.php");
	include_once("goLookupGMT.php");
	
	$theList = $_REQUEST["goListId"];
	$goDupcheck = $_REQUEST["goDupcheck"];
	$jsonData = json_decode($_REQUEST['jsonData'], true);
	$goCountInsertedLeads = 0;
	echo "<pre>";
	print_r($jsonData);die;
	$log_user = mysqli_real_escape_string($link, $_REQUEST['log_user']);
	$log_group = mysqli_real_escape_string($link, $_REQUEST['log_group']);
	$ip_address = mysqli_real_escape_string($link, $_REQUEST['hostname']);
	$apiresults = array("result" => "success", "data" => $jsonData['list_id']);

	// $list = $jsonData['list_id'];
	// $leads = $jsonData['list_id'];
	// foreach(){

	// }
	
?>
