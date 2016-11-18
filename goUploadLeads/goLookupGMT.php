<?php

	####################################################
	#### Name: goLookupGMT.php                      ####
	#### Description: API for Uploading Leads       ####
	#### Version: 4                                 ####
	#### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
	#### Written by: Jerico James Milo              ####
	#### License: AGPLv2                            ####
	####################################################
	
	#ini_set('display_errors', 'on');
	#error_reporting(E_ALL);
	#ini_set('memory_limit','64M');
	#ini_set('upload_max_filesize', '200M');
	
	include_once('../goAgent/includes/MySQLiDB.php');
	@include_once('../goDBasterisk.php');
	@include_once('../goDBgoautodial.php');
	@include_once('../goFunctions.php');
	
	### Check if DB variables are not set ###
		$VARDB_server   = (!isset($VARDB_server)) ? "162.254.144.92" : $VARDB_server;
		$VARDB_user     = (!isset($VARDB_user)) ? "justgocloud" : $VARDB_user;
		$VARDB_pass     = (!isset($VARDB_pass)) ? "justgocloud1234" : $VARDB_pass;
		$VARDB_database = (!isset($VARDB_database)) ? "asterisk" : $VARDB_database;
		
		$VARDBgo_server   = (!isset($VARDBgo_server)) ? "162.254.144.92" : $VARDBgo_server;
		$VARDBgo_user     = (!isset($VARDBgo_user)) ? "goautodialu" : $VARDBgo_user;
		$VARDBgo_pass     = (!isset($VARDBgo_pass)) ? "pancit8888" : $VARDBgo_pass;
		$VARDBgo_database = (!isset($VARDBgo_database)) ? "goautodial" : $VARDBgo_database;
	### End of DB variables ###
	
		$goGMTastDB = new MySQLiDB($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database);
	

?>
