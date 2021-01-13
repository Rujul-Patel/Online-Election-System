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
	
	/***
		PUBLIC PAGE
	****/
	
	$sel_election_id = -1;	//No Election Selected
	if(isset($_GET['election']))
	{
		$sel_election_id = $_GET['election'];
		
		//Open Database Connection
		$dbObj = new dbManager();
		$conn = $dbObj->getConnection();
		
		
		$msg =  '';
		//Retreive Election Master Information
		$masterInfo = $conn->query("select title,start_time,end_time from election_master where el_id = $sel_election_id");
		if(mysqli_num_rows($masterInfo)== 0)
		{
			$msg = "Election Not Found";
		}
		$mInfo = $masterInfo->fetch_array(MYSQLI_ASSOC);
				
		
		$sTime = explode(" ",$mInfo['start_time']);
		$eTime = explode(" ",$mInfo['end_time']);
			
	}
	
?>
<!doctype html>
<html>
	<head>
		<title>Vote</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
		<script src='tbl.js'></script>
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
			Election Name
			
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			if($sel_election_id == -1)
			{
				die("<a href='#'>No Election Selected</a>");
			}		
			if($msg <> "")
				die($msg);
				
		?>
		
		
		<table>
			<tr>
				<th>Title</th>
				<td><?php echo $mInfo['title']; ?></td>
			</tr>
			<tr>
				<th>Voting Date</th>
				<td><?php echo date('d-m-Y',strtotime($sTime[0])); ?></td>
			</tr>
			<tr>
				<th>Voting Time</th>
				<td><?php echo date('g:i A',strtotime($sTime[1]))." to ".date('g:i A',strtotime($eTime[1])); ?></td>
			</tr>
			
		</table>
		
		
		<?php
			//Retreive Election Post and Candidates Details
			$qry = "select p.post_num,p.post_title,g.groupName from election_structure p inner join voter_groups g on g.groupId = p.allowed_group and  p.el_id = $sel_election_id";
			$res = $conn->query($qry);
				
			//Loop For Each Post
			while($row = $res->fetch_array(MYSQLI_ASSOC))
			{
					
				echo "<div class='el_post'>";
				echo "<h4>".$row['post_title']."</h4>";
					
					
				//Retrieve Candidates From that post
				$subqry = "select candidateName,votesAcquired from election_candidates where el_id = $sel_election_id and post_no='".$row['post_num']."' order by votesAcquired desc";
				$subres = $conn->query($subqry);
				while($subrow = $subres->fetch_array(MYSQLI_ASSOC))
				{
					echo "<div class='el_cndt'>";
					echo $subrow['candidateName'];
					echo $subrow['votesAcquired'];
					echo "</div>";
				}
						
				echo "</div>";	
						
					
			}
					

			
			
			
		?>
			
		
		
		
		
		
		
		
		
		
		
	</body>
</html>	