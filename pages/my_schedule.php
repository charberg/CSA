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
							GlobalSched = rxml.getElementsByTagName('schedules')[0].getElementsByTagName('courses');
							for(var i=0;i<GlobalSched.length;i++){
								if(GlobalSched[i].textContent != ""){	//prevent options from being added when a schedule is empty / invalid
									document.getElementById('schedSelect').innerHTML = document.getElementById('schedSelect').innerHTML + "<option value='"+i+"'>"+(i+1)+"</option>";
								}
							}
							GlobalCurrentSched = 0;
							fillTable();
						}else{
							alert("Did not receive usable XML: "+rxml);
							alert("Response string: "+request.responseText);
						}
					}
				}
				request.send("&requesttype=GetCourseFile&fileName="+courseList);
			}
		</script>
	</head>
	<body onload="getSchedules()">
		<center>
		<h1>My Schedule</h1>
		Your schedule options: 
		<select id="schedSelect"></select>
		<input type="button" value="SELECT" onclick="fillTable()"/>
		<br/><br/>
		<table>	<!-- timetable -->
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
			
			<!-- All cells in the table (mapped by time and day) -->
			
			<tr id='0800'>
				<td>08:00</td>
				<td id='sun0800'></td>
				<td id='mon0800'></td>
				<td id='tues0800'></td>
				<td id='wed0800'></td>
				<td id='thurs0800'></td>
				<td id='fri0800'></td>
				<td id='sat0800'></td>
			</tr>

			<tr id='0830'>
				<td>08:30</td>
				<td id='sun0830'></td>
				<td id='mon0830'></td>
				<td id='tues0830'></td>
				<td id='wed0830'></td>
				<td id='thurs0830'></td>
				<td id='fri0830'></td>
				<td id='sat0830'></td>
			</tr>

			<tr id='0900'>
				<td>09:00</td>
				<td id='sun0900'></td>
				<td id='mon0900'></td>
				<td id='tues0900'></td>
				<td id='wed0900'></td>
				<td id='thurs0900'></td>
				<td id='fri0900'></td>
				<td id='sat0900'></td>
			</tr>

			<tr id='0930'>
				<td>09:30</td>
				<td id='sun0930'></td>
				<td id='mon0930'></td>
				<td id='tues0930'></td>
				<td id='wed0930'></td>
				<td id='thurs0930'></td>
				<td id='fri0930'></td>
				<td id='sat0930'></td>
			</tr>

			<tr id='1000'>
				<td>10:00</td>
				<td id='sun1000'></td>
				<td id='mon1000'></td>
				<td id='tues1000'></td>
				<td id='wed1000'></td>
				<td id='thurs1000'></td>
				<td id='fri1000'></td>
				<td id='sat1000'></td>
			</tr>

			<tr id='1030'>
				<td>10:30</td>
				<td id='sun1030'></td>
				<td id='mon1030'></td>
				<td id='tues1030'></td>
				<td id='wed1030'></td>
				<td id='thurs1030'></td>
				<td id='fri1030'></td>
				<td id='sat1030'></td>
			</tr>

			<tr id='1100'>
				<td>11:00</td>
				<td id='sun1100'></td>
				<td id='mon1100'></td>
				<td id='tues1100'></td>
				<td id='wed1100'></td>
				<td id='thurs1100'></td>
				<td id='fri1100'></td>
				<td id='sat1100'></td>
			</tr>

			<tr id='1130'>
				<td>11:30</td>
				<td id='sun1130'></td>
				<td id='mon1130'></td>
				<td id='tues1130'></td>
				<td id='wed1130'></td>
				<td id='thurs1130'></td>
				<td id='fri1130'></td>
				<td id='sat1130'></td>
			</tr>

			<tr id='1200'>
				<td>12:00</td>
				<td id='sun1200'></td>
				<td id='mon1200'></td>
				<td id='tues1200'></td>
				<td id='wed1200'></td>
				<td id='thurs1200'></td>
				<td id='fri1200'></td>
				<td id='sat1200'></td>
			</tr>

			<tr id='1230'>
				<td>12:30</td>
				<td id='sun1230'></td>
				<td id='mon1230'></td>
				<td id='tues1230'></td>
				<td id='wed1230'></td>
				<td id='thurs1230'></td>
				<td id='fri1230'></td>
				<td id='sat1230'></td>
			</tr>

			<tr id='1300'>
				<td>13:00</td>
				<td id='sun1300'></td>
				<td id='mon1300'></td>
				<td id='tues1300'></td>
				<td id='wed1300'></td>
				<td id='thurs1300'></td>
				<td id='fri1300'></td>
				<td id='sat1300'></td>
			</tr>

			<tr id='1330'>
				<td>13:30</td>
				<td id='sun1330'></td>
				<td id='mon1330'></td>
				<td id='tues1330'></td>
				<td id='wed1330'></td>
				<td id='thurs1330'></td>
				<td id='fri1330'></td>
				<td id='sat1330'></td>
			</tr>

			<tr id='1400'>
				<td>14:00</td>
				<td id='sun1400'></td>
				<td id='mon1400'></td>
				<td id='tues1400'></td>
				<td id='wed1400'></td>
				<td id='thurs1400'></td>
				<td id='fri1400'></td>
				<td id='sat1400'></td>
			</tr>

			<tr id='1430'>
				<td>14:30</td>
				<td id='sun1430'></td>
				<td id='mon1430'></td>
				<td id='tues1430'></td>
				<td id='wed1430'></td>
				<td id='thurs1430'></td>
				<td id='fri1430'></td>
				<td id='sat1430'></td>
			</tr>

			<tr id='1500'>
				<td>15:00</td>
				<td id='sun1500'></td>
				<td id='mon1500'></td>
				<td id='tues1500'></td>
				<td id='wed1500'></td>
				<td id='thurs1500'></td>
				<td id='fri1500'></td>
				<td id='sat1500'></td>
			</tr>

			<tr id='1530'>
				<td>15:30</td>
				<td id='sun1530'></td>
				<td id='mon1530'></td>
				<td id='tues1530'></td>
				<td id='wed1530'></td>
				<td id='thurs1530'></td>
				<td id='fri1530'></td>
				<td id='sat1530'></td>
			</tr>

			<tr id='1600'>
				<td>16:00</td>
				<td id='sun1600'></td>
				<td id='mon1600'></td>
				<td id='tues1600'></td>
				<td id='wed1600'></td>
				<td id='thurs1600'></td>
				<td id='fri1600'></td>
				<td id='sat1600'></td>
			</tr>

			<tr id='1630'>
				<td>16:30</td>
				<td id='sun1630'></td>
				<td id='mon1630'></td>
				<td id='tues1630'></td>
				<td id='wed1630'></td>
				<td id='thurs1630'></td>
				<td id='fri1630'></td>
				<td id='sat1630'></td>
			</tr>

			<tr id='1700'>
				<td>17:00</td>
				<td id='sun1700'></td>
				<td id='mon1700'></td>
				<td id='tues1700'></td>
				<td id='wed1700'></td>
				<td id='thurs1700'></td>
				<td id='fri1700'></td>
				<td id='sat1700'></td>
			</tr>

			<tr id='1730'>
				<td>17:30</td>
				<td id='sun1730'></td>
				<td id='mon1730'></td>
				<td id='tues1730'></td>
				<td id='wed1730'></td>
				<td id='thurs1730'></td>
				<td id='fri1730'></td>
				<td id='sat1730'></td>
			</tr>

			<tr id='1800'>
				<td>18:00</td>
				<td id='sun1800'></td>
				<td id='mon1800'></td>
				<td id='tues1800'></td>
				<td id='wed1800'></td>
				<td id='thurs1800'></td>
				<td id='fri1800'></td>
				<td id='sat1800'></td>
			</tr>

			<tr id='1830'>
				<td>18:30</td>
				<td id='sun1830'></td>
				<td id='mon1830'></td>
				<td id='tues1830'></td>
				<td id='wed1830'></td>
				<td id='thurs1830'></td>
				<td id='fri1830'></td>
				<td id='sat1830'></td>
			</tr>

			<tr id='1900'>
				<td>19:00</td>
				<td id='sun1900'></td>
				<td id='mon1900'></td>
				<td id='tues1900'></td>
				<td id='wed1900'></td>
				<td id='thurs1900'></td>
				<td id='fri1900'></td>
				<td id='sat1900'></td>
			</tr>

			<tr id='1930'>
				<td>19:30</td>
				<td id='sun1930'></td>
				<td id='mon1930'></td>
				<td id='tues1930'></td>
				<td id='wed1930'></td>
				<td id='thurs1930'></td>
				<td id='fri1930'></td>
				<td id='sat1930'></td>
			</tr>

			<tr id='2000'>
				<td>20:00</td>
				<td id='sun2000'></td>
				<td id='mon2000'></td>
				<td id='tues2000'></td>
				<td id='wed2000'></td>
				<td id='thurs2000'></td>
				<td id='fri2000'></td>
				<td id='sat2000'></td>
			</tr>

			<tr id='2030'>
				<td>20:30</td>
				<td id='sun2030'></td>
				<td id='mon2030'></td>
				<td id='tues2030'></td>
				<td id='wed2030'></td>
				<td id='thurs2030'></td>
				<td id='fri2030'></td>
				<td id='sat2030'></td>
			</tr>

			<tr id='2100'>
				<td>21:00</td>
				<td id='sun2100'></td>
				<td id='mon2100'></td>
				<td id='tues2100'></td>
				<td id='wed2100'></td>
				<td id='thurs2100'></td>
				<td id='fri2100'></td>
				<td id='sat2100'></td>
			</tr>

			<tr id='2130'>
				<td>21:30</td>
				<td id='sun2130'></td>
				<td id='mon2130'></td>
				<td id='tues2130'></td>
				<td id='wed2130'></td>
				<td id='thurs2130'></td>
				<td id='fri2130'></td>
				<td id='sat2130'></td>
			</tr>

			<tr id='2200'>
				<td>22:00</td>
				<td id='sun2200'></td>
				<td id='mon2200'></td>
				<td id='tues2200'></td>
				<td id='wed2200'></td>
				<td id='thurs2200'></td>
				<td id='fri2200'></td>
				<td id='sat2200'></td>
			</tr>

			<tr id='2230'>
				<td>22:30</td>
				<td id='sun2230'></td>
				<td id='mon2230'></td>
				<td id='tues2230'></td>
				<td id='wed2230'></td>
				<td id='thurs2230'></td>
				<td id='fri2230'></td>
				<td id='sat2230'></td>
			</tr>

			<tr id='2300'>
				<td>23:00</td>
				<td id='sun2300'></td>
				<td id='mon2300'></td>
				<td id='tues2300'></td>
				<td id='wed2300'></td>
				<td id='thurs2300'></td>
				<td id='fri2300'></td>
				<td id='sat2300'></td>
			</tr>

			<tr id='2330'>
				<td>23:30</td>
				<td id='sun2330'></td>
				<td id='mon2330'></td>
				<td id='tues2330'></td>
				<td id='wed2330'></td>
				<td id='thurs2330'></td>
				<td id='fri2330'></td>
				<td id='sat2330'></td>
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
			var request = new XMLHttpRequest();
			request.open("post","../php/courseServer.php",true);
			request.setRequestHeader("content-type","application/xml");
			request.onreadystatechange = function(){
				if(request.readyState == 4 && request.status == 200){
					var response = request.responseText;
					alert(response);
				}
			}
			request.send(GlobalSched[GlobalCurrentSched]);
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
			while(time != "2400"){
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