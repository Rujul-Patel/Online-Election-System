<?php
	//includes
	require_once '../include/functions/menu.php';
	require_once '../include/functions/dbManager.php';
	require_once '../include/functions/func.php';

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
	
	
	///Database Connection
	$dbObj = new dbManager();
	$conn = $dbObj->getConnection();
	
	$msg = "";
	$fail = false;
	//Check if Form is Submitted
	if(isset($_POST['grp_name']) && isset($_POST['under']))
	{
		
		
		if($_POST['new_grp'] == 1)
		{
			//New Group Query
			if($conn->query("insert into voter_groups values(null,'".$_POST['grp_name']."','".$_POST['under']."')"))
			{
				$msg = "New Group Created";
			}
			else
			{
				$fail = true;
				$msg = "Failed To Create Group";
			}
			
		}
		else
		{
			if($conn->query("update voter_groups set groupName = '".$_POST['grp_name']."',parent='".$_POST['under']."' where groupId = '".$_POST['grp_id']."'"))
			{
				$msg = "Group Updated Successfully";
			}
			else
			{
				$fail = true;
				$msg = "Failed to Update Group";
			}
			
			
			
		}
		
		
	}
	

	$is_modify = false;
	if(isset($_GET['grp']))
	{
		$is_modify = true;
		//modify grp
		
		//Retreive Info
		$res  = $conn->query("select groupName,parent from voter_groups where groupId = ".$_GET['grp']);
		$grp_info = $res->fetch_array(MYSQLI_ASSOC);
		
		
	}
	
	
	

?>
<!doctype html>
<html>
	<head>
		<title>Manage Voter Groups</title>
		<link rel='stylesheet' href='../include/styles/general.css'>
		<link rel='stylesheet' href='../include/styles/new_style.css'>
	</head>
	<body>
		
	
		<div class='head'>
			Manage Voter Groups
			
		</div>
		
		<?php
			printMenu(($voter_logged_in or $admin_logged_in),$admin_logged_in,$username);
			
			
			echo $msg;
				
			
		?>
		
		
		
		
		<div class='main'>
			
			
			<div class='frm'>
				<form action='groups.php' method='post'>
					<table class='frm'>
						<tr>
							<input type='hidden' name='new_grp' value='<?php if($is_modify == true)echo 0;else echo 1; ?>'>
							<input type='hidden' name='grp_id' value='<?php if($is_modify == true)echo $_GET['grp'];else echo -1;?>'>
							<th><span id='grp_name'>Group Name</span></th>
							<th><input type='text' name='grp_name' value='<?php if($is_modify == true)echo $grp_info['groupName'];?>' autofocus></th>
						</tr>
						<tr>
							<th>Parent Group</th>
							<td>
								<select name='under'>
									<?php fillSelectBox($conn,"select gc.groupId,if(gc.parent = -1,gc.groupName,concat(concat(gp.groupName,'/'),gc.groupName)) as groupName from voter_groups gc left join voter_groups gp on gc.parent = gp.groupId",($is_modify?$grp_info['parent']:""));
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th>Remarks</th>
							<td><input type='text' name='remarks'></td>
						</tr>
						<tr>
							<td><input type='submit' value='<?php if(!$is_modify)echo "Create Group";else echo "Modify Group";?>'></td>
							<td><input type='button' value='Cancel'></td>
						</tr>
						 
						 
					</table>
				</form>
			</div>
			
			
			<!-- Displays all Groups -->
			<div id='grp_tree'>
				
				<h3>Group List(Click Group to Modify it)</h3>
				<?php
					//Group List and Display in Tree Format
					
					echo "<div class='list'>";
					printchild($conn,-1);
					echo "</div>";
					
					$even = "even";
					function printchild($conn,$parentId)
					{
						global $even;
						
						
						$res = $conn->query("select groupId,groupName from voter_groups where parent = $parentId");
						while($row = $res->fetch_array(MYSQLI_ASSOC))
						{
							
							echo "<div class='list_grp'>";
							echo "<a href='groups.php?grp=".$row['groupId']."'>".$row['groupName']."</a>";
							
							printchild($conn,$row['groupId']);
							echo "</div>";
							
							
						}
					}
					
				
				
				?>
				
				
				
				
				
			</div>
			
			
			
		</div>
		
	</body>
</html>