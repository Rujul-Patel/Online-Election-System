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
	
	if($voter_logged_in == false)
	{
		die("You do not have Permission to access this page");
		//Redirect with 403 response
	}
	
	///Database Connection
	$dbObj = new dbManager();
	$conn = $dbObj->getConnection();
?>
<!doctype html>
<html>
	<head>
		<title>Profile</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
	</head>
	<body>
		
	
		<div class='head'>
			Account Settings
			
		</div>
			
			<?php
				printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
				
				
			
			$info = $conn->query("select voter_uid,first_name,last_name,email from voter_master where voterId = '".$_SESSION['oes']['voterId']."'");
			$row = $info->fetch_array(MYSQLI_ASSOC);
			
			
			
			
		?>
		
		<table>
			<caption>Profile</caption>
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
				<th>Email</th>
				<td><?php echo $row['email'];?></td>
			</tr>
		</table>
		<!--a href='../voter/manage_voter.php'>Edit Profile</a-->
		<a href='change_password.php'>Change Password</a>
		
	</body>
</html>	