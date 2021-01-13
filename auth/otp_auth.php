<?php
	//includes
	require_once '../include/functions/menu.php';
	require_once '../include/functions/dbManager.php';
	require_once '../include/functions/func.php';
	require_once '../include/functions/mail_func.php';
	require_once '../include/functions/dbManager.php';
	
	$voter_logged_in = false;
	$admin_logged_in = false;
	$username = "";
	
	//Check if Voter or ADmin Logged in
	session_start();
	if(isset($_SESSION['oes']))
	{
		if($_SESSION['oes']['admin'] == true)
			$admin_logged_in = true;
		else
			$voter_logged_in = true;
		$username = $_SESSION['oes']['username'];
		
	}
	
	if($voter_logged_in == false)
	{
		die("You do not have Permission to access this page");
		//Redirect with 403 response
	}
	
	
	//Open Database Connection
	$dbObj = new dbManager();
	$conn = $dbObj->getConnection();

	
	$send_otp = 1;
	$validate_otp = -1;
	$sel_election_id = -1;	//No Election Selected
	
	//Retrieve Voter Details and verify if election is valid
	$res = $conn->query("select voter_uid,first_name,last_name,email from voter_master where voterId = ".$_SESSION['oes']['voterId']);
	$row = $res->fetch_array(MYSQLI_ASSOC);
	
	date_default_timezone_set('Asia/Kolkata');
	$cur_time = date("Y-m-d H:i");
	
	
	$msg = "";
	$auth_fail = false;
	if(isset($_POST['otp']))
	{
		$result = $conn->query("update vote_data set otp_validated = 1 where elId = '".$_GET['election']."' and voterId = '".$_SESSION['oes']['voterId']."'and otp = '".$_POST['otp']."' and otp_validated = 0 and NOW() <= DATE_ADD(otp_time,INTERVAL 15 MINUTE)");
		if(mysqli_affected_rows($conn) == 0)
		{
			//Authentication Failed
			$auth_fail = true;
			$send_otp = -1;
			$validate_otp = 1;
		}else
		{
			header("Location:../election/vote.php?election=".$_GET['election']);
		}
	}
		
	
	
	
	if(isset($_POST['authenticate']))
	{
		$send_otp = -1;
		$validate_otp = 1;
		
		// generate OTP
		$otp = rand(100000,999999);
		// Send OTP
		$mail_status = sendMail($row['email'],"Your One Time Password for Voting Authentication is ".$otp,"OTP for Online Election");
		
		if($mail_status == 1) {
			$result = mysqli_query($conn,"update vote_data set otp = '".$otp."',otp_time = '".$cur_time."' where el_id = '".$_GET['election']."' and voter_id = '".$_SESSION['oes']['voterId']."'");
			
		}else
		{
			die("Failed");
		}
	}
		
	
	
	
 
	
	
	if(isset($_GET['election']))
	{
		$sel_election_id = $_GET['election'];
	}
	
	
 
	
 
 
?>
<!doctype html>
<html>
	<head>
		<title>Vote</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
		<script src='tbl.js'></script>
	</head>
	<body>
		<div class='head'>
			Verify Yourself
			
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			if($sel_election_id == -1)
				die("Error");
		?>
		
		
		<?php if($send_otp == 1){ ?>
		<h3>An OTP will be send to the email address described below. If you wish to proeceed to voting, click Send OTP Button Below</h3>
		<form action='otp_auth.php?election=<?php echo $sel_election_id; ?>' method='post'>
			<input type='hidden' name='authenticate' value='1'>
			<table class='rep'>
				<tr>
					<th>Voter Id</th>
					<td><?php echo $row['voter_uid'];?></td>
				</tr>
				<tr>
					<th>First Name</th>
					<td><?php echo $row['first_name'];?></td>
				</tr>
				<tr>
					<th>Last Name</th>
					<td><?php echo $row['last_name'];?></td>
				</tr>
				<tr>
					<th>Email ID</th>
					<td><?php echo $row['email'];?></td>
				</tr>
				<tr>
					<th colspan=2><input type='submit' value='Send OTP'></td>
				</tr>
			</table>
		</form>
		<?php } ?>
		
		
		
		<?php if($validate_otp == 1) { 
			if($auth_fail == true)
				echo "<h3>Invalid OTP</h3>";
		?>
		
		<h3>On OTP has been sent at <?php echo $row['email']; ?> 
		<form action='otp_auth.php?election=$sel_election_id' method='post'>
			<table>
				<tr>
					<th>Enter OTP</th>
					<td><input type='text' name='otp'></td>
				</tr>
				<tr>
					<td colspan=2><input type='submit' value='Validate OTP'></td>
				</tr>
			</table>
		</form>
		
		
		<?php } ?>
		
		
		
	</body>
</html>














