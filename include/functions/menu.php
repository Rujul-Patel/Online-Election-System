 <?php
	//Prints Menu
	
	function printMenu($logged_in,$is_admin,$username)
	{
		echo "<div class='menubar'>";
		
		//public links
		echo <<<_frm
			<div class='mhead'>
					<a href='/ict/oes/'>Home</a>
			</div>
			
			<div class='mhead'>
					<a href='/ict/oes/election/past_election.php'>Recent Elections</a>
			</div>
			
_frm;
		
		if($logged_in && (!$is_admin))
		{
			echo <<<_frm
				<div class='mhead'>
					<a href='/ict/oes/election/vote.php'>Vote</a>
				</div>
_frm;

		}
		
		
		if($logged_in && $is_admin)
		{
			//Links to pages having admin access
			
			echo <<<_frm
			
				<div class='mhead'>
					<a href='/ict/oes/election/new_election.php'>Create New Election</a>
				</div>
				<div class='mhead'>
					<a href='/ict/oes/group/groups.php'>Manage Voter Groups</a>
				</div>
				<div class='mhead'>
					<a href='/ict/oes/voter/manage_voter.php'>Add Voters</a>
				</div>	
				<div class='mhead'>
					<a href='/ict/oes/voter/voter_list.php'>Voter List</a>
				</div>
				
			
			
			
			
_frm;
			
			
			
		}
		
		
		
		
		if($logged_in)
		{
			echo "<div class='rightMenu'>";
			echo "<div class='mhead'>".($is_admin?"<a href='/ict/oes/login/admin/admin_profile.php'>":"<a href='/ict/oes/login/voter_profile.php'>")."$username</a></div>";
			echo "<div class='mhead'><a href='/ict/oes/login/admin/logout.php'>Logout</a></div>";
		}
		else
		{
			echo "<div class='mhead'><a href='/ict/oes/login/login.php'>Voter Login</a></div>";
			echo "<div class='mhead'><a href='/ict/oes/login/admin/login.php'>Admin Login</a></div>";
		}
				
		echo "</div></div>";

	}
	


?>