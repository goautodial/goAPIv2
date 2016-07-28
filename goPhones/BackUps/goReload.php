<?php
 ####################################################
 #### Name: goSample.php                         ####
 #### Description: API for dashboard php encode  ####
 #### Version: 0.9                               ####
 #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
 #### Written by: Jeremiah Sebastian V. Samatra  ####
 #### License: AGPLv2                            ####
 ####################################################

// function get_answered_calls($action){

	 $url = "https://jameshv.goautodial.com/goAPI/goAPI.php"; # URL to GoAutoDial API file
	 #$username = "2012107124"; # Admin/Tenant/Non-Tenant username goes here
	 #$password = "liSB92qd"; # Admin/Tenant/Non-Tenant password goes here
 

	 $username = "goautodial";
	 $password = "JUs7g0P455W0rD11214";
 	 $action = "getTotalDroppedCalls";
 
	 $postfields["goUser"] = $username;
	 $postfields["goPass"] = $password;
	 $postfields["goAction"] = "getTotalCalls"; #action performed by the [[API:Functions]]
	 

	 $ch = curl_init();
	 curl_setopt($ch, CURLOPT_URL, $url);
	 curl_setopt($ch, CURLOPT_POST, 1);
	 curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	 curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, POSTVARS);
	 $data = curl_exec($ch);
	 curl_close($ch);
 	
	//  var_dump($data);
 
	 $data = explode(";",$data);
	 foreach ($data AS $temp) {
	   $temp = explode("=",$temp);
	   $results[$temp[0]] = $temp[1];
 	}
 
	 if ($results["result"]=="success") {
	echo "Answered Calls: ".$results['answers_today'];   # Result was OK!
//	   var_dump($results); #to see the returned arrays.
	   #echo $results['TotalAgentsCall'];
	 } else {
	   # An error occured
		   echo "The following error occured: ".$results["message"];
 		}

//}


