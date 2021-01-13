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
	
	if($voter_logged_in == false)
	{
		die("You do not have Permission to access this page");
		//Redirect with 403 response
	}
	
	
	//Open Database Connection
	$dbObj = new dbManager();
	$conn = $dbObj->getConnection();
		
	$die_msg = "";
	$error_flag = false;
	
	$sel_election_id = -1;	//No Election Selected
	if(isset($_GET['election']))
	{
		$sel_election_id = $_GET['election'];
	}
	
	date_default_timezone_set('Asia/Kolkata');
	$cur_time = date("Y-m-d H:i");
	
	//Verify If Election in Live
	$res = $conn->query("select * from election_master where el_id = $sel_election_id");
	
	if(mysqli_num_rows($res) == 0)
	{
		$error_flag = true;
		$die_msg = "No Election Found";
	}
	$el_master = $res->fetch_array(MYSQLI_ASSOC);
	
	if(!$error_flag)
	{
		if($cur_time < $el_master['start_time'])
		{
			$error_flag = true;
			$die_msg = "Voting has not yet begun for this Election.Voting opens at ".date("d-m-Y H:i A",strtotiime($el_master['start_time']));
		}
		elseif($cur_time > $el_master['end_time'])
		{
			$error_flag  = true;
			$die_msg = "You can no longer vote for this election. Voting closed at ".date("d-m-Y H:i A",strtotime($el_master['end_time']));
		}
		
		
		//Voting Live => Verify Voter
		$voter_verify = $conn->query("select * from vote_data where el_id = $sel_election_id and voter_id = ".$_SESSION['oes']['voterId']);
		if(mysqli_num_rows($voter_verify) == 0)
		{
			//Register voter
			if($conn->query("insert into vote_data(el_id,voter_id) values($sel_election_id,'".$_SESSION['oes']['voterId']."')"))
			{
				//Successfully	 Registered Voter
				//Redirect to OTP Authentication
			//	header("Location:../auth/otp_auth.php?election=$sel_election_id");
				
			}else
			{
				//Failed to Register Voter
				$error_flag = true;
				$die_msg = "Sorry! We are having an error registering you!<br> Make sure you are connected with server<br>Please Try to vote after logging out and Logging in again, If the Problem persists then contact the election Organisers for further help	 ";
			}
		}else
		{
			$voter_verify = $voter_verify->fetch_array(MYSQLI_ASSOC);	
			if($voter_verify['vote_casted'] == 1)
			{
				$error_flag = true;
				$die_msg = "Your Vote has already been casted";
			}elseif($voter_verify['otp_validated'] == 0)
			{
				//Validate with OTP
				//Redirect to OTP Authentication
			//	header("Location:../auth/otp_auth.php?election=$sel_election_id");
				
			}
			
		//All Code from here assumes
		//Election is Live
		//Voter has not Voted
		//Voter Verified with OTP
		
		
		}
			
		
		
	
	
	
	
		
		
		
			
	}
	
	
	
	
	
	
	
	
	
	
	
	if(isset($_POST['election_id']))
	{
		/*************************************************************
			Submit Vote Procedure
			1. Check if Election is live.
			2. Check whether voter has already cast their vote
			
			Loop for each candidate
				1.Check if vote for that post by this voter has been submitted
				2.Increment the vote for candidate 
				3.Log Voting Data
		
		*************************************************************/
		
		//Check if Election is Live
			
		date_default_timezone_set('Asia/Kolkata');
		$cur_time = date("Y-m-d H:i");
		
		
		$stime = strtotime($el_master['start_time']);
		$etime = strtotime($el_master['end_time']);
		
		//Verify If Election in Live
		
		
		//Check if Voter has already cast vote
		$res1 = $conn->query("update vote_data set vote_casted = 1 where el_id = '".$_POST['election_id']."' and voter_id = '".$_SESSION['oes']['voterId']."' and vote_casted = 0");
		
		if(mysqli_affected_rows($conn) == 0)
		{
			die("Your Vote has already been recorded.");
		}
		
		//Register Voter
		
		$ip_add = $_SERVER['REMOTE_ADDR'];
		//echo "update vote_data set vote_time='$cur_time',ip_address='$ip_add' where el_id = '".$_POST['election_id']."' and voter_id = '".$_SESSION['oes']['voterId']."' and vote_casted = 1";
		$r1 = $conn->query("update vote_data set vote_time=$cur_time,ip_address=$ip_add where el_id = '".$_POST['election_id']."' and voter_id = '".$_SESSION['oes']['voterId']."' and vote_casted = 1");
		if(mysqli_affected_rows($conn) == 0)
			die("Fatal Error! Unable to Cast Vote. Please Try again or contact Election Organisers");
		
		
		$chk_post = $conn->prepare("select candidate_voted from vote_logs where pollId = ? and post_num = ? and voterId = ? and candidate_voted = ?");
		$vote_log = $conn->prepare("insert into vote_logs values(?,?,?,?)");
		$vote_inc = $conn->prepare("update election_candidates set votesAcquired = votesAcquired + 1 where el_id = ? and post_no = ? and candidateName = ?");
		
		
		//Loop Each Candidate
		
		foreach($_POST['cndt'] as $pno=>$candidate)
		{
			//check if vote for that post
			$chk_post->bind_param("ddds",$_POST['election_id'],$pno,$_SESSION['oes']['voterId'],$candidate);
			$chk_post->execute();
			
			$res = $chk_post->get_result();
			if(mysqli_num_rows($res) > 0)
			{
				continue;
			}
			
			$vote_inc->bind_param("dds",$_POST['election_id'],$pno,$candidate);
			$vote_inc->execute();
			
			$vote_log->bind_param("ddds",$_POST['election_id'],$pno,$_SESSION['oes']['voterId'],$candidate);
			$vote_log->execute();
			
			
			
			
			
		}
		
		
			
			
		
		
		
		
	}
	
	
	
		//Check if Election has begun
			
			//get current time
			date_default_timezone_set('Asia/Kolkata');
			$cur_time = date("Y-m-d H:i:s");
			$cur_time = strtotime($cur_time);
			$eTime = strtotime($el_master['end_time']);
			
			$diff = $eTime - $cur_time;
	
	
	
