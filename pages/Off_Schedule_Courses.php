<?php
	/* Saved Cookies */
	if (!isset($_COOKIE['programName']) || !isset($_COOKIE['yearCompleted']) || !isset($_COOKIE['term'])){
		echo "Missing information";
		header("refresh:2;url=intro_page.html");
		exit;
	}
	
	$programName = $_COOKIE['programName'];
	$yearCompleted = $_COOKIE['yearCompleted'];
	$term = $_COOKIE['term'];
	
?>

<html>
	<head>
		<link type="text/css" rel="stylesheet" href="../css/off_schedule_course_select.css"/>
		<!-- Link to .css file -->
	</head>
	<body onload="getClasses()">
		<center>
			<form method="post" action="../php/server.php">
				<h3>Please select the courses you have taken:</h3>
				<table>
					<tr>
						<td width="120px" style="border:0px">Year 1 FALL</td>
						<td width="120px" style="border:0px">Year 1 WINTER</td>
						<td width="120px" style="border:0px">Year 2 FALL</td>
						<td width="120px" style="border:0px">Year 2 WINTER</td>
						<td width="120px" style="border:0px">Year 3 FALL</td>
						<td width="120px" style="border:0px">Year 3 WINTER</td>
						<td width="120px" style="border:0px">Year 4 FALL</td>
						<td width="120px" style="border:0px">Year 4 WINTER</td>
					</tr>
					
					<!-- Rows will be filled by javascript -->
					<tr id="row1"></tr>
					<tr id="row2"></tr>
					<tr id="row3"></tr>
					<tr id="row4"></tr>
					<tr id="row5"></tr>
					<tr id="row6"></tr>

				</table>
				<br/>
				
				<input type="hidden" name="requesttype" value="OffPatternSchedule"/>
				<input type="hidden" name="program"/>
				<input type="hidden" name="year"/>
				<input type="hidden" name="term"/>
				<input type="submit" value="SUBMIT"/>
				
			</form>
			<br/><br/>
			<h3><p id="classinfo"></p></h3>
		</center>
	</body>
	
	<script>
		/*Connects to server and gets the possible courses to take through XML */
		function getClasses(){
			var prog ="<?php echo $programName;?>";
			var request = new XMLHttpRequest();
			request.open("post","../php/server.php",true);
			request.setRequestHeader("content-type","application/x-www-form-urlencoded");
			request.onreadystatechange = function(){
				if(request.readyState == 4 && request.status == 200){
					var rxml = request.responseXML;
					//alert(request.responseText);
					//alert(request.responseXML);
					if(rxml){
						fillTable(rxml);
					}
				}
			}
			request.send("&requesttype=GetPattern&program="+prog);
		}

		function changeName(nom){
			document.getElementById(nom).name = "coursesTaken[]";
		}
		
		/* This function fills the table with the course selections from XML input*/
		function fillTable(classList){
			var pattern = classList.getElementsByTagName('pattern')[0];
			var items = pattern.getElementsByTagName('item');
			var row = 1;
			var i=0;
			var counter = 1;
			while(counter < 9){
				switch(counter){
					case 1:	//Y1F 
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '1' && items[i].getElementsByTagName('termRequired')[0].textContent == "fall"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '1' && items[i].getElementsByTagName('termRequired')[0].textContent == "both"){
							//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '1' && items[i].getElementsByTagName('termRequired')[0].textContent == "either"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 2:  //Y1W
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '1' && items[i].getElementsByTagName('termRequired')[0].textContent == "winter"){
							//(ORIGINAL) document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input class='checks' type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 3:  //Y2F
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '2' && items[i].getElementsByTagName('termRequired')[0].textContent == "fall"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '2' && items[i].getElementsByTagName('termRequired')[0].textContent == "both"){
							//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '2' && items[i].getElementsByTagName('termRequired')[0].textContent == "either"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 4:  //Y2W
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '2' && items[i].getElementsByTagName('termRequired')[0].textContent == "winter"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 5:  //Y3F
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '3' && items[i].getElementsByTagName('termRequired')[0].textContent == "fall"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '3' && items[i].getElementsByTagName('termRequired')[0].textContent == "both"){
							//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '3' && items[i].getElementsByTagName('termRequired')[0].textContent == "either"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 6:  //Y3W
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '3' && items[i].getElementsByTagName('termRequired')[0].textContent == "winter"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 7:  //Y4F
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '4' && items[i].getElementsByTagName('termRequired')[0].textContent == "fall"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '4' && items[i].getElementsByTagName('termRequired')[0].textContent == "both"){
							//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}else if(items[i].getElementsByTagName('yearRequired')[0].textContent == '4' && items[i].getElementsByTagName('termRequired')[0].textContent == "either"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					case 8:  //Y4W
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '4' && items[i].getElementsByTagName('termRequired')[0].textContent == "winter"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input id='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' value='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"' class='checks' type='checkbox' name='checks' onclick=\"changeName('"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"')\"/></td>";
							row++;
						}
						break;
					default:
						//do nothing
						//break;
				}
				i++;
				if(i >= items.length){
					if(row < 7){
						for(;row<7;row++){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td style='border:0px'></td>";
						}
					}
					i=0;
					row=1;
					counter++;
				}
			}
		}

		document.getElementsByName('program')[0].value = "<?php echo $programName; ?>";
		document.getElementsByName('year')[0].value = "<?php echo $yearCompleted; ?>";
		document.getElementsByName('term')[0].value = "<?php echo $term; ?>";
	</script>
</html>