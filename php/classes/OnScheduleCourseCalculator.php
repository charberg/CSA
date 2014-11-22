<?php

	require_once("database.php");
	require_once("Section.php");
	require_once("pattern.php");
	
	class OnScheduleCourseCalculator {
	
		private $year;
		private $term;
		private $program;
		private $pattern;
		private $courses;
		
		private $Schedules;
	
		function __construct($y, $p, $c, $t) {
		
			$this->year = $y;
			$this->program = $p;
			$this->courses = new SectionList();
			$this->term = $t;
			$this->Schedules = array();	//Array of SectionLists
			$this->pattern = Pattern::getPatternByProgram($this->program);
			$this->calculateCourses();
			$this->calculateConflictFreeSchedules();
		}
		
		/*private function getCoursesCompleted() {
		
			$db = new Database("SchedulerDatabase");
			
			for ($i = 0; $i < count($this->pattern->patternItems);$i = $i + 1) {	//parse through all courses in pattern
			
				if ($this->pattern->patternItems[$i]->yearRequired < $this->year) {	//if that courses year requirement is less than or equal to the year the user is at, then add that course to the ones completed
				
					$getCourseQuery = "SELECT * FROM Section
										WHERE Year = '$this->pattern->patternItems[$i]->yearRequired'
										AND Term = '$this->term'
										AND SubjectID = '$this->pattern->patternItems[$i]->subjectID'
										AND CourseNumber = '$this->pattern->patternItems[$i]->courseNumber'
										AND ScheduleCode = 'LEC';";
										
					$rows = $db->execute($getCourseQuery);
				
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
			
						$this->courses->addItem($newItem);
					
					}
				
				}	//end if patternItem <= year completed
			
				//if pattern item is of given year  + 1, and calculating courses for winter, all fall courses of this year add to list
				if ($this->pattern->patternItems[$i]->yearRequired == $this->year
					&& $this->pattern->patternItems[$i]->termRequired == 'fall'
					&& $this->term == 'winter') {
					
					$getCourseQuery = "SELECT * FROM Section
										WHERE Year = '$this->year'
										AND SubjectID = '$this->pattern->patternItems[$i]->subjectID'
										AND CourseNumber = '$this->pattern->patternItems[$i]->courseNumber'
										AND ScheduleCode = 'LEC'
										AND Term = 'fall';";
										
					$rows = $db->execute($getCourseQuery);
				
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
			
						$this->courses->addItem($newItem);
					
					}
					
				}
			
			}	//end for each pattern item
		
		}*/
	
		function calculateCourses() {
		
			$db = new Database("SchedulerDatabase");
			
			for ($i = 0; $i < count($this->pattern->patternItems);$i = $i + 1) {	//parse through all courses in pattern
			
				if ($this->pattern->patternItems[$i]->yearRequired == $this->year
					&& $this->pattern->patternItems[$i]->termRequired == $this->term) {
				
					$getCourseQuery = "SELECT * FROM Section
										WHERE Year = '$this->year'
										AND Term = '$this->term'
										AND SubjectID = '$this->pattern->patternItems[$i]->subjectID'
										AND CourseNumber = '$this->pattern->patternItems[$i]->courseNumber'
										AND ScheduleCode = 'LEC'
										AND NumberOfStudents < Capacity;";
										
					$rows = $db->execute($getCourseQuery);
				
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
			
						$this->courses->addItem($newItem);
				
					}	//end while
				}	//end if
			}	//end for
			
			//Should now have all possible lectures that still have room
			//Now need to make conflict free schedules using courses found
			
		}	//end function
		
		function calculateConflictFreeSchedules() {
		
			//break apart courses into lists per course
			$class1 = new SectionList();
			$class1->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $class1[0]->subjectID
					&& $this->courses[$i]->courseNum == $class1[0]->courseNum) {
					
					$class1->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$class2 = new SectionList();
			$class2->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $class2[0]->subjectID
					&& $this->courses[$i]->courseNum == $class2[0]->courseNum) {
					
					$class2->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$class3 = new SectionList();
			$class3->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $class3[0]->subjectID
					&& $this->courses[$i]->courseNum == $class3[0]->courseNum) {
					
					$class3->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$class4 = new SectionList();
			$class4->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $class4[0]->subjectID
					&& $this->courses[$i]->courseNum == $class4[0]->courseNum) {
					
					$class4->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$class5 = new SectionList();
			$class5->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $class5[0]->subjectID
					&& $this->courses[$i]->courseNum == $class5[0]->courseNum) {
					
					$class5->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			if (count($this->courses) > 0) {	///May not have 6th course, only create final list if 6th course exists
				$class6 = new SectionList();
				$class6->addItem($this->courses[0]);
				unset($this->courses[0]);
				
				for ($i = 0; $i < count($this->courses);$i = $i + 1) {
				
					if ($this->courses[$i]->subjectID == $class6[0]->subjectID
						&& $this->courses[$i]->courseNum == $class6[0]->courseNum) {
						
						$class6->addItem($this->courses[$i]);
						unset($this->courses[$i]);
						$i = $i - 1;
					}
				
				}
			}
			
			
		
		}
		
		
		
		function exportScedulesXML() {
		
			$returnval = "<shedules>";
			
			for ($i = 0; $i < count($this->patternItems);$i = $i + 1) {
			
				$returnval .= $this->Schedules[$i]->exportXML();
			}
		
			$returnval .= "</shedules>";
			
			return $returnval;
		}
		
	}

?>