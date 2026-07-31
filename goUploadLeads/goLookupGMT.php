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

	include_once(__DIR__ . '/../MySQLiDB.php');
	@include_once(__DIR__ . '/../goDBasterisk.php');
	@include_once(__DIR__ . '/../goDBgoautodial.php');
	@include_once(__DIR__ . '/../goFunctions.php');
	
	### Check if DB variables are not set ###
		$VARDB_server ??= "162.254.144.92";
		$VARDB_user ??= "justgocloud";
		$VARDB_pass ??= "justgocloud1234";
		$VARDB_database ??= "asterisk";
		
		$VARDBgo_server ??= "162.254.144.92";
		$VARDBgo_user ??= "goautodialu";
		$VARDBgo_pass ??= "pancit8888";
		$VARDBgo_database ??= "goautodial";
	### End of DB variables ###
	
	$goGMTastDB = new MySQLiDB($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database);
?>