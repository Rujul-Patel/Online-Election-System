<?php
	//includes
	require_once 'include/functions/menu.php';

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




?>
<!doctype html>
<html>
	<head>
		<title>OES</title>
		<link rel='stylesheet' href='include/styles/general.css'>
	</head>
	<body>
		<div class='head'>
			<h2>Online Election System</h2>
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
		?>
		
	
	
	
	
	</body>
</html>




