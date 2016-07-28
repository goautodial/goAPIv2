<?php
 ####################################################
 #### Name: goGetDashboardALL.php                ####
 #### Description: API for dashboard php encode  ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Ltd. (c) 2011-2015   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################


	 $url = "https://jameshv.goautodial.com/goAPI/goDashboard/goAPI.php"; # URL to GoAutoDial API file
	 $username = "goautodial";
	 $password = "JUs7g0P455W0rD11214";
	 #$username = "2012107124"; # Admin/Tenant/Non-Tenant username goes here
	 #$password = "liSB92qd"; # Admin/Tenant/Non-Tenant password goes here
 



############# Total Answered Calls ######################

	 $postfields["goUser"] = $username;
	 $postfields["goPass"] = $password;
	 $postfields["goAction"] = "goGetTotalAnsweredCalls"; #action performed by the [[API:Functions]]
	 

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	 $data = curl_exec($ch);
	 curl_close($ch);
 	
 
	 $data = explode(";",$data);
	 foreach ($data AS $temp) {
	   $temp = explode("=",$temp);
	   $results[$temp[0]] = $temp[1];
 	}
 
	 if ($results["result"]=="success") {
	#	echo "Answered Calls: ".$results['answers_today'];   # Result was OK!
	 } else {
	   # An error occured
		   echo "The following error occured: ".$results["message"];
 		}


############# Total Dropped Calls ######################

         $droppedcalls = "goGetTotalDroppedCalls";
         define('POSTVARS', 'goUser='.$username.'&goPass='.$password.'&goAction='.$droppedcalls);
         $ch1 = curl_init();
         curl_setopt($ch1, CURLOPT_URL, $url);
         curl_setopt($ch1, CURLOPT_POST, 1);
         curl_setopt($ch1, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch1, CURLOPT_POSTFIELDS, POSTVARS);
         $data1 = curl_exec($ch1);
         curl_close($ch1);
        
         $data1 = explode(";",$data1);
         foreach ($data1 AS $temp1) {
           $temp1 = explode("=",$temp1);
           $results1[$temp1[0]] = $temp1[1];
        }
 
         if ($results1["result"]=="success") {
         } else {
           # An error occured
                   echo "The following error occured: ".$results1["message"];
                }

############# Dropped Percentage ######################

         $percentage["goUser"] = $username;         
         $percentage["goPass"] = $password;
         $percentage["goAction"] = "goGetDroppedPercentage"; #action performed by the [[API:Functions]]

         $ch2 = curl_init();
         curl_setopt($ch2, CURLOPT_URL, $url);
         curl_setopt($ch2, CURLOPT_POST, 1);
         curl_setopt($ch2, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch2, CURLOPT_POSTFIELDS, $percentage);
         $data2 = curl_exec($ch2);
         curl_close($ch2);

         $data2 = explode(";",$data2);
         foreach ($data2 AS $temp2) {
           $temp2 = explode("=",$temp2);
           $results2[$temp2[0]] = $temp2[1];
        }

         if ($results2["result"]=="success") {
        //echo "<br />"."Dropped calls percentage: ".$results2['drop_call_per'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results2["message"];
                }
         
############# Leads in Hopper ######################

         $leadshopper["goUser"] = $username;
         $leadshopper["goPass"] = $password;
         $leadshopper["goAction"] = "goGetLeadsinHopper"; #action performed by the [[API:Functions]]

         $ch3 = curl_init();
         curl_setopt($ch3, CURLOPT_URL, $url);
         curl_setopt($ch3, CURLOPT_POST, 1);
         curl_setopt($ch3, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch3, CURLOPT_POSTFIELDS, $leadshopper);
         $data3 = curl_exec($ch3);
         curl_close($ch3);

         $data3 = explode(";",$data3);
         foreach ($data3 AS $temp3) {
           $temp3 = explode("=",$temp3);
           $results3[$temp3[0]] = $temp3[1];
        }

         if ($results3["result"]=="success") {
        //echo "<br />"."Leads in Hopper: ".$results3['getLeadsinHopper'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results3["message"];
                }

