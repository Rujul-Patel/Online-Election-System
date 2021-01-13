<?php
	//includes
	require_once '../include/functions/menu.php';
	require_once '../include/functions/dbManager.php';
	require_once '../include/functions/func.php';

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
	
	if($voter_logged_in == false && $admin_logged_in == false)
	{
		die("You do not have Permission to access this page");
		//Redirect with 403 response
	}

	/** FLAGS **/
	$form_submit = false;
	$msg = "";

	if(isset($_POST['cur_pass']) && isset($_POST['new_pass']))
	{
		$form_submit = true;
		
		///Database Connection
		$dbObj = new dbManager();
		$conn = $dbObj->getConnection();
			
		//Verify Old Password
				
		//Hash Password 
		$salt1 = "a&%2c";
		$salt2 = "!s1@A";
		$token = hash('sha256',$salt1.$_POST['cur_pass'].$salt2);
		
		
		
		if($voter_logged_in)
		{
			//Change password for voter account
			
			//Check if username and password matches
			$result = $conn->query("select voterId,voter_uid from voter_master where voterId = '".$_SESSION['oes']['voterId']."' and password = '".$token."'");
			
			if(mysqli_num_rows($result) == 1)
			{
				//Password Correct
				//Hash Password 
				$salt1 = "a&%2c";
				$salt2 = "!s1@A";
				$token = hash('sha256',$salt1.$_POST['new_pass'].$salt2);
				
				
				//Change Password in Database
				if($res = $conn->query("update voter_master set password = '$token' where voterId = '".$_SESSION['oes']['voterId']."'"))
				{
					$msg = "Password Updated";
				}
				else
				{
					$msg = "Error! Failed to Update Password. Please Try again";
				}
			}else
			{
				$msg = "Invalid Current Password";
			}
			
		}elseif($admin_logged_in)
		{
			//Change password for voter account
			
			//Check if username and password matches
			$result = $conn->query("select userId from administrators where userId = '".$_SESSION['oes']['adminId']."' and password = '".$token."'");
			
			if(mysqli_num_rows($result) == 1)
			{
				//Password Correct
				//Hash Password 
				$salt1 = "a&%2c";
				$salt2 = "!s1@A";
				$token = hash('sha256',$salt1.$_POST['new_pass'].$salt2);
				
				
				//Change Password in Database
				if($res = $conn->query("update administrators set password = '$token' where userId = '".$_SESSION['oes']['adminId']."'"))
				{
					$msg = "Password Updated";
				}
				else
				{
					$msg = "Error! Failed to Update Password. Please Try again";
				}
			}else
			{
				$msg = "Invalid Current Password";
			}
			
		}
		
		
		
		
		
		
		
	
	
	}


	
	
?>
<!doctype html>
<html>
	<head>
		<title>Change Credentials</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
	</head>
	<body>
		
	
		<div class='head'>
			Edit Login Settings
		</div>
		
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			echo $msg;
		?>	
		
		<form action='change_password.php' method='post'>
			<table>
				<tr>
					<th>Enter your Current Password</th>
					<td><input type='password' name='cur_pass' autofocus></td>
				</tr>
				<tr>
					<th>Enter New Password</th>
					<td><input type='password' name='new_pass'></td>
				</tr>
				<tr>
					<th>Confirm New Password</th>
					<td><input type='password' name='cnfrm_pass'></td>
				</tr>
				<tr>	
					<td><input type='submit' onclick='return confirm("Are you sure you want to change your password?")' value='Change Password'></td>
				</tr>
			</table>
		</form>
		
		
		
	</body>
</html>	