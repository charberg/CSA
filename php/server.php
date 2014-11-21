<?php

	require_once("classes/database.php");
	require_once("classes/Section.php");
	require_once("classes/pattern.php");

	$requestType = $_POST['requesttype'];
	
	$db = new DataBase("SchedulerDatabase");
	
	switch ($requestType) {
	
		case "GetPattern":

			$program = $_POST['program'];
			
			header("content-type: text/xml");

			$pat = new Pattern();
			
			$getProgramPattern = "SELECT * FROM Patterns WHERE ProgramID = '$program';";
			
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
		
		case "RegisterCourses":
		
			
			exit;
			
		case "GetPrograms":
	
			$getPrograms = "SELECT * FROM AcademicPrograms;";
			
			$rows = $db->execute($getProgramPattern);
			
			$returnval = "<programs>";
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$returnval .= "<ProgramID>".$row->ProgramID."</ProgramID>
							   <ProgramCode>".$row->ProgramCode."</ProgramCode>";
			
			}
			
			$returnval .= "</programs>";
			
			header("content-type: text/xml");
			echo $returnval;
	
			exit;
	
		case "GetScienceElectives":
		
			$program = $_POST['program'];
			$term = $_POST['term'];
			$year = $_POST['year'];
		
			$getScienceElectives = "SELECT SubjectID, CourseNumber FROM Electives WHERE ProgramID = '$program' AND ElectiveType LIKE '%science%';";
		
			$rows = $db->execute($getScienceElectives);
			
			$courses = new SectionList();
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$getSection = "SELECT * FROM Section WHERE SubjectID = '$row->SubjectID.' AND CourseNumber = '$row->CourseNumber;';";
			
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
		
			$getcomplementaryElectives = "SELECT SubjectID, CourseNumber FROM Electives WHERE ProgramID = '$program' AND ElectiveType LIKE '%complementary%';";
		
			$rows = $db->execute($getcomplementaryElectives);
			
			$courses = new SectionList();
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$getSection = "SELECT * FROM Section WHERE SubjectID = '$row->SubjectID.' AND CourseNumber = '$row->CourseNumber;';";
			
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
		
			$getengElectives = "SELECT SubjectID, CourseNumber FROM Electives WHERE ProgramID = '$program' AND ElectiveType LIKE '%$elecType%';";
		
			$rows = $db->execute($getengElectives);
			
			$courses = new SectionList();
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$getSection = "SELECT * FROM Section WHERE SubjectID = '$row->SubjectID.' AND CourseNumber = '$row->CourseNumber;';";
			
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
	
		default:
			header("content-type: text/plain");
			echo "Un-recognized request type";
			exit;
	}	

?>