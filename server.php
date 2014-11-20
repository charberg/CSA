<?php

	require_once("database.php");
	require_once("Section.php");
	require_once("pattern.php");

	$requestType = $_POST['requesttype'];
	
	$db = new DataBase("SchedulerDatabase");
	
	switch ($requestType) {
	
		case "GetPattern":

			$program = $_POST['program'];
			
			$pat = new Pattern();
			
			$getProgramPattern = "SELECT * FROM AcademicProgramToCourseMapping WHERE ProgramID = '$program';";
			
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
			
			header("content-type: text/xml");
			echo $pat->exportXML();
			
			exit;
		
		case "RegisterCourses":
		
			
			exit;
			
		case "getPrograms":
	
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
	
		case "getScienceElectives":
		
			exit;
			
		case "GetEngineeringElectives":
	
			exit;
	
		default:
			header("content-type: text/plain");
			echo "Un-recognized request type";
			exit;
	}	

?>