############# Dialable Leads ######################
         
         $dialableleads["goUser"] = $username;
         $dialableleads["goPass"] = $password;
         $dialableleads["goAction"] = "goGetTotalDialableLeads"; #action performed by the [[API:Functions]]


         $ch4 = curl_init();
         curl_setopt($ch4, CURLOPT_URL, $url);
         curl_setopt($ch4, CURLOPT_POST, 1);
         curl_setopt($ch4, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch4, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch4, CURLOPT_POSTFIELDS, $dialableleads);
         $data4 = curl_exec($ch4);
         curl_close($ch4);

         $data4 = explode(";",$data4);
         foreach ($data4 AS $temp4) {
           $temp4 = explode("=",$temp4);
           $results4[$temp4[0]] = $temp4[1];
        }

         if ($results4["result"]=="success") {
        //echo "<br />"."Dialable Leads: ".$results4['getTotalDialableLeads'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results4["message"];
                }

############# Total Active Leads ######################         

         $activeleads["goUser"] = $username;
         $activeleads["goPass"] = $password;
         $activeleads["goAction"] = "goGetTotalActiveLeads"; #action performed by the [[API:Functions]]


         $ch5 = curl_init();
         curl_setopt($ch5, CURLOPT_URL, $url);
         curl_setopt($ch5, CURLOPT_POST, 1);
         curl_setopt($ch5, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch5, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch5, CURLOPT_POSTFIELDS, $activeleads);
         $data5 = curl_exec($ch5);
         curl_close($ch5);

         $data5 = explode(";",$data5);
         foreach ($data5 AS $temp5) {
           $temp5 = explode("=",$temp5);
           $results5[$temp5[0]] = $temp5[1];
        }

         if ($results5["result"]=="success") {
        //echo "<br />"."Active Leads: ".$results5['getTotalActiveLeads'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results5["message"];
                }

############# Total Sales ######################

         $totalsales["goUser"] = $username;
         $totalsales["goPass"] = $password;
         $totalsales["goAction"] = "goGetTotalSales"; #action performed by the [[API:Functions]]


         $ch6 = curl_init();
         curl_setopt($ch6, CURLOPT_URL, $url);
         curl_setopt($ch6, CURLOPT_POST, 1);
         curl_setopt($ch6, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch6, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch6, CURLOPT_POSTFIELDS, $totalsales);
         $data6 = curl_exec($ch6);
         curl_close($ch6);


         $data6 = explode(";",$data6);
         foreach ($data6 AS $temp6) {
           $temp6 = explode("=",$temp6);
           $results6[$temp6[0]] = $temp6[1];
        }

         if ($results6["result"]=="success") {
        //echo "<br />"."Total Sales: ".$results6['TotalSales'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results6["message"];
                }

############# Total Inbound Sales ######################

         $inboundsales["goUser"] = $username;
         $inboundsales["goPass"] = $password;
         $inboundsales["goAction"] = "goGetTotalInboundSales"; #action performed by the [[API:Functions]]


         $ch7 = curl_init();
         curl_setopt($ch7, CURLOPT_URL, $url);
         curl_setopt($ch7, CURLOPT_POST, 1);
         curl_setopt($ch7, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch7, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch7, CURLOPT_POSTFIELDS, $inboundsales);
         $data7 = curl_exec($ch7);
         curl_close($ch7);


         $data7 = explode(";",$data7);
         foreach ($data7 AS $temp7) {
           $temp7 = explode("=",$temp7);
           $results7[$temp7[0]] = $temp7[1];
        }

         if ($results7["result"]=="success") {
        //echo "<br />"."Inbound Sales ".$results7['InboundSales'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results7["message"];
                }

