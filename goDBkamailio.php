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
                        
                if (preg_match("/^VARDBgokam_server/", $DBCline))
                        {$VARDBgokam_server = $DBCline;   $VARDBgokam_server = preg_replace("/.*=/","",$VARDBgokam_server);}
                if (preg_match("/^VARDBgokam_database/", $DBCline))
                        {$VARDBgokam_database = $DBCline;   $VARDBgokam_database = preg_replace("/.*=/","",$VARDBgokam_database);}
                if (preg_match("/^VARDBgokam_user/", $DBCline))
                        {$VARDBgokam_user = $DBCline;   $VARDBgokam_user = preg_replace("/.*=/","",$VARDBgokam_user);}
                if (preg_match("/^VARDBgokam_pass/", $DBCline))
                        {$VARDBgokam_pass = $DBCline;   $VARDBgokam_pass = preg_replace("/.*=/","",$VARDBgokam_pass);}
                if (preg_match("/^VARDBgokam_port/", $DBCline))
                        {$VARDBgokam_port = $DBCline;   $VARDBgokam_port = preg_replace("/.*=/","",$VARDBgokam_port);}
                }
        }

$linkgokam=mysqli_connect("$VARDBgokam_server", "$VARDBgokam_user", "$VARDBgokam_pass", "$VARDBgokam_database", "$VARDBgokam_port");
mysqli_query($linkgokam,"set character_set_results='utf8'");
//print_r($VARDBgo_server."/".$VARDBgo_user."/".$VARDBgo_pass."/".$VARDBgo_port);
//die;
//$linkgo=mysqli_connect("162.254.144.92", "goautodialu", "pancit8888", "goautodial");
if (!$linkgokam)
        {
//    die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
    echo "Error: Unable to connect to MySQL goautodial." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
        }
//mysqli_select_db("$VARDBgo_database");

$VARDBgokam_server   = (!isset($VARDBgokam_server)) ? "localhost" : $VARDBgokam_server;
$VARDBgokam_user     = (!isset($VARDBgokam_user)) ? "kamailio" : $VARDBgokam_user;
$VARDBgokam_pass     = (!isset($VARDBgokam_pass)) ? "kamailiorw" : $VARDBgokam_pass;
$VARDBgokam_database = (!isset($VARDBgokam_database)) ? "kamailio" : $VARDBgokam_database;
?>
