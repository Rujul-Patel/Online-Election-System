<?php
	//includes
	require_once '../include/functions/menu.php';
	require_once '../include/functions/dbManager.php';
	require_once '../include/functions/func.php';
	require_once '../include/functions/dbManager.php';
	require_once '../include/functions/mail_func.php';
	
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
	$is_new_voter = true;		//Flag to differentiate interface between Create New Voter and Modify Voter 
	if($admin_logged_in == false)
	{
		die("You do not have Enough Permission to view this page.");
		$is_new_voter = false;
		//Redirect with 403 response
	}
	
	
	///Database Connection
		$dbObj = new dbManager();
		$conn = $dbObj->getConnection();
	
	$msg = "";
	$fail = false;
	//Save Form to Database
	if(isset($_POST['uid']) && isset($_POST['fname']))
	{			
		//Generate a Random Password
		$new_pass = rand(100000,999999);
		
		
		//Mail Password to voter
		if(sendMail($_POST['email'],"Welcome to Online Voting System.Your Password is $new_pass","Password for OES") != 1)
		{
			$fail = true;
			$msg = "Error! Your Email may be Invalid";
		}
		else
		{
			
			//Hash Password 
			$salt1 = "a&%2c";
			$salt2 = "!s1@A";
			$token = hash('sha256',$salt1.$new_pass.$salt2);
			
			$qry = "insert into voter_master values(null,?,?,?,?,?,?)";
			$stmt = $conn->prepare($qry);
			$stmt->bind_param("ssssds",$_POST['uid'],$_POST['fname'],$_POST['lname'],$_POST['email'],$_POST['group'],$token);
			
			if($stmt->execute())
			{
				$msg = "New Voter Created and  password sent to voter via Email";
			}
			else
			{
				$msg = "Failed to create Voter";
				$fail = true;
			}
			$stmt->close();
			
		}
		
		
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	///Strings For New Voter and Modify Voter
	$labels;
	if($is_new_voter)
	{
		//Labels For New Voter
		$labels = array(
		"html_title"=>"Manage Voter",
		"html_head"=>"Create New Voter",
		"btn_val"=>"Create Voter"
		);
	}else
	{
		//Labels For Modify Voter
		$labels = array(
		"html_title"=>"Manage Voter",
		"html_head"=>"Modify Voter",
		"btn_val"=>"Save Changes"
		);
	}
	
?>
<!doctype html>
<html>
	<head>
		<title><?php echo $labels['html_title']; ?></title>
		<link rel='stylesheet' href='../include/styles/general.css'>
	</head>
	<body>
		<div class='head'>
			<?php echo $labels['html_head']; ?>
		</div>
		
			<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			echo $msg;
				
			
		?>
		
		
		<form action='manage_voter.php' method='post'>
			<input type='hidden' name='new_voter' value='<?php if($is_new_voter)echo 1;else echo 0;?>'>
			<table class='frm'>
				<tr>
					<th>Unique Voter Id</th>
					<td><input type='text' name='uid' autofocus></td>
				</tr>
				<tr>
					<th>First Name</th>
					<td><input type='text' name='fname'></td>
				</tr>
				<tr>
					<th>Last Name</th>
					<td><input type='text' name='lname'></td>
				</tr>
				<tr>
					<th>Email Id</th>
					<td><input type='text' name='email'></td>
				</tr>
				<tr>
					<th>Group</th>
					<td>
						<select name='group'>
							<?php fillSelectBox($conn,"select gc.groupId,if(gc.parent = -1,gc.groupName,concat(concat(gp.groupName,'/'),gc.groupName)) as groupName from voter_groups gc left join voter_groups gp on gc.parent = gp.groupId",""); ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><input type='submit' value='<?php echo $labels['btn_val']; ?>'></td>
					<td><input type='button' value='Cancel'></td>
				</tr>
			</table>
		</form>
		
	</body>
</html>