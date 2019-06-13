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
/*

- make sure researcher ID cannot be null in contributers_beta 

*/
$experiment_folder = "../../../../experiments";

require_once '../../Code/initiateCollector.php';
require_once "../../../../sqlConnect.php";

function generateRandomString($length = 10) {
	return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

$action 		= $_POST['action'];
$experiment = $_POST['experiment'];
$user_email = $_SESSION['user_email'];


function unique_published_id($conn){
	$published_id = generateRandomString(16);
	//check that published_id doesn't already exist
	$sql = "SELECT * FROM `experiment` WHERE `published_id` = '$published_id'";
	$result = $conn->query($sql);
	if($result -> num_rows == 0){
		return $published_id;
	} else {
		unique_published_id($conn);
	}	
}

if($action == "delete"){
	//run a mysql function that does
		//remove from contributers_beta
		//remove from experiments_beta
}

if($action == "load"){	
	$sql 	= "SELECT `location` FROM `view_experiment_researchers` WHERE `name` ='$experiment' AND `email` = '$user_email'";
	$result = $conn->query($sql);
	$row 	= mysqli_fetch_assoc($result);
	$location = $row['location'];
	echo file_get_contents("$experiment_folder/$location/experiment.json"); //note the relative location difference	
}

if($action == "new"){
	
	$check_exists_sql = "SELECT COUNT(*) experiment_count FROM `view_experiment_researchers` WHERE `name`='$experiment' AND `email` = '$user_email'";
	$result = $conn->query($check_exists_sql);
	
	$row = mysqli_fetch_assoc($result);
	//$row = $result[0]->fetch_row());

	$exp_exists = $row['experiment_count'];
	echo $exp_exists;
	if ($exp_exists == 0){
				
		$location = $_POST['location'];
				
		// create experiment in beta
		$sql = "INSERT INTO `experiments_beta`(`name`, `location`) VALUES ('$experiment','$location');";
		
		if ($conn->query($sql) === TRUE) {
			
			$sql = "INSERT INTO `contributors` (`experiment_id`,`user_id`) VALUES(
				(SELECT `experiment_id` FROM `experiments_beta` WHERE `location` = '$location'), 
				(SELECT `user_id` FROM `users_beta` WHERE `email`='$user_email'));";
			
			if ($conn->query($sql) === TRUE) {
				echo "success";				
			} else {
				echo  $conn->error;;
			}			
			
		} else {
			echo  $conn->error;;
		}					
	}
}

if($action == "publish"){	 
	
	print_r ($conn);
	
	$config = array(
		"digest_alg" => "sha512",
		"private_key_bits" => 4096,
		"private_key_type" => OPENSSL_KEYTYPE_RSA,
	);
	
	$res = openssl_pkey_new($config);
	// Get private key
	openssl_pkey_export($res, $privkey);
	// Get public key
	$pubKey = openssl_pkey_get_details($res);
	$pubKey = $pubKey["key"];
	
	
	// encrypt privKey using session_key 
	$cipher = "aes-256-cbc";
	//$key = strToHex(openssl_random_pseudo_bytes(24));
	
	$local_key = $_POST['local_key'];
	
	$encrypted_privKey = openssl_encrypt ($privkey, $cipher, $local_key, true);
	
	$sql = "SELECT `experiment_id`,`published_id` FROM `experiments_beta` where `name` ='$experiment' AND `experiment_id` in (SELECT `experiment_id` from `contributors` where `researcher_id` in  (SELECT `researcher_id` from `researchers_beta` where `user_id` in (SELECT `user_id` FROM `users_beta` where `email` = '$user_email')))";
	 
	// need to change published to true;
	
	
	$result = $conn->query($sql);

	$row = mysqli_fetch_assoc($result);
	
	if($row['published_id'] == ''){		
		$published_id = unique_published_id($conn);
	} else {
		$published_id = $row['published_id'];		
	}
	
	file_put_contents("../../../../../keys/$published_id-$user_email-public.txt",$pubKey);
	file_put_contents("../../../../../keys/$published_id-$user_email-private.txt",$encrypted_privKey);
	
	
	$exp_no = $row['experiment_id'];	
	$sql = "UPDATE `experiments_beta` SET `published` = '1',`published_id` = '$published_id' WHERE `experiment_id` = '$exp_no'"; 
	
	if($conn->query($sql) === TRUE){
		echo "success";
	} else {
		echo $conn->error;
	};
	
	$release = explode("/",$_SERVER['REQUEST_URI'])[1]; //e.g. antelope/badger
	
	echo "https://www.open-collector.org/$release/sqlExperiment.php?experiment_id=$published_id|$exp_no";
	openssl_free_key($res);	// clear key	
}

