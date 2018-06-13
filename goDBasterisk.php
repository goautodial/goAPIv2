<?php
/**
 * @file        goDBasterisk.php
 * @brief       Configurations to connect to asterisk DB
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Jerico James Flores Milo  <jericojames@goautodial.com>
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
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
include_once ("includes/MySQLiDB.php");

if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/astguiclient.conf")) {
	$conf_path = "{$_SERVER['DOCUMENT_ROOT']}/astguiclient.conf";
} else {
	die ("ERROR: 'astguiclient.conf' file not found.");
}

if ( file_exists($conf_path) )
    {
    $DBCagc = file($conf_path);
    foreach ($DBCagc as $DBCline)
        {
        $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
        if (preg_match("/^PATHlogs/", $DBCline))
                {$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",$PATHlogs);}
        if (preg_match("/^PATHweb/", $DBCline))
                {$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);}
         if (preg_match("/^PATHsounds/", $DBCline))
                {$path_sounds = $DBCline;   $path_sounds = preg_replace("/.*=/","",$path_sounds);}
        if (preg_match("/^VARserver_ip/", $DBCline))
                {$WEBserver_ip = $DBCline;   $WEBserver_ip = preg_replace("/.*=/","",$WEBserver_ip);}
        if (preg_match("/^VARDB_server/", $DBCline))
                {$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
        if (preg_match("/^VARDB_database/", $DBCline))
                {$VARDB_database = $DBCline;   $VARDB_database = preg_replace("/.*=/","",$VARDB_database);}
        if (preg_match("/^VARDB_user/", $DBCline))
                {$VARDB_user = $DBCline;   $VARDB_user = preg_replace("/.*=/","",$VARDB_user);}
        if (preg_match("/^VARDB_pass/", $DBCline))
                {$VARDB_pass = $DBCline;   $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);}
        if (preg_match("/^VARDB_port/", $DBCline))
                {$VARDB_port = $DBCline;   $VARDB_port = preg_replace("/.*=/","",$VARDB_port);}
        }
    }

$astDB = new MySQLiDB($VARDB_server, $VARDB_user, $VARDB_pass, $VARDB_database);

if (!$astDB)
    {
    echo "Error: Unable to connect to MySQL asterisk." . PHP_EOL;
    echo "Debugging Error: " . $astDB->getLastError() . PHP_EOL;
    exit;
    //die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
    }
?>
