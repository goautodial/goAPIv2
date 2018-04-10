<?php
   #####################################################
   #### Name: goCheckCampaign.php	                ####
   #### Description: API to check for existing data ####
   #### Version: 4.0                                ####
   #### Copyright: GOAutoDial Ltd. (c) 2011-2016    ####
   #### Written by: Alexander Jim H. Abenoja        ####
   #### License: AGPLv2                             ####
   #####################################################
    $list_id = mysqli_real_escape_string($link, $_REQUEST['list_id']);
    
	if(!empty($list_id)) {
		$astDB->where('list_id', $list_id);
		$astDB->orderBy('field_rank');
		$astDB->orderBy('field_order');
		$astDB->orderBy('field_label');
		$rsltv = $astDB->get('vicidial_lists_fields', null, 'field_id,field_label,field_name,field_description,field_rank,field_help,field_type,field_options,field_size,field_max,field_default,field_cost,field_required,multi_position,name_position,field_order');
		
		foreach($rsltv as $fresults){
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