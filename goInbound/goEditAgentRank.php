<?php

    #######################################################
    #### Name:  goEditAgentRank.php		               ####
    #### Description: API to update ingroup agents     ####
    #### Version: 0.9                                  ####
    #### Copyright: GOAutoDial Ltd. (c) 2011-2016      ####
    #### Written by: Jerico James F. Milo 	           ####
    #### License: AGPLv2                               ####
    #######################################################
    
    include_once ("goFunctions.php");
    
	$goItemRank	= $_REQUEST['itemrank'];
	$goidIDgroup 	= $_REQUEST['idgroup'];
	
	if($goidIDgroup == null ) {
		$apiresults = array(  "result" => "Error: Set a value for group_id");
	} else {
		$itemsumitexplode = explode('&', $goItemRank);
		$group_id = $goidIDgroup;
		
		for( $i = 0; $i < count( $itemsumitexplode ); $i++ ) {
				$itemsumitsplit = split('=', $itemsumitexplode[$i]);
		 		$showval = htmlspecialchars(urldecode($itemsumitsplit[0]));
				$datavals = htmlspecialchars(urldecode($itemsumitsplit[1]));
				$finalvalues = $showval."||".$datavals.""; 
	
				if(preg_match("/CHECK/i", "$itemsumitexplode[$i]")) {
					
					if (preg_match("/YES/i", "$itemsumitexplode[$i]")) {
						$checked = $itemsumitexplode[$i]."\n";	
						$repcheck = str_replace("CHECK_", "", $itemsumitexplode[$i]);
						$user = str_replace("=YES", "", $repcheck);
						
						//$query = $this->asteriskDB->query("SELECT closer_campaigns FROM vicidial_users WHERE user='$user'");
						$query = "SELECT closer_campaigns FROM vicidial_users WHERE user='$user'";
						$rsltv = mysqli_query($link,$query);
						$fresults = mysqli_fetch_assoc($rsltv);
						//$closer_campaigns = $query->row()->closer_campaigns;
						$closer_campaigns = $fresults['closer_campaigns'];
						$closer_campaigns = rtrim($closer_campaigns,"-");
						$closer_campaigns = str_replace(" $group_id", "", $closer_campaigns);
						$closer_campaigns = trim($closer_campaigns);
						if (strlen($closer_campaigns) > 1)
							$closer_campaigns = " $closer_campaigns";
						$NEWcloser_campaigns = " $group_id{$closer_campaigns} -";
					} else {
						$checked = $itemsumitexplode[$i]."\n";	
						$repcheck = str_replace("CHECK_", "", $itemsumitexplode[$i]);
						$user = str_replace("=NO", "", $repcheck);
						
						//$query = $this->asteriskDB->query("SELECT closer_campaigns FROM vicidial_users WHERE user='$user'");
						//$closer_campaigns = $query->row()->closer_campaigns;
						$query2 = "SELECT closer_campaigns FROM vicidial_users WHERE user='$user'";
						$rsltv2 = mysqli_query($link,$query2);
						$fresults2 = mysqli_fetch_assoc($rsltv2);
						$closer_campaigns = $fresults2['closer_campaigns'];
						$closer_campaigns = rtrim($closer_campaigns,"-");
						$closer_campaigns = str_replace(" $group_id", "", $closer_campaigns);
						$closer_campaigns = trim($closer_campaigns);
						$NEWcloser_campaigns = "{$closer_campaigns} -";
					}
					
					//$query = $this->asteriskDB->query("UPDATE vicidial_users set closer_campaigns='$NEWcloser_campaigns' where user='$user';");
					//$query_log .= "UPDATE vicidial_users set closer_campaigns='$NEWcloser_campaigns' where user='$user';\n";
					//echo "UPDATE vicidial_users set closer_campaigns='$NEWcloser_campaigns' where user='$user';";
					$query3 = "UPDATE vicidial_users set closer_campaigns='$NEWcloser_campaigns' where user='$user';";
					$rsltv3 = mysqli_query($link,$query3);
					//$apiresults = array("result" => "success");
				}
				
				if(preg_match("/RANK/i", "$itemsumitexplode[$i]")) {
					$itemsumitsplit1 = split('=', $itemsumitexplode[$i]);
					$datavals1 = htmlspecialchars(urldecode($itemsumitsplit1[1]));
					
					$itemsexplode = explode("_",$itemsumitsplit1[0]);
					//$query = $this->asteriskDB->query("UPDATE vicidial_inbound_group_agents SET group_rank='$datavals1',group_weight='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';");
					$query4 = "UPDATE vicidial_inbound_group_agents SET group_rank='$datavals1',group_weight='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';";
					$rsltv4 = mysqli_query($link,$query4);
					//$apiresults = array("result" => "success");
					//echo "UPDATE vicidial_inbound_group_agents SET group_rank='$datavals1',group_weight='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';";
					//$query_log .= "UPDATE vicidial_inbound_group_agents SET group_rank='$datavals1',group_weight='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';\n";
					if($datavals1 != 0){
						$ranknotzero .= $itemsumitexplode[$i]."\n";
					}
				}
				
				if(preg_match("/GRADE/i", "$itemsumitexplode[$i]")) {
					$itemsumitsplit1 = split('=', $itemsumitexplode[$i]);
					$datavals1 = htmlspecialchars(urldecode($itemsumitsplit1[1]));
					
					$itemsexplode = explode("_",$itemsumitsplit1[0]);
					//$query = $this->asteriskDB->query("UPDATE vicidial_inbound_group_agents SET group_grade='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';");
					$query5 = "UPDATE vicidial_inbound_group_agents SET group_grade='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';";
					$rsltv5 = mysqli_query($link,$query5);
					//$apiresults = array("result" => "success");
					//echo "UPDATE vicidial_inbound_group_agents SET group_rank='$datavals1',group_weight='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';";
					//$query_log .= "UPDATE vicidial_inbound_group_agents SET group_grade='$datavals1' WHERE user='{$itemsexplode[1]}' AND group_id='$group_id';\n";
				}
				$apiresults = array("result" => "success");
		}
	}
		//echo $checked."\n".$ranknotzero."\n";
		//$this->commonhelper->auditadmin('MODIFY',"Modified Agent Rank(s)","$query_log");
		

		
		/*$reprank = str_replace("RANK_", "", $ranknotzero);
		
		$itemsumitexplode = explode('=', $reprank);

		for( $i = 0; $i < count( $itemsumitexplode ); $i++ ) {
		
		}
		$time = $reprank;
		$length = strlen($$reprank);
		$characters = 2;
		$start = $length - $characters;
		$xreprank = substr($time , $start ,$characters);*/
?>
