<?php
   #####################################################
   #### Name: goCheckCampaign.php	                ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    
    include_once ("../goFunctions.php");
    
    $list_id = mysqli_real_escape_string($link, $_REQUEST['list_id']);
    
	if(!empty($list_id)) {
		$query = "SELECT
                field_id,field_label,field_name,field_description,field_rank,field_help,field_type,
                field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,
                name_position,field_order
            FROM vicidial_lists_fields
            WHERE list_id='$list_id'
            ORDER BY field_rank,field_order,field_label;";
		$rsltv = mysqli_query($link, $query);
		
		while($fresults = mysqli_fetch_array($rsltv, MYSQLI_ASSOC)){
			$data[] = array(
				'field_id'          => $fresults['field_id'],
				'field_label'       => $fresults['field_label'],
				'field_name'        => $fresults['field_name'],
				'field_description' => $fresults['field_description'],
				'field_rank'        => $fresults['field_rank'],
				'field_help'        => $fresults['field_help'],
				'field_type'        => $fresults['field_type'],
				'field_options'     => $fresults['field_options'],
				'field_size'        => $fresults['field_size'],
				'field_max'         => $fresults['field_max'],
				'field_default'     => $fresults['field_default'],
				'field_cost'        => $fresults['field_cost'],
				'field_required'    => $fresults['field_required'],
				'multi_position'    => $fresults['multi_position'],
				'name_position'     => $fresults['name_position'],
				'field_order'       => $fresults['field_order']
			);
		}
		$apiresults = array("result" => "success", "data" => $data);
	}else{
		$err_msg = error_handle("10107");
		$apiresults = array("error_code" => "10107","result" => $err_msg);
	}
    
?>