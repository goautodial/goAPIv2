<?php

if (file_exists("/etc/goautodial.conf")) {
	$conf_path = "/etc/goautodial.conf";
} elseif (file_exists("{$_SERVER['DOCUMENT_ROOT']}/goautodial.conf")) {
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
          		     if (ereg("^VARSERVTYPE", $DBCline))
                        {$VARSERVTYPE = $DBCline;   $VARSERVTYPE = preg_replace("/.*=/","",$VARSERVTYPE);}
					 if (ereg("^VARSIPPYAPIUSER", $DBCline))
                        {$VARSIPPYAPIUSER = $DBCline;   $VARSIPPYAPIUSER = preg_replace("/.*=/","",$VARSIPPYAPIUSER);}
                     if (ereg("^VARSIPPYAPIPASS", $DBCline))
                        {$VARSIPPYAPIPASS = $DBCline;   $VARSIPPYAPIPASS = preg_replace("/.*=/","",$VARSIPPYAPIPASS);}                        
                     if (ereg("^VARSIPPYXMLRPCINCFILE", $DBCline))
                        {$VARSIPPYXMLRPCINCFILE = $DBCline;   $VARSIPPYXMLRPCINCFILE = preg_replace("/.*=/","",$VARSIPPYXMLRPCINCFILE);}                        
                     if (ereg("^VARSIPPYXMLRPCCLIENT", $DBCline))
                        {$VARSIPPYXMLRPCCLIENT = $DBCline;   $VARSIPPYXMLRPCCLIENT = preg_replace("/.*=/","",$VARSIPPYXMLRPCCLIENT);}                        
                     if (ereg("^VARSIPPYXMLRPCHTMLFILE", $DBCline))
                        {$VARSIPPYXMLRPCHTMLFILE = $DBCline;   $VARSIPPYXMLRPCHTMLFILE = preg_replace("/.*=/","",$VARSIPPYXMLRPCHTMLFILE);}                                 
                     if (ereg("^VARCLOUDCOMPANY", $DBCline))
                        {$VARCLOUDCOMPANY = $DBCline;   $VARCLOUDCOMPANY = preg_replace("/.*=/","",$VARCLOUDCOMPANY);}                                 
                     if (ereg("^VARLOGINLOGO", $DBCline))
                        {$VARLOGINLOGO = $DBCline;   $VARLOGINLOGO = preg_replace("/.*=/","",$VARLOGINLOGO);}
                     if (ereg("^VARCLOUDURL", $DBCline))
                        {$VARCLOUDURL = $DBCline;   $VARCLOUDURL = preg_replace("/.*=/","",$VARCLOUDURL);}
                     if (ereg("^VARSIPPYPASSRECOVERYSUPPORTEMAIL", $DBCline))
                        {$VARSIPPYPASSRECOVERYSUPPORTEMAIL = $DBCline;   $VARSIPPYPASSRECOVERYSUPPORTEMAIL = preg_replace("/.*=/","",$VARSIPPYPASSRECOVERYSUPPORTEMAIL);}   
                     if (ereg("^VARSHOWSIGNUP", $DBCline))
                        {$VARSHOWSIGNUP = $DBCline;   $VARSHOWSIGNUP = preg_replace("/.*=/","",$VARSHOWSIGNUP);}
                }
        }


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
                if (ereg("^VARDB_server", $DBCline))
                        {$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
                if (ereg("^VARDB_database", $DBCline))
                        {$VARDB_database = $DBCline;   $VARDB_database = preg_replace("/.*=/","",$VARDB_database);}
                if (ereg("^VARDB_user", $DBCline))
                        {$VARDB_user = $DBCline;   $VARDB_user = preg_replace("/.*=/","",$VARDB_user);}
                if (ereg("^VARDB_pass", $DBCline))
                        {$VARDB_pass = $DBCline;   $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);}
                if (ereg("^VARDB_port", $DBCline))
                        {$VARDB_port = $DBCline;   $VARDB_port = preg_replace("/.*=/","",$VARDB_port);}
                }
        }

$link=mysqli_connect("$VARDB_server:$VARDB_port", "$VARDB_user", "$VARDB_pass");
if (!$link)
        {
    die('MySQL connect ERROR: ' . mysqli_error());
        }
mysqli_select_db("$VARDB_database");
?>
