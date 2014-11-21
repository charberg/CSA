<?php

	class PatternItem {
		
		public $programID;
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
	
	}

?>