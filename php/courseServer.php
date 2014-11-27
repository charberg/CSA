<?php
	
	//to register courses
	require_once("classes/database.php");
	require_once("classes/Section.php");
	
	header("content-type: text/plain");

	$db = new DataBase("SchedulerDatabase");
	
	$courseList = $_POST['xml']
	$coursesObject = simplexml_load_string($courseList);

	echo count($coursesObject->courses);
	exit;
	
	$Sections = new SectionList();
	
	//List of section that have already been updated, will be used incase one section fails to update, will revert 
	$SectionsUpdated = new SectionList();	
	
	//Create section list out of courses return by client
	for ($i = 0;$i < count($coursesObject->courses);$i = $i + 1) { 
	
		$Sections->addItem(new Section($coursesObject->courses[$i]->subjectID,
									   $coursesObject->courses[$i]->courseNum,
									   $coursesObject->courses[$i]->year,
									   $coursesObject->courses[$i]->term,
									   $coursesObject->courses[$i]->title,
									   $coursesObject->courses[$i]->credits,
									   $coursesObject->courses[$i]->scheduleCode,
									   $coursesObject->courses[$i]->sectionCode,
									   $coursesObject->courses[$i]->time,
									   $coursesObject->courses[$i]->days,
									   $coursesObject->courses[$i]->capacity,
									   $coursesObject->courses[$i]->numberOfStudents));
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
						SectionCode LIKE '%$SC%' AND
						Year = $year AND
						Term LIKE '%$term%';";
		
		$row = $db->execute($getSection);	//get section to be updated
	
		if ($row->num_rows != 1) {
			$revert = true;
		} else {
		
			$sec = $row->fetch_object();
			
			if (!is_null($sec->Capacity)) {	//check if course has capacity
				$result = ($row->Capacity == $row->NumberOfStudents);	//ensure its not full
			} else {
				$result = false;	//if it doesn't it can be updated
			}
			
			if ($result == false) {	//if the section can be updated, update it
			
				//Query to increment given course
				$incrementSection = "UPDATE Section 
									SET NumberOfStudents = NumberOfStudents + 1 
									WHERE SubjectID LIKE '%$subID%' AND 
									CourseNumber LIKE '%$CN%' AND 
									SectionCode LIKE '%$SC%' AND
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
	
	if ($revert) {
	
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

	if ($revert) {
		echo "FAIL";
	} else {
		echo "PASS";
	}
	exit;
	
?>