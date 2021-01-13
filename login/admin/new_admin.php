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
	
	
	$frm_submit = false;
	$msg = "";
	if(isset($_POST['admin_name']) && isset($_POST['password']))
	{
		$frm_submit  = true;
		
		///Database Connection
		$dbObj = new dbManager();
		$conn = $dbObj->getConnection();
		
		
		
		//Hash Password 
		$salt1 = "a&%2c";
		$salt2 = "a&%2c";
		$token = hash('sha256',$salt1.$_POST['password'].$salt2);
	
		
		
		$stmt = $conn->prepare("insert into administrators values(null,?,?)");
		$stmt->bind_param("ss",$_POST['admin_name'],$token);
		
		if($stmt->execute())
			$msg = "New Administrator Created Successfully";
		else
			$msg = "Failed to create new Admin Account";
		
		
	}
	
	
	
	
	
	
?>
<!doctype html>
<html>
	<head>
		<title>Create Admin Account</title>
		<link rel='stylesheet' href='../../include/styles/general.css'>
	</head>
	<body>
		
	
		<div class='head'>
			Create New Administrator
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			
			echo "<h3>".$msg."</h3>";
		?>
		
		<form action='new_admin.php' method='post'>
			<table class='frm'>
				<tr>
					<th>Adminstrator Name</th>
					<td><input type='text' name='admin_name' autofocus></td>
				</tr>
				<tr>
					<th>Password</th>
					<td><input type='password' name='password'></td>
				</tr>
				<tr>
					<th>Confirm Password</th>
					<td><input type='password' name='cnfrm_pass'></td>
				</tr>
				<tr>
					<td><input type='submit' value='Create Account' onclick='return confirm("Create New Admin?")'></td>
					<td><input type='button' value='Cancel'></td>
				</tr>
			</table>
		</form>
		
	</body>
</html>
