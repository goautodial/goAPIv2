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
                        
                if (preg_match("/^VARDBgo_server/", $DBCline))
                        {$VARDBgo_server = $DBCline;   $VARDBgo_server = preg_replace("/.*=/","",$VARDBgo_server);}
                if (preg_match("/^VARDBgo_database/", $DBCline))
                        {$VARDBgo_database = $DBCline;   $VARDBgo_database = preg_replace("/.*=/","",$VARDBgo_database);}
                if (preg_match("/^VARDBgo_user/", $DBCline))
                        {$VARDBgo_user = $DBCline;   $VARDBgo_user = preg_replace("/.*=/","",$VARDBgo_user);}
                if (preg_match("/^VARDBgo_pass/", $DBCline))
                        {$VARDBgo_pass = $DBCline;   $VARDBgo_pass = preg_replace("/.*=/","",$VARDBgo_pass);}
                if (preg_match("/^VARDBgo_port/", $DBCline))
                        {$VARDBgo_port = $DBCline;   $VARDBgo_port = preg_replace("/.*=/","",$VARDBgo_port);}
                }
        }

$linkgo=mysqli_connect("$VARDBgo_server", "$VARDBgo_user", "$VARDBgo_pass", "$VARDBgo_database", "$VARDBgo_port");
//print_r($VARDBgo_server."/".$VARDBgo_user."/".$VARDBgo_pass."/".$VARDBgo_port);
//die;
//$linkgo=mysqli_connect("162.254.144.92", "goautodialu", "pancit8888", "goautodial");
if (!$linkgo)
        {
//    die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
    echo "Error: Unable to connect to MySQL goautodial." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
        }
//mysqli_select_db("$VARDBgo_database");
?>
