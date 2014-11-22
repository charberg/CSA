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
	$courses = $_COOKIE['courses'];
	
?>

<html>
	<!-- Shows the current schedule -->
	<head>
		<link type="text/css" rel="stylesheet" href="../css/my_schedule.css"/>
		<!-- Link to .css file -->
	</head>
	<body>
		<center>
		<h1>My Schedule</h1>
		<input type="button" onclick="switchPanel('fall')" value="FALL"/>
		<input type="button" onclick="switchPanel('winter')" value="WINTER"/>
		<p id="head">FALL TERM</p>
		<input type="button" onclick="serverSend('addcourse')" value="ADD COURSE"/>
		<p id="result"></p>
		
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
			<tr id="0700">
				<td>07:00</td>
				<td id="sun0700"/>
				<td id="mon0700"/>
				<td id="tues0700"/>
				<td id="wed0700"/>
				<td id="thurs0700"/>
				<td id="fri0700"/>
				<td id="sat0700"/>
			</tr>
			<tr id="0730">
				<td>07:30</td>
				<td id="sun0730"/>
				<td id="mon0730"/>
				<td id="tues0730"/>
				<td id="wed0730"/>
				<td id="thurs0730"/>
				<td id="fri0730"/>
				<td id="sat0730"/>
			</tr>
			<tr id="0800">
				<td>08:00</td>
			</tr>
			<tr id="0830">
				<td>08:30</td>
			</tr>
			<tr id="0900">
				<td>09:00</td>
			</tr>
			<tr id="0930">
				<td>09:30</td>
			</tr>
			<tr id="1000">
				<td>10:00</td>
			</tr>
			<tr id="1030">
				<td>10:30</td>
			</tr>
			<tr id="1100">
				<td>11:00</td>
			</tr>
			<tr id="1130">
				<td>11:30</td>
			</tr>
			<tr id="1200">
				<td>12:00</td>
			</tr>
			<tr id="1230">
				<td>12:30</td>
			</tr>
			<tr id="1300">
				<td>13:00</td>
			</tr>
			<tr id="1330">
				<td>13:30</td>
			</tr>
			<tr id="1400">
				<td>14:00</td>
			</tr>
			<tr id="1430">
				<td>14:30</td>
			</tr>
			<tr id="1500">
				<td>15:00</td>
			</tr>
			<tr id="1530">
				<td>15:30</td>
			</tr>
			<tr id="1600">
				<td>16:00</td>
			</tr>
			<tr id="1630">
				<td>16:30</td>
			</tr>
			<tr id="1700">
				<td>17:00</td>
			</tr>
			<tr id="1730">
				<td>17:30</td>
			</tr>
			<tr id="1800">
				<td>18:00</td>
			</tr>
			<tr id="1830">
				<td>18:30</td>
			</tr>
			<tr id="1900">
				<td>19:00</td>
			</tr>
			<tr id="1930">
				<td>19:30</td>
			</tr>
			<tr id="2000">
				<td>20:00</td>
			</tr>
			<tr id="2030">
				<td>20:30</td>
			</tr>
			<tr id="2100">
				<td>21:00</td>
			</tr>
			<tr id="2130">
				<td>21:30</td>
			</tr>
			<tr id="2200">
				<td>22:00</td>
			</tr>
			<tr id="2230">
				<td>22:30</td>
			</tr>
			<tr id="2300">
				<td>23:00</td>
			</tr>
			<tr id="2330">
				<td>23:30</td>
			</tr>
		</table>
		</center>
	</body>
	<script>
		function switchPanel(term){
			if(term == 'fall'){
				document.getElementById('head').innerHTML = "FALL TERM";
			}else{
				document.getElementById('head').innerHTML = "WINTER TERM";
			}
		}
		
		function serverSend(command){
			document.getElementById('result').innerHTML = "SEND TO SERVER: "+command;
			document.getElementById('sun0700').innerHTML = " ECOR1010A<input type='button' onclick='' value='X'/>";
		}
		
		function fillTable(term){
			//This function should fill the timetable with the courses selected for the term argument
		}
	</script>
</html>