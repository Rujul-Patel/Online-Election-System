<?php
	//Check whether admin is already logged in 

	
	require_once '../../include/functions/dbManager.php';
	$form_submitted = false;	//flag
	
	if(isset($_POST['user']) && isset($_POST['password']))
	{			
		$form_submitted = true;
		
		//New Database Connection
		$dbObj = new dbManager();
		$conn = $dbObj->getConnection();
		
		//Hash Password 
		$salt1 = "a&%2c";
		$salt2 = "!s1@A";
		$token = hash('sha256',$salt1.$_POST['password'].$salt2);
		
	
		//Check if username and password matches
		$result = $conn->query("select userId,username,password from administrators where username = '".$_POST['user']."' and password = '".$token."'");
		
		
		if(mysqli_num_rows($result) == 1)
		{
			$row = $result->fetch_array(MYSQLI_ASSOC);
			session_start();
			$_SESSION['oes']['admin'] = true;
			$_SESSION['oes']['adminId'] = $row['userId'];
			$_SESSION['oes']['username'] = $row['username'];
			
			header("Location: ../../index.php");
		
			die();
		}
	}

?>
<!doctype html>
<html>
	<head>
		<title>Login</title>
	</head>
	<body>
		<h2>Administrator Login</h2>
		<?php
			//printMenu(false,'');
			if($form_submitted)
			{
				echo "<h4>Invalid Username or Password</h4>";
			}
		?>
		
		<form action='login.php' method='post'>
			<table class='frm'>
				<tr>
					<td>Username</td>
					<td><input type='text' name='user' autofocus></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input type='password' name='password'></td>
				</tr>
				<tr>
					<td colspan=2><input type='submit' value='Login'></td>
				</tr>
			</table>
		</form>
	</body>
</html>

 