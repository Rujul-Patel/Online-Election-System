<?php
	/********************************************************
		Modify Election
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
	
	
	//Modify Election
	$sel_election_id = -1;
	if(isset($_GET['election']))
	{
		$sel_election_id = $_GET['election'];
	}
	
	
	//Open Database Connection
	$dbObj =   new dbManager();
	$conn = $dbObj->getConnection();
	
	
	//Add Post Submission
	if(isset($_POST['post_title']) && isset($_POST['post_cnd']))
	{
		if($_POST['new_post'] == '1')
		{
			//Add New Post to Election
			
			//Retrieve Next Post Number
			$res = $conn->query("select if(max(post_num) is null,0,max(post_num)) as post_num from election_structure where el_id = '".$_POST['election_id']."'");
			$row = $res->fetch_array(MYSQLI_NUM);
			$row[0]++;
			//Add Post
			$stmt = $conn->prepare("insert into election_structure(el_id,post_num,post_title,allowed_group) values(?,?,?,?)");
			
			$stmt->bind_param("ddsd",$_POST['election_id'],$row[0],$_POST['post_title'],$_POST['post_grp']);
			if(!$stmt->execute())
			{
				die("Error");
			}
			
			
			//Add Candidates
			
			
			$stmt = $conn->prepare("insert into election_candidates(el_id,post_no,candidateName) values(?,?,?)");
			
			foreach($_POST['post_cnd'] as $cnd)
			{
				$stmt->bind_param("dds",$_POST['election_id'],$row[0],$cnd);
				$stmt->execute();
					
			}
			
			
			//Redirect
			header("Location: manage_election.php?election=".$_POST['election_id']);
		}
		
		
		
		
		
		
		
		
	}
	
	$msg = "";	
	//Modify Form Master Data 
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
			
			//Calculate Start timestamp and end timestamp
			$s_time = $_POST['el_date']." ".$_POST['start_time'];
			$e_time = $_POST['el_date']." ".$_POST['end_time'];
			
			
			//Create New Election
			$stmt = $conn->prepare("update election_master set title = ?,start_time =?,end_time=? where el_id = ?");
			$stmt->bind_param("sssd",$_POST['el_title'],$s_time,$e_time,$_POST['election_id']);
			
			if($stmt->execute())
			{
				$el_id = $_POST['election_id'];
				
				//Redirect to Manage Election
				header("Location: manage_election.php?election=".$el_id);
			}
			else
			{
				$msg = "Error";
			}
				
		}
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
		
	
	//Retreive Election Master Information
	$masterInfo = $conn->query("select title,start_time,end_time from election_master where el_id = $sel_election_id");
	if(mysqli_num_rows($masterInfo)== 0)
	{
		$msg = "Election Not Found";
	}
	$mInfo = $masterInfo->fetch_array(MYSQLI_ASSOC);
			
	
	$sTime = explode(" ",$mInfo['start_time']);
	$eTime = explode(" ",$mInfo['end_time']);

	
	
?>


<!doctype html>
<html>
	<head>
		<title>Manage Elections</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
		<link rel='stylesheet' href='tab_style.css'>
		<script src='tbl.js'>
		</script>
		<script src='tab_js.js'>
		</script>
	</head>
	<body>
		<div class='head'>
			Manage Election
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			if($sel_election_id == -1)
				die("<a href='#'>Select an Election to Modify</a>");
			echo $msg;
		?>
		
		
		
		<div class='main'>
				<!-- Print Election Information -->
			<div class='right'>
				
				<div class='tab_head'>
					Election Summary
				</div>
				<div class='master'>
					
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
							<th>Voting Starts at</th>
							<td><?php echo date('g:i A',strtotime($sTime[1])); ?></td>
						</tr>
						<tr>
							<th>Voting Ends at</th>
							<td><?php echo date('g:i A',strtotime($eTime[1])); ?></td>
						</tr>
					</table>
				</div>
				
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
						$subqry = "select candidateName from election_candidates where el_id = $sel_election_id and post_no='".$row['post_num']."'";
						$subres = $conn->query($subqry);
						while($subrow = $subres->fetch_array(MYSQLI_ASSOC))
						{
							echo "<div class='el_cndt'>";
							echo $subrow['candidateName'];
							echo "</div>";
						}
							
						echo "</div>";	
							
						
					}
						

				
				
				
				?>
				
				
				
			</div>
			
		
		
		
		<div class='content'>
			
			<div class='tab_nav' style='overflow:auto'>
				<div style="float:left">
					<button type="button" class='btn' id="0" onclick="showTab(0)">Election Info</button>
					<button type="button" id="1" onclick="showTab(1)">Posts and Candidates</button>
				</div>
				
			</div>
			
			
		<div class='tab'>
			<div class='tab_head'>Election Details</div>
			<div class='tab_content'>
				
				<!-- Form to Modify Election Master Data -->
				<form action='manage_election.php' method='post'>
					<input type='hidden' name='election_id' value='<?php echo $sel_election_id; ?>'>
					<table class='frm'>
						<tr>
							<th>Election Title</th>
								<td><input type='text' name='el_title' value='<?php echo $mInfo['title']; ?>'></td>
							</tr>
						<tr>
							<th>Voting Date</th>
							<td><input type='date' name='el_date' value='<?php echo $sTime[0]; ?>'></td>
						</tr>
						<tr>
							<th>Voting Start Time</th>
							<td><input type='time' name='start_time' value='<?php echo date('H:i',strtotime($sTime[1])); ?>'></td>
						</tr>
						<tr>
							<th>Voting End Time</th>
							<td><input type='time' name='end_time' value='<?php echo date('H:i',strtotime($eTime[1])); ?>'></td>
						</tr>
						<tr>
							<td><input type='submit' value='Save Changes'></td>
						</tr>
					</table>
				</form>
				
				
			</div>
		</div>
		
		
		
		
		
		
		
		<!-- Form to Add New Post and Candidates to Election -->
		<div class='tab'>
			<div class='tab_head'>
				<h3>Organize Election</h3>
			</div>
				
			<div class='tab_content'>	
				<form action='manage_election.php' id='post_setup' method='post'>
				<input type='hidden' name='election_id' value='<?php echo $sel_election_id; ?>'>
				<input type='hidden' name='new_post' value='1'>
				
				<!-- POST MASTER DATA FORM -->
				<table class='frm'>
					<caption>Manage Post</caption>	
					<tr>
						<th>Post Title</th>
						<td><input type='text' name='post_title' autofocus></td>
					</tr>
					<tr>
						<th>Voters Allowed</th>
						<td>
							<select name='post_grp'>
								<?php fillSelectBox($conn,"select gc.groupId,if(gc.parent = -1,gc.groupName,concat(concat(gp.groupName,'/'),gc.groupName)) as groupName from voter_groups gc left join voter_groups gp on gc.parent = gp.groupId","");
								?>
							</select>
						</td>
					</tr>
				</table>
				
				<!-- Candidates Form -->
				<table id='post_cnd'>
					<caption>Candidates</caption>
					<tr>
						<th>S. No</th>
						<th>Candidate Name</th>
					</tr>
					<tr>
						<td><span class='post_cnd_srl'>1</span></td>
						<td><input type='text' name='post_cnd[]' placeholder='Candidate Name'></td>
						<td><input type='button' value='Add Another Candidate' onclick='newRow()'></td>
					</tr>
				</table>
					<input type='submit' value='Create Post'>
				</form>

		
			</div>
			
				
		
		
		
		</div>
		
		</div>
		
		
		
		</div>
		
		
		
		
	
		
		
		
		
		
		
		
		<script>
			showTab(1)

function showTab(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  //if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  if(n == 1)
  {
	  x[0].style.display = "none";
	  x[1].style.display = "block";
	  
	  
	  document.getElementById("1").style.backgroundColor = "#145214";
	  document.getElementById("0").style.backgroundColor = "#4CAF50";
  }
  else
  {
	  x[1].style.display = "none";
	  x[0].style.display = "block";
	  
	  
	  document.getElementById("0").style.backgroundColor = "#145214";
	  document.getElementById("1").style.backgroundColor = "#4CAF50";
	  
  }
	  
  
}
		</script>
		
		
		
		
		
	</body>
	
</html>	