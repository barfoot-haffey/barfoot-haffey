<?php

require("../sqlConnect.php");

//print_r($_POST);

$sql = "SELECT * FROM `institutions`";
$result = $conn->query($sql);
$institutions_array = [];
while($row = $result->fetch_assoc()) {
  array_push($institutions_array,$row['institute']);
}

echo json_encode($institutions_array);


/* while() {
	array_push($institutions,$row);
}
 */

//print_r($result);
/*
if($result -> num_rows > 0){
  $sql = "UPDATE `participant_countries` `country_table` SET country_table.frequency = country_table.frequency + 1 WHERE `country` = '$country_name'"; 
} else {
  $sql = "INSERT INTO `participant_countries` (`country`,`code`,`frequency`) VALUES ('$country_name','$country_id',1)";  
}
if ($conn->query($sql) === TRUE) {
  echo "success";				
} else {
  echo  $conn->error;;
}
*/


/*
$sql = "SELECT * FROM `participant_countries`";
$row = $result->fetch_assoc()


while() {
	array_push($institutions,$row);
}

echo json_encode($institutions);
*/

?>