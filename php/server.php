<?php

	require_once("classes/database.php");	//get classes
	require_once("classes/Section.php");
	require_once("classes/pattern.php");
	require_once("classes/ScheduleCourseCalculator.php");

	$requestType = $_POST['requesttype'];
	
	$db = new DataBase("SchedulerDatabase");	//create database object
	
	switch (trim($requestType)) {
	
		case "GetCourseFile":	//reads in file specified by client and returns contents to client, deleting file after read
		
			$filename = $_POST['fileName'];	//get unique filename from client
			
			$handle = fopen($filename,"r");	//open file for reading
			$returnval = "";				//initialize return value
			while (!feof($handle)) {		
				$returnval .= fgets($handle);	//read from file intil EOF
			}
			fclose($handle);	//close file
			unlink($filename);	//delete file
			
			header("content-type: text/xml");
			echo $returnval;
			exit;
		
		case "OffPatternSchedule":
			
			$program = $_POST['program'];
			$year = $_POST['year'];
			$term = $_POST['term'];
			$coursesTaken = $_POST['coursesTaken'];
			$source = $_POST['source'];
			
			//generate possible schedules and send those schedules to schedule selection page
			$scheduleGen = new OffScheduleCourseCalculator($year, $program, $term, $coursesTaken);
			$ScheduleList = $scheduleGen->exportScedulesXML();
			$filename =  "../tempSchedules/".uniqid().".txt";	//create unique file in temp folder
			$handle = fopen($filename,"w");						//open file for writing
			fwrite($handle,$ScheduleList);						//output schedules to fileatime
			fclose($handle);									//close file		

			setcookie("yearCompleted", $year, time() + 3600, "/");
			setcookie("programName", $program, time() + 3600, "/");
			setcookie("term", $term, time() + 3600, "/");
			setcookie("courses", $filename, time() + 3600, "/");
			if($source == "html"){
				header("location:../pages/my_schedule.php");
			}else{
				echo "success-myschedule=";
				echo $filename;
			}
			exit;
			
		case "SubmitInfo":
			
			$program = $_POST['programName'];	//Retrieve info from client-side
			$year = $_POST['yearCompleted'];
			$term = $_POST['term'];
			$schedType = $_POST['sched'];
			$source = $_POST['source'];
			
			setcookie("yearCompleted", $year, time() + 3600, "/");	//Set cookies to send to client-side pages
			setcookie("programName", $program, time() + 3600, "/");
			setcookie("term", $term, time() + 3600, "/");
			
			if ($schedType == "off") {
				if($source == "html") {
					header("location:../pages/Off_Schedule_Courses.php");	//If off schedule send to off_schedule page so user can specify courses taken
				} else {
					header("content-type: text/plain");
					echo "success-offsched";
				}
				exit;
				
			} else {
				//If on schedule, generate possible schedules and send those schedules to schedule selection page
				$scheduleGen = new OnScheduleCourseCalculator($year, $program, $term);
				$ScheduleList = $scheduleGen->exportScedulesXML();
				$filename =  "../tempSchedules/".uniqid().".txt";	//create unique file in temp folder
				$handle = fopen($filename,"w");						//open file for writing
				fwrite($handle,$ScheduleList);						//output schedules to fileatime
				fclose($handle);									//close file		
				setcookie("courses", $filename, time() + 3600, "/");
				if($source == "html") {
					header("location:../pages/my_schedule.php");
				} else {
					header("content-type: text/plain");
					echo "success-onsched=";
					echo $filename;
				}
				exit;
			}
			
			exit;
	
		case "GetPattern":	//returns pattern of program specified in XML format

			$program = $_POST['program'];
			
			header("content-type: text/xml");

			$pat = new Pattern();
			
			//Select all courses within the pattern table whose program is equal to the one specified by user
			$getProgramPattern = "SELECT * FROM Patterns 
									WHERE ProgramID = '$program';";
			
			$rows = $db->execute($getProgramPattern);
			
			while ( ($row = $rows->fetch_object()) ) {
				//Add all courses found in query to list
				$newItem = new PatternItem($row->ProgramID,
										   $row->CourseType,
										   $row->YearRequired,
										   $row->TermRequired,
										   $row->SubjectID,
										   $row->CourseNumber);
			
				$pat->addItem($newItem);
			
			}
			
			//Return XML of pattern to client-side
			echo $pat->exportXML();
			
			exit;
		
		case "GetPrograms":
	
			//Retrieves all academic programs in database and returns them to client-side as XML string
	
			$getPrograms = "SELECT * FROM AcademicPrograms;";
			
			$rows = $db->execute($getPrograms);
			
			$returnval = "<programs>";
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$returnval .= "<program><ProgramID>".$row->ProgramID."</ProgramID>
							   <ProgramCode>".$row->ProgramCode."</ProgramCode></program>";
			
			}
			
			$returnval .= "</programs>";
			
			header("content-type: text/xml");
			echo $returnval;
			
			exit;
			
		case "GetElectives":
	
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
			$elecType = $_POST['electtype'];	//Need to specify which type of engineering elective
			
			//Get Engineering Electives of given type for specific program
			$getengElectives = "SELECT SubjectID, CourseNumber FROM Electives 
								WHERE ProgramID = '$program' AND 
								ElectiveType LIKE '%$elecType%';";
		
			$rows = $db->execute($getengElectives);
			
			$returnval = "<Electives>";
			
			//For every course in the electives add to XML
			while ( ($row = $rows->fetch_object()) ) {
				
				$returnval .= "<Elective><SubjectID>".$row->SubjectID."</SubjectID>
								<CourseNumber>".$row->CourseNumber."</CourseNumber></Elective>";
			}
			
			$returnval .= "</Electives>";
			
			header("content-type: text/xml");
			//Return XML of pattern to client-side
			echo $returnval;
	
			exit;
	
		default:
		
			//If client-side gave an invalid request type...
			header("content-type: text/plain");
			echo "Un-recognized request type";
			exit;
	}

?>