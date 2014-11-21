<?php

	class Section {
	
		
		public $subjectID;
		public $courseNum;
		public $year;
		public $term;
		public $title;
		public $credits;
		public $scheduleCode;
		public $sectionCode;
		public $time;
		public $days;
		public $capacity;
		public $numberOfStudents;
	
		function __construct($subjectID,
							 $courseNum,
							 $year,
							 $term,
							 $title,
							 $credits,
							 $scheduleCode,
							 $sectionCode,
							 $time,
							 $days,
							 $capacity,
							 $numStudents) {
		
			$this->subjectID = $subjectID;
			$this->courseNum = $courseNum;
			$this->year = $year;
			$this->term = $term;
			$this->title = $title;
			$this->credits = $credits;
			$this->scheduleCode = $scheduleCode;
			$this->sectionCode = $sectionCode;
			$this->time = $time;
			$this->days = $days;
			$this->capacity = $capacity;
			$this->numberOfStudents = $numStudents;
		
		}
	
		function isFull() {
			return $this->numberOfStudents == $this->capacity;
		}
		
		function exportXML() {
		
			return "<Section>
						<subjectID>".trim($this->subjectID)."</subjectID>
						<courseNum>".trim($this->courseNum)."</courseNum>
						<year>".trim($this->year)."</year>
						<term>".trim($this->term)."</term>
						<title>".trim($this->title)."</title>
						<credits>".trim($this->credits)."</credits>
						<scheduleCode>".trim($this->scheduleCode)."</scheduleCode>
						<sectionCode>".trim($this->sectionCode)."</sectionCode>
						<time>".trim($this->time)."</time>
						<days>".trim($this->days)."</days>
						<capacity>".trim($this->capacity)."</capacity>
						<numberOfStudents>".trim($this->numberOfStudents)."</numberOfStudents>
					</Section>";
		
		}
		
		function getPrereqFromString($prereqs) {
			
			$yearStatusID = strtoupper("-year status");
			$concurrentID = strtoupper("concurrently");
			$permissionID = strtoupper("permission");
			$programID = strtoupper("in");
			
			$prereqs = trim(strtoupper($prereqs));
			
		}
	
	}
	
	
	class SectionList {
	
		public $SectionItems;
		
		function __construct() {
			$SectionItems = array();
		}
	
		function exportXML() {
		
			$returnval = "<courses>";
			
			for ($i = 0; $i < count($this->SectionItems);$i = $i + 1) {
			
				$returnval .= $this->SectionItems[$i]->exportXML();
			}
		
			$returnval .= "</courses>";
			
			return $returnval;
		
		}
		
		function addItem($item) {
			array_push($this->patternItems,$item);
		}

	}

?>