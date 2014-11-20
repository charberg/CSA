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
		
		$this->programID = trim($programID);
		$this->courseType = trim($courseType);
		$this->yearRequired = trim($yearRequired);
		$this->termRequired = trim($termRequired);
		$this->subjectID = trim($subjectID);
		$this->courseNumber = trim($courseNumber);
		
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