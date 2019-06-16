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


require_once 'Code/initiateCollector.php';

function encrypt_decrypt($action, $string,$local_key,$this_iv) {
  $output = false;
  $encrypt_method = "AES-256-CBC";
  $secret_key = $local_key;
  $secret_iv = $this_iv;
  // hash
  $key = hash('sha256', $secret_key);
  
  // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
  $iv = substr(hash('sha256', $secret_iv), 0, 16);
  if ( $action == 'encrypt' ) {
    $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output = base64_encode($output);
  } else if( $action == 'decrypt' ) {
    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
  }
  return $output;
}

$all_data 	 		 = $_POST['all_data'];
$participant 		 = $_SESSION['participant_code'];
$completion_code = $_SESSION['completion_code'];
$experiment_id	 = $_SESSION['experiment_id'];

// identify researchers here
//mysql to find researchers who contributed to this experiment...?

require_once "../../mailerPassword.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';


$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
try {
  //Server settings
  $mail->SMTPDebug = 0;                                 // Enable verbose debug output
  $mail->isSMTP();                                      // Set mailer to use SMTP
  $mail->Host = 'ocollector.org';  											// smtp2.example.com, Specify main and backup SMTP servers
  $mail->SMTPAuth = true;                               // Enable SMTP authentication
  $mail->Username = "$mailer_user";											// SMTP username
  $mail->Password = "$mailer_password";                 // SMTP password
  $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
  $mail->Port = 587;                                    // TCP port to connect to

	
	//Recipients
  $mail->setFrom('no-reply@ocollector.org', 'Open-Collector');
    
  $mail->addAddress("anthony.haffey@gmail.com");     // Add a recipient	
  
	//$researcher = $_SESSION['researchers'][0];
  //$mail->addAddress($researcher);     // Add a recipient	
  
  //$mail->AddStringAttachment($encrypted_data,"encrypted_$experiment_id-$participant.txt");
  $body_alt_body = "The experiment_id is: $experiment_id <---";
  
  //Content
  $mail->isHTML(true);                                  // Set email format to HTML
  $mail->Subject = "Collector - completed with code:";
  $mail->Body    = $body_alt_body;
  $mail->AltBody = $body_alt_body;

  $mail->send();
	echo "Your encrypted data has been emailed to the researcher(s). Completion code is: <br><br><b> $completion_code </b><br><br> Warning - completion codes may get muddled if you try to do multiple experiments at the same time. Please don't. encrypted data = $encrypted_data";
  //echo "Your encrypted data has been emailed to the researcher(s). Completion code is: <br><br><b> $completion_code </b><br><br> Warning - completion codes may get muddled if you try to do multiple experiments at the same time. Please don't. encrypted data = $encrypted_data";
	
	/*
  //Recipients
    
  
  
  $public_key = file_get_contents("../../simplekeys/public_$researcher.txt");
  $cipher = "aes-256-cbc";
  
  
  $symmetric_key = openssl_random_pseudo_bytes(32);		
  $this_iv			 =  openssl_random_pseudo_bytes(16);
  
  $encrypted_data = encrypt_decrypt("encrypt",$all_data,$symmetric_key,$this_iv);
  
  openssl_public_encrypt ($symmetric_key, $encrypted_symmetric_key, $public_key); 
  
  file_put_contents("../../simplekeys/symmetric-$researcher-$experiment_id-$participant.txt",$encrypted_symmetric_key);
  file_put_contents("../../simplekeys/iv-$researcher-$experiment_id-$participant.txt",$this_iv);
  
  $mail->AddStringAttachment($encrypted_data,"encrypted_$experiment_id-$participant.txt");
  $body_alt_body = "A participant just completed your task! <br><br> Participant: $participant  <br>Completion Code: $completion_code <br><br> Go to <b>https://www.ocollector.org/".$_SESSION['version']."/</b> and click on the <b>Data</b> tab to decrypt this file.";
  
  //Content
  $mail->isHTML(true);                                  // Set email format to HTML
  $mail->Subject = "Collector - $participant completed with code: $completion_code";
  $mail->Body    = $body_alt_body;
  $mail->AltBody = $body_alt_body;

  $mail->send();
  
	*/	
} catch (Exception $e) {
  echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
}
/*
*/
?>