<?php
	
	//to register courses
	require_once("classes/database.php");
	require_once("classes/Section.php");
	
	$db = new DataBase("SchedulerDatabase");
	
	
	$courseList = $_POST['courses'];
	
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
			
	
?>