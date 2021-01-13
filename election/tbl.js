function newRow()
{
	//Table and Next Serial Number
	var dtlTable = document.getElementById("post_cnd");
	var serial = dtlTable.rows.length;
	
	//Delete the Previous Add Button
	var	prevRow = dtlTable.rows[dtlTable.rows.length - 1];
	prevRow.deleteCell(-1);//Delete Last Cell
	
	
	//New Row
	var row = dtlTable.insertRow(dtlTable.rows.length);
	var cell = row.insertCell(-1);	//Serial No
	cell.innerHTML = "<span class='post_cnd_srl'>"+serial+"</span>"
	
	
	cell = row.insertCell(-1);
	cell.innerHTML = "<input type='text' name='post_cnd[]' placeholder='Candidate Name'>";
	
	cell = row.insertCell(-1);
	cell.innerHTML = "<input type='button' value='Add Another Candidate' onclick='newRow()'>";
			
}
