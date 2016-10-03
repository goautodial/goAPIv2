<?php

if (file_exists("/etc/astguiclient.conf")) {
	$conf_path = "/etc/astguiclient.conf";
} elseif (file_exists("{$_SERVER['DOCUMENT_ROOT']}/astguiclient.conf")) {
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
                if (ereg("^PATHlogs", $DBCline))
                        {$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",$PATHlogs);}
                if (ereg("^PATHweb", $DBCline))
                        {$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);}
                if (ereg("^VARserver_ip", $DBCline))
                        {$WEBserver_ip = $DBCline;   $WEBserver_ip = preg_replace("/.*=/","",$WEBserver_ip);}
                        
                if (ereg("^VARDBgo_server", $DBCline))
                        {$VARDBgo_server = $DBCline;   $VARDBgo_server = preg_replace("/.*=/","",$VARDBgo_server);}
                if (ereg("^VARDBgo_database", $DBCline))
                        {$VARDBgo_database = $DBCline;   $VARDBgo_database = preg_replace("/.*=/","",$VARDBgo_database);}
                if (ereg("^VARDBgo_user", $DBCline))
                        {$VARDBgo_user = $DBCline;   $VARDBgo_user = preg_replace("/.*=/","",$VARDBgo_user);}
                if (ereg("^VARDBgo_pass", $DBCline))
                        {$VARDBgo_pass = $DBCline;   $VARDBgo_pass = preg_replace("/.*=/","",$VARDBgo_pass);}
                if (ereg("^VARDBgo_port", $DBCline))
                        {$VARDBgo_port = $DBCline;   $VARDBgo_port = preg_replace("/.*=/","",$VARDBgo_port);}
                }
        }

$linkgo=mysqli_connect("$VARDBgo_server:$VARDBgo_port", "$VARDBgo_user", "$VARDBgo_pass");
if (!$linkgo)
        {
    die('MySQL connect ERROR: ' . mysqli_error());
        }
mysqli_select_db("$VARDBgo_database");
?>
