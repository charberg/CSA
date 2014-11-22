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
				
				<div id="electiveArea"></div>
				<br/><br/>
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

		function changeName(thisID){
			document.getElementById(thisID).name = "coursesTaken[]";
		}
		
		/* This function fills the table with the course selections from XML input*/
		function fillTable(classList){
			var pattern = classList.getElementsByTagName('pattern')[0];
			var items = pattern.getElementsByTagName('item');
			var row = 1;
			var i = 0;
			var counter = 1;
			while(counter < 9){		//this loop goes through all courses given by the server and inserts them into the table in the proper order
									//because of the nature of inserting cells into tables, this method is a bit complicated/long
				var courseNumber = items[i].getElementsByTagName('courseNumber')[0].textContent;
				var year = items[i].getElementsByTagName('yearRequired')[0].textContent;
				var term = items[i].getElementsByTagName('termRequired')[0].textContent;
				var subjectID = items[i].getElementsByTagName('subjectID')[0].textContent;
				var onclick = "";
				if(subjectID == ""){
					onclick = "getElectives('"+courseNumber+"','"+year+"','"+term+"')";
				}else{
					onclick = "changeName('"+subjectID+courseNumber+year+term+"')";
				}
				switch(counter){
					
					case 1:	//Y1F 
						if(year == '1'){
							if(term == "fall"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "both"){
								//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "either"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}
						}
						break;
					case 2:  //Y1W
						if(year == '1' && term == "winter"){
							//(ORIGINAL) document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input class='checks' type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
							row++;
						}
						break;
					case 3:  //Y2F
						if(year == '2'){
							if(term == "fall"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "both"){
								//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "either"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}
						}
						break;
					case 4:  //Y2W
						if(year == '2' && term == "winter"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
							row++;
						}
						break;
					case 5:  //Y3F
						if(items[i].getElementsByTagName('yearRequired')[0].textContent == '3'){
							if(term == "fall"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "both"){
								//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "either"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}
						}
						break;
					case 6:  //Y3W
						if(year == '3' && term == "winter"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
							row++;
						}
						break;
					case 7:  //Y4F
						if(year == '4'){
							if(term == "fall"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "both"){
								//document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td colspan='2' id='"+items[i].getElementsByTagName('subjectID')[0].textContent+"'>"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"<br/><input type='checkbox' id='course' name='"+items[i].getElementsByTagName('subjectID')[0].textContent+items[i].getElementsByTagName('courseNumber')[0].textContent+"'/></td>";
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}else if(term == "either"){
								document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
								row++;
							}
						}
						break;
					case 8:  //Y4W
						if(year == '4' && term == "winter"){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td id='"+subjectID+"'>"+subjectID+courseNumber+"<br/><input id='"+subjectID+courseNumber+year+term+"' value='"+subjectID+courseNumber+"' class='checks' type='checkbox' name='checks' onclick=\""+onclick+"\"/></td>";
							row++;
						}
						break;
					default:
						//do nothing
						//break;
				}
				i++;
				if(i >= items.length){
					if(row < 7){		//fill the empty spots in the term with empty cells (so that everything lines up)
						for(;row<7;row++){
							document.getElementById('row'+row).innerHTML = document.getElementById('row'+row).innerHTML + "<td style='border:0px'></td>";
						}
					}
					i = 0;
					row = 1;
					counter++;
				}
			}
		}

		/* Sends the checked elective to the server, and returns a list of electives that correspond to that option */
		function getElectives(elective,year,term){
			//alert("Elective: "+elective+", Year: "+year+", Term: "+term);
			if(document.getElementById(elective+year+term).checked == false){	//check to see if removing the elective or adding it
				var elem = document.getElementById('selectLabel'+elective+year+term);  //if element is unchecked, remove the combination box + label
				elem.remove();
				return;
			}
			
			var req = "";
			var electtype = "";
			if(elective == "COMPLEMENTARY"){
				req = "GetComplementaryElectives";
			}else if(elective == "SCIENCE"){
				req = "GetScienceElectives";
			}else if(elective == "NOTEA" || elective == "NOTEB"){
				req = "GetEngineeringElectives";
				electtype = "&electtype=" + elective;
			}
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
						fillElectives(elective, year, term, rxml);
					}
				}
			}
			request.send("&requesttype="+req+"&program="+prog+"&year="+year+"&term="+term+electtype);
		}
		
		/* Adds a combination box of possible electives that fill the description of the selected elective */
		function fillElectives(electName, year, term, electiveList){
			var electives = electiveList.getElementsByTagName('Electives')[0].getElementsByTagName('Elective');
			var inner = document.getElementById('electiveArea').innerHTML;
			inner = inner + "<div id='selectLabel"+electName+year+term+"'><br/>" + electName + ", Year "+year+", "+capFirst(term)+" Term:    " + "<select id='select"+electName+year+term+"' name='electsChosen[]'>";
			var subjectID, courseNumber, year, term;
			for(var i=0; i<electives.length;i++){
				inner = inner + "<option value='"+electives[i].getElementsByTagName('SubjectID')[0].textContent+electives[i].getElementsByTagName('CourseNumber')[0].textContent+year+term+"'>"+electives[i].getElementsByTagName('SubjectID')[0].textContent+electives[i].getElementsByTagName('CourseNumber')[0].textContent+"</option>";
			}
			inner = inner + "</select><br/></div>";
			document.getElementById('electiveArea').innerHTML = inner;
		}
		
		function capFirst(string){
			return string.charAt(0).toUpperCase() + string.slice(1);
		}
		
		document.getElementsByName('program')[0].value = "<?php echo $programName; ?>";
		document.getElementsByName('year')[0].value = "<?php echo $yearCompleted; ?>";
		document.getElementsByName('term')[0].value = "<?php echo $term; ?>";
	</script>
</html>