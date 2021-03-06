<?php

	require_once("database.php");
	require_once("Section.php");
	require_once("pattern.php");
	
	
	class PrereqNode {
			
		/*
			Class to represent possible prereq requirement set. If the user has passed all items
			in prereqs array then he/she is allowed to take class being considered
		*/
			
		public $children;
		public $prereqs;
		
		function __construct() {
		
			$this->children = array();
			$this->prereqs = array();
		}
		
	}
	
	abstract class ScheduleCourseCalculator {
		
		const NUMSCHED = 10;
	
		protected $counter;	//keeps track of number of schedules
	
		protected $year;	//Year completed
		protected $term;	//Term that will be generated
		protected $program;	//program to generate schedule for
		protected $pattern;	//pattern of above program
		protected $courses;	//courses list that schedules will be made from
		protected $Schedules;	//list of possible schedules
		protected $monday, $tuesday, $wednesday, $thursday, $friday;

		function __construct($y, $p, $t) {
		
			$this->counter = 0;
			$this->year = $y;
			$this->program = $p;
			$this->courses = new SectionList();
			$this->term = $t;
			$this->cursched = new SectionList();
			$this->Schedules = array();	//Array of SectionLists
			
			$this->pattern = Pattern::getPatternByProgram($this->program);
			$this->calculateCourses();
			$this->calculateConflictFreeSchedules();
		}

		abstract function calculateCourses();
		
		function addToSchedule($C) {
		
			$m = array_merge(array(), $this->monday);	//Make copies of array to work with temporarily through function
			$t = array_merge(array(), $this->tuesday);
			$w = array_merge(array(), $this->wednesday);
			$r = array_merge(array(), $this->thursday);
			$f = array_merge(array(), $this->friday);
			
			$days = str_split($C->days);
			
			$times = explode("-",$C->time);
			
			if (strlen($times[0]) == 4) {	//if time is >= 1000
				if (substr($times[0],2,2) == "05") {
					$starttime = substr($times[0],0,2).":00"; //Make 00 if 05
				} else {
					$starttime = substr($times[0],0,2).":".(substr($times[0],2,2)-5); //Subtract 5 from 35 to make 30
				}
			} else {	//Else if time < 1000 (ex. 900, 800...)
				if (substr($times[0],1,2) == "05") {
					$starttime = substr($times[0],0,1).":00"; //Make 00 if 05
				} else {
					$starttime = substr($times[0],0,1).":".(substr($times[0],1,2)-5); //Subtract 5 from 35 to make 30
				}
			}

			if (strlen($times[1]) == 4) {	//if time is >= 1000
				if (substr($times[1],2,2) == "25") {	//if end at 25, add 5 to make 30
					$endtime = substr($times[1],0,2).":".(substr($times[1],2,2)+5);
				} else {
					//If not (55) then make beginning of next hour
					$endtime = (substr($times[1],0,2)+1).":00";
				}
			} else {
				if (substr($times[1],1,2) == "25") {	//if end at 25, add 5 to make 30
					$endtime = substr($times[1],0,1).":".(substr($times[1],1,2)+5);
				} else {
					//If not (55) then make beginning of next hour
					$endtime = (substr($times[1],0,1)+1).":00";
				}
			}
			
			//For every day the course is scheduled
			for ($i = 0; $i < count($days);$i = $i + 1) {
	
				if (strlen($times[0]) == 4) {	//if time is >= 1000
					if (substr($times[0],2,2) == "05") {
						$starttime = substr($times[0],0,2).":00"; //Make 00 if 05
					} else {
						$starttime = substr($times[0],0,2).":".(substr($times[0],2,2)-5); //Subtract 5 from 35 to make 30
					}
				} else {	//Else if time < 1000 (ex. 900, 800...)
					if (substr($times[0],1,2) == "05") {
						$starttime = substr($times[0],0,1).":00"; //Make 00 if 05
					} else {
						$starttime = substr($times[0],0,1).":".(substr($times[0],1,2)-5); //Subtract 5 from 35 to make 30
					}
				}
				
				switch ($days[$i]) {
				
					case 'M':
					
						if (strlen($starttime) == 5) {	//if time >= 10:00
							if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						} else {
							if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,2).(substr($starttime,2,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						}

						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($m[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$m[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (strlen($starttime) == 5) {	//if time >= 10:00
									if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
									} else {
										$starttime = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,2).(substr($starttime,2,2) + 30);
									} else {
										$starttime = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								//Increment half hour end
								if (strlen($nexthalfhour) == 5) {	//if time >= 10:00
									if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($nexthalfhour,2,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,2).(substr($nexthalfhour,2,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								
							}//end if
							
						}//end while
					
						break;
					case 'T':
					
						if (strlen($starttime) == 5) {	//if time >= 10:00
							if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						} else {
							if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,2).(substr($starttime,2,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						}

						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($t[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$t[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (strlen($starttime) == 5) {	//if time >= 10:00
									if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
									} else {
										$starttime = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,2).(substr($starttime,2,2) + 30);
									} else {
										$starttime = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								//Increment half hour end
								if (strlen($nexthalfhour) == 5) {	//if time >= 10:00
									if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($nexthalfhour,2,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,2).(substr($nexthalfhour,2,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								
							}//end if
							
						}//end while
					
						break;
					case 'W':
					
						if (strlen($starttime) == 5) {	//if time >= 10:00
							if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						} else {
							if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,2).(substr($starttime,2,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($w[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$w[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (strlen($starttime) == 5) {	//if time >= 10:00
									if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
									} else {
										$starttime = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,2).(substr($starttime,2,2) + 30);
									} else {
										$starttime = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								//Increment half hour end
								if (strlen($nexthalfhour) == 5) {	//if time >= 10:00
									if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($nexthalfhour,2,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,2).(substr($nexthalfhour,2,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								
							}//end if
							
						}//end while
					
						break;
					case 'R':
					
						if (strlen($starttime) == 5) {	//if time >= 10:00
							if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						} else {
							if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,2).(substr($starttime,2,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time

							if ($r[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$r[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (strlen($starttime) == 5) {	//if time >= 10:00
									if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
									} else {
										$starttime = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,2).(substr($starttime,2,2) + 30);
									} else {
										$starttime = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								//Increment half hour end
								if (strlen($nexthalfhour) == 5) {	//if time >= 10:00
									if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($nexthalfhour,2,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,2).(substr($nexthalfhour,2,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								
							}//end if
							
						}//end while
					
						break;
					case 'F':
					
						if (strlen($starttime) == 5) {	//if time >= 10:00
							if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						} else {
							if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
								$nexthalfhour = substr($starttime,0,2).(substr($starttime,2,2) + 30);
							} else {
								$nexthalfhour = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
							}
						}
						
						//Go through time of course and check if already booked, if it is return false, if it isnt then book it
						while ($starttime != $endtime) {	//While haven't reached end of time
							
							if ($f[$starttime."-".$nexthalfhour] == 1) {	//if already booked, fail
								return false;
								
							} else {	//Else book time and increment
							
								$f[$starttime."-".$nexthalfhour] = 1;	//Book time
								
								//Increment half hour start
								if (strlen($starttime) == 5) {	//if time >= 10:00
									if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
									} else {
										$starttime = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
										$starttime = substr($starttime,0,2).(substr($starttime,2,2) + 30);
									} else {
										$starttime = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								//Increment half hour end
								if (strlen($nexthalfhour) == 5) {	//if time >= 10:00
									if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								} else {
									if (substr($nexthalfhour,2,2) == "00") {	//if on the hour, increment minutes by 30
										$nexthalfhour = substr($nexthalfhour,0,2).(substr($nexthalfhour,2,2) + 30);
									} else {
										$nexthalfhour = (substr($nexthalfhour,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
									}
								}
								
							}//end if
							
						}//end while
					
						break;
					default:
						return false;
						break;
				}
			
			}//end for (loop through days)
			
			//If gotten to this point then course was successfully added to schedule, return true and make day arrays = copies
			$this->monday = array_merge(array(), $m);
			$this->tuesday = array_merge(array(), $t);
			$this->wednesday = array_merge(array(), $w);
			$this->thursday = array_merge(array(), $r);
			$this->friday = array_merge(array(), $f);
			
			return true;
		}
		
		function removeFromSchedule($C) {
		
			$m = array_merge(array(), $this->monday);	//Make copies of array to work with temporarily through function
			$t = array_merge(array(), $this->tuesday);
			$w = array_merge(array(), $this->wednesday);
			$r = array_merge(array(), $this->thursday);
			$f = array_merge(array(), $this->friday);
			
			$days = str_split($C->days);
			
			$times = explode("-",$C->time);
			
			if (strlen($times[0]) == 4) {	//if time is >= 1000
				if (substr($times[0],2,2) == "05") {
					$starttime = substr($times[0],0,2).":00"; //Make 00 if 05
				} else {
					$starttime = substr($times[0],0,2).":".(substr($times[0],2,2)-5); //Subtract 5 from 35 to make 30
				}
			} else {	//Else if time < 1000 (ex. 900, 800...)
				if (substr($times[0],1,2) == "05") {
					$starttime = substr($times[0],0,1).":00"; //Make 00 if 05
				} else {
					$starttime = substr($times[0],0,1).":".(substr($times[0],1,2)-5); //Subtract 5 from 35 to make 30
				}
			}

			if (strlen($times[1]) == 4) {	//if time is >= 1000
				if (substr($times[1],2,2) == "25") {	//if end at 25, add 5 to make 30
					$endtime = substr($times[1],0,2).":".(substr($times[1],2,2)+5);
				} else {
					//If not (55) then make beginning of next hour
					$endtime = (substr($times[1],0,2)+1).":00";
				}
			} else {
				if (substr($times[1],1,2) == "25") {	//if end at 25, add 5 to make 30
					$endtime = substr($times[1],0,1).":".(substr($times[1],1,2)+5);
				} else {
					//If not (55) then make beginning of next hour
					$endtime = (substr($times[1],0,1)+1).":00";
				}
			}

			//For every day the course is scheduled
			for ($i = 0; $i < count($days);$i = $i + 1) {

				if (strlen($starttime) == 5) {	//if time >= 10:00
					if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
						$nexthalfhour = substr($starttime,0,3).(substr($starttime,3,2) + 30);
					} else {
						$nexthalfhour = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
					}
				} else {
					if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
						$nexthalfhour = substr($starttime,0,2).(substr($starttime,2,2) + 30);
					} else {
						$nexthalfhour = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
					}
				}

				while ($starttime != $endtime) {	//While haven't reached end of time
					
					switch ($days[$i]) {
						case "M":
							$m[$starttime."-".$nexthalfhour] = 0;	//un-Book time
							break;
						case "T":
							$t[$starttime."-".$nexthalfhour] = 0;	//un-Book time
							break;
						case "W":
							$w[$starttime."-".$nexthalfhour] = 0;	//un-Book time
							break;
						case "R":
							$r[$starttime."-".$nexthalfhour] = 0;	//un-Book time
							break;
						case "F":
							$f[$starttime."-".$nexthalfhour] = 0;	//un-Book time
							break;
						default:
							return false;
					}
					//Increment half hour start
					if (strlen($starttime) == 5) {	//if time >= 10:00
						if (substr($starttime,3,2) == "00") {	//if on the hour, increment minutes by 30
							$starttime = substr($starttime,0,3).(substr($starttime,3,2) + 30);
						} else {
							$starttime = (substr($starttime,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
						}
					} else {
						if (substr($starttime,2,2) == "00") {	//if on the hour, increment minutes by 30
							$starttime = substr($starttime,0,2).(substr($starttime,2,2) + 30);
						} else {
							$starttime = (substr($starttime,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
						}
					}
					//Increment half hour end
					if (strlen($nexthalfhour) == 5) {	//if time >= 10:00
						if (substr($nexthalfhour,3,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($nexthalfhour,0,3).(substr($nexthalfhour,3,2) + 30);
						} else {
							$nexthalfhour = (substr($nexthalfhour,0,3) + 1).":00";	//if at halfhour, set to beginning of next hour
						}
					} else {
						if (substr($nexthalfhour,2,2) == "00") {	//if on the hour, increment minutes by 30
							$nexthalfhour = substr($nexthalfhour,0,2).(substr($nexthalfhour,2,2) + 30);
						} else {
							$nexthalfhour = (substr($nexthalfhour,0,2) + 1).":00";	//if at halfhour, set to beginning of next hour
						}
					}

				}//end while
			
			}//end for (loop through days)
			
			//If gotten to this point then course was successfully removed to schedule, return true and make day arrays = copies
			$this->monday = array_merge(array(), $m);	
			$this->tuesday = array_merge(array(), $t);
			$this->wednesday = array_merge(array(), $w);
			$this->thursday = array_merge(array(), $r);
			$this->friday = array_merge(array(), $f);
			
			return true;
		}
		
		function calculateConflictFreeSchedules() {
			
			//break apart courses into lists per course
			
			$classlist1 = null;
			
			if (count($this->courses->SectionItems) > 0) {
				$classlist1 = new SectionList();
				$classlist1->addItem($this->courses->itemAt(0));	//Add first course to list (this will be what will be matched in loop)
				$this->courses->removeItem(0);	//Delete that item from original list
				
				for ($i = 0; $i < count($this->courses->SectionItems);$i = $i + 1) {	//go through rest of list
				
				//If course is of same type as on in classlist then add it
				if ($this->courses->itemAt($i)->subjectID == $classlist1->itemAt(0)->subjectID
					&& $this->courses->itemAt($i)->courseNum == $classlist1->itemAt(0)->courseNum) {
					
					$classlist1->addItem($this->courses->itemAt($i));//add to class list
					$this->courses->removeItem($i);	//remove from original list
					$i = $i - 1;	//set i back 1 to accomidate deleted item
				}
				
			}
			}
			
			$classlist2 = null;
			
			if (count($this->courses->SectionItems) > 0) {
				$classlist2 = new SectionList();
				$classlist2->addItem($this->courses->itemAt(0));
				$this->courses->removeItem(0);
				
				for ($i = 0; $i < count($this->courses->SectionItems);$i = $i + 1) {
			
				if ($this->courses->itemAt($i)->subjectID == $classlist2->itemAt(0)->subjectID
					&& $this->courses->itemAt($i)->courseNum == $classlist2->itemAt(0)->courseNum) {
					
					$classlist2->addItem($this->courses->itemAt($i));
					$this->courses->removeItem($i);
					$i = $i - 1;
				}
			
			}
			}
			
			$classlist3 = null;
			
			if (count($this->courses->SectionItems) > 0) {
				$classlist3 = new SectionList();
				$classlist3->addItem($this->courses->itemAt(0));
				$this->courses->removeItem(0);
				
				for ($i = 0; $i < count($this->courses->SectionItems);$i = $i + 1) {
			
				if ($this->courses->itemAt($i)->subjectID == $classlist3->itemAt(0)->subjectID
					&& $this->courses->itemAt($i)->courseNum == $classlist3->itemAt(0)->courseNum) {
					
					$classlist3->addItem($this->courses->itemAt($i));
					$this->courses->removeItem($i);
					$i = $i - 1;
				}
			
			}
			}
			
			$classlist4 = null;
			
			if (count($this->courses->SectionItems) > 0) {
				$classlist4 = new SectionList();
				$classlist4->addItem($this->courses->itemAt(0));
				$this->courses->removeItem(0);
			
				for ($i = 0; $i < count($this->courses->SectionItems);$i = $i + 1) {
			
				if ($this->courses->itemAt($i)->subjectID == $classlist4->itemAt(0)->subjectID
					&& $this->courses->itemAt($i)->courseNum == $classlist4->itemAt(0)->courseNum) {
					
					$classlist4->addItem($this->courses->itemAt($i));
					$this->courses->removeItem($i);
					$i = $i - 1;
				}
			
			}
			}

			$classlist5 = null;
			
			if (count($this->courses->SectionItems) > 0) {
				$classlist5 = new SectionList();
				$classlist5->addItem($this->courses->itemAt(0));
				$this->courses->removeItem(0);
			
				for ($i = 0; $i < count($this->courses->SectionItems);$i = $i + 1) {
			
				if ($this->courses->itemAt($i)->subjectID == $classlist5->itemAt(0)->subjectID
					&& $this->courses->itemAt($i)->courseNum == $classlist5->itemAt(0)->courseNum) {
					
					$classlist5->addItem($this->courses->itemAt($i));
					$this->courses->removeItem($i);
					$i = $i - 1;
				}
			
			}
			}

			$classlist6 = null;
			
			if (count($this->courses->SectionItems) > 0) {
				$classlist6 = new SectionList();
				$classlist6->addItem($this->courses->itemAt(0));
				$this->courses->removeItem(0);
				
				for ($i = 0; $i < count($this->courses);$i = $i + 1) {
				
					if ($this->courses->itemAt($i)->subjectID == $classlist6->itemAt(0)->subjectID
						&& $this->courses->itemAt($i)->courseNum == $classlist6->itemAt(0)->courseNum) {
						
						$classlist6->addItem($this->courses->itemAt($i));
						$this->courses->removeItem($i);
						$i = $i - 1;
					}
				
				}
			}
			
			//Now each posibility of every course to be taken is in their own lists
			//Now go through each list, pick one and generate schedule
			//Run through all possibilities
			
			{	//Create arrays to keep track of what times are booked
			$this->monday = array("8:00-8:30" => 0,
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
			
			$this->tuesday = array("8:00-8:30" => 0,
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
			
			$this->wednesday = array("8:00-8:30" => 0,
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
							
			$this->thursday = array("8:00-8:30" => 0,
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
							
			$this->friday = array("8:00-8:30" => 0,
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
			}
			
			//Now create possible schedules

			if (!is_null($classlist1)) {
			
				$nextclassesarray = array();
				
				if (!is_null($classlist2)) {
					array_push($nextclassesarray, $classlist2);
				}
				if (!is_null($classlist3)) {
					array_push($nextclassesarray, $classlist3);
				}
				if (!is_null($classlist4)) {
					array_push($nextclassesarray, $classlist4);
				}
				if (!is_null($classlist5)) {
					array_push($nextclassesarray, $classlist5);
				}
				if (!is_null($classlist6)) {
					array_push($nextclassesarray, $classlist6);
				}
				$this->generateSchedules($classlist1,new SectionList(), $nextclassesarray);
			}
			//Now have a list of all possible schedules for given courses
		}
		
		function generateSchedules($classlist, $cursched, $otherclasses) {
		
			for ($i = 0; $i < count($classlist->SectionItems);$i = $i + 1) {	//for every class
			
				if ($this->counter == self::NUMSCHED) {
					return;
				}
				
				$class = clone $classlist->itemAt($i);	//pick class
				
				if ($this->addToSchedule($class) == false) {	//add class to schedule
					$this->removeFromSchedule($class);
					continue;	//go to next course possibility
				} else {
					$cursched->addItem($class);
				}
				
				//get labs from class	
				$classLabs = $class->getLabs();
				
				if (count($classLabs->SectionItems) == 0) {	//if no labs
					
					if (count($otherclasses) == 0) {	//if no more classes to add
						array_push($this->Schedules, clone $cursched);	//add schedule to list
						$this->counter = $this->counter + 1;
					} else {	//if there are classes to add
						
						$nextclasslist = array_pop($otherclasses);
						$this->generateSchedules($nextclasslist, $cursched, $otherclasses);
						array_push($otherclasses,$nextclasslist);
					}//end if classes to add
					
				} else {	//if class has labs
					
					for ($j = 0; $j < count($classLabs->SectionItems);$j = $j + 1) {
					
						if ($this->counter == self::NUMSCHED) {
							return;
						}
					
						$lab = clone $classLabs->itemAt($j);

						if ($this->addToSchedule($lab) == false) {	//add lab to schedule
							$this->removeFromSchedule($lab);
							continue;	//go to next course possibility
						} else {
							$cursched->addItem($lab);
						}
							
						if (count($otherclasses) == 0) {	//if no more classes to add
							array_push($this->Schedules, clone $cursched);	//add schedule to list
							$this->counter = $this->counter + 1;
						} else {	//if there are classes to add
							
							$nextclasslist = array_pop($otherclasses);
							$this->generateSchedules($nextclasslist, $cursched, $otherclasses);
							array_push($otherclasses,$nextclasslist);
						}//end if classes to add
						
						$this->removeFromSchedule($lab);
						$cursched->popItem();
					}//for each lab
					
				}//if has labs

				$this->removeFromSchedule($class);
				$cursched->popItem();

			}//for loop classes
			
		}
		
		//Export All schedules in XML format
		function exportScedulesXML() {		
	
			$returnval = "<schedules>";	
			for ($i = 0; $i < count($this->Schedules);$i = $i + 1) {
				$returnval .= $this->Schedules[$i]->exportXML();
			}	
			$returnval .= "</schedules>";		
			return $returnval;
		}
		
	}
	
	class OnScheduleCourseCalculator extends ScheduleCourseCalculator{

		function calculateCourses() {
			$db = new Database("SchedulerDatabase");
			
			for ($i = 0; $i < count($this->pattern->patternItems);$i = $i + 1) {	//parse through all courses in pattern
				
				//Get all pattern items that are one year more than the number of years the student has completed, and for the proper terms
				if ($this->pattern->patternItems[$i]->yearRequired == ($this->year + 1) 
					&& trim($this->pattern->patternItems[$i]->termRequired) == trim($this->term)) {
					
					$subID = $this->pattern->patternItems[$i]->subjectID;
					$CN = $this->pattern->patternItems[$i]->courseNumber;
					$term = $this->term;
					
					//Select all non-full LECTURE sections in proper term, with matching SubjectID and CourseNumber
					$getCourseQuery = "SELECT * FROM Section WHERE
										Term LIKE '%$term%'
										AND SubjectID LIKE '%$subID%'
										AND CourseNumber LIKE '%$CN%'
										AND ScheduleCode LIKE '%LEC%'
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
		
	}
	
	class OffScheduleCourseCalculator extends ScheduleCourseCalculator{

		private $coursesTaken;	//Array of courses taken
		private $numCourses = 0;	//counter to keep track of number of courses added to course list
		
		function __construct($y, $p, $t, $coursestaken) {
			$this->coursesTaken = $coursestaken;
			parent::__construct($y, $p, $t);
		}
	
		function haveTaken($course) {
			for ($i = 0; $i < count ($this->coursesTaken);$i++) {
				if ($this->coursesTaken[$i] == $course) {
					return true;
				}
			}
			return false;
		}
		
		function completedYear($year) {
		
			for ($i = 0; $i < count($this->pattern->patternItems);$i = $i + 1) {
				if ($this->pattern->patternItems[$i]->yearRequired == $year) {
					if ($this->pattern->patternItems[$i]->courseNumber != "") {
						if (!$this->haveTaken($this->pattern->patternItems[$i]->subjectID.":".$this->pattern->patternItems[$i]->courseNumber)) {
							return false;
						}
					}
				}
			}
			return true;
		
		}
		
		function coursesNotTaken($year) {
		
			$returnval = array();
		
			for ($i = 0; $i < count($this->pattern->patternItems);$i = $i + 1) {
				if ($this->pattern->patternItems[$i]->yearRequired == $year &&
					$this->pattern->patternItems[$i]->courseNumber != "") {
					if (!$this->haveTaken($this->pattern->patternItems[$i]->subjectID.":".$this->pattern->patternItems[$i]->courseNumber)) {
						array_push($returnval,$this->pattern->patternItems[$i]);
					}
				}
			}
			return $returnval;
		
		}
	
		//Returns true if course can be taken
		function hasPrereq($year,$subID,$CN) {

			$db = new DataBase("SchedulerDatabase");
			
			$getPrereq = "SELECT Prerequisites FROM CourseToPrerequisiteMapping
							WHERE SubjectID LIKE '%$subID%' AND
							CourseNumber LIKE '%$CN%';";
			
			$result = $db->execute($getPrereq);
			
			if ($result->num_rows < 1) {
				return true;	//NO Prereqs
			}

			$prereq = $result->fetch_object();
			
			if (strlen(trim($prereq->Prerequisites)) == 0) {
				return true;	//No Prereqs
			}
			
			//parse through prereqs and ensure that user meets them
			
			$words = explode(' ',$prereq->Prerequisites);
			
			$root = new PrereqNode();

			$current = new PrereqNode();
			
			array_push($root->children, $current);
			
			for ($i = 0;$i < count($words);$i++) {
				
				switch (strtoupper(trim($words[$i]))) {
				
					case 'AND':
					
						$current = $current;
						break;
					case 'OR':
						
						$current = new PrereqNode();
						array_push($root->children, $current);
						break;
					default:
						
						array_push($current->prereqs, $words[$i]);
						break;				
				}
				
			}	//end for

			//prerequisites are now organized into a tree
			//check each branch for succesfull prereq
			
			for ($i = 0;$i < count($root->children);$i = $i + 1) {
				
				$passFlag = true;
				
				for ($j = 0;$j < count($root->children[$i]->prereqs);$j = $j + 1) {
					
					if ($root->children[$i]->prereqs[$j][0] === 'C') {
						
						$SID = substr($root->children[$i]->prereqs[$j], 2, 4);
						$Coursenum = substr($root->children[$i]->prereqs[$j], 6, 4);

						//If havent taken class before then not passed
						if (!$this->haveTaken($SID.":".$Coursenum)) {
							$passFlag = false;
						}
			
					} else if ($root->children[$i]->prereqs[$j][0] === 'U') {
						
						$SID = substr($root->children[$i]->prereqs[$j], 2, 4);
						$Coursenum = substr($root->children[$i]->prereqs[$j], 6, 4);
						
						if (!$this->haveTaken($SID.":".$Coursenum)) {
							$passFlag = false;
						} else {
							$concurrencyFlag = false;
							foreach ($this->courses->SectionItems as $section) {
								if ($section->subjectID == $SID && $section->courseNum == $Coursenum) {
									$concurrencyFlag = true;
								}
							}
							if ($concurrencyFlag == false) {
								$passFlag = false;
							}
						}
						
					} else if ($root->children[$i]->prereqs[$j][0] === 'S') {
						
						$y = intval(substr($root->children[$i]->prereqs[$j], 2, 1));
						if ($year + 1 < $y) {	// if completed less than the year required then not passed
							$passFlag = false;
						}
						
					} else if ($root->children[$i]->prereqs[$j][0] === 'S') {
						
						$p = intval(substr($root->children[$i]->prereqs[$j], 2, strlen($root->children[$i]->prereqs[$j]) - 3));
						//if not in same program then fail
						if ($p != $this->program) {
							$passFlag = false;
						}
						
					}
				}
				if ($passFlag) {
					return true;
				}
			}
			
			return false;
				
		}
	
		function calculateCourses() {
			
			$db = new DataBase("SchedulerDatabase");
			
			//Arrays to keep track of courses not taken year 1-4
			$coursesNotTaken1 = $this->coursesNotTaken(1);	
			$coursesNotTaken2 = $this->coursesNotTaken(2);
			$coursesNotTaken3 = $this->coursesNotTaken(3);
			$coursesNotTaken4 = $this->coursesNotTaken(4);
			
			$yearsCompleted = 0;
			for ($i = 1; $i < 5;$i++) {
				if ($this->completedYear($i)) {
					$yearsCompleted++;
				} else {
					$i = 5;	//if does not complete previous year, break out of loop because impossible to complete years after
				}
			}
			
			if (count($coursesNotTaken1) > 0) {	//If the student hasn't completed year 1
			
				for ($i = 0;$i < count($coursesNotTaken1);$i++) {	//For every course not yet taken in that year
				
					if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
						return;
					}
				
					//If you have the prerequistes and the course if for the proper term then add it to list
					if ($this->hasPrereq($yearsCompleted,
										 $coursesNotTaken1[$i]->subjectID,
										 $coursesNotTaken1[$i]->courseNumber)) {

						$this->numCourses++;	//increment number of courses added
						
						$subID = $coursesNotTaken1[$i]->subjectID;
						$CN = $coursesNotTaken1[$i]->courseNumber;
						$term = $this->term;

						$getSections = "SELECT * FROM Section WHERE
										Term LIKE '%$term%'
										AND SubjectID LIKE '%$subID%'
										AND CourseNumber LIKE '%$CN%'
										AND ScheduleCode LIKE '%LEC%'
										AND NumberOfStudents < Capacity;";
										
						$rows = $db->execute($getSections);

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
					}//end if
				}//end for
			}//end if
			
			if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
				return;
			}
			
			if (count($coursesNotTaken2) > 0) {	//If the student hasn't completed year 2
			
				for ($i = 0;$i < count($coursesNotTaken2);$i++) {	//For every course not yet taken in that year
				
					if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
						return;
					}
				
					//If you have the prerequistes and the course if for the proper term then add it to list
					if ($this->hasPrereq($yearsCompleted,
										 $coursesNotTaken2[$i]->subjectID,
										 $coursesNotTaken2[$i]->courseNumber)) {

						$this->numCourses++;	//increment number of courses added
						
						$subID = $coursesNotTaken2[$i]->subjectID;
						$CN = $coursesNotTaken2[$i]->courseNumber;
						$term = $this->term;

						$getSections = "SELECT * FROM Section WHERE
										Term LIKE '%$term%'
										AND SubjectID LIKE '%$subID%'
										AND CourseNumber LIKE '%$CN%'
										AND ScheduleCode LIKE '%LEC%'
										AND NumberOfStudents < Capacity;";
										
						$rows = $db->execute($getSections);

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
					}//end if
				}//end for
			}//end if

			if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
				return;
			}
			
			if (count($coursesNotTaken3) > 0) {	//If the student hasn't completed year 3
			
				for ($i = 0;$i < count($coursesNotTaken3);$i++) {	//For every course not yet taken in that year

					if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
						return;
					}
				
					//If you have the prerequistes and the course if for the proper term then add it to list
					if ($this->hasPrereq($yearsCompleted,
										 $coursesNotTaken3[$i]->subjectID,
										 $coursesNotTaken3[$i]->courseNumber)) {

						$this->numCourses++;	//increment number of courses added
						
						$subID = $coursesNotTaken3[$i]->subjectID;
						$CN = $coursesNotTaken3[$i]->courseNumber;
						$term = $this->term;

						$getSections = "SELECT * FROM Section WHERE
										Term LIKE '%$term%'
										AND SubjectID LIKE '%$subID%'
										AND CourseNumber LIKE '%$CN%'
										AND ScheduleCode LIKE '%LEC%'
										AND NumberOfStudents < Capacity;";
										
						$rows = $db->execute($getSections);

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
					}//end if
				}//end for
			}//end if

			if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
				return;
			}
			
			if (count($coursesNotTaken4) > 0) {	//If the student hasn't completed year 4
			
				for ($i = 0;$i < count($coursesNotTaken4);$i++) {	//For every course not yet taken in that year
				
					if ($this->numCourses == 5) {	//if reached max amount of courses to be taken exit funtion
						return;
					}
				
					//If you have the prerequistes and the course if for the proper term then add it to list
					if ($this->hasPrereq($yearsCompleted,
										 $coursesNotTaken4[$i]->subjectID,
										 $coursesNotTaken4[$i]->courseNumber)) {

						$subID = $coursesNotTaken4[$i]->subjectID;
						$CN = $coursesNotTaken4[$i]->courseNumber;
						$term = $this->term;

						$getSections = "SELECT * FROM Section WHERE
										Term LIKE '%$term%'
										AND SubjectID LIKE '%$subID%'
										AND CourseNumber LIKE '%$CN%'
										AND ScheduleCode LIKE '%LEC%'
										AND NumberOfStudents < Capacity;";
										
						$rows = $db->execute($getSections);
						
						if (!is_null($rows)) {
						
							$this->numCourses++;	//increment number of courses added
							
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
						}//end if
					}//end if
				}//end for
			}//end if
		}	//end function
		
	}

?>