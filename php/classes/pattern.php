<?php

	require_once("database.php");

	class PatternItem {
		
		public $programID;	//Variables to reflect columns in Pattern Table
		public $courseType;
		public $yearRequired;
		public $termRequired;
		public $subjectID;
		public $courseNumber;
		
		function __construct($programID,
							$courseType,
							$yearRequired,
							$termRequired,
							$subjectID,
							$courseNumber) {
		
		$this->programID = $programID;
		$this->courseType = $courseType;
		$this->yearRequired = $yearRequired;
		$this->termRequired = $termRequired;
		$this->subjectID = $subjectID;
		$this->courseNumber = $courseNumber;
		
		}
	
		function exportXML() {
		
			return "<item>
						<programID>".trim($this->programID)."</programID>
						<courseType>".trim($this->courseType)."</courseType>
						<yearRequired>".trim($this->yearRequired)."</yearRequired>
						<termRequired>".trim($this->termRequired)."</termRequired>
						<subjectID>".trim($this->subjectID)."</subjectID>
						<courseNumber>".trim($this->courseNumber)."</courseNumber>
					</item>";
		
		}
	
	}
	
	class Pattern {
	
		public $patternItems;
	
		function __construct() {
			$this->patternItems = array();
		}
	
		function exportXML() {
		
			$returnval = "<pattern>";
			
			for ($i = 0; $i < count($this->patternItems);$i = $i + 1) {
			
				$returnval .= $this->patternItems[$i]->exportXML();
			}
		
			$returnval .= "</pattern>";
			
			return $returnval;
		
		}
		
		function addItem($item) {
			array_push($this->patternItems,$item);
		}
		
		static function getPatternByProgram($program) {
		
			$pattern = new Pattern();
		
			$db = new DataBase("SchedulerDatabase");
		
			$getProgramPattern = "SELECT * FROM Patterns 
									WHERE ProgramID = '$program'
									ORDER BY YearRequired, TermRequired, SubjectID;";
			
			$rows = $db->execute($getProgramPattern);
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$newItem = new PatternItem($row->ProgramID,
										   $row->CourseType,
										   $row->YearRequired,
										   $row->TermRequired,
										   $row->SubjectID,
										   $row->CourseNumber);
			
				$pattern->addItem($newItem);
			
			}
			return $pattern;
		}
	
	}

?>