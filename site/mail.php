<?php
require('PHPMailer/PHPMailerAutoload.php');

$email = $_POST['email'];
$mail = new PHPMailer(true);  
try {
	$mail->SMTPDebug = 0;                                       
	$mail->isSMTP();                                            
	$mail->Host       = 'smtp.gmail.com;';                    
	$mail->SMTPAuth   = true;                             
	$mail->Username   = 'dhruvilnaik5828@gmail.com';                 
	$mail->Password   = 'leeyxuvxumagtnli';
	$mail->SMTPSecure = 'tls';                              
	$mail->Port       = 587;  

	$mail->setFrom('dhruvilnaik5828@gmail.com', 'Name');           
	$mail->addAddress($email);
	$mail->addAddress($email, 'Name');

	$mail->isHTML(true);                                  
	$mail->Subject = 'Form Submitted successfully';
	$mail->Body    = 'Hi '.$email.'. Your Account has been created successfully ';
	$mail->AltBody = 'Body in plain text for non-HTML mail clients';
	$mail->send();
	echo "Your Account has been created successfully!";
} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