############# Total Outbound Sales ######################

         $outboundsales["goUser"] = $username;
         $outboundsales["goPass"] = $password;
         $outboundsales["goAction"] = "goGetTotalOutboundSales"; #action performed by the [[API:Functions]]


         $ch8 = curl_init();
         curl_setopt($ch8, CURLOPT_URL, $url);
         curl_setopt($ch8, CURLOPT_POST, 1);
         curl_setopt($ch8, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch8, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch8, CURLOPT_POSTFIELDS, $outboundsales);
         $data8 = curl_exec($ch8);
         curl_close($ch8);


         $data8 = explode(";",$data8);
         foreach ($data8 AS $temp8) {
           $temp8 = explode("=",$temp8);
           $results8[$temp8[0]] = $temp8[1];
        }

         if ($results8["result"]=="success") {
        //echo "<br />"."Outbound Sales: ".$results8['OutboundSales'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results8["message"];
                }

############# IN Sales/Hour ######################

         $insales["goUser"] = $username;
         $insales["goPass"] = $password;
         $insales["goAction"] = "goGetINSalesHour"; #action performed by the [[API:Functions]]


         $ch9 = curl_init();
         curl_setopt($ch9, CURLOPT_URL, $url);
         curl_setopt($ch9, CURLOPT_POST, 1);
         curl_setopt($ch9, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch9, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch9, CURLOPT_POSTFIELDS, $insales);
         $data9 = curl_exec($ch9);
         curl_close($ch9);


         $data9 = explode(";",$data9);
         foreach ($data9 AS $temp9) {
           $temp9 = explode("=",$temp9);
           $results9[$temp9[0]] = $temp9[1];
        }

         if ($results9["result"]=="success") {
        //echo "<br />"."IN Sales / Hour: ".$results9['getInSalesPerHour'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results9["message"];
                }

############# OUT Sales/Hour ######################

         $outsales["goUser"] = $username;
         $outsales["goPass"] = $password;
         $outsales["goAction"] = "goGetOutSalesPerHour"; #action performed by the [[API:Functions]]


         $ch11 = curl_init();
         curl_setopt($ch11, CURLOPT_URL, $url);
         curl_setopt($ch11, CURLOPT_POST, 1);
         curl_setopt($ch11, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch11, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch11, CURLOPT_POSTFIELDS, $outsales);
         $data11 = curl_exec($ch11);
         curl_close($ch11);

         $data11 = explode(";",$data11);
         foreach ($data11 AS $temp11) {
           $temp11 = explode("=",$temp11);
           $results11[$temp11[0]] = $temp11[1];
        }

         if ($results11["result"]=="success") {
        //echo "<br />"."OUT Sales / Hour: ".$results11['OutSalesPerHour'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results11["message"];
                }

############# Total Agents Online ######################

         $agentsonline["goUser"] = $username;
         $agentsonline["goPass"] = $password;
         $agentsonline["goAction"] = "goGetAgentsOnline"; #action performed by the [[API:Functions]]


         $ch14 = curl_init();
         curl_setopt($ch14, CURLOPT_URL, $url);
         curl_setopt($ch14, CURLOPT_POST, 1);
         curl_setopt($ch14, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch14, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch14, CURLOPT_POSTFIELDS, $agentsonline);
         $data14 = curl_exec($ch14);
         curl_close($ch14);

         $data14 = explode(";",$data14);
         foreach ($data14 AS $temp14) {
           $temp14 = explode("=",$temp14);
           $results14[$temp14[0]] = $temp14[1];
        }

         if ($results14["result"]=="success") {
        //echo "<br />"."Agent(s) online: ".$results14['TotalAgentsOnline'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results14["message"];
                }

