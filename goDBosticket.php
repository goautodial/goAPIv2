<?php

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

$linkost=mysqli_connect("$VARDBost_server", "$VARDBost_user", "$VARDBost_pass", "$VARDBost_database", "$VARDBost_port");
mysqli_query($linkost,"set character_set_results='utf8'");
if (!$linkost)
        {
    echo "Error: Unable to connect to MySQL goautodial." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
        }

$VARDBost_server   = (!isset($VARDBost_server)) ? "localhost" : $VARDBost_server;
$VARDBost_user     = (!isset($VARDBost_user)) ? "osticketu" : $VARDBost_user;
$VARDBost_pass     = (!isset($VARDBost_pass)) ? "osticket1234" : $VARDBost_pass;
$VARDBost_database = (!isset($VARDBost_database)) ? "osticketdb" : $VARDBost_database;
?>
