<?php
	
	//Script to register courses user has choosen
	
	require_once("classes/database.php");
	require_once("classes/Section.php");
	
	header("content-type: text/plain");
	
	$db = new DataBase("SchedulerDatabase");
	
	$courseList = $_POST['xml'];
	
	$coursesObject = simplexml_load_string($courseList);	//load xml string of courses into XML object
	
	$Sections = new SectionList();
	
	//List of sections that have already been updated, will be used in case one section fails to update, will revert 
	$SectionsUpdated = new SectionList();	
	
	//Create section list out of courses return by client
	foreach ($coursesObject->children() as $course) { 
	
		$Sections->addItem(new Section($course->children()[0]->__toString(),
									   $course->children()[1]->__toString(),
									   $course->children()[2]->__toString(),
									   $course->children()[3]->__toString(),
									   $course->children()[4]->__toString(),
									   $course->children()[5]->__toString(),
									   $course->children()[6]->__toString(),
									   $course->children()[7]->__toString(),
									   $course->children()[8]->__toString(),
									   $course->children()[9]->__toString(),
									   $course->children()[10]->__toString(),
									   $course->children()[11]->__toString()));
									  
	}
	
	//Query to lock table during transaction
	$lockSectionTable = "LOCK TABLE Section WRITE;";
	
	//Query to unlock table once completed transaction
	$unlockSectionTable = "UNLOCK TABLES;";
	
	$revert = false;	//boolean to flag if revert required
	
	$db->execute($lockSectionTable);	//lock DB
	
	for ($i = 0; $i < count($Sections->SectionItems) && $revert == false;$i = $i + 1) {
	
		$subID = $Sections->itemAt($i)->subjectID;
		$CN = $Sections->itemAt($i)->courseNum;
		$SC = $Sections->itemAt($i)->sectionCode;
		$year = $Sections->itemAt($i)->year;
		$term = $Sections->itemAt($i)->term;
	
		//Query to retrieve given course, used to check that course is not already full
		$getSection = "SELECT * FROM Section 
						WHERE SubjectID LIKE '%$subID%' AND 
						CourseNumber LIKE '%$CN%' AND 
						SectionCode = '$SC' AND
						Year = $year AND
						Term LIKE '%$term%';";
		
		$row = $db->execute($getSection);	//get section to be updated
	
		if ($row->num_rows != 1) {
			$revert = true;
		} else {
		
			$sec = $row->fetch_object();
			
			if (!is_null($sec->Capacity)) {	//check if course has capacity
				$result = ($sec->Capacity <= $sec->NumberOfStudents);	//ensure its not full
			} else {
				$result = false;	//if it doesn't it can be updated
			}
			
			if ($result == false) {	//if the section can be updated, update it
			
				//Query to increment given course
				$incrementSection = "UPDATE Section 
									 SET NumberOfStudents = NumberOfStudents + 1 
									 WHERE SubjectID LIKE '%$subID%' AND 
									 CourseNumber LIKE '%$CN%' AND 
									 SectionCode = '$SC' AND
									 Year = $year AND
									 Term LIKE '%$term%';";
				
				$incrementresult = $db->execute($incrementSection);
				
				if ($incrementresult != 1) {	//if increment failed
					$revert = true;
				} else {
					$SectionsUpdated->addItem($Sections->itemAt($i));	//else add to list of updated sections
				}
				
			} else {
				$revert = true;
			}
		}
	}
	
	if ($revert) {	//if revert is true, run through list of courses already updated and undo update
	
		for ($i = 0; $i < count($SectionsUpdated->SectionItems);$i = $i + 1) {
		
			$subID = $Sections->itemAt($i)->subjectID;
			$CN = $Sections->itemAt($i)->courseNum;
			$SC = $Sections->itemAt($i)->sectionCode;
			$year = $Sections->itemAt($i)->year;
			$term = $Sections->itemAt($i)->term;
			
			//Query to decrement given course
			$decrementSection = "UPDATE Section 
								SET NumberOfStudents = NumberOfStudents - 1 
								WHERE SubjectID LIKE '%$subID%' AND 
								CourseNumber LIKE '%$CN%' AND 
								SectionCode LIKE '%$SC%' AND
								Year = $year AND
								Term LIKE '%$term%';";
								
			$db->execute($decrementSection);
		}
	}
	
	$db->execute($unlockSectionTable);	//unlock table

	if ($revert) {	//Return to client outcome of update
		echo "FAIL";
	} else {
		echo "PASS";
	}
	exit;
	
?>