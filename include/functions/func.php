<?php

	/**
		Function to Fill a Select Input and 
		Set a Default Selected Value
	**/
	function fillSelectBox(&$connection,$query,$match)
	{
		$result = $connection->query($query);
		if(!$result) die("Error!".$connection->error);
		
		while($r = $result->fetch_array(MYSQLI_NUM))
			echo "\n<option value='".$r[0]."' ".($match==$r[0]?'selected = True':'').">".$r[1]."</option>";
		
		
		$result->free();
	}




?>