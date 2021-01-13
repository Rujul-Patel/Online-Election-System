<?php
	//includes
	require_once '../../include/functions/menu.php';
	require_once '../../include/functions/dbManager.php';
	require_once '../../include/functions/func.php';

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
	
	if($admin_logged_in == false)
	{
		die("You do not have Permission to access this page");
		//Redirect with 403 response
	}
?>
<!doctype html>
<html>
	<head>
		<title>Admin Settings</title>
		<link rel='stylesheet' href='../../include/styles/general.css'>
	</head>
	<body>
		
	
		<div class='head'>
			Admin
			
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			
			
		?>
		
		<a href='../change_password.php'>Change Password</a>
		<a href='new_admin.php'>Add another Admin Account</a>
		
		
	</body>
</html>	