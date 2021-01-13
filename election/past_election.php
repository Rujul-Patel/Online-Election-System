<?php
	
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
	
	
		//Open Database Connection
	$dbObj =   new dbManager();
	$conn = $dbObj->getConnection();

	
	



?>
<!doctype html>
<html>
	<head>
		<title>OES</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
		<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class='head'>
			Past Elections
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			date_default_timezone_set('Asia/Kolkata');
			$cur_time = date("Y-m-d H:i");
			//Retrieve Lists of Upcoming Elections
			$res = $conn->query("select * from election_master where NOW() > end_time order by start_time asc");
				
				
				
				
			
		?>
		
		
		<?php
			while($row = $res->fetch_array(MYSQLI_ASSOC))
			{
				echo "<div class='el'>";
				echo "<h4>".$row['title']."</h4>";
				echo "Voting Date ".date("d-m-Y",strtotime($row['start_time']));
				echo "<h4><a href='results.php?election=".$row['el_id']."'>View Results</a></h4>";
			}
		
		
		?>
		
	</body>
</html>




