<?php
/**
 * @file        goGetAllCustomFields.php
 * @brief       API to get all custom fields of a list
 * @copyright   Copyright (C) GOautodial Inc.
 * @author      Alexander Jim Abenoja  <alex@goautodial.com>
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

    $list_id = $astDB->escape($_REQUEST['list_id']);
    
	if(!empty($list_id)) {
		$astDB->where('list_id', $list_id);
		$astDB->orderBy('field_rank,field_order,field_label');
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
