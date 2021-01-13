<?php
	//Check whether already logged in 
	session_start();
	if(isset($_SESSION['oes']))
		header("Location: ../index.php");
	
	require_once '../include/functions/dbManager.php';
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
		$result = $conn->query("select voterId,voter_uid,password,first_name,last_name from voter_master where voter_uid = '".$_POST['user']."' and password = '".$token."'");
		
		
		if(mysqli_num_rows($result) == 1)
		{
			$row = $result->fetch_array(MYSQLI_ASSOC);
			session_start();
			$_SESSION['oes']['admin'] = false;
			$_SESSION['oes']['voterId'] = $row['voterId'];
			$_SESSION['oes']['username'] = $row['first_name']." ".$row['last_name'];
			
			header("Location: ../index.php");
		
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
		<h2>Voter Login</h2>
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
					<td>Unique Voter Id</td>
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

 