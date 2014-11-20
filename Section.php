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
						<subjectID>".$this->subjectID."</subjectID>
						<courseNum>".$this->courseNum."</courseNum>
						<year>".$this->year."</year>
						<term>".$this->term."</term>
						<title>".$this->title."</title>
						<credits>".$this->credits."</credits>
						<scheduleCode>".$this->scheduleCode."</scheduleCode>
						<sectionCode>".$this->sectionCode."</sectionCode>
						<time>".$this->time."</time>
						<days>".$this->days."</days>
						<capacity>".$this->capacity."</capacity>
						<numberOfStudents>".$this->numberOfStudents."</numberOfStudents>
					</Section>";
		
		}
	
	}

?>