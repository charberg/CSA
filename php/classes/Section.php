<?php

	require_once("database.php");

	class Section {
		
		public $subjectID;	//Variables to reflect columns in Section table
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
	
		//Returns wether or not the Section is full
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
		
		//Returns true if course can be taken based on parameter list & year
		function testPrereq($prevCourses, $year) {
			
			$programID = "P[";
			$yearID = "S[";
			$courseID = "C[";
			$concourseID = "U[";
			$permissonID = "R[]";
			
			$db = new DataBase("SchedulerDatabase");
			
			$subID = $this->subjectID;
			$CN = $this->courseNum;
			
			$getPrereq = "SELECT Prerequisites FROM CourseToPrerequisiteMapping
							WHERE SubjectID LIKE '$subID' AND
							CourseNumber LIKE '$CN';";
			
			$result = $db->execute($getPrereq);
			
			if ($result->num_rows != 1) {
				return false;	//ERROR
			}
			
			$prereq = $result->fetch_object();
			
			
			
			
		}
		
		//Returns all labs that are not full
		function getLabs() {
		
			$db = new DataBase("SchedulerDatabase");
			
			$labs = new SectionList();
			
			$subID = $this->subjectID;
			$CN = $this->courseNum;
			$term = $this->term;
			$sectionLetter = trim($this->sectionCode);
			
			$sqlquery = "SELECT * FROM Section
							WHERE SubjectID LIKE '%$subID%'
								  AND CourseNumber LIKE '%$CN%'
								  AND Term LIKE '%$term%'
								  AND (SectionCode LIKE '%".$sectionLetter."_%' OR SectionCode LIKE '%L_%')
								  AND (NumberOfStudents < Capacity);";
			
			$rows = $db->execute($sqlquery);
			
			while ( ($row = $rows->fetch_object()) ) {
			
				$newItem = new Section($row->SubjectID,
									   $row->CourseNumber,
									   $row->Year,
									   $row->Term,
									   $row->Title,
									   $row->Credits,
									   $row->ScheduleCode,
									   $row->SectionCode,
									   $row->Time,
									   $row->Days,
									   $row->Capacity,
									   $row->NumberOfStudents);
			
				$labs->addItem($newItem);
			
			}
			return $labs;
			
		}
	
	}
	
	
	class SectionList {
	
		public $SectionItems;
		
		function __construct() {
			$this->SectionItems = array();
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
			array_push($this->SectionItems,$item);
		}
		
		function popItem() {
			return array_pop($this->SectionItems);
		}
		
		function itemAt($i) {
			return $this->SectionItems[$i];
		}
		
		function removeItem($i) {
			unset($this->SectionItems[$i]);
			$this->SectionItems = array_values($this->SectionItems);
		}
		
	}

?>