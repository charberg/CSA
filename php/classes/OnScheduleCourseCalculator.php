<?php

	require_once("database.php");
	require_once("Section.php");
	require_once("pattern.php");
	
	class OnScheduleCourseCalculator {
	
		private $year;	//Year completed
		private $term;	//Term that will be generated
		private $program;	//program to generate schedule for
		private $pattern;	//pattern of above program
		private $courses;	//courses that will be included in schedule
		
		private $Schedules;	//list of possible schedules
	
		function __construct($y, $p, $t) {
		
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
			
				//Get all pattern items that are one year more than the number of years the student has completed, and for the proper terms
				if ($this->pattern->patternItems[$i]->yearRequired == ($this->year + 1) 
					&& $this->pattern->patternItems[$i]->termRequired == $this->term) {
				
					//Select all non-full LECTURE sections in proper term, with matching SubjectID and CourseNumber
					$getCourseQuery = "SELECT * FROM Section
										AND Term = '$this->term'
										AND SubjectID = '$this->pattern->patternItems[$i]->subjectID'
										AND CourseNumber = '$this->pattern->patternItems[$i]->courseNumber'
										AND ScheduleCode = 'LEC'
										AND NumberOfStudents < Capacity;";
										
					$rows = $db->execute($getCourseQuery);
				
					//Go through all sections and add to course list
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
		
		function addToSchedule(&$m, &$t, &$w, &$h, &$f, $C) {
		
			$monday = array_merge(array(), $m);	//Make copies of array to work with temporarily through function
			$tuesday = array_merge(array(), $t);
			$wednesday = array_merge(array(), $w);
			$thursday = array_merge(array(), $h);
			$friday = array_merge(array(), $f);
			
			$days = str_split($C->days);
			
			$times = explode("-",$C->time);
			
			$starttime = substr($times[0],0,2).":".(substr($times[0],2,2)-5);//Always subtracrt 5 as either 05 or 35
			
			if (substr($times[1],2,2) == "25") {	//if end at 25, add 5 to make 30
				$endtime = substr($times[1],0,2).":".(substr($times[1],2,2)+5);
			} else {
				//If not (55) then make beginning of next hour
				$endtime = (substr($times[1],0,2)+1).":00";
			}
			
			//For every day the course is scheduled
			for ($i = 0; $i < count($days);$i = $i + 1) {
			
				switch ($days[$i]) {
				
					case 'M':
					
						if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
						} else {
							$nexthalfhour = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($monday[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$monday[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
									$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
								} else {
									$starttime = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								//Increment half hour end
								if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
									$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
								} else {
									$nexthalfhour = (substr($nexthalfhour,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								
							}//end if
							
						}//end while
					
					continue;
					case 'T':
					
						if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
						} else {
							$nexthalfhour = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($tuesday[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$tuesday[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
									$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
								} else {
									$starttime = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								//Increment half hour end
								if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
									$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
								} else {
									$nexthalfhour = (substr($nexthalfhour,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								
							}//end if
							
						}//end while
					
					continue;
					case 'W':
					
						if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
						} else {
							$nexthalfhour = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($wednesday[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$wednesday[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
									$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
								} else {
									$starttime = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								//Increment half hour end
								if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
									$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
								} else {
									$nexthalfhour = (substr($nexthalfhour,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								
							}//end if
							
						}//end while
					
					continue;
					case 'R':
					
						if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
						} else {
							$nexthalfhour = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($thursday[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$thursday[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
									$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
								} else {
									$starttime = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								//Increment half hour end
								if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
									$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
								} else {
									$nexthalfhour = (substr($nexthalfhour,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								
							}//end if
							
						}//end while
					
					continue;
					case 'F':
					
						if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
						} else {
							$nexthalfhour = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($friday[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$friday[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
									$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
								} else {
									$starttime = (substr($starttime,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								//Increment half hour end
								if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
									$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
								} else {
									$nexthalfhour = (substr($nexthalfhour,0,3) + 1)."00";	//if at halfhour, set to beginning of next hour
								}
								
							}//end if
							
						}//end while
					
					continue;
					default:
						return false;
				}
			
			}//end for (loop through days)
			
			//If gotten to this point then course was successfully added to schedule, return true and make day arrays = copies
			$m = array_merge(array(), $monday);	//make original arrays = new array schedules
			$t = array_merge(array(), $tuesday);
			$w = array_merge(array(), $wednesday);
			$h = array_merge(array(), $thursday);
			$f = array_merge(array(), $friday);
			
			return true;
		}
		
		function calculateConflictFreeSchedules() {
			
			//break apart courses into lists per course
			$classlist1 = new SectionList();
			$classlist1->addItem($this->courses->itemAt(0));	//Add first course to list (this will be what will be matched in loop)
			unset($this->courses[0]);	//Delete that item from original list
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {	//go through rest of list
				
				//If course is of same type as on in classlist then add it
				if ($this->courses[$i]->subjectID == $classlist1[0]->subjectID
					&& $this->courses[$i]->courseNum == $classlist1[0]->courseNum) {
					
					$classlist1->addItem($this->courses[$i]);//add to class list
					unset($this->courses[$i]);	//remove from original list
					$i = $i - 1;	//set i back 1 to accomidate deleted item
				}
				
			}
			
			$classlist2 = new SectionList();
			$classlist2->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $classlist2[0]->subjectID
					&& $this->courses[$i]->courseNum == $classlist2[0]->courseNum) {
					
					$classlist2->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$classlist3 = new SectionList();
			$classlist3->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $classlist3[0]->subjectID
					&& $this->courses[$i]->courseNum == $classlist3[0]->courseNum) {
					
					$classlist3->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$classlist4 = new SectionList();
			$classlist4->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $classlist4[0]->subjectID
					&& $this->courses[$i]->courseNum == $classlist4[0]->courseNum) {
					
					$classlist4->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			$classlist5 = new SectionList();
			$classlist5->addItem($this->courses[0]);
			unset($this->courses[0]);
			
			for ($i = 0; $i < count($this->courses);$i = $i + 1) {
			
				if ($this->courses[$i]->subjectID == $classlist5[0]->subjectID
					&& $this->courses[$i]->courseNum == $classlist5[0]->courseNum) {
					
					$classlist5->addItem($this->courses[$i]);
					unset($this->courses[$i]);
					$i = $i - 1;
				}
			
			}
			
			if (count($this->courses) > 0) {	///May not have 6th course, only create final list if 6th course exists
				$classlist6 = new SectionList();
				$classlist6->addItem($this->courses[0]);
				unset($this->courses[0]);
				
				for ($i = 0; $i < count($this->courses);$i = $i + 1) {
				
					if ($this->courses[$i]->subjectID == $classlist6[0]->subjectID
						&& $this->courses[$i]->courseNum == $classlist6[0]->courseNum) {
						
						$classlist6->addItem($this->courses[$i]);
						unset($this->courses[$i]);
						$i = $i - 1;
					}
				
				}
			}
			
			//Now each posibility of every course to be taken is in their own lists
			//Now go through each list, pick one and generate schedule
			//Run through all possibilities
			
			//Create arrays to keep track of what times are booked
			$monday = array("8:00-8:30" => 0,
							"8:30-9:00" => 0,
							"9:00-9:30" => 0,
							"9:30-10:00" => 0,
							"10:00-10:30" => 0,
							"10:30-11:00" => 0,
							"11:00-11:30" => 0,
							"11:30-12:00" => 0,
							"12:00-12:30" => 0,
							"12:30-13:00" => 0,
							"13:00-13:30" => 0,
							"13:30-14:00" => 0,
							"14:00-14:30" => 0,
							"14:30-15:00" => 0,
							"15:00-15:30" => 0,
							"15:30-16:00" => 0,
							"16:00-16:30" => 0,
							"16:30-17:00" => 0,
							"17:00-17:30" => 0,
							"17:30-18:00" => 0,
							"18:00-18:30" => 0,
							"18:30-19:00" => 0,
							"19:00-19:30" => 0,
							"19:30-20:00" => 0,
							"20:00-20:30" => 0,
							"20:30-21:00" => 0,
							"21:00-21:30" => 0,
							"21:30-22:00" => 0,
							"22:00-22:30" => 0,
							"22:30-23:00" => 0,
							"23:00-23:30" => 0,
							"23:30-24:00" => 0);
			
			$tuesday = array("8:00-8:30" => 0,
							"8:30-9:00" => 0,
							"9:00-9:30" => 0,
							"9:30-10:00" => 0,
							"10:00-10:30" => 0,
							"10:30-11:00" => 0,
							"11:00-11:30" => 0,
							"11:30-12:00" => 0,
							"12:00-12:30" => 0,
							"12:30-13:00" => 0,
							"13:00-13:30" => 0,
							"13:30-14:00" => 0,
							"14:00-14:30" => 0,
							"14:30-15:00" => 0,
							"15:00-15:30" => 0,
							"15:30-16:00" => 0,
							"16:00-16:30" => 0,
							"16:30-17:00" => 0,
							"17:00-17:30" => 0,
							"17:30-18:00" => 0,
							"18:00-18:30" => 0,
							"18:30-19:00" => 0,
							"19:00-19:30" => 0,
							"19:30-20:00" => 0,
							"20:00-20:30" => 0,
							"20:30-21:00" => 0,
							"21:00-21:30" => 0,
							"21:30-22:00" => 0,
							"22:00-22:30" => 0,
							"22:30-23:00" => 0,
							"23:00-23:30" => 0,
							"23:30-24:00" => 0);
			
			$wednesday = array("8:00-8:30" => 0,
							"8:30-9:00" => 0,
							"9:00-9:30" => 0,
							"9:30-10:00" => 0,
							"10:00-10:30" => 0,
							"10:30-11:00" => 0,
							"11:00-11:30" => 0,
							"11:30-12:00" => 0,
							"12:00-12:30" => 0,
							"12:30-13:00" => 0,
							"13:00-13:30" => 0,
							"13:30-14:00" => 0,
							"14:00-14:30" => 0,
							"14:30-15:00" => 0,
							"15:00-15:30" => 0,
							"15:30-16:00" => 0,
							"16:00-16:30" => 0,
							"16:30-17:00" => 0,
							"17:00-17:30" => 0,
							"17:30-18:00" => 0,
							"18:00-18:30" => 0,
							"18:30-19:00" => 0,
							"19:00-19:30" => 0,
							"19:30-20:00" => 0,
							"20:00-20:30" => 0,
							"20:30-21:00" => 0,
							"21:00-21:30" => 0,
							"21:30-22:00" => 0,
							"22:00-22:30" => 0,
							"22:30-23:00" => 0,
							"23:00-23:30" => 0,
							"23:30-24:00" => 0);
							
			$thursday = array("8:00-8:30" => 0,
							"8:30-9:00" => 0,
							"9:00-9:30" => 0,
							"9:30-10:00" => 0,
							"10:00-10:30" => 0,
							"10:30-11:00" => 0,
							"11:00-11:30" => 0,
							"11:30-12:00" => 0,
							"12:00-12:30" => 0,
							"12:30-13:00" => 0,
							"13:00-13:30" => 0,
							"13:30-14:00" => 0,
							"14:00-14:30" => 0,
							"14:30-15:00" => 0,
							"15:00-15:30" => 0,
							"15:30-16:00" => 0,
							"16:00-16:30" => 0,
							"16:30-17:00" => 0,
							"17:00-17:30" => 0,
							"17:30-18:00" => 0,
							"18:00-18:30" => 0,
							"18:30-19:00" => 0,
							"19:00-19:30" => 0,
							"19:30-20:00" => 0,
							"20:00-20:30" => 0,
							"20:30-21:00" => 0,
							"21:00-21:30" => 0,
							"21:30-22:00" => 0,
							"22:00-22:30" => 0,
							"22:30-23:00" => 0,
							"23:00-23:30" => 0,
							"23:30-24:00" => 0);
							
			$friday = array("8:00-8:30" => 0,
							"8:30-9:00" => 0,
							"9:00-9:30" => 0,
							"9:30-10:00" => 0,
							"10:00-10:30" => 0,
							"10:30-11:00" => 0,
							"11:00-11:30" => 0,
							"11:30-12:00" => 0,
							"12:00-12:30" => 0,
							"12:30-13:00" => 0,
							"13:00-13:30" => 0,
							"13:30-14:00" => 0,
							"14:00-14:30" => 0,
							"14:30-15:00" => 0,
							"15:00-15:30" => 0,
							"15:30-16:00" => 0,
							"16:00-16:30" => 0,
							"16:30-17:00" => 0,
							"17:00-17:30" => 0,
							"17:30-18:00" => 0,
							"18:00-18:30" => 0,
							"18:30-19:00" => 0,
							"19:00-19:30" => 0,
							"19:30-20:00" => 0,
							"20:00-20:30" => 0,
							"20:30-21:00" => 0,
							"21:00-21:30" => 0,
							"21:30-22:00" => 0,
							"22:00-22:30" => 0,
							"22:30-23:00" => 0,
							"23:00-23:30" => 0,
							"23:30-24:00" => 0);
							
			
			for ($c1 = 0; $c1 < count($classlist1);$c1 = $c1 + 1) {
			
				$class1 = $classlist1[$c1];	//pick class1
				
				if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class1) == false) {	//add class 1 to schedule
					continue;	//go to next course possibility
				}
				
				//get labs from class	
				$class1Labs = $class1->getLabs();
				
				for ($cl1 = 0; $cl1 < count($class1Labs);$cl1 = $cl1 + 1) {
					
					$class1lab = $class1Labs[$cl1];	//pick class 1 lab
					
					if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class1lab) == false) {	//add class 1 lab to schedule
						continue;	//go to next lab possibility
					}
					
					for ($c2 = 0; $c2 < count($classlist2);$c2 = $c2 + 1) {
						
						$class2 = $classlist2[$c2];	//pick class2
				
						if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class2) == false) {	//add class 2 to schedule
							continue;	//go to next course possibility
						}
						
						//get labs from class	
						$class2Labs = $class2->getLabs();
						
						for ($cl2 = 0; $cl2 < count($class2Labs);$cl2 = $cl2 + 1) {
							
							$class2lab = $class2Labs[$cl2];	//pick class 2 lab
							
							if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class2lab) == false) {	//add class 2 lab to schedule
								continue;	//go to next lab possibility
							}
							
							for ($c3 = 0; $c3 < count($classlist3);$c3 = $c3 + 1) {
						
								$class3 = $classlist3[$c3];	//pick class3
						
								if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class3) == false) {	//add class 3 to schedule
									continue;	//go to next course possibility
								}
								
								//get labs from class	
								$class3Labs = $class3->getLabs();
								
								for ($cl3 = 0; $cl3 < count($class3Labs);$cl3 = $cl3 + 1) {
									
									$class3lab = $class3Labs[$cl3];	//pick class 3 lab
									
									if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class3lab) == false) {	//add class 3 lab to schedule
										continue;	//go to next lab possibility
									}
									
									for ($c4 = 0; $c4 < count($classlist4);$c4 = $c4 + 1) {
						
										$class4 = $classlist4[$c4];	//pick class4
								
										if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class4) == false) {	//add class 4 to schedule
											continue;	//go to next course possibility
										}
										
										//get labs from class	
										$class4Labs = $class4->getLabs();
										
										for ($cl4 = 0; $cl4 < count($class4Labs);$cl4 = $cl4 + 1) {
											
											$class4lab = $class4Labs[$cl4];	//pick class 4 lab
											
											if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class4lab) == false) {	//add class 4 lab to schedule
												continue;	//go to next lab possibility
											}
											
											for ($c5 = 0; $c5 < count($classlist5);$c5 = $c5 + 1) {
						
												$class5 = $classlist5[$c5];	//pick class5
										
												if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class5) == false) {	//add class 5 to schedule
													continue;	//go to next course possibility
												}
												
												//get labs from class	
												$class5Labs = $class5->getLabs();
												
												for ($cl5 = 0; $cl5 < count($class5Labs);$cl5 = $cl5 + 1) {
													
													$class5lab = $class5Labs[$cl5];	//pick class 5 lab
													
													if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class5lab) == false) {	//add class 5 lab to schedule
														continue;	//go to next lab possibility
													}
													
													if (count($classlist6) > 0) {	//If there is 6 classes...

														for ($c6 = 0; $c6 < count($classlist6);$c6 = $c6 + 1) {
						
															$class6 = $classlist6[$c6];	//pick class6
													
															if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class6) == false) {	//add class 6 to schedule
																continue;	//go to next course possibility
															}
															
															//get labs from class	
															$class6Labs = $class6->getLabs();
															
															for ($cl6 = 0; $cl6 < count($class6Labs);$cl6 = $cl6 + 1) {
																
																$class6lab = $class6Labs[$cl6];	//pick class 6 lab
																
																if (addToSchedule($monday, $tuesday, $wednesday, $thursday, $friday, $class6lab) == false) {	//add class 6 lab to schedule
																	continue;	//go to next lab possibility
																}
																
																//Now have conflict free schedule, add all classes to list
																$sched = new SectionList();
																$sched->addItem($class1);
																$sched->addItem($class1lab);
																$sched->addItem($class2);
																$sched->addItem($class2lab);
																$sched->addItem($class3);
																$sched->addItem($class3lab);
																$sched->addItem($class4);
																$sched->addItem($class4lab);
																$sched->addItem($class5);
																$sched->addItem($class5lab);
																$sched->addItem($class6);
																$sched->addItem($class6lab);
																
																array_push($this->Schedules, $sched);
																
															}//for class6labs
														}//for class6
													} else {
														
														//Now have conflict free schedule, add all classes to list
														$sched = new SectionList();
														$sched->addItem($class1);
														$sched->addItem($class1lab);
														$sched->addItem($class2);
														$sched->addItem($class2lab);
														$sched->addItem($class3);
														$sched->addItem($class3lab);
														$sched->addItem($class4);
														$sched->addItem($class4lab);
														$sched->addItem($class5);
														$sched->addItem($class5lab);
														
														array_push($this->Schedules, $sched);
														
													}//if class 6 exists
												}//for class5labs
											}//for class5
										}//for class4labs
									}//for class4
								}//for class3labs
							}//for class3
						}//for class2labs
					}//for class2
				}//for class1labs
			}//for class1
			
		}
		
		//Export All schedules in XML format
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