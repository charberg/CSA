<?php
	/* Saved Cookies */
	
	if (!isset($_COOKIE['programName']) || !isset($_COOKIE['yearCompleted']) || !isset($_COOKIE['term']) || !isset($_COOKIE['courses'])){
		echo "Missing information";
		header("refresh:2;url=intro_page.html");
		exit;
	}
	
	$programName = $_COOKIE['programName'];
	$yearCompleted = $_COOKIE['yearCompleted'];
	$term = $_COOKIE['term'];
	$courseList = $_COOKIE['courses'];
	
?>

<html>
	<!-- Shows the current schedule -->
	<head>
		<link type="text/css" rel="stylesheet" href="../css/my_schedule.css"/>
		<!-- Link to .css file -->
		<xml ID="scheduleexample" SRC="../scheduleexample.xml"></xml>
		<script>
			GlobalSched = "";  //Global variable containing all schedules given by the server
			GlobalCurrentSched = 0;
			
			/* Calls the server to give the stored schedules, puts the first on the table, and sets the buttons to be able to switch between them */
			function getSchedules(){
				var courseList = "<?php echo $courseList; ?>";
				var request = new XMLHttpRequest();
				request.open("post","../php/server.php",true);
				request.setRequestHeader("content-type","application/x-www-form-urlencoded");
				request.onreadystatechange = function(){
					if(request.readyState == 4 && request.status == 200){
						var rxml = request.responseXML;
						if(rxml){
							var xmlSchedule = rxml.getElementsByTagName('schedules')[0];
							if(xmlSchedule.textContent == ""){
								alert("There were no compatible schedules found.");
								document.location.href = "intro_page.html";
								return;
							}
							GlobalSched = xmlSchedule.getElementsByTagName('courses');
							for(var i=0;i<GlobalSched.length;i++){
								if(GlobalSched[i].textContent != ""){	//prevent options from being added when a schedule is empty / invalid
									document.getElementById('schedSelect').innerHTML = document.getElementById('schedSelect').innerHTML + "<option value='"+i+"'>"+(i+1)+"</option>";
								}
							}
							GlobalCurrentSched = 0;
							fillTable();
						}
					}
				}
				request.send("?source=html&requesttype=GetCourseFile&fileName="+courseList);
			}
			
			/* Creates the table to put schedule times into */
			function createTable(){
				var extra = "";
				var inStr = "";
				for(var i=8;i<22;i++){
					if(i < 10){
						extra = "0";
					}else{
						extra = "";
					}
					inStr += "<tr id='"+extra+i+"00'>";
					inStr += "<td>"+extra+i+":00</td>";
					inStr += "<td id='sun"+extra+i+"00'></td>";
					inStr += "<td id='mon"+extra+i+"00'></td>";
					inStr += "<td id='tues"+extra+i+"00'></td>";
					inStr += "<td id='wed"+extra+i+"00'></td>";
					inStr += "<td id='thurs"+extra+i+"00'></td>";
					inStr += "<td id='fri"+extra+i+"00'></td>";
					inStr += "<td id='sat"+extra+i+"00'></td>";
					inStr += "</tr>";
					
					inStr += "<tr id='"+extra+i+"30'>";
					inStr += "<td>"+extra+i+":30</td>";
					inStr += "<td id='sun"+extra+i+"30'></td>";
					inStr += "<td id='mon"+extra+i+"30'></td>";
					inStr += "<td id='tues"+extra+i+"30'></td>";
					inStr += "<td id='wed"+extra+i+"30'></td>";
					inStr += "<td id='thurs"+extra+i+"30'></td>";
					inStr += "<td id='fri"+extra+i+"30'></td>";
					inStr += "<td id='sat"+extra+i+"30'></td>";
					inStr += "</tr>";
				}
				
				document.getElementById("scheduleTable").innerHTML += inStr;
			}
			
			function setupFunc(){
				createTable();
				getSchedules();
			}
			
		</script>
	</head>
	<body onload="setupFunc()">
		<center>
		<h1>My Schedule</h1>
		Your schedule options: 
		<select id="schedSelect"></select>
		<input type="button" value="SELECT" onclick="fillTable()"/>
		<br/><br/>
		<table id="scheduleTable">	<!-- timetable -->
			<tr>
				<!-- headers -->
				<td>TIME</td>
				<td>Sunday</td>
				<td>Monday</td>
				<td>Tuesday</td>
				<td>Wednesday</td>
				<td>Thursday</td>
				<td>Friday</td>
				<td>Saturday</td>
			</tr>
		</table>
		<br/><br/>
		<input type="button" value="Pick This Schedule" onclick="submitSchedule()"/>
		<br/><br/>
		<br/><br/>
		</center>
	</body>
	<script>
		/* Fills the table displayed with the schedule index input */
		function fillTable(){
			
			var index = parseInt(document.getElementById('schedSelect').value);
			if(index < 0) return;	//prevent using empty index
			var termSched = GlobalSched[index].getElementsByTagName('Section');
			if(!termSched) return;	//Index did not return a proper schedule
			GlobalCurrentSched = index;
			
			clearTable();	//make sure the table is clean before putting a schedule on it
			
			var day = "";
			for(var i=0;i < termSched.length;i++){
				var days = termSched[i].getElementsByTagName('days')[0].textContent;	//Gets the days the class is held
				for(var j=0;j<days.length;j++){		//for every day the class is held, set that cell on the table
					switch(days.charAt(j)){
						case 'M':
							day = "mon";
							break;
						case 'T':
							day = "tues";
							break;
						case 'W':
							day = "wed";
							break;
						case 'R':
							day = "thurs";
							break;
						case "F":
							day = "fri";
							break;
						case 'S':
							day = "sat";
							break;
						case 'U':
							day = "sun";
							break;
						default:
							alert("Unknown day: <"+days.charAt(j)+">");
							break;
					}
					
					var times = termSched[i].getElementsByTagName('time')[0].textContent.split("-");
					var orgStart = times[0];				//ensure the "original numbers" is zero padded
					if(times[0].length == 3) orgStart = '0' + times[0];		
					var orgEnd = times[1];
					if(times[1].length == 3) orgEnd = '0' + times[1];
					
					var startTime = normTime(times[0]);
					var endTime = normTime(times[1]);
					var tempTime = startTime;		//this variable keeps track of the current time ( what info will be played next )
					
					//loop through until end time and add in those as well
					while(tempTime != endTime){
						/* Get the variables to go into the HTML ( for readability ) */
						var subject = termSched[i].getElementsByTagName('subjectID')[0].textContent;
						var courseNum = termSched[i].getElementsByTagName('courseNum')[0].textContent;
						var section = termSched[i].getElementsByTagName('sectionCode')[0].textContent;
						var classType = termSched[i].getElementsByTagName('scheduleCode')[0].textContent;
						
						document.getElementById(day+tempTime).innerHTML = subject + courseNum + section + " " + classType + "<br/>"+orgStart.slice(0,2)+ ":"+orgStart.slice(2)+" - "+orgEnd.slice(0,2)+":"+orgEnd.slice(2);
						tempTime = incTime(tempTime);
					}
				}
			}
		}
		
		/* Sends the current schedule selected to the course server */
		function submitSchedule(){
			if(GlobalSched[GlobalCurrentSched]){
				var request = new XMLHttpRequest();
				request.open("post","../php/courseServer.php",true);
				request.setRequestHeader("content-type","application/x-www-form-urlencoded");
				request.onreadystatechange = function(){
					if(request.readyState == 4 && request.status == 200){
						var response = request.responseText;
						if(response == "PASS"){
							alert("You have successfully registered for your courses!");
						}else{
							alert("Unable to register your courses.");
						}
						document.location.href = "intro_page.html";
					}
				}
				request.send("&source=html&xml="+(new XMLSerializer().serializeToString(GlobalSched[GlobalCurrentSched]))); //send xml schedule as a string
			}else{
				alert("Schedule is empty, cannot be submitted.");
			}
		}
		
		/* Take time that has 5 min offset and normalize it */
		function normTime(time){
			var newTime = time.trim();
			if(newTime.length == 3){
				newTime = '0' + newTime;
			}
			if(newTime.charAt(2) == '2'){
				newTime = newTime.slice(0,2) + "30";
			}else if(newTime.charAt(2) == '5'){
				newTime = (parseInt(newTime.slice(0,2)) + 1).toString() + "00";
			}else{
				newTime = newTime.slice(0,3) + '0';
			}
			return newTime;
		}
		
		/* Function to increment time by the half hour */
		function incTime(time){
			var thisTime = time;
			if(thisTime.slice(2) == "00"){
				thisTime = thisTime.slice(0,2) + "30";
			}else{
				thisTime = (parseInt(thisTime.slice(0,2)) + 1).toString() + "00";
				if(thisTime.length == 3) thisTime = '0' + thisTime;
			}
			return thisTime;
		}
		
		/* Clears the table displayed */
		function clearTable(){
			var i=0;
			var time = "0800";
			while(time != "2200"){
				document.getElementById('sun'+time).innerHTML = "";
				document.getElementById('mon'+time).innerHTML = "";
				document.getElementById('tues'+time).innerHTML = "";
				document.getElementById('wed'+time).innerHTML = "";
				document.getElementById('thurs'+time).innerHTML = "";
				document.getElementById('fri'+time).innerHTML = "";
				document.getElementById('sat'+time).innerHTML = "";
				time = incTime(time);
			}
		}
	</script>
</html>