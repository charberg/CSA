<?php

	require_once("classes/database.php");
	require_once("classes/Section.php");
	require_once("classes/pattern.php");
	require_once("classes/OnScheduleCourseCalculator.php");

	$requestType = $_POST['requesttype'];
	
	$db = new DataBase("SchedulerDatabase");
	
	switch ($requestType) {
	
		case "OffPatternSchedule":
			
			$program = $_POST['programName'];
			$year = $_POST['yearCompleted'];
			$term = $_POST['term'];
			$coursesTaken = $_POST['coursesTaken'];
			
			setcookie("yearCompleted", $year, time() + 3600, "/");
			setcookie("programName", $program, time() + 3600, "/");
			setcookie("term", $term, time() + 3600, "/");
			setcookie("courses", "Something", time() + 3600, "/");
			echo("FACK");
			header("location:../pages/my_schedule.php");
			exit;
			
		case "SubmitInfo":	//WORKING
			
			$program = $_POST['programName'];
			$year = $_POST['yearCompleted'];
			$term = $_POST['term'];
			$schedType = $_POST['sched'];
			
			setcookie("yearCompleted", $year, time() + 3600, "/");
			setcookie("programName", $program, time() + 3600, "/");
			setcookie("term", $term, time() + 3600, "/");
			
			if ($schedType == "off") {
				header("location:../pages/Off_Schedule_Courses.php");	
				exit;
				
			} else {
			
				setcookie("courses", "", time() + 3600, "/");
				header("location:../pages/my_schedule.php");
				exit;
			}
			
			exit;
	
		case "GetPattern":	//WORKING

			$program = $_POST['program'];
			
			header("content-type: text/xml");

			$pat = new Pattern();
			
			$getProgramPattern = "SELECT * FROM Patterns 
									WHERE ProgramID = '$program';";
			
			$rows = $db->execute($getProgramPattern);
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$newItem = new PatternItem($row->ProgramID,
										   $row->CourseType,
										   $row->YearRequired,
										   $row->TermRequired,
										   $row->SubjectID,
										   $row->CourseNumber);
			
				$pat->addItem($newItem);
			
			}
			
			echo $pat->exportXML();
			
			exit;
		
		case "RegisterCourse":
		
			$SubjectID = $_POST['SubjectID'];
			$CourseNumber = $_POST['CourseNumber'];
			$Section = $_POST['section'];
			$Term = $_POST['term'];
			$Year = $_POST['year'];
			
			$lockSectionTable = "LOCK TABLE Section WRITE;";
			$unlockSectionTable = "UNLOCK TABLES;";
			$incrementSection = "UPDATE Section 
									SET NumberOfStudents = NumberOfStudents + 1 
									WHERE SubjectID = '$SubjectID' AND 
									CourseNumber = '$CourseNumber' AND 
									Year = '$Year' AND 
									Term = '$Term' AND 
									SectionCode = '$Section';";
			$getSection = "SELECT * FROM Section 
							WHERE SubjectID = '$SubjectID' AND 
							CourseNumber = '$CourseNumber' AND 
							Year = '$Year' AND 
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
		
			$getScienceElectives = "SELECT SubjectID, CourseNumber FROM Electives 
									WHERE ProgramID = '$program' AND 
									ElectiveType LIKE '%science%';";
		
			$rows = $db->execute($getScienceElectives);
			
			$courses = new SectionList();
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$getSection = "SELECT * FROM Section 
								WHERE SubjectID = '$row->SubjectID' AND 
								CourseNumber = '$row->CourseNumber' AND
								Year = '$year' AND
								Term = '$term' AND
								ScheduleCode = 'LEC';";
			
				$sec = $db->execute($getSection);
			
				$newItem = new Section($sec->SubjectID,
									   $sec->CourseNumber,
									   $sec->Year,
									   $sec->Term,
									   $sec->Title,
									   $sec->Credits,
									   $sec->ScheduleCode,
									   $sec->SectionCode,
									   $sec->Time,
									   $sec->Days,
									   $sec->Capacity,
									   $sec->NumberOfStudents);
			
				$courses->addItem($newItem);
				
			}
			
			header("content-type: text/xml");
			echo $courses->exportXML();
			
			exit;
			
		case "GetComplementoryElectives":
		
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
		
			$getcomplementaryElectives = "SELECT SubjectID, CourseNumber FROM Electives 
											WHERE ProgramID = '$program' AND 
											ElectiveType LIKE '%complementary%';";
		
			$rows = $db->execute($getcomplementaryElectives);
			
			$courses = new SectionList();
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$getSection = "SELECT * FROM Section 
								WHERE SubjectID = '$row->SubjectID' AND 
								CourseNumber = '$row->CourseNumber' AND
								Year = '$year' AND
								Term = '$term' AND
								ScheduleCode = 'LEC';";
			
				$sec = $db->execute($getSection);
			
				$newItem = new Section($sec->SubjectID,
									   $sec->CourseNumber,
									   $sec->Year,
									   $sec->Term,
									   $sec->Title,
									   $sec->Credits,
									   $sec->ScheduleCode,
									   $sec->SectionCode,
									   $sec->Time,
									   $sec->Days,
									   $sec->Capacity,
									   $sec->NumberOfStudents);
			
				$courses->addItem($newItem);
				
			}
			
			header("content-type: text/xml");
			echo $courses->exportXML();
			
			exit;
			
		case "GetEngineeringElectives":
	
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
			$elecType = $_POST['electype'];
		
			$getengElectives = "SELECT SubjectID, CourseNumber FROM Electives 
								WHERE ProgramID = '$program' AND 
								ElectiveType LIKE '%$elecType%';";
		
			$rows = $db->execute($getengElectives);
			
			$courses = new SectionList();
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$getSection = "SELECT * FROM Section 
								WHERE SubjectID = '$row->SubjectID' AND 
								CourseNumber = '$row->CourseNumber' AND
								Year = '$year' AND
								Term = '$term' AND
								ScheduleCode = 'LEC';";
			
				$sec = $db->execute($getSection);
			
				$newItem = new Section($sec->SubjectID,
									   $sec->CourseNumber,
									   $sec->Year,
									   $sec->Term,
									   $sec->Title,
									   $sec->Credits,
									   $sec->ScheduleCode,
									   $sec->SectionCode,
									   $sec->Time,
									   $sec->Days,
									   $sec->Capacity,
									   $sec->NumberOfStudents);
			
				$courses->addItem($newItem);
				
			}
			
			header("content-type: text/xml");
			echo $courses->exportXML();
	
			exit;
	
		default:	//WORKING
			header("content-type: text/plain");
			echo "Un-recognized request type";
			exit;
	}	

?>