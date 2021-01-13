<?php
	/********************************************************
		Registers New Election in Database
	********************************************************/

	//includes
	require_once '../include/functions/menu.php';
	require_once '../include/functions/dbManager.php';
	require_once '../include/functions/func.php';
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
	
	if($admin_logged_in == false)
	{
		die("You do not have Permission to access this page");
		//Redirect with 403 response
	}
	
	
	
	$el_id;
	$msg = "";
	$fail = false;
	if(isset($_POST['start_time']) && isset($_POST['el_title']) && isset($_POST['end_time']) && isset($_POST['el_date']))
	{
		
		if($_POST['start_time'] == "" or $_POST['end_time'] == "")
		{
			$msg = "Invalid Time";
		}
		elseif($_POST['el_title'] == "")
		{
			$msg = "Invalid Title";
		}elseif($_POST['el_date'] == "")
		{
			$msg = "Invalid Date";
		}
		else{
			//Open Database Connection
			$dbObj =   new dbManager();
			$conn = $dbObj->getConnection();
			
			//Calculate Start timestamp and end timestamp
			$s_time = $_POST['el_date']." ".$_POST['start_time'];
			$e_time = $_POST['el_date']." ".$_POST['end_time'];
			
			
			//Create New Election
			$stmt = $conn->prepare("insert into election_master(title,start_time,end_time,isComplete,description) values(?,?,?,0,?)");
			$stmt->bind_param("ssss",$_POST['el_title'],$s_time,$e_time,$_POST['desc']);
			
			if($stmt->execute())
			{
				$el_id = $conn->insert_id;
				
				//Redirect to Manage Election
				header("Location: manage_election.php?election=".$el_id);
			}
			else
			{
				$msg = "Error";
			}
				
		}
		
		
		
		
	}
	

?>

<!doctype html>
<html>
	<head>
		<title>New Election</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
	</head>
	<body>
		<div class='head'>
			Setup New Election
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			echo $msg;
		?>
		
		
		<div class='main'>
			<h3>Step 1.  Enter Election Details</h3>
			<hr>
			
			<form action='new_election.php' method='post'>
				<table class='frm'>
					<tr>
						<th>Election Title</th>
						<td><input type='text' name='el_title' autofocus></td>
					</tr>
					<tr>
						<th>Voting Date</th>
						<td><input type='date' name='el_date'></td>
					</tr>
					<tr>
						<th>Voting Start Time</th>
						<td><input type='time' name='start_time' placeholder='time'></td>
					</tr>
					<tr>
						<th>Voting End Time</th>
						<td><input type='time' name='end_time'></td>
					</tr>
					<tr>
						<td><input type='submit' value='Create Election'></td>
						<td><input type='button' value='Cancel'></td>
					</tr>
				</table>
			</form>
			
			
			
			
			
		</div>
		 
	</body>
</html>
		
		