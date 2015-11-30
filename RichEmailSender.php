<?php

/*
 * Allows sending emails with attachments. The followig arguments are required:
 * First arg: Recipient's email address
 * Second arg: Sender's email address
 * Third arg: Email Subject
 * Fourth arg: Message Body
 * Fifth arg:  IsHTML (1 or 0)
 * Sixth arg: Full path to attachment
 */

require 'PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;
$mail->set("Organization" , "RiskMP");
$mail->set("MIME-Version" , "1.0");
$mail->set("X-Mailer", "PHP 5.x");
$mail->From = $argv[2];
$mail->FromName = "The RiskMP Team";
$mail->addAddress($argv[1]);
$mail->AddBCC("andrew@esctt.com");
$mail->addReplyTo($argv[2]);
if (isset($argv[6])) { //check for attachment
    $mail->addAttachment($argv[6]);
}
$mail->isHTML($argv[5]);
$mail->Subject = $argv[3];
$mail->Body = $argv[4];
//$mail->AltBody($argv[4]); //optional plain text body for non-HTML clients
if ($mail->send()) {
    echo "Message was sent successfully.\n";
} else {
    echo "Message could not be sent.\n";
    echo "Mailer Error: " . $mail->ErrorInfo ."\n";
}