############# Agent(s) Waiting ######################

         $agentswait["goUser"] = $username;
         $agentswait["goPass"] = $password;
         $agentswait["goAction"] = "goGetTotalAgentsWaitCalls"; #action performed by the [[API:Functions]]


         $ch13 = curl_init();
         curl_setopt($ch13, CURLOPT_URL, $url);
         curl_setopt($ch13, CURLOPT_POST, 1);
         curl_setopt($ch13, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch13, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch13, CURLOPT_POSTFIELDS, $agentswait);
         $data13 = curl_exec($ch13);
         curl_close($ch13);

        //  var_dump($data);

         $data13 = explode(";",$data13);
         foreach ($data13 AS $temp13) {
           $temp13 = explode("=",$temp13);
           $results13[$temp13[0]] = $temp13[1];
        }

         if ($results13["result"]=="success") {
       // echo "<br />"."Agent(s) on wait: ".$results13['TotalAgentsWaitCalls'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results13["message"];
                }

############# Agent(s) on Paused ######################

         $agentspaused["goUser"] = $username;
         $agentspaused["goPass"] = $password;
         $agentspaused["goAction"] = "goGetTotalAgentsPaused"; #action performed by the [[API:Functions]]


         $ch12 = curl_init();
         curl_setopt($ch12, CURLOPT_URL, $url);
         curl_setopt($ch12, CURLOPT_POST, 1);
         curl_setopt($ch12, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch12, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch12, CURLOPT_POSTFIELDS, $agentspaused);
         $data12 = curl_exec($ch12);
         curl_close($ch12);


         $data12 = explode(";",$data12);
         foreach ($data12 AS $temp12) {
           $temp12 = explode("=",$temp12);
           $results12[$temp12[0]] = $temp12[1];
        }

         if ($results12["result"]=="success") {
        //echo "<br />"."Agent(s) on Paused: ".$results12['TotalAgentsPaused'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results12["message"];
                }

############# Agent(s) on Call ######################

         $agentscall["goUser"] = $username;
         $agentscall["goPass"] = $password;
         $agentscall["goAction"] = "goGetTotalAgentsCall"; #action performed by the [[API:Functions]]


         $ch10 = curl_init();
         curl_setopt($ch10, CURLOPT_URL, $url);
         curl_setopt($ch10, CURLOPT_POST, 1);
         curl_setopt($ch10, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch10, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch10, CURLOPT_POSTFIELDS, $agentscall);
         $data10 = curl_exec($ch10);
         curl_close($ch10);

        //  var_dump($data);

         $data10 = explode(";",$data10);
         foreach ($data10 AS $temp10) {
           $temp10 = explode("=",$temp10);
           $results10[$temp10[0]] = $temp10[1];
        }

         if ($results10["result"]=="success") {
        //echo "<br />"."Agent(s) on Call: ".$results10['TotalAgentsCall'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results10["message"];
                }

############# Call(s) Ringing ######################

         $callring["goUser"] = $username;
         $callring["goPass"] = $password;
         $callring["goAction"] = "goGetRingingCall"; #action performed by the [[API:Functions]]


         $ch14 = curl_init();
         curl_setopt($ch14, CURLOPT_URL, $url);
         curl_setopt($ch14, CURLOPT_POST, 1);
         curl_setopt($ch14, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch14, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch14, CURLOPT_POSTFIELDS, $callring);
         $data14 = curl_exec($ch14);
         curl_close($ch14);


         $data14 = explode(";",$data14);
         foreach ($data14 AS $temp14) {
           $temp14 = explode("=",$temp14);
           $results14[$temp14[0]] = $temp14[1];
        }

 
        if ($results14["result"]=="success") {
        //echo "<br />"."Call(s) Ringing: ".$results14['ringing'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results14["message"];
                }

