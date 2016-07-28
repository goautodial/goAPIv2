<?php
    ####################################################
    #### Name: goFunctions.php                      ####
    #### Type: API Functions                        ####
    #### Version: 0.9                               ####
    #### Copyright: GOAutoDial Inc. (c) 2011-2014   ####
    #### Written by: Jerico James Flores Milo       ####
    #### License: AGPLv2                            ####
    ####################################################
    
    ##### get usergroup #########
    function go_get_groupid($goUser){
        include "goDBconnectA.php";
        $query_userv = "SELECT user_group FROM vicidial_users WHERE user='$goUser'";
        $rsltv = mysqli_query($link, $query_userv);
        $check_resultv = mysqli_num_rows($rsltv);
    
        if ($check_resultv > 0) {
            $rowc=mysqli_fetch_assoc($rsltv);
            $goUser_group = $rowc["user_group"];
            return $goUser_group;
        }
        
    }
    
    ##### checkiftenant ######
    function checkIfTenant($groupId){
        include "goDBconnectB.php";
        $query_tenant = "SELECT * FROM go_multi_tenant WHERE tenant_id='$groupId'";
        $rslt_tenant = mysqli_query($linkgo, $query_tenant);
        $check_result_tenant = mysqli_num_rows($rslt_tenant);
    
        if ($check_result_tenant > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    
    function go_getall_allowed_users($groupId) {
        include "goDBconnectA.php";
        if ($groupId=='ADMIN' || $groupId=='admin') {
                   $query = "select user as userg from vicidial_users";
                   $rsltv = mysqli_query($link, $query); 
        } else {
                   $query = "select user as userg from vicidial_users where user_group='$groupId'";
                   $rsltv = mysqli_query($link, $query); 
        }
                
        $fresults=mysqli_fetch_array($rsltv);
        $callfunc = go_total_agents_callv($groupId);
        $v = $callfunc - 1;
        $allowed_users='';
        $i=0;
        
        while($info = mysqli_fetch_array( $rsltv )) {
            $users = $info['userg'];
                if ($i==$v) {
                      $allowed_users .= "'" . $users. "'";
                } else {
                      $allowed_users .= "'" . $users. "'" . ',';
                }
            $i++;
        }
    
        return $allowed_users;
    }
    
    
    function go_total_agents_callv($groupId) {
        include_once ("goDBconnectA.php");
        if (!checkIfTenant($groupId)) {
                   $query = "select count(*) as qresult from vicidial_users";
                   $rsltv = mysqli_query($link, $query);
        } else {
                   $query = "select count(*) as qresult from vicidial_users where user_group='$groupId'";
                   $rsltv = mysqli_query($link, $query);
        }
                
        $fresults = mysqli_fetch_assoc($rsltv);
        $fresults = $fresults['qresult'];
                
        if ($fresults == NULL) {
            $fresults = 0;
        }
        
        return $fresults;
    }
      function go_getall_allowed_campaigns($groupId)
      {
            /*$groupId = $this->go_get_groupid();
                if (!is_null($tenant)) {
                        $groupId = $tenant;
                }*/
            $query_date =  date('Y-m-d');
            $query = "select trim(allowed_campaigns) as qresult from vicidial_user_groups where user_group='$groupId'";
            $resultsu = mysqli_query($link, $query);

            if(count($resultsu) > 0){
                $fresults = $resultsu['qresult'];
                $allowedCampaigns = explode(",",str_replace("",',',rtrim(ltrim(str_replace('-','',$fresults)))));

                $allAllowedCampaigns = implode("','",$allowedCampaigns);

            }else{
                $allAllowedCampaigns = '';
            }
            return $allAllowedCampaigns;
      }




    
    
    #### Jerico James Flores Milo ####
    #### My APIxmlOuput           #### 
    function apiXMLOutput($val, $lastk = "") {
    	foreach ($val as $k => $v) {
    
    		if (is_array( $v )) {
    			if (is_numeric( $k )) {
    				echo "<{$lastk}>\n";
    			}
    			else {
    				if (( !is_numeric( key( $v ) ) && count( $v ) )) {
    					echo "<{$k}>\n";
    				}
    			}
    
    			apiXMLOutput( $v, $k );
    
    			if (is_numeric( $k )) {
    				echo "</{$lastk}>\n";
    				continue;
    			}
    
    
    			if (( !is_numeric( key( $v ) ) && count( $v ) )) {
    				echo "</{$k}>\n";
    				continue;
    			}
    
    			continue;
    		}
    
    		$v = html_entity_decode( $v );
    
    		if (( strpos( $v, "<![CDATA[" ) === false && htmlspecialchars( $v ) != $v )) {
    			$v = ( "<![CDATA[" . $v . "]" ) . "]>";
    		}
    
    		echo "<{$k}>{$v}</{$k}>\n";
    	}
    
    }
?>
