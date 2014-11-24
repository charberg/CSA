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
				//alert(courseList);
				var request = new XMLHttpRequest();
				request.open("post","../php/server.php",true);
				//request.open("post","../tempServer.php",true);
				request.setRequestHeader("content-type","application/x-www-form-urlencoded");
				request.onreadystatechange = function(){
					if(request.readyState == 4 && request.status == 200){
						var rxml = request.responseXML;
						if(rxml){
							alert(request.responseText);
							GlobalSched = rxml.getElementsByTagName('schedules')[0].getElementsByTagName('courses');
							for(var i=0;i<GlobalSched.length;i++){
								if(GlobalSched[i].textContent != ""){	//prevent options from being added when a schedule is empty / invalid
									document.getElementById('schedSelect').innerHTML = document.getElementById('schedSelect').innerHTML + "<option value='"+i+"'>"+(i+1)+"</option>";
								}
							}
							GlobalCurrentSched = 0;
							fillTable(0);
						}else{
							alert("Did not receive usable XML: "+rxml);
							alert("Response string: "+request.responseText);
						}
					}
				}
				//request.send();
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
				<td>TIME</td>
				<td>Sunday</td>
				<td>Monday</td>
				<td>Tuesday</td>
				<td>Wednesday</td>
				<td>Thursday</td>
				<td>Friday</td>
				<td>Saturday</td>
			</tr>
			<tr id='800'>
				<td>08:00</td>
				<td id='sun800'></id>
				<td id='mon800'></id>
				<td id='tues800'></id>
				<td id='wed800'></id>
				<td id='thurs800'></id>
				<td id='fri800'></id>
				<td id='sat800'></id>
			</tr>

			<tr id='830'>
				<td>08:30</td>
				<td id='sun830'></id>
				<td id='mon830'></id>
				<td id='tues830'></id>
				<td id='wed830'></id>
				<td id='thurs830'></id>
				<td id='fri830'></id>
				<td id='sat830'></id>
			</tr>

			<tr id='900'>
				<td>09:00</td>
				<td id='sun900'></id>
				<td id='mon900'></id>
				<td id='tues900'></id>
				<td id='wed900'></id>
				<td id='thurs900'></id>
				<td id='fri900'></id>
				<td id='sat900'></id>
			</tr>

			<tr id='930'>
				<td>09:30</td>
				<td id='sun930'></id>
				<td id='mon930'></id>
				<td id='tues930'></id>
				<td id='wed930'></id>
				<td id='thurs930'></id>
				<td id='fri930'></id>
				<td id='sat930'></id>
			</tr>

			<tr id='1000'>
				<td>10:00</td>
				<td id='sun1000'></id>
				<td id='mon1000'></id>
				<td id='tues1000'></id>
				<td id='wed1000'></id>
				<td id='thurs1000'></id>
				<td id='fri1000'></id>
				<td id='sat1000'></id>
			</tr>

			<tr id='1030'>
				<td>10:30</td>
				<td id='sun1030'></id>
				<td id='mon1030'></id>
				<td id='tues1030'></id>
				<td id='wed1030'></id>
				<td id='thurs1030'></id>
				<td id='fri1030'></id>
				<td id='sat1030'></id>
			</tr>

			<tr id='1100'>
				<td>11:00</td>
				<td id='sun1100'></id>
				<td id='mon1100'></id>
				<td id='tues1100'></id>
				<td id='wed1100'></id>
				<td id='thurs1100'></id>
				<td id='fri1100'></id>
				<td id='sat1100'></id>
			</tr>

			<tr id='1130'>
				<td>11:30</td>
				<td id='sun1130'></id>
				<td id='mon1130'></id>
				<td id='tues1130'></id>
				<td id='wed1130'></id>
				<td id='thurs1130'></id>
				<td id='fri1130'></id>
				<td id='sat1130'></id>
			</tr>

			<tr id='1200'>
				<td>12:00</td>
				<td id='sun1200'></id>
				<td id='mon1200'></id>
				<td id='tues1200'></id>
				<td id='wed1200'></id>
				<td id='thurs1200'></id>
				<td id='fri1200'></id>
				<td id='sat1200'></id>
			</tr>

			<tr id='1230'>
				<td>12:30</td>
				<td id='sun1230'></id>
				<td id='mon1230'></id>
				<td id='tues1230'></id>
				<td id='wed1230'></id>
				<td id='thurs1230'></id>
				<td id='fri1230'></id>
				<td id='sat1230'></id>
			</tr>

			<tr id='1300'>
				<td>13:00</td>
				<td id='sun1300'></id>
				<td id='mon1300'></id>
				<td id='tues1300'></id>
				<td id='wed1300'></id>
				<td id='thurs1300'></id>
				<td id='fri1300'></id>
				<td id='sat1300'></id>
			</tr>

			<tr id='1330'>
				<td>13:30</td>
				<td id='sun1330'></id>
				<td id='mon1330'></id>
				<td id='tues1330'></id>
				<td id='wed1330'></id>
				<td id='thurs1330'></id>
				<td id='fri1330'></id>
				<td id='sat1330'></id>
			</tr>

			<tr id='1400'>
				<td>14:00</td>
				<td id='sun1400'></id>
				<td id='mon1400'></id>
				<td id='tues1400'></id>
				<td id='wed1400'></id>
				<td id='thurs1400'></id>
				<td id='fri1400'></id>
				<td id='sat1400'></id>
			</tr>

			<tr id='1430'>
				<td>14:30</td>
				<td id='sun1430'></id>
				<td id='mon1430'></id>
				<td id='tues1430'></id>
				<td id='wed1430'></id>
				<td id='thurs1430'></id>
				<td id='fri1430'></id>
				<td id='sat1430'></id>
			</tr>

			<tr id='1500'>
				<td>15:00</td>
				<td id='sun1500'></id>
				<td id='mon1500'></id>
				<td id='tues1500'></id>
				<td id='wed1500'></id>
				<td id='thurs1500'></id>
				<td id='fri1500'></id>
				<td id='sat1500'></id>
			</tr>

			<tr id='1530'>
				<td>15:30</td>
				<td id='sun1530'></id>
				<td id='mon1530'></id>
				<td id='tues1530'></id>
				<td id='wed1530'></id>
				<td id='thurs1530'></id>
				<td id='fri1530'></id>
				<td id='sat1530'></id>
			</tr>

			<tr id='1600'>
				<td>16:00</td>
				<td id='sun1600'></id>
				<td id='mon1600'></id>
				<td id='tues1600'></id>
				<td id='wed1600'></id>
				<td id='thurs1600'></id>
				<td id='fri1600'></id>
				<td id='sat1600'></id>
			</tr>

			<tr id='1630'>
				<td>16:30</td>
				<td id='sun1630'></id>
				<td id='mon1630'></id>
				<td id='tues1630'></id>
				<td id='wed1630'></id>
				<td id='thurs1630'></id>
				<td id='fri1630'></id>
				<td id='sat1630'></id>
			</tr>

			<tr id='1700'>
				<td>17:00</td>
				<td id='sun1700'></id>
				<td id='mon1700'></id>
				<td id='tues1700'></id>
				<td id='wed1700'></id>
				<td id='thurs1700'></id>
				<td id='fri1700'></id>
				<td id='sat1700'></id>
			</tr>

			<tr id='1730'>
				<td>17:30</td>
				<td id='sun1730'></id>
				<td id='mon1730'></id>
				<td id='tues1730'></id>
				<td id='wed1730'></id>
				<td id='thurs1730'></id>
				<td id='fri1730'></id>
				<td id='sat1730'></id>
			</tr>

			<tr id='1800'>
				<td>18:00</td>
				<td id='sun1800'></id>
				<td id='mon1800'></id>
				<td id='tues1800'></id>
				<td id='wed1800'></id>
				<td id='thurs1800'></id>
				<td id='fri1800'></id>
				<td id='sat1800'></id>
			</tr>

			<tr id='1830'>
				<td>18:30</td>
				<td id='sun1830'></id>
				<td id='mon1830'></id>
				<td id='tues1830'></id>
				<td id='wed1830'></id>
				<td id='thurs1830'></id>
				<td id='fri1830'></id>
				<td id='sat1830'></id>
			</tr>

			<tr id='1900'>
				<td>19:00</td>
				<td id='sun1900'></id>
				<td id='mon1900'></id>
				<td id='tues1900'></id>
				<td id='wed1900'></id>
				<td id='thurs1900'></id>
				<td id='fri1900'></id>
				<td id='sat1900'></id>
			</tr>

			<tr id='1930'>
				<td>19:30</td>
				<td id='sun1930'></id>
				<td id='mon1930'></id>
				<td id='tues1930'></id>
				<td id='wed1930'></id>
				<td id='thurs1930'></id>
				<td id='fri1930'></id>
				<td id='sat1930'></id>
			</tr>

			<tr id='2000'>
				<td>20:00</td>
				<td id='sun2000'></id>
				<td id='mon2000'></id>
				<td id='tues2000'></id>
				<td id='wed2000'></id>
				<td id='thurs2000'></id>
				<td id='fri2000'></id>
				<td id='sat2000'></id>
			</tr>

			<tr id='2030'>
				<td>20:30</td>
				<td id='sun2030'></id>
				<td id='mon2030'></id>
				<td id='tues2030'></id>
				<td id='wed2030'></id>
				<td id='thurs2030'></id>
				<td id='fri2030'></id>
				<td id='sat2030'></id>
			</tr>

			<tr id='2100'>
				<td>21:00</td>
				<td id='sun2100'></id>
				<td id='mon2100'></id>
				<td id='tues2100'></id>
				<td id='wed2100'></id>
				<td id='thurs2100'></id>
				<td id='fri2100'></id>
				<td id='sat2100'></id>
			</tr>

			<tr id='2130'>
				<td>21:30</td>
				<td id='sun2130'></id>
				<td id='mon2130'></id>
				<td id='tues2130'></id>
				<td id='wed2130'></id>
				<td id='thurs2130'></id>
				<td id='fri2130'></id>
				<td id='sat2130'></id>
			</tr>

			<tr id='2200'>
				<td>22:00</td>
				<td id='sun2200'></id>
				<td id='mon2200'></id>
				<td id='tues2200'></id>
				<td id='wed2200'></id>
				<td id='thurs2200'></id>
				<td id='fri2200'></id>
				<td id='sat2200'></id>
			</tr>

			<tr id='2230'>
				<td>22:30</td>
				<td id='sun2230'></id>
				<td id='mon2230'></id>
				<td id='tues2230'></id>
				<td id='wed2230'></id>
				<td id='thurs2230'></id>
				<td id='fri2230'></id>
				<td id='sat2230'></id>
			</tr>

			<tr id='2300'>
				<td>23:00</td>
				<td id='sun2300'></id>
				<td id='mon2300'></id>
				<td id='tues2300'></id>
				<td id='wed2300'></id>
				<td id='thurs2300'></id>
				<td id='fri2300'></id>
				<td id='sat2300'></id>
			</tr>

			<tr id='2330'>
				<td>23:30</td>
				<td id='sun2330'></id>
				<td id='mon2330'></id>
				<td id='tues2330'></id>
				<td id='wed2330'></id>
				<td id='thurs2330'></id>
				<td id='fri2330'></id>
				<td id='sat2330'></id>
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
			alert("Index: "+index);
			if(index == "") return;		//prevent using empty index
			var termSched = GlobalSched[index].getElementsByTagName('Section');
			alert("termSched: "+termSched);
			if(!termSched) return;	//Index did not return a proper schedule
			GlobalCurrentSched = index;
			alert("Got a schedule for displaying");
			var i=0;
			var day = 1;
			while(day < 8){
				switch(day){
					case 1:	//Sunday
 						break;
					case 2: //Monday
						if(termSched[i].getElementsByTagName('days')[0].textContent.indexOf("M") != -1){
							alert("Monday Class");
							var times = termSched[i].getElementsByTagName('time')[0].textContent.split("-");
							var startTime = normTime(times[0]);
							var endTime = normTime(times[1]);
							var tempTime = startTime;
							alert("Start time: <"+startTime+">");
							alert("End time: <"+endTime+">");
							//loop through until end time and add in those as well
							while(tempTime != endTime){
								document.getElementById('mon'+tempTime).innerHTML = termSched[i].getElementsByTagName('subjectID')[0].textContent + termSched[i].getElementsByTagName('courseNumber')[0].textContent;
								tempTime = incTime(tempTime);
							}
						}
						break;
					case 3: //Tuesday
						break;
					case 4: //Wednesday
						break;
					case 5: //Thursday
						break;
					case 6: //Friday
						break;
					case 7: //Saturday
						break;
				}
				i++;
				if(i > termSched.length){
					i=0;
					day++;
				}
			}
		
		}
		
		/* Sends the current schedule selected to the course server */
		function submitSchedule(){
			var request = new XMLHttpRequest();
			request.open("post","../php/courseServer.php",true);
			request.setRequestHeader("content-type","text/xml");
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
			var digit1 = '';
			var newTime = "";
			if(time.length == 3){
				newTime = '0' + time;
			}
			if(newTime.charAt(3) == '2'){
				newTime = newTime.charAt(0) + newTime.charAt(1) + "30";
			}else if(newTime.charAt(3) == '5'){
				if(newTime.charAt(0) == '0'){
					newTime = (parseInt(newTime.charAt(1))+1).toString() + "00";
				}else{
					newTime = (parseInt(newTime.charAt(0) + newTime.charAt(1)) + 1).toString() + "00";
				}
			}
			return newTime;
		}
		
		/* Function to increment time by the half hour */
		function incTime(time){
			var thisTime = time;
			if(thisTime.length != 4){
				thisTime = '0' + thisTime;
			}
			if(thisTime.slice(2,2) == "00"){
				thisTime = thisTime.slice(0,2) + "30";
			}else{
				thisTime = (parseInt(thisTime.slice(0,2)) + 1).toString() + "00";
			}
			return thisTime;
		}
		
	</script>
</html>