############# Call(s) in Incoming Queue ######################

         $incomingqueue["goUser"] = $username;
         $incomingqueue["goPass"] = $password;
         $incomingqueue["goAction"] = "goGetIncomingQueue"; #action performed by the [[API:Functions]]


         $ch15 = curl_init();
         curl_setopt($ch15, CURLOPT_URL, $url);
         curl_setopt($ch15, CURLOPT_POST, 1);
         curl_setopt($ch15, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch15, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch15, CURLOPT_POSTFIELDS, $incomingqueue);
         $data15 = curl_exec($ch15);
         curl_close($ch15);

        //  var_dump($data);

         $data15 = explode(";",$data15);
         foreach ($data15 AS $temp15) {
           $temp15 = explode("=",$temp15);
           $results15[$temp15[0]] = $temp15[1];
        }

         if ($results15["result"]=="success") {
        //echo "<br />"."Call(s) in Incoming Queue: ".$results15['queue'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results15["message"];
                }


############# Live Inbound ######################

         $liveinbound["goUser"] = $username;
         $liveinbound["goPass"] = $password;
         $liveinbound["goAction"] = "goGetLiveInbound"; #action performed by the [[API:Functions]]


         $ch16 = curl_init();
         curl_setopt($ch16, CURLOPT_URL, $url);
         curl_setopt($ch16, CURLOPT_POST, 1);
         curl_setopt($ch16, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch16, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch16, CURLOPT_POSTFIELDS, $liveinbound);
         $data16 = curl_exec($ch16);
         curl_close($ch16);

        //  var_dump($data);

         $data16 = explode(";",$data16);
         foreach ($data16 AS $temp16) {
           $temp16 = explode("=",$temp16);
           $results16[$temp16[0]] = $temp16[1];
        }

         if ($results16["result"]=="success") {
        //echo "<br />"."Live Inbound: ".$results16['inbound'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results16["message"];
                }

############# Live Outbound ######################

         $liveoutbound["goUser"] = $username;
         $liveoutbound["goPass"] = $password;
         $liveoutbound["goAction"] = "goGetLiveOutbound"; #action performed by the [[API:Functions]]


         $ch17 = curl_init();
         curl_setopt($ch17, CURLOPT_URL, $url);
         curl_setopt($ch17, CURLOPT_POST, 1);
         curl_setopt($ch17, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch17, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch17, CURLOPT_POSTFIELDS, $liveoutbound);
         $data17 = curl_exec($ch17);
         curl_close($ch17);


         $data17 = explode(";",$data17);
         foreach ($data17 AS $temp17) {
           $temp17 = explode("=",$temp17);
           $results17[$temp17[0]] = $temp17[1];
        }

         if ($results17["result"]=="success") {
        //echo "<br />"."Live Outbound: ".$results17['outbound'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results17["message"];
                }


############# Total Calls ######################

         $totalcalls["goUser"] = $username;
         $totalcalls["goPass"] = $password;
         $totalcalls["goAction"] = "goGetTotalCalls"; #action performed by the [[API:Functions]]


         $ch18 = curl_init();
         curl_setopt($ch18, CURLOPT_URL, $url);
         curl_setopt($ch18, CURLOPT_POST, 1);
         curl_setopt($ch18, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch18, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch18, CURLOPT_POSTFIELDS, $totalcalls);
         $data18 = curl_exec($ch18);
         curl_close($ch18);

        //  var_dump($data);

         $data18 = explode(";",$data18);
         foreach ($data18 AS $temp18) {
           $temp18 = explode("=",$temp18);
           $results18[$temp18[0]] = $temp18[1];
        }

         if ($results18["result"]=="success") {
        //echo "<br />"."Total Calls: ".$results18['calls_today'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results18["message"];
                }

############# Number of Agents ######################

         $numberagents["goUser"] = $username;
         $numberagents["goPass"] = $password;
         $numberagents["goAction"] = "goGetTotalNumberOfAgents"; #action performed by the [[API:Functions]]


         $ch20 = curl_init();
         curl_setopt($ch20, CURLOPT_URL, $url);
         curl_setopt($ch20, CURLOPT_POST, 1);
         curl_setopt($ch20, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch20, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch20, CURLOPT_POSTFIELDS, $numberagents);
         $data20 = curl_exec($ch20);
         curl_close($ch20);


         $data20 = explode(";",$data20);
         foreach ($data20 AS $temp20) {
           $temp20 = explode("=",$temp20);
           $results20[$temp20[0]] = $temp20[1];
        }

         if ($results20["result"]=="success") {
        //echo "<br />"."Live Outbound: ".$results17['outbound'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results20["message"];
                }

