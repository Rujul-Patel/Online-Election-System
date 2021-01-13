<?php	
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	function sendMail($email,$msg,$hdr) {
		
	echo $msg."<br>";
	echo $hdr."<br>";
	echo $email."<br>";
			
		require_once 'phpMailer/PHPMailer.php';
	require_once 'phpMailer/Exception.php';
	require_once 'phpMailer/SMTP.php';
	require_once 'phpMailer/OAuth.php';
	require_once 'phpMailer/POP3.php';

		
	
		$message_body = $msg;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPDebug = 2;
		$mail->SMTPAuth = TRUE;
		$mail->SMTPSecure = 'ssl'; // tls or ssl
		$mail->Port     = "465";
		$mail->Username = "";
		$mail->Password = "";
		$mail->Host     = "";
		$mail->From = "Online";
		$mail->Mailer   = "smtp";
		
		
		
		$mail->SetFrom("oes.notify@gmx.com");
		$mail->AddAddress($email);
		$mail->Subject = $hdr;
		$mail->MsgHTML($message_body);
		$mail->IsHTML(true);	
		
		
		$result = $mail->Send();
		
		return $result;
	}
?>