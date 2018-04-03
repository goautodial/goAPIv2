<?php
 /**
 * @file 		goLookupGMT.php
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

	include_once('../MySQLiDB.php');
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