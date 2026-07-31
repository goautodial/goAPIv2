<?php
declare(strict_types=1);

    /**
 * @file    	getAllAudioFiles.php
 * @brief     	Get audio files from sound directory
 * @copyright 	Copyright (c) 2018 GOautodial Inc.
 * @author		Demian Lizandro A. Biscocho
 * @author      Alexander Jim Abenoja 
 * @author      Noel Umandap 
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

	include_once ( __DIR__ . "/goAPI.php" );
    
    // Check exisiting status
	if ( !isset($log_user) || is_null($log_user) ) {
		$apiresults 								= [
			"result" 									=> "Error: Session User Not Defined."
		];
	} else { 		
		$sounds_web_directory 						= '../../sounds';
		$files 										= scandir($sounds_web_directory);
		
		if ( $files !== [] && $files !== false ) {
			$apiresults 							=  [
				"result" 								=> "success", 
				"data" 									=> $files
			];
		} else {
			$apiresults 							=  [
				"result" 								=> "error", 
				"data" 									=> $files
			];
		}
	}
?>
