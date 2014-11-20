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
		public $labsList;
		public $prereq;
	
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
							 $numStudents,
							 $lablist) {
		
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
			$this->labsList = $lablist;
		
		}
	
		function isFull() {
			return $this->numberOfStudents == $this->capacity;
		}
	
		function freeLabs() {
			
			$freelablist = array();
			
			for ($i = 0;$i < count($this->labsList);$i = $i + 1) {
			
				if ($this->labsList[$i]->capacity > $this->labsList[$i]->numberOfStudents) {
					array_push($freelablist,$this->labsList[$i]);
				}
			
			}
			
			return $freelablist;
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

?>