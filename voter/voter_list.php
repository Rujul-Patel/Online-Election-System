<?php
	//includes
	require_once '../include/functions/menu.php';
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

	
	$dbObj = new dbManager();
	$conn = $dbObj->getConnection();
	
	
	$result = $conn->query('select v.voter_uid,v.first_name,v.last_name,v.email,g.groupName from voter_master v left join voter_groups g on v.groupId = g.groupId');
	
	
	
	
	


?>
<!doctype html>
<html>
	<head>
		<title>OES</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
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
			Voter List
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
		?>
		
		<table class='rep'>
			<tr>
				<th>Sr. No</th>
				<th>Voter Id</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Email</th>
			</tr>
			
			<?php
				$cnt = 1;
				while($row = $result->fetch_array(MYSQLI_ASSOC))
				{
					echo "<tr>";
					echo "<th>$cnt</th>";
					echo "<td>".$row['voter_uid']."</td>";
					echo "<td>".$row['first_name']."</td>";
					echo "<td>".$row['last_name']."</td>";
					echo "<td>".$row['email']."</td>";
					echo "</tr>";
					$cnt++;
					
				}
			
			
			?>
		</table>
	
	
	
	</body>
</html>