//function get_dropped_calls($action){

         $droppedcalls = "getTotalDroppedCalls";

         define('POSTVARS', 'goUser='.$username.'&goPass='.$password.'&goAction='.$droppedcalls);
         $ch1 = curl_init();
         curl_setopt($ch1, CURLOPT_URL, $url);
         curl_setopt($ch1, CURLOPT_POST, 1);
         curl_setopt($ch1, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
         curl_setopt($ch1, CURLOPT_POSTFIELDS, POSTVARS);
         $data1 = curl_exec($ch1);
         curl_close($ch1);
        
         $data1 = explode(";",$data1);
         foreach ($data1 AS $temp1) {
           $temp1 = explode("=",$temp1);
           $results1[$temp1[0]] = $temp1[1];
        }
 
         if ($results1["result"]=="success") {
echo "<br />";

echo "Dropped calls: ".$results1['drops_today'];
       
 echo $results1['answers_today'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results1["message"];
                }

//end of function
//}


//function get_dropped_percentage(){
         



         $percentage["goUser"] = $username;
         $percentage["goPass"] = $password;
         $percentage["goAction"] = "getDroppedPercentage"; #action performed by the [[API:Functions]]


         $ch2 = curl_init();
         curl_setopt($ch2, CURLOPT_URL, $url);
         curl_setopt($ch2, CURLOPT_POST, 1);
         curl_setopt($ch2, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch2, CURLOPT_POSTFIELDS, $percentage);
         $data2 = curl_exec($ch2);
         curl_close($ch2);

        //  var_dump($data);

         $data2 = explode(";",$data2);
         foreach ($data2 AS $temp2) {
           $temp2 = explode("=",$temp2);
           $results2[$temp2[0]] = $temp2[1];
        }

         if ($results2["result"]=="success") {
        echo "<br />"."Dropped calls percentage: ".$results2['drop_call_per'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results2["message"];
                }
//end of function
//}


//function get_leads_hopper(){
         
         $leadshopper["goUser"] = $username;
         $leadshopper["goPass"] = $password;
         $leadshopper["goAction"] = "getLeadsinHopper"; #action performed by the [[API:Functions]]


         $ch3 = curl_init();
         curl_setopt($ch3, CURLOPT_URL, $url);
         curl_setopt($ch3, CURLOPT_POST, 1);
         curl_setopt($ch3, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch3, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch3, CURLOPT_POSTFIELDS, $leadshopper);
         $data3 = curl_exec($ch3);
         curl_close($ch3);

        //  var_dump($data);

         $data3 = explode(";",$data3);
         foreach ($data3 AS $temp3) {
           $temp3 = explode("=",$temp3);
           $results3[$temp3[0]] = $temp3[1];
        }

         if ($results3["result"]=="success") {
        echo "<br />"."Leads in Hopper: ".$results3['getLeadsinHopper'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results3["message"];
                }
//end of function
//}



//function get_total_dialable_leads(){
         
         $dialableleads["goUser"] = $username;
         $dialableleads["goPass"] = $password;
         $dialableleads["goAction"] = "getTotalDialableLeads"; #action performed by the [[API:Functions]]


         $ch4 = curl_init();
         curl_setopt($ch4, CURLOPT_URL, $url);
         curl_setopt($ch4, CURLOPT_POST, 1);
         curl_setopt($ch4, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch4, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch4, CURLOPT_POSTFIELDS, $dialableleads);
         $data4 = curl_exec($ch4);
         curl_close($ch4);

        //  var_dump($data);

         $data4 = explode(";",$data4);
         foreach ($data4 AS $temp4) {
           $temp4 = explode("=",$temp4);
           $results4[$temp4[0]] = $temp4[1];
        }

         if ($results4["result"]=="success") {
        echo "<br />"."Dialable Leads: ".$results4['getTotalDialableLeads'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results4["message"];
                }
//end of function
//}



//function get_total_active_leads(){
         
         $activeleads["goUser"] = $username;
         $activeleads["goPass"] = $password;
         $activeleads["goAction"] = "getTotalActiveLeads"; #action performed by the [[API:Functions]]


         $ch5 = curl_init();
         curl_setopt($ch5, CURLOPT_URL, $url);
         curl_setopt($ch5, CURLOPT_POST, 1);
         curl_setopt($ch5, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch5, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch5, CURLOPT_POSTFIELDS, $activeleads);
         $data5 = curl_exec($ch5);
         curl_close($ch5);

        //  var_dump($data);

         $data5 = explode(";",$data5);
         foreach ($data5 AS $temp5) {
           $temp5 = explode("=",$temp5);
           $results5[$temp5[0]] = $temp5[1];
        }

         if ($results5["result"]=="success") {
        echo "<br />"."Active Leads: ".$results5['getTotalActiveLeads'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results5["message"];
                }
//end of function
//}




//function get_total_Sales(){

         $totalsales["goUser"] = $username;
         $totalsales["goPass"] = $password;
         $totalsales["goAction"] = "getTotalSales"; #action performed by the [[API:Functions]]


         $ch6 = curl_init();
         curl_setopt($ch6, CURLOPT_URL, $url);
         curl_setopt($ch6, CURLOPT_POST, 1);
         curl_setopt($ch6, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch6, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch6, CURLOPT_POSTFIELDS, $totalsales);
         $data6 = curl_exec($ch6);
         curl_close($ch6);

        //  var_dump($data);

         $data6 = explode(";",$data6);
         foreach ($data6 AS $temp6) {
           $temp6 = explode("=",$temp6);
           $results6[$temp6[0]] = $temp6[1];
        }

         if ($results6["result"]=="success") {
        echo "<br />"."Total Sales: ".$results6['TotalSales'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results6["message"];
                }
//end of function
//}



//function getTotalInboundSales(){

         $inboundsales["goUser"] = $username;
         $inboundsales["goPass"] = $password;
         $inboundsales["goAction"] = "getTotalInboundSales"; #action performed by the [[API:Functions]]


         $ch7 = curl_init();
         curl_setopt($ch7, CURLOPT_URL, $url);
         curl_setopt($ch7, CURLOPT_POST, 1);
         curl_setopt($ch7, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch7, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch7, CURLOPT_POSTFIELDS, $inboundsales);
         $data7 = curl_exec($ch7);
         curl_close($ch7);

        //  var_dump($data);

         $data7 = explode(";",$data7);
         foreach ($data7 AS $temp7) {
           $temp7 = explode("=",$temp7);
           $results7[$temp7[0]] = $temp7[1];
        }

         if ($results7["result"]=="success") {
        echo "<br />"."Inbound Sales ".$results7['InboundSales'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results7["message"];
                }
//end of function
//}


//function getTotalOutboundSales(){

         $outboundsales["goUser"] = $username;
         $outboundsales["goPass"] = $password;
         $outboundsales["goAction"] = "getTotalOutboundSales"; #action performed by the [[API:Functions]]


         $ch8 = curl_init();
         curl_setopt($ch8, CURLOPT_URL, $url);
         curl_setopt($ch8, CURLOPT_POST, 1);
         curl_setopt($ch8, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch8, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch8, CURLOPT_POSTFIELDS, $outboundsales);
         $data8 = curl_exec($ch8);
         curl_close($ch8);

        //  var_dump($data);

         $data8 = explode(";",$data8);
         foreach ($data8 AS $temp8) {
           $temp8 = explode("=",$temp8);
           $results8[$temp8[0]] = $temp8[1];
        }

         if ($results8["result"]=="success") {
        echo "<br />"."Outbound Sales: ".$results8['OutboundSales'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results8["message"];
                }
//end of function
//}



//function getINSalesHour(){

         $insales["goUser"] = $username;
         $insales["goPass"] = $password;
         $insales["goAction"] = "getINSalesHour"; #action performed by the [[API:Functions]]


         $ch9 = curl_init();
         curl_setopt($ch9, CURLOPT_URL, $url);
         curl_setopt($ch9, CURLOPT_POST, 1);
         curl_setopt($ch9, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch9, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch9, CURLOPT_POSTFIELDS, $insales);
         $data9 = curl_exec($ch9);
         curl_close($ch9);

        //  var_dump($data);

         $data9 = explode(";",$data9);
         foreach ($data9 AS $temp9) {
           $temp9 = explode("=",$temp9);
           $results9[$temp9[0]] = $temp9[1];
        }

         if ($results9["result"]=="success") {
        echo "<br />"."IN Sales / Hour: ".$results9['getInSalesPerHour'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results9["message"];
                }
//end of function
//}


//function getOutSalesPerHour(){

         $outsales["goUser"] = $username;
         $outsales["goPass"] = $password;
         $outsales["goAction"] = "getOutSalesPerHour"; #action performed by the [[API:Functions]]


         $ch11 = curl_init();
         curl_setopt($ch11, CURLOPT_URL, $url);
         curl_setopt($ch11, CURLOPT_POST, 1);
         curl_setopt($ch11, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch11, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch11, CURLOPT_POSTFIELDS, $outsales);
         $data11 = curl_exec($ch11);
         curl_close($ch11);

        //  var_dump($data);

         $data11 = explode(";",$data11);
         foreach ($data11 AS $temp11) {
           $temp11 = explode("=",$temp11);
           $results11[$temp11[0]] = $temp11[1];
        }

         if ($results11["result"]=="success") {
        echo "<br />"."OUT Sales / Hour: ".$results11['OutSalesPerHour'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results11["message"];
                }
//end of function
//}


//function getTotalAgentsCall(){

         $agentscall["goUser"] = $username;
         $agentscall["goPass"] = $password;
         $agentscall["goAction"] = "getTotalAgentsCall"; #action performed by the [[API:Functions]]


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
        echo "<br />"."Agent(s) on Call: ".$results10['TotalAgentsCall'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results10["message"];
                }
//end of function
//}


//function getTotalAgentsPaused(){

         $agentspaused["goUser"] = $username;
         $agentspaused["goPass"] = $password;
         $agentspaused["goAction"] = "getTotalAgentsPaused"; #action performed by the [[API:Functions]]


         $ch12 = curl_init();
         curl_setopt($ch12, CURLOPT_URL, $url);
         curl_setopt($ch12, CURLOPT_POST, 1);
         curl_setopt($ch12, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch12, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch12, CURLOPT_POSTFIELDS, $agentspaused);
         $data12 = curl_exec($ch12);
         curl_close($ch12);

        //  var_dump($data);

         $data12 = explode(";",$data12);
         foreach ($data12 AS $temp12) {
           $temp12 = explode("=",$temp12);
           $results12[$temp12[0]] = $temp12[1];
        }

         if ($results12["result"]=="success") {
        echo "<br />"."Agent(s) on Paused: ".$results12['TotalAgentsPaused'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results12["message"];
                }
//end of function
//}


//function getTotalAgentsWaitCalls(){

         $agentswait["goUser"] = $username;
         $agentswait["goPass"] = $password;
         $agentswait["goAction"] = "getTotalAgentsWaitCalls"; #action performed by the [[API:Functions]]


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
        echo "<br />"."Agent(s) on wait: ".$results13['TotalAgentsWaitCalls'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results13["message"];
                }
//end of function
//}


//function getAgentsOnline(){

         $agentsonline["goUser"] = $username;
         $agentsonline["goPass"] = $password;
         $agentsonline["goAction"] = "getAgentsOnline"; #action performed by the [[API:Functions]]


         $ch14 = curl_init();
         curl_setopt($ch14, CURLOPT_URL, $url);
         curl_setopt($ch14, CURLOPT_POST, 1);
         curl_setopt($ch14, CURLOPT_TIMEOUT, 100);
         curl_setopt($ch14, CURLOPT_RETURNTRANSFER, 1);
         curl_setopt($ch14, CURLOPT_POSTFIELDS, $agentsonline);
         $data14 = curl_exec($ch14);
         curl_close($ch14);

        //  var_dump($data);

         $data14 = explode(";",$data14);
         foreach ($data14 AS $temp14) {
           $temp14 = explode("=",$temp14);
           $results14[$temp14[0]] = $temp14[1];
        }

         if ($results14["result"]=="success") {
        echo "<br />"."Agent(s) online: ".$results14['TotalAgentsOnline'];   # Result was OK!
         } else {
           # An error occured
                   echo "The following error occured: ".$results14["message"];
                }
//end of function
//}












?>





<!DOCTYPE html>
<br />
<div id = "show" style="background-color:blue;width:30%;">
<h3>Dropped Call Percentage</h3>
<br />
<p><b>Dropped Percentage:</b>  <?php echo $results2['drop_call_per']; ?></p>
<p><strong>Dropped Calls:</strong> <?php echo $results1['drops_today']; ?></p>
<p><b>Answered Calls:</b> <?php echo $results['answers_today']; ?> </p>
</div>









