<?php
    include_once ("../goFunctions.php");
	//include_once ("../licensed-conf.php");

	$seats_param = mysqli_real_escape_string($linkgo, $_REQUEST["seats"]);
	
	if(!empty($seats_param) && $seats_param > 0){
	        $seats = $seats_param;
        }else{
                $seats = 0;
        }

        $query = "SELECT * FROM settings WHERE setting = 'GO_licensed_seats' LIMIT 1;";
        $rsltv = mysqli_query($linkgo, $query);
        $exist = mysqli_num_rows($rsltv);

                if($exist <= 0){
                        $create_default_query = "INSERT INTO settings (setting, context, value) VALUES('GO_licensed_seats', 'module_licensedSeats', '$seats');";
                        $exec_create_default = mysqli_query($linkgo, $create_default_query);

                        if($exec_create_default){
				if($seats > 0)
					$msg_seats = $seats;
				else
					$msg_seats = "Unlimited";
                                
				$apiresults = array("result" => "success", "msg" => "Created ( $msg_seats ) Default Licensed Seats.");
                        }else{
                                $apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => mysqli_error($exec_create_default) );
                        }
                }else{
			$update_query = "UPDATE settings SET value = '$seats' WHERE setting = 'GO_licensed_seats';";
			$exec_update = mysqli_query($linkgo, $update_query);
			
			if($exec_create_default){
                                if($seats > 0)
                                        $msg_seats = $seats;
                                else
                                        $msg_seats = "Unlimited";

                                $apiresults = array("result" => "success", "msg" => "Updated ( $msg_seats ) Licensed Seats.");
                        }else{
                                $apiresults = array("result" => "error", "msg" => "An error has occured, please contact the System Administrator to fix the issue.", "query" => mysqli_error($exec_update) );
                        }

		}

?>
