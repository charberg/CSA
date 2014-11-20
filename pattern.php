<?php

	class patternItem {
		
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
						<programID>".$this->programID."</programID>
						<courseType>".$this->courseType."</courseType>
						<yearRequired>".$this->yearRequired."</yearRequired>
						<termRequired>".$this->termRequired."</termRequired>
						<subjectID>".$this->subjectID."</subjectID>
						<courseNumber>".$this->courseNumber."</courseNumber>
					</item>";
		
		}
	
	}
	
	class pattern {
	
		public $patternItems;
	
		function __construct($items) {
		$this->patternItems = $items;
		}
	
		function exportXML() {
		
			$returnval = "<pattern>";
			
			for ($i = 0; $i < count($this->patternItems);$i = $i + 1) {
			
				$returnval .= $this->patternItems[$i]->exportXML();
			}
		
			$returnval .= "</pattern>";
			
			return $returnval;
		
		}
	
	}

?>