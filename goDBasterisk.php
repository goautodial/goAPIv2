<?php
if (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
	$conf_path = "{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf";
} else {
	die ("ERROR: 'goautodial.conf' file not found.");
}
if ( file_exists($conf_path) )
        {
        $DBCagc = file($conf_path);
        foreach ($DBCagc as $DBCline)
                {
                $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
          		     if (preg_match("/^VARSERVTYPE/", $DBCline))
                        {$VARSERVTYPE = $DBCline;   $VARSERVTYPE = preg_replace("/.*=/","",$VARSERVTYPE);}
					 if (preg_match("/^VARSIPPYAPIUSER/", $DBCline))
                        {$VARSIPPYAPIUSER = $DBCline;   $VARSIPPYAPIUSER = preg_replace("/.*=/","",$VARSIPPYAPIUSER);}
                     if (preg_match("/^VARSIPPYAPIPASS/", $DBCline))
                        {$VARSIPPYAPIPASS = $DBCline;   $VARSIPPYAPIPASS = preg_replace("/.*=/","",$VARSIPPYAPIPASS);}                        
                     if (preg_match("/^VARSIPPYXMLRPCINCFILE/", $DBCline))
                        {$VARSIPPYXMLRPCINCFILE = $DBCline;   $VARSIPPYXMLRPCINCFILE = preg_replace("/.*=/","",$VARSIPPYXMLRPCINCFILE);}                        
                     if (preg_match("/^VARSIPPYXMLRPCCLIENT/", $DBCline))
                        {$VARSIPPYXMLRPCCLIENT = $DBCline;   $VARSIPPYXMLRPCCLIENT = preg_replace("/.*=/","",$VARSIPPYXMLRPCCLIENT);}                        
                     if (preg_match("/^VARSIPPYXMLRPCHTMLFILE/", $DBCline))
                        {$VARSIPPYXMLRPCHTMLFILE = $DBCline;   $VARSIPPYXMLRPCHTMLFILE = preg_replace("/.*=/","",$VARSIPPYXMLRPCHTMLFILE);}                                 
                     if (preg_match("/^VARCLOUDCOMPANY/", $DBCline))
                        {$VARCLOUDCOMPANY = $DBCline;   $VARCLOUDCOMPANY = preg_replace("/.*=/","",$VARCLOUDCOMPANY);}                                 
                     if (preg_match("/^VARLOGINLOGO/", $DBCline))
                        {$VARLOGINLOGO = $DBCline;   $VARLOGINLOGO = preg_replace("/.*=/","",$VARLOGINLOGO);}
                     if (preg_match("/^VARCLOUDURL/", $DBCline))
                        {$VARCLOUDURL = $DBCline;   $VARCLOUDURL = preg_replace("/.*=/","",$VARCLOUDURL);}
                     if (preg_match("/^VARSIPPYPASSRECOVERYSUPPORTEMAIL/", $DBCline))
                        {$VARSIPPYPASSRECOVERYSUPPORTEMAIL = $DBCline;   $VARSIPPYPASSRECOVERYSUPPORTEMAIL = preg_replace("/.*=/","",$VARSIPPYPASSRECOVERYSUPPORTEMAIL);}   
                     if (preg_match("/^VARSHOWSIGNUP/", $DBCline))
                        {$VARSHOWSIGNUP = $DBCline;   $VARSHOWSIGNUP = preg_replace("/.*=/","",$VARSHOWSIGNUP);}
                }
        }


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

$link=mysqli_connect("$VARDB_server", "$VARDB_user", "$VARDB_pass", "$VARDB_database", "$VARDB_port");
mysqli_query($link,"set character_set_results='utf8'");
//print_r($VARDB_server."/".$VARDB_user."/".$VARDB_pass."/".$VARDB_port);
//die;
//$link=mysqli_connect("162.254.144.92", "justgocloud", "justgocloud1234", "asterisk");
if (!$link)
        {
    echo "Error: Unable to connect to MySQL asterisk." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
    //die('MySQL connect ERROR: ' . mysqli_error('mysqli'));
        }
//mysqli_select_db("$VARDB_database");
?>
