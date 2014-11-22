<?php

	require_once("classes/database.php");
	require_once("classes/Section.php");
	require_once("classes/pattern.php");
	require_once("classes/OnScheduleCourseCalculator.php");

	$requestType = $_POST['requesttype'];
	
	$db = new DataBase("SchedulerDatabase");
	
	switch ($requestType) {
	
		case "OffPatternSchedule":
			
			$program = $_POST['program'];
			$year = $_POST['year'];
			$term = $_POST['term'];
			$coursesTaken = $_POST['coursesTaken'];
			
			setcookie("yearCompleted", $year, time() + 3600, "/");
			setcookie("programName", $program, time() + 3600, "/");
			setcookie("term", $term, time() + 3600, "/");
			setcookie("courses", "Something", time() + 3600, "/");
			header("location:../pages/my_schedule.php");
			exit;
			
		case "SubmitInfo":	//WORKING
			
			$program = $_POST['programName'];	//Retrieve info from client-side
			$year = $_POST['yearCompleted'];
			$term = $_POST['term'];
			$schedType = $_POST['sched'];
			
			setcookie("yearCompleted", $year, time() + 3600, "/");	//Set cookies to send to client-side pages
			setcookie("programName", $program, time() + 3600, "/");
			setcookie("term", $term, time() + 3600, "/");
			
			if ($schedType == "off") {
				header("location:../pages/Off_Schedule_Courses.php");	//If off schedule sendto off_schedule page so user can specify courses taken
				exit;
				
			} else {
				//If on schedule, generate possible schedules and send those schedules to schedule selection page
				setcookie("courses", "", time() + 3600, "/");
				header("location:../pages/my_schedule.php");
				exit;
			}
			
			exit;
	
		case "GetPattern":	//WORKING

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
		
		case "RegisterCourse":
		
			$SubjectID = $_POST['SubjectID'];
			$CourseNumber = $_POST['CourseNumber'];
			$Section = $_POST['section'];
			$Term = $_POST['term'];
			$Year = $_POST['year'];
			
			//Query to lock table during transaction
			$lockSectionTable = "LOCK TABLE Section WRITE;";
			
			//Query to unlock table once completed transaction
			$unlockSectionTable = "UNLOCK TABLES;";
			
			//Query to increment given course
			$incrementSection = "UPDATE Section 
									SET NumberOfStudents = NumberOfStudents + 1 
									WHERE SubjectID = '$SubjectID' AND 
									CourseNumber = '$CourseNumber' AND 
									Term = '$Term' AND 
									SectionCode = '$Section';";
			
			//Query to retrieve given course, used to check that course is not already full
			$getSection = "SELECT * FROM Section 
							WHERE SubjectID = '$SubjectID' AND 
							CourseNumber = '$CourseNumber' AND 
							Term = '$Term';";
			
			$db->execute($lockSectionTable);	//lock DB
			
			$row = $db->execute($getSection);	//get section to be updated
			
			if (!is_null($row->Capacity)) {	//check if course has capacity
			
				$result = ($row->Capacity == $row->NumberOfStudents);	//ensure its not full
				
			} else {
				$result = false;	//if it doesn't it can be updated
			}
			
			if ($result == false) {	//if the section can be updated, update it
			
				$result = $db->execute($incrementSection);
			}
			
			$db->execute($unlockSectionTable);	//unlock table
			
			if ($result->num_rows = 1) {
				$returnval = "Section was successfully updated";
			} else { 
				$returnval = "Course is full";
			}
			
			header("content-type: text/plain");
			echo $returnval;
			
			exit;
			
		case "GetPrograms":	//WORKING
	
			//Retrieves all academic programs in database and returns them to client-side
	
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
			
		case "GetScienceElectives":
			
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
		
			//Retrieve all Electives to type Science from electives table for given program
			$getScienceElectives = "SELECT SubjectID, CourseNumber FROM Electives 
									WHERE ProgramID = '$program' AND 
									ElectiveType LIKE '%science%';";
		
			$rows = $db->execute($getScienceElectives);
			
			$courses = new SectionList();
			
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
			
		case "GetComplementaryElectives":
		
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
			
			//Retrieve all Electives to type Complementary from electives table for given program
			$getcomplementaryElectives = "SELECT * FROM Electives 
											WHERE ProgramID = '$program' AND 
											ElectiveType LIKE '%complementary%';";
		
			$rows = $db->execute($getcomplementaryElectives);
			
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
			
		case "GetEngineeringElectives":
	
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
			$elecType = $_POST['electtype'];	//Need to specidy which type of engineering elctive
			
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
	
		default:	//WORKING
		
			//If client-side gave an invalid request type...
			header("content-type: text/plain");
			echo "Un-recognized request type";
			exit;
	}

?>