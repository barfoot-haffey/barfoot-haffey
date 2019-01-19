<?php
/*  Collector (Garcia, Kornell, Kerr, Blake & Haffey)
    A program for running experiments on the web
    Copyright 2012-2016 Mikey Garcia & Nate Kornell


    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License version 3 as published by
    the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>
 		
		Kitten release (2019) author: Dr. Anthony Haffey (a.haffey@reading.ac.uk)		
*/

$trialtypes_folder = "../../../../../trialtypes";
require_once '../../../Code/initiateCollector.php';
require_once "../../../../../sqlConnect.php";

$user_email = $_SESSION['user_email'];
$action = $_POST['action'];

if($action == 'initiate'){

	$default_trialtypes = new stdClass();
	$default_trialtypes_list = file_get_contents("default/list.txt");
	$default_trialtypes_list = explode(",",$default_trialtypes_list);
	
	foreach($default_trialtypes_list as $name){		
		$default_trialtypes -> $name = file_get_contents("default/$name.html");
	}
	echo json_encode($default_trialtypes);	
}

if($action == 'rename'){
	$original_name = $_POST['original_name'];
	$new_name 		 = $_POST['new_name'];
	
	$sql = "UPDATE `trialtypes` SET `name` = '$new_name' WHERE `name`='$original_name' AND `trialtype_id` in (SELECT `trialtype_id` FROM `trialtypes_researchers` where `user_id` = (SELECT `user_id` FROM `users_beta` WHERE `email` = '$user_email'))";
	
	if ($conn->query($sql) === TRUE) {
		echo "<b>Success</b>: trialtype renamed from $original_name to $new_name";
	}	else {
		echo  $conn->error;;
	}		
}

if($action == 'save'){
	
	$content  = $_POST['content'];	
	$name 	  = strtolower($_POST['name']);	
	
	if($user_email == 'preview' | $user_email == ''){
		exit("You appear to have been logged out");
	}
	
	//check if trialtype_name and user_email both exist
	
	$sql = "SELECT * FROM `view_trialtype_name_emails` WHERE `name`='$name' AND `email` = '$user_email'";	
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();	
	
	if(isset($row['location'])){		
		$location = $row['location'];		
		file_put_contents("$trialtypes_folder/$location/trialtype.html",$content);
		echo "<b>Success</b>: Trialtype at location $location updated";
	} else {
		
		$current_folders = array_diff(scandir($trialtypes_folder), array('..', '.'));
		
		if(count($current_folders) > 1){
		
			$arr2 = range(1,max($current_folders));                                                    

			$missing = array_diff($arr2,$current_folders); // (3,6)
			
		} else {
			$missing = 0;
		}
		
		if(count($missing) == 0){
			$new_folder_no = count($current_folders) + 1;					
		} else {
			$new_folder_no = min($missing);
		}
		
		//echo json_encode($content);
		mkdir("$trialtypes_folder/$new_folder_no");
		file_put_contents("$trialtypes_folder/$new_folder_no/trialtype.html",$content);
		$sql = "INSERT INTO `trialtypes`(`location`,`public`,`name`) VALUES ('$new_folder_no', 'U' ,'$name')";
		
		if ($conn->query($sql) === TRUE) {    
			$sql = "INSERT INTO `trialtypes_researchers` 
				(`trialtype_id`,`user_id`) VALUES(
				(SELECT `trialtype_id` FROM `trialtypes` WHERE `location` = '$new_folder_no'),
				(SELECT `user_id` FROM `users_beta` WHERE `email`='$user_email'));";
			if ($conn->query($sql) === TRUE) {
				echo "<b>Success</b>: new trial type created";
			}	else {
				echo  $conn->error;;
			}		
		} else {		
			echo  $conn->error;;		
		}						
	}	
}

mysqli_close($conn);
?>