?>


<!-- Dashboard View -->

<h3>Sales</h3>
<hr /> 
<table><tr><td>
<p style="font-size:30px;color:#464646;"> <?php echo $results6['TotalSales'];  ?>
   </p></td><td> <p><b>&nbsp;&nbsp;&nbsp;Total Sales</b> </p></td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results7['InboundSales'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Inbound Sales</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results8['OutboundSales'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Outbound Sales</td><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results9['getInSalesPerHour'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;IN Sales / Hour</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results11['OutSalesPerHour'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;OUT Sales / Hour</td></tr>
</table>

<h3>Calls</h3>
<hr />
<table><tr><td>
<p style="font-size:30px;color:#464646;"><?php  echo $results14['ringing'];  ?> <div class="callstoday" id="callstoday"> </div>  </p></td><td><b>&nbsp;&nbsp;&nbsp;Call(s) Ringing</b></td></tr>
<tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results15['queue'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Call(s) in Incoming Queue</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results16['inbound']; ?></p></td><td>&nbsp;&nbsp;&nbsp;Live Inbound</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results17['outbound']; ?></p></td><td>&nbsp;&nbsp;&nbsp;Live Outbound</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results18['calls_today']; ?></p></td><td>&nbsp;&nbsp;&nbsp;Total Calls</td></tr>
</table>

<h3>Dropped Call Percentage</h3>
<hr /><table><tr><td>
<p style="font-size:30px;color:#464646;"> <?php echo $results2['drop_call_per']; ?></p></td><td><b>%&nbsp;&nbsp;&nbsp;Dropped Percentage</b></td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results1['drops_today']; ?></p></td><td>&nbsp;&nbsp;&nbsp;Dropped Calls</td></tr>
<tr><td><p style="font-size:15px;color:#464646;float:right;"> <?php echo $results['answers_today']; ?> </p></td><td>&nbsp;&nbsp;&nbsp;Answered Calls</td></tr></table>

<h3>Agents Resources</h3>
<hr /><table><tr><td>
<p style="font-size:30px;color:#464646;"> <?php echo $results10['TotalAgentsCall']; ?></p></td><td><b>&nbsp;&nbsp;&nbsp;Agent(s) on Call</b></td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results12['TotalAgentsPaused'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Agent(s) on Paused</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results13['TotalAgentsWaitCalls'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Agent(s) Waiting</td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"><?php echo $results14['TotalAgentsOnline']; ?></p></td><td>&nbsp;&nbsp;&nbsp;Total Agents Online</td></tr>
</table>

<h3>Lead Resources</h3>
<hr /><table><tr><td>
<p style="font-size:30px;color:#464646;"> <?php echo $results3['getLeadsinHopper'];  ?></p></td><td> <b>&nbsp;&nbsp;&nbsp;Leads in Hopper</b></td></tr><tr><td>
<p style="font-size:15px;color:#464646;float:right;"> <?php echo $results4['getTotalDialableLeads'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Dialable Leads</td></tr>
<tr><td><p style="font-size:15px;color:#464646;float:right;"> <?php echo $results5['getTotalActiveLeads'];  ?></p></td><td>&nbsp;&nbsp;&nbsp;Total Active Leads</td></tr>
</table>

<h3>Agents</h3>
<hr /><table><tr><td>
<p style="font-size:30px;color:#464646;"><?php echo $results20['num_seats']; ?></p></td><td> <b>&nbsp;&nbsp;&nbsp;Number of agent(s)</b></td></tr><tr><td>


<!-- End of View -->
