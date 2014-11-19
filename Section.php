<?php

	class Section {
	
		
		public $subjectID;
		public $courseNum;
		public $CRN;
		public $scheduleCode;
		public $sectionCode;
		public $year;
		public $term;
		public $time;
		public $days;
		public $capacity;
		public $numberOfStudents;
		public $labsList;
	
		function __construct($subjectID,
							 $courseNum,
							 $CRN,
							 $scheduleCode,
							 $sectionCode,
							 $year,
							 $term,
							 $time,
							 $days,
							 $capacity,
							 $numStudents,
							 $lablist) {
		
		}
	
		function isFull() {
			return $numberOfStudents == $capacity;
		}
	
		function freeLabs() {
			
		}
	
	}

?>