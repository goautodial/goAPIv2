<?php
/**
 * @file        goDBosticket.php
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
        if (preg_match("/^VARDBost_server/", $DBCline))
                {$VARDBost_server = $DBCline;   $VARDBost_server = preg_replace("/.*=/","",$VARDBost_server);}
        if (preg_match("/^VARDBost_database/", $DBCline))
                {$VARDBost_database = $DBCline;   $VARDBost_database = preg_replace("/.*=/","",$VARDBost_database);}
        if (preg_match("/^VARDBost_user/", $DBCline))
                {$VARDBost_user = $DBCline;   $VARDBost_user = preg_replace("/.*=/","",$VARDBost_user);}
        if (preg_match("/^VARDBost_pass/", $DBCline))
                {$VARDBost_pass = $DBCline;   $VARDBost_pass = preg_replace("/.*=/","",$VARDBost_pass);}
        if (preg_match("/^VARDBost_port/", $DBCline))
                {$VARDBost_port = $DBCline;   $VARDBost_port = preg_replace("/.*=/","",$VARDBost_port);}
        }
    }

$osticketDB = new MySQLiDB($VARDBost_server, $VARDBost_user, $VARDBost_pass, $VARDBost_database);

if (!$osticketDB)
    {
    echo "Error: Unable to connect to MySQL osticket_db." . PHP_EOL;
    echo "Debugging Error: " . $osticketDB->getLastError() . PHP_EOL;
    exit;
    //die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
    }
?>