if($action == "rename"){
	$new_name 		 = $_POST['new_name'];
	$original_name = $_POST['original_name'];
	
	$sql = "UPDATE `view_experiment_researchers` 
						SET `name`='$new_name'
						WHERE `name`='$original_name' AND `email`='$user_email'";
						
	if ($conn->query($sql) === TRUE) {
		echo "success";				
	} else {
		echo  $conn->error;;
	}
	
}
	

if($action == "retrieve commits"){
	$sql = "SELECT `location` FROM `view_experiment_researchers` WHERE `name`='$experiment' AND `email` = '$user_email'";
	$result = $conn->query($sql);	
	$row = mysqli_fetch_assoc($result);
	$location = $row['location'];
	
	$versions = glob("$experiment_folder/$location/v*.json");
	$versions_json = [];
	foreach($versions as $version){
		array_push($versions_json,json_decode(file_get_contents($version)));
	}
	echo json_encode($versions_json);	
}



if($action == "save"){		
	$content = $_POST['content'];	
	//print_r(json_decode($content));
	
	
	$check_exists_sql = "SELECT COUNT(*) experiment_count FROM `view_experiment_researchers` WHERE `name`='$experiment' AND `email` = '$user_email'";	
	
	

	$result 	= $conn->query($check_exists_sql);
	$row 		= mysqli_fetch_assoc($result);
	$exp_exists = $row['experiment_count'];

	echo "hello - now you have a choice - exp_exists: echo name = '$experiment' and email = '$user_email' therefore: $exp_exists";
	
	if($exp_exists == 1){ // that's what's expected
		// add or replace latest version
		$sql 	  = "SELECT * FROM `view_experiment_researchers` WHERE `name`='$experiment' AND `email` = '$user_email'";
		$result   = $conn->query($sql);
		$row 	  = mysqli_fetch_assoc($result);
		$location = $row['location'];
		echo "location = $location";		
		file_put_contents("$experiment_folder/$location/experiment.json",json_encode(json_decode($content)));
		echo "success";
			
	} else if($exp_exists > 1){
		echo "error: more than 1 experiment with this name!";
	}
	
	$result->close();

}

if($action == "unpublish"){	 
	$sql = "SELECT `experiment_id` FROM `experiments_beta` where `name` ='$experiment' AND `experiment_id` in (SELECT `experiment_id` from `contributors` where `researcher_id` in  (SELECT `researcher_id` from `researchers_beta` where `user_id` in (SELECT `user_id` FROM `users_beta` where `email` = '$user_email')))";
	
	// need to change published to true;
		
	$result = $conn->query($sql);

	$row = mysqli_fetch_assoc($result);
	
	$exp_no = $row['experiment_id'];
	
	// published_id = random code combined with exp_no; 
	
	$sql = "UPDATE `experiments_beta` SET `published`=false, `published_id`='' WHERE `experiment_id`=$exp_no"; 
	
	if($conn->query($sql) === TRUE){
		echo "success";
	} else {
		echo  $conn->error;
	};	
}





mysqli_close($conn);

?>