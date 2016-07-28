<?php
   ####################################################
   #### Name: goAddLeads.php                        ####
   #### Description: API to Load Leads             ####
   #### Version: 0.9                               ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016   ####
   #### Written by: Jeremiah Sebastian Samatra     ####
   #### License: AGPLv2                            ####
   ####################################################
    
    include "../goFunctions.php";
 

                $final = $_REQUEST['final'];

                //$list_id = $this->input->post('list_id');
                //$query = $this->db->query("SELECT campaign_id FROM vicidial_lists WHERE list_id='$list_id'");
                //$campaign_id = $query->row()->campaign_id;
                //$query = $this->db->query("UPDATE campaign_changedate FROM vicidial_campaigns WHERE campaign_id='$campaign_id'");

                if ($final != 'final')
                {
                        $config['upload_path'] = '/tmp/';
                        $config['allowed_types'] = 'xls|xlsx|csv';
                        $config['overwrite'] = true;
                        $this->load->library('upload', $config);
                        $this->upload->initialize($config);
        //
        //              $data['file_exten'] = $file_exten;
        //              $data['column_name'] = $result['column_name'];
        //              $this->load->view('go_campaign/go_campaign_wizard_fields',$data);
                        $LF_name = $_FILES['leadFile']['name'];
                        if (preg_match("/\.csv$/i", $LF_name))
                        {
                                $_FILES['leadFile']['type'] = "text/x-comma-separated-values";
                        }

                        if ( ! $this->upload->do_upload("leadFile"))
                        {
                                $error = array('error' => $this->upload->display_errors());

        //var_dump($error);     
                                $this->load->view('go_campaign/go_campaign_wizard_output', $error);
                        }
                        else
                        {
        //                      $data = array('upload_data' => $this->upload->data());
                                $result = $this->go_campaign->go_upload_leads();

        //var_dump($data);
                                $data['delim_name'] = $result['delim_name'];
                                $data['columns'] = $result['column_name'];
                                $data['list_id'] = $result['list_id'];
                                $data['phone_code'] = $result['phone_code'];
                                $data['file_name'] = $result['file_name'];
                                $data['file_ext'] = $result['file_ext'];
                                $this->load->view('go_campaign/go_campaign_wizard_fields', $data);
                        }
                }
                else
                {
                        $dupcheck = $this->uri->segment(4);
                        $list_id_override = $this->uri->segment(5);
                        $phone_code_override = $this->uri->segment(6);
                        $args = $this->uri->segment(7);
                        $file_name = $this->uri->segment(8);
                        $file_ext = $this->uri->segment(9);
                        $lead_file = "/tmp/{$file_name}.{$file_ext}";
                        $resultHTML = '';

                        $fields = $this->go_campaign->go_unserialize($args);

                        foreach ($fields as $field => $value)
                        {
                                ${$field} = $value;
                        }
                        $dupcheck = str_replace("CHECK","DUP",$dupcheck);

                        flush();
                        $total=0; $good=0; $bad=0; $dup=0; $post=0; $phone_list='';

                        $file=fopen("$lead_file", "r");

                        $buffer=fgets($file, 4096);
                        $tab_count=substr_count($buffer, "\t");
                        $pipe_count=substr_count($buffer, "|");

                        if ($tab_count>$pipe_count) {$delimiter="\t";  $delim_name="tab";} else {$delimiter="|";  $delim_name="pipe";}
                        $field_check=explode($delimiter, $buffer);

                                if (count($field_check)>=2) {
                                        flush();
                                        $file=fopen("$lead_file", "r");
                                        //$data['processfile'] = "<center><font face='arial, helvetica' size=3 color='#009900'><B>Processing file...\n";

                                        if (strlen($list_id_override)>0) {
                                        //print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
                                        }

                                        if (strlen($phone_code_override)>0) {
                                        //print "<BR><BR>PHONE CODE OVERRIDE FOR THIS FILE: $phone_code_override<BR><BR>";
                                        }

                                        $systemlookup = $this->golist->systemsettingslookup();
                                        foreach($systemlookup as $sysinfo){
                                                $use_non_latin = $sysinfo->use_non_latin;
                                                $admin_web_directory = $sysinfo->admin_web_directory;
                                                $custom_fields_enabled = $sysinfo->custom_fields_enabled;
                                        }


                                        if ($custom_fields_enabled > 0) {
                                                        $tablecount_to_print=0;
                                                        $fieldscount_to_print=0;
                                                        $fields_to_print=0;

                                                        $stmt="SHOW TABLES LIKE \"custom_$list_id_override\";";
                                                        //$rslt = $this->db->query($stmt);
							$rslt = mysqli_query($link, $stmt);
                                                        $tablecount_to_print = $rslt->num_rows;

                                                        if ($tablecount_to_print > 0) {
                                                                        $stmt="SELECT count(*) from vicidial_lists_fields where list_id='$list_id_override';";
                                                                        //$rslt = $this->db->query($stmt);
									$rslt = mysqli_query($link,$stmt);
                                                                        $fieldscount_to_print = $rslt->num_rows;

                                                                        if ($fieldscount_to_print > 0) {
                                                                                $stmt="SELECT field_label,field_type from vicidial_lists_fields where list_id='$list_id_override' order by field_rank,field_order,field_label;";

                                                                                //$rslt = $this->db->query($stmt);
										$rslt = mysqli_query($link,$stmt);
                                                                                $fields_to_print = $rslt->num_rows;

                                                                                $fields_list='';
                                                                                $o=0;

                                                                                while ($fields_to_print > $o) {
                                                                                        $rowx = $rslt->row();
                                                                                        $A_field_label[$o] =    $rowx->field_label;
                                                                                        $A_field_type[$o] =     $rowx->field_type;
                                                                                        $A_field_value[$o] =    '';
                                                                                $o++;
                                                                                }
                                                                        }
                                                        }
                                        }


                                        while (!feof($file)) {
                                                        $record++;
                                                        $buffer=rtrim(fgets($file, 4096));
                                                        $buffer=stripslashes($buffer);

                                                        if (strlen($buffer)>0) {
                                                                $row=explode($delimiter, preg_replace("/[\'\"]/", "", $buffer));
                                                                $lrow=$row;

                                                                        $pulldate=date("Y-m-d H:i:s");
                                                                        $entry_date =                   "$pulldate";
                                                                        $modify_date =                  "";
                                                                        $status =                               "NEW";
                                                                        $user ="";
                                                                        $vendor_lead_code =             $row[$vendor_lead_code_field];
                                                                        $source_code =                  $row[$source_id_field];
                                                                        $source_id=$source_code;
                                                                        $list_id =                              $row[$list_id_field];
                                                                        $gmt_offset =                   '0';
                                                                        $called_since_last_reset='N';
                                                                        $phone_code =                   preg_replace("/[^0-9]/", "", $row[$phone_code_field]);
                                                                        $phone_number =                 preg_replace("/[^0-9]/", "", $row[$phone_number_field]);
                                                                        $title =                                $row[$title_field];
                                                                        $first_name =                   $row[$first_name_field];
                                                                        $middle_initial =               $row[$middle_initial_field];
                                                                        $last_name =                    $row[$last_name_field];
                                                                        $address1 =                             $row[$address1_field];
                                                                        $address2 =                             $row[$address2_field];
                                                                        $address3 =                             $row[$address3_field];
                                                                        $city =                                 $row[$city_field];
                                                                        $state =                                $row[$state_field];
                                                                        $province =                             $row[$province_field];
                                                                        $postal_code =                  $row[$postal_code_field];
                                                                        $country_code =                 $row[$country_code_field];
                                                                        $gender =                               $row[$gender_field];
                                                                        $date_of_birth =                $row[$date_of_birth_field];
                                                                        $alt_phone =                    preg_replace("/[^0-9]/", "", $row[$alt_phone_field]);
                                                                      $email =                                $row[$email_field];
                                                                        $security_phrase =              $row[$security_phrase_field];
                                                                        $comments =                             trim($row[$comments_field]);
                                                                        $rank =                                 $row[$rank_field];
                                                                        $owner =                                $row[$owner_field];

                                                                        ### REGEX to prevent weird characters from ending up in the fields
                                                                        $field_regx = "/['\"`\\;]/";



                                                                        # replace ' " ` \ ; with nothing
                                                                        $vendor_lead_code =             preg_replace($field_regx, "", $vendor_lead_code);
                                                                        $source_code =                  preg_replace($field_regx, "", $source_code);
                                                                        $source_id =                    preg_replace($field_regx, "", $source_id);
                                                                        $list_id =                              preg_replace($field_regx, "", $list_id);
                                                                        $phone_code =                   preg_replace($field_regx, "", $phone_code);
                                                                        $phone_number =                 preg_replace($field_regx, "", $phone_number);
                                                                        $title =                                preg_replace($field_regx, "", $title);
                                                                        $first_name =                   preg_replace($field_regx, "", $first_name);
                                                                        $middle_initial =               preg_replace($field_regx, "", $middle_initial);
                                                                        $last_name =                    preg_replace($field_regx, "", $last_name);
                                                                        $address1 =                             preg_replace($field_regx, "", $address1);
                                                                        $address2 =                             preg_replace($field_regx, "", $address2);
                                                                        $address3 =                             preg_replace($field_regx, "", $address3);
                                                                        $city =                                 preg_replace($field_regx, "", $city);
                                                                        $state =                                preg_replace($field_regx, "", $state);
                                                                        $province =                             preg_replace($field_regx, "", $province);
                                                                        $postal_code =                  preg_replace($field_regx, "", $postal_code);
                                                                        $country_code =                 preg_replace($field_regx, "", $country_code);
                                                                        $gender =                               preg_replace($field_regx, "", $gender);
                                                                        $date_of_birth =                preg_replace($field_regx, "", $date_of_birth);
                                                                        $alt_phone =                    preg_replace($field_regx, "", $alt_phone);
                                                                        $email =                                preg_replace($field_regx, "", $email);
                                                                        $security_phrase =              preg_replace($field_regx, "", $security_phrase);
                                                                        $comments =                             preg_replace($field_regx, "", $comments);
                                                                        $rank =                                 preg_replace($field_regx, "", $rank);
                                                                        $owner =                                preg_replace($field_regx, "", $owner);

                                                                        $USarea =                       substr($phone_number, 0, 3);

                                                                        if (strlen($list_id_override)>0) {
                                                                                #       print "<BR><BR>LIST ID OVERRIDE FOR THIS FILE: $list_id_override<BR><BR>";
                                                                                $list_id = $list_id_override;
                                                                        }
                                                                        if (strlen($phone_code_override)>0) {
                                                                                $phone_code = $phone_code_override;
                                                                        }
                                                                                ##### BEGIN custom fields columns list ###
                                                                                $custom_SQL='';
                                                                                if ($custom_fields_enabled > 0)
                                                                                        {
                                                                                        if ($tablecount_to_print > 0)
                                                                                                {
                                                                                                if ($fieldscount_to_print > 0)
                                                                                                        {
                                                                                                        $o=0;
                                                                                                        while ($fields_to_print > $o)
                                                                                                                {
                                                                                                                $A_field_value[$o] =    '';
                                                                                                                $field_name_id = $A_field_label[$o] . "_field";

                                                                                                        #       if ($DB>0) {echo "$A_field_label[$o]|$A_field_type[$o]\n";}

                                                                                                                if ( ($A_field_type[$o]!='DISPLAY') and ($A_field_type[$o]!='SCRIPT') )
                                                                                                                        {
                                                                                                                        if (!preg_match("/\|$A_field_label[$o]\|/",$vicidial_list_fields))
                                                                                                                                {
                                                                                                                                if (isset($_GET["$field_name_id"]))                             {$form_field_value=$_GET["$field_name_id"];}
                                                                                                                                        elseif (isset($_POST["$field_name_id"]))        {$form_field_value=$_POST["$field_name_id"];}

                                                                                                                                if ($form_field_value >= 0)
                                                                                                                                        {
                                                                                                                                        $A_field_value[$o] =    $row[$form_field_value];
                                                                                                                                        # replace ' " ` \ ; with nothing
                                                                                                                                        $A_field_value[$o] =    preg_replace($field_regx, "", $A_field_value[$o]);

                                                                                                                                        $custom_SQL .= "$A_field_label[$o]='$A_field_value[$o]',";
                                                                                                                                        }
                                                                                                                                }
                                                                                                                        }
                                                                                                                $o++;
                                                                                                                }
                                                                                                        }
                                                                                                }
                                                                                        }
                                                                                ##### END custom fields columns list ###

                                                                                $custom_SQL = preg_replace("/,$/","",$custom_SQL);

                                                                        ## checking duplicate portion

                                                                        ##### Check for duplicate phone numbers in vicidial_list table for all lists in a campaign #####
                                                                        if ($dupcheck=='DUPCAMP') {

                                                                                $dup_lead=0;
                                                                                $dup_lists='';
                                                                                $stmt="select campaign_id from vicidial_lists where list_id='$list_id';";

                                                                                //$rslt = $this->db->query($stmt);
										$rslt = mysqli_query($link,$stmt);
                                                                                $ci_recs = $rslt->num_rows;

                                                                                if ($ci_recs > 0) {
                                                                                        $row = $rslt->row();
                                                                                        $dup_camp = $row->campaign_id;
                                                                                        $stmt="select list_id from vicidial_lists where campaign_id='$dup_camp';";

                                                                                        //$rslt = $this->db->query($stmt);
											$rslt = mysqli_query($link, $stmt);
                                                                                        $li_recs = $rslt->num_rows;

                                                                                        if ($li_recs > 0)       {
                                                                                                $L=0;
                                                                                                while ($li_recs > $L) {
                                                                                                        $row = $rslt->row();
                                                                                                        $dup_lists .=   "'$row->list_id',";
                                                                                                        $L++;
                                                                                                }
                                                                                                $dup_lists = preg_replace("/,$/",'',$dup_lists);

                                                                                                $stmt="select list_id from vicidial_list where phone_number='$phone_number' and list_id IN($dup_lists) limit 1;";
                                                                                                //$rslt = $this->db->query($stmt);
												$rslt = mysqli_query($link, $stmt);
                                                                                                $pc_recs = $rslt->num_rows;

                                                                                                if ($pc_recs > 0) {
                                                                                                        $dup_lead=1;
                                                                                                        $row = $rslt->row();
                                                                                                        $dup_lead_list = $row->list_id;
                                                                                                        $dup++;

                                                                                                }
                                                                                                if ($dup_lead < 1) {
                                                                                                        if (eregi("$phone_number$US$list_id",$phone_list))
                                                                                                                {$dup_lead++; $dup++;}
                                                                                                        }
                                                                                                }
                                                                                        }
                                                                        }

                                                                        ##### Check for duplicate phone numbers in vicidial_list table entire database #####
                                                                        if ($dupcheck == "DUPSYS") {
                                                                                $dup_lead=0;
                                                                                $stmt="select list_id from vicidial_list where phone_number='$phone_number';";
                                                                                //$rslt = $this->db->query($stmt);
										$rslt = mysqli_query($link, $stmt);
                                                                                $pc_recs = $rslt->num_rows;

                                                                                if ($pc_recs > 0) {
                                                                                        $dup_lead=1;
                                                                                        $row = $rslt->row();
                                                                                        $dup_lead_list = $row->list_id;
                                                                                        $dup++;
                                                                                }

                                                                                if ($dup_lead < 1) {
                                                                                        if (eregi("$phone_number$US$list_id",$phone_list))
                                                                                                {$dup_lead++; $dup++;}
                                                                                }
                                                                        }
                                                                        ##### Check for duplicate phone numbers in vicidial_list table for one list_id #####
                                                                        if ($dupcheck == "DUPLIST") {
                                                                                $dup_lead=0;
                                                                                $stmt="select count(*) as cnt from vicidial_list where phone_number='$phone_number' and list_id='$list_id';";
                                                                                //$rslt = $this->db->query($stmt);
										$rslt = mysqli_query($link, $stmt);
                                                                                $pc_recs = $rslt->num_rows;

                                                                                if ($pc_recs > 0) {
                                                                                        $row = $rslt->row();
                                                                                        $dup_lead = $row->cnt;
                                                                                        $dup_lead_list =        $list_id;
                                                                                        $dup++;
                                                                                        //die($dup_lead_list);
                                                                                }

                                                                                if ($dup_lead < 1) {
                                                                                        if (eregi("$phone_number$US$list_id",$phone_list))
                                                                                                {$dup_lead++; $dup++;}
                                                                                }


                                                                        }

                                                                        ##### Check for duplicate title and alt-phone in vicidial_list table for one list_id #####
                                                                        if ($dupcheck == "DUPTITLEALTPHONELIST")
                                                                                {
                                                                                $dup_lead=0;
                                                                                $stmt="select count(*) as cnt from vicidial_list where title='$title' and alt_phone='$alt_phone' and list_id='$list_id';";
                                                                                //$rslt = $this->db->query($stmt);
										$rslt = mysqli_query($link, $stmt);
                                                                                $pc_recs = $rslt->num_rows;
                                                                                if ($pc_recs > 0) {
                                                                                        $row = $rslt->row();
                                                                                        $dup_lead = $row->cnt;
                                                                                        $dup_lead_list =        $list_id;
                                                                                        $dup++;
                                                                                }
                                                                                if ($dup_lead < 1) {
                                                                                        if (eregi("$alt_phone$title$US$list_id",$phone_list))
                                                                                                {$dup_lead++; $dup++;}
                                                                                        }
                                                                                }

                                                                                ##### Check for duplicate phone numbers in vicidial_list table entire database #####
                                                                                if ($dupcheck == "DUPTITLEALTPHONESYS") {
                                                                                        $dup_lead=0;
                                                                                        $stmt="select list_id from vicidial_list where title='$title' and alt_phone='$alt_phone';";
                                                                                        //$rslt = $this->db->query($stmt);
											$rslt = mysqli_query($link, $stmt);
                                                                                        $pc_recs = $rslt->num_rows;
                                                                                        if ($pc_recs > 0) {
                                                                                                $dup_lead=1;
                                                                                                $row = $rslt->row();
                                                                                                $dup_lead_list = $row->list_id;
                                                                                                $dup++;
                                                                                        }
                                                                                        if ($dup_lead < 1) {
                                                                                                if (eregi("$alt_phone$title$US$list_id",$phone_list))
                                                                                                        {$dup_lead++; $dup++;}
                                                                                                }
                                                                                } #end check dups



                                                                if ( (strlen($phone_number)>6 && strlen($phone_number) < 11) and ($dup_lead<1) and ($list_id >= 100 )) {

                                                                                if (strlen($phone_code)<1) {$phone_code = '1';}
                                                                                if ($dupcheck == "TITLEALTPHONE") {
                                                                                        $phone_list .= "$alt_phone$title$US$list_id|";
                                                                                } else {
                                                                                        $phone_list .= "$phone_number$US$list_id|";
                                                                                }

                                                                                $gmt_offset = $this->lookup_gmt($phone_code,$USarea,$state,$LOCAL_GMT_OFF_STD,$Shour,$Smin,$Ssec,$Smon,$Smday,$Syear,$postalgmt,$postal_code,$owner);

                                                                                //$gmt_offset = 10.00; //ganito muna

                                                                                if (strlen($custom_SQL)>3) {
                                                                                        $stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','$list_id');";

                                                                                //$rslt = $this->db->query($stmtZ);
										$rslt = mysqli_query($link, $stmtZ);
                                                                                $lead_id = $this->db->insert_id();
                                                                                $affected_rows = $this->db->affected_rows();

                                                                                        $multistmt='';

                                                                                        $custom_SQL_query = "INSERT INTO custom_$list_id_override SET lead_id='$lead_id',$custom_SQL;";
                                                                                        //$rslt = $this->db->query($custom_SQL_query);
											$rslt = mysqli_query($link, $custom_SQL_query);
                                                                                        } else {

                                                                                                if ($multi_insert_counter > 8) {
                                                                                                        ### insert good record into vicidial_list table ###
                                                                                                        $stmtZx = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values$multistmt('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0');";

                                                                                                        //$rslt = $this->db->query($stmtZx);
													$rslt = mysqli_query($link, $stmtZx);

                                                                                                        $multistmt='';
                                                                                                        $multi_insert_counter=0;

                                                                                                } else {

                                                                                                        $multistmt .= "('','$entry_date','$modify_date','$status','$user','$vendor_lead_code','$source_id','$list_id','$gmt_offset','$called_since_last_reset','$phone_code','$phone_number','$title','$first_name','$middle_initial','$last_name','$address1','$address2','$address3','$city','$state','$province','$postal_code','$country_code','$gender','$date_of_birth','$alt_phone','$email','$security_phrase','$comments',0,'2008-01-01 00:00:00','$rank','$owner','0'),";
                                                                                                        $multi_insert_counter++;

                                                                                                }
                                                                                        }
                                                                                $good++;
                                                                        } else {

                                                                                if ($bad < 1000000)     {
                                                                                        if ( $list_id < 100 ) {
                                                                                                $resultHTML .= "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$lrow[0]| INVALID LIST ID</font><b>\n";
                                                                                        } else {
                                                                                                $resultHTML .= "<BR></b><font size=1 color=red>record $total BAD- PHONE: $phone_number ROW: |$lrow[0]| DUP: $dup_lead LIST ID: $dup_lead_list</font><b>\n";
                                                                                        }
                                                                                }
                                                                                $bad++;
                                                                        }

                                                                        if ($bad < 1) {
                                                                                $resultHTML = "<br /><font size=1 color=red>No duplicate numbers found.</font>";
                                                                        }

                                                                        $total++;

                                                                        if ($total%100==0) {

                                                                                        usleep(1000);
                                                                                        flush();
                                                                        }

                                                                        ## end checking duplicate

                                                        } //end buffer if
                                        } // end while

                                        if ($multi_insert_counter!=0) {
                                                        $stmtZ = "INSERT INTO vicidial_list (lead_id,entry_date,modify_date,status,user,vendor_lead_code,source_id,list_id,gmt_offset_now,called_since_last_reset,phone_code,phone_number,title,first_name,middle_initial,last_name,address1,address2,address3,city,state,province,postal_code,country_code,gender,date_of_birth,alt_phone,email,security_phrase,comments,called_count,last_local_call_time,rank,owner,entry_list_id) values".substr($multistmt, 0, -1).";";
                                                        //$rslt = $this->db->query($stmtZ);
							$rslt = mysqli_query($link, $stmtZ);

                                        }

                        } else {

                                $resultHTML .= "<script type=\"text/javascript\">";
                                $resultHTML .= "alert('<B>ERROR: The file does not have the required number of fields to process it.</B>');";
                                $resultHTML .= "</script>";


                        }  //dulong dulo
//                      var_dump($resultHTML);

//                      $this->extgetval($lead_file, $list_id_override, $phone_code_override, $dupcheck);
                        $data['final'] = $final;
                        $data['good'] = $good;
                        $data['bad'] = $bad;
                        $data['total'] = $total;
                        $data['dup'] = $dup;
                        $data['post'] = $post;
                        $data['resultHTML'] = $resultHTML;
                        $this->load->view('go_campaign/go_campaign_wizard_fields', $data);
                }






######### Test ##########

		$apiresults = array("result" => "success");
                $stmtUpdate = "UPDATE servers SET sounds_update='Y';";
                $rsltUpdate = mysqli_query($link, $stmtUpdate);



                } else {
                        //$data['uploadfail'] = "{$this->lang->line("go_file_type_wav")}";
		$apiresults = array("result" => "Error: Upload Failed.");

                }

?>