?>
<!doctype html>
<html>
	<head>
		<title>Vote</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
		<script src='tbl.js'></script>
		<script>
			var initialTime = <?php echo $diff; ?>;//Place here the total of seconds you receive on your PHP code. ie: var initialTime = <? echo $remaining; ?>;

			var seconds = initialTime;
			function timer() {
				var days        = Math.floor(seconds/24/60/60);
				var hoursLeft   = Math.floor((seconds) - (days*86400));
				var hours       = Math.floor(hoursLeft/3600);
				var minutesLeft = Math.floor((hoursLeft) - (hours*3600));
				var minutes     = Math.floor(minutesLeft/60);
				var remainingSeconds = seconds % 60;
				if (remainingSeconds < 10) {
					remainingSeconds = "0" + remainingSeconds; 
				}
				document.getElementById('countdown').innerHTML = hours + " Hours " + minutes + " Minutes " + remainingSeconds+ " Seconds ";
				if (seconds == 0) {
					clearInterval(countdownTimer);
					document.getElementById('countdown').innerHTML = "Completed";
				} else {
					seconds--;
				}
			}
			var countdownTimer = setInterval('timer()', 1000);
		</script>
	</head>
	<body>
		<div class='head'>
			Election Name
			
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			if($sel_election_id == -1)
			{
				die("<a href='#'>Select Election to Vote</a>");
			}
			
			if($error_flag == true)
				die($die_msg);
			
			//check if voting is competed
			if($el_master['isComplete'] == 1)
			{
				die("Votings For this election has been completed");
			}
			
			
			
			
				
		?>
		<div id='countdown'>
		</div>
		
		<!-- VOTING FORM -->
		<form action='vote.php' method='post' id='vote_res'>
			
			
			<?php
					
				echo "<input type='hidden' name='election_id' value='".$sel_election_id."'>";
				
				//Retrieve Election Details	
				$qry = "select p.post_num,p.post_title,g.groupName from election_structure p inner join voter_groups g on g.groupId = p.allowed_group and  p.el_id = $sel_election_id";
				$res = $conn->query($qry);
				
				


				
				while($row = $res->fetch_array(MYSQLI_ASSOC))
				{
					echo "<div class='post'><span><h3>".$row['post_title']."</h3></span>";				
					
					
					
					
					$subqry = "select candidateName from election_candidates where el_id = $sel_election_id and post_no='".$row['post_num']."'";
					$subres = $conn->query($subqry);
					
					while($subrow = $subres->fetch_array(MYSQLI_ASSOC))
					{
						
						echo "<div class='cndt'><input type='radio' name=cndt[".$row['post_num']."] value='".$subrow['candidateName']."'>".$subrow['candidateName']."</div>";
					}
							
					echo "</div>";
						
					
				}
			
			
			
			?>
			
			<input type='submit' value='VOTE'>
			
		</form>
		
		
		<script type="text/javascript">
<!--
			 window.onload=function(){
        var auto = setTimeout(function(){ autoRefresh(); }, <?php echo ($diff*10); ?>);

        function submitform(){
          document.forms["vote_res"].submit();
        }

        function autoRefresh(){
           clearTimeout(auto);
           auto = setTimeout(function(){ submitform(); autoRefresh(); }, 10000);
        }
    }
//-->
		</script>
			
		
		
		
	</body>
</html>