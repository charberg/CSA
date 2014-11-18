<?php

/*
Have one file install.php which sets up the environment (create database, 
tables, read files to fill in the tables �).  If you had exported files from mySql, have 
your install.php read the file to import the data in the tables. This file will be 
executed first before your application is evaluated.
*/

	//Define database name and table names

	$dbName = "SchedulerDatabase";
		
	$table_CTCMapping = "CourseToCourseMapping";
	$table_Section = "Section";
	$table_Programs = "AcademicPrograms";
	$table_ProgramToCourseMapping = "AcademicProgramToCourseMapping";
	$table_CourseToPrereqMapping = "CourseToPrerequisiteMapping";
	$table_CourseTypes = "CourseType";
	$table_PrereqTypes = "PrerequisiteTypes";
	$table_Schedule = "Schedule";
	$table_Course = "Course";
	$table_Subjects = "Subjects";
	
	//Define queries to create database and its tables
		
	$createDB = "CREATE DATABASE IF NOT EXISTS $dbName;";
	$dropDB = "DROP DATABASE IF EXIST $dbName;";
	
	$CreateSubjectsTable = "CREATE TABLE IF NOT EXISTS $table_Subjects
								(SubjectID VARCHAR(10) NOT NULL PRIMARY KEY,
								 SubjectCode VARCHAR(200) NOT NULL);";
								 
	$CreateCourseTable = "CREATE TABLE IF NOT EXISTS $table_Course
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 Title VARCHAR(200) NOT NULL,
								 Credits DECIMAL(1,1) NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber),
								 FOREIGN KEY(SubjectID) REFERENCES $table_Subjects(SubjectID));";
								 
	$CreateScheduleTable = "CREATE TABLE IF NOT EXISTS $table_Schedule
								(ScheduleID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								 ScheduleCode VARCHAR(200) NOT NULL);";
								 
	$CreatePrereqTypeTable = "CREATE TABLE IF NOT EXISTS $table_PrereqTypes
								(PrerequisiteTypeID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								 PrerequisiteTypeCode VARCHAR(200) NOT NULL);";	

	$CreateCourseTypeTable = "CREATE TABLE IF NOT EXISTS $table_CourseTypes
								(CourseTypeID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								 CourseTypeCode VARCHAR(200) NOT NULL);";	
	
	$CreateProgramsTable = "CREATE TABLE IF NOT EXISTS $table_Programs
								(ProgramID VARCHAR(10) NOT NULL PRIMARY KEY,
								 ProgramCode VARCHAR(200) NOT NULL);";
								 
	$CreateSectionTable = "CREATE TABLE IF NOT EXISTS $table_Section
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 CourseCRN VARCHAR(10) NOT NULL,
								 ScheduleID INT NOT NULL,
								 SectionCode CHAR(1) NOT NULL,
								 Year INT NOT NULL,
								 Term CHAR(1) NOT NULL,
								 Time VARCHAR(200) NULL,
								 Days CHAR(5) NULL,
								 Capacity INT NULL,
								 NumberOfStudents INT NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber, CourseCRN),
								 FOREIGN KEY(SubjectID) REFERENCES $table_Subjects(SubjectID),
								 FOREIGN KEY(CourseNumber) REFERENCES $table_Course(CourseNumber),
								 FOREIGN KEY(ScheduleID) REFERENCES $table_Schedule(ScheduleID));";	

	$CreateCTCMappingTable = "CREATE TABLE IF NOT EXISTS $table_CTCMapping
								(PrimarySubjectID VARCHAR(10) NOT NULL,
								 PrimaryCourseNumber VARCHAR(200) NOT NULL,
								 PrimaryCRN VARCHAR(10) NOT NULL,
								 SecondarySubjectID VARCHAR(10) NOT NULL,
								 SecondaryCourseNumber VARCHAR(200) NOT NULL,
								 SecondaryCRN VARCHAR(10) NOT NULL,
								 PRIMARY KEY(PrimarySubjectID, PrimaryCourseNumber, PrimaryCRN, SecondarySubjectID, SecondaryCourseNumber, SecondaryCRN),
								 FOREIGN KEY(PrimarySubjectID, PrimaryCourseNumber, PrimaryCRN) REFERENCES $table_Section(SubjectID, CourseNumber, CourseCRN),
								 FOREIGN KEY(SecondarySubjectID, SecondaryCourseNumber, SecondaryCRN) REFERENCES $table_Section(SubjectID, CourseNumber, CourseCRN));";
								 
	$CreatePTCMappingTable = "CREATE TABLE IF NOT EXISTS $table_ProgramToCourseMapping
								(ProgramID VARCHAR(10) NOT NULL,
								 CourseTypeID INT NOT NULL,
								 SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 CourseCRN VARCHAR(10) NOT NULL,
								 PRIMARY KEY(ProgramID, CourseTypeID, SubjectID, CourseNumber, CourseCRN),
								 FOREIGN KEY(ProgramID) REFERENCES $table_Programs(ProgramID),
								 FOREIGN KEY(CourseTypeID) REFERENCES $table_CourseTypes(CourseTypeID),
								 FOREIGN KEY(SubjectID, CourseNumber, CourseCRN) REFERENCES $table_Section(SubjectID, CourseNumber, CourseCRN));";

	$CreateCTPMappingTable = "CREATE TABLE IF NOT EXISTS $table_CourseToPrereqMapping
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 CourseCRN VARCHAR(10) NOT NULL,
								 PrerequisiteID INT NOT NULL,
								 PrerequisiteTypeID INT NOT NULL,
								 PrerequisiteAttribute VARCHAR(10) NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber, CourseCRN, PrerequisiteID, PrerequisiteTypeID),	
								 FOREIGN KEY(SubjectID, CourseNumber, CourseCRN) REFERENCES $table_Section(SubjectID, CourseNumber, CourseCRN),
								 FOREIGN KEY(PrerequisiteTypeID) REFERENCES $table_PrereqTypes(PrerequisiteTypeID));";
	
	//Create Database
	
	require_once("database.php");
	
	$db = new DataBase("");
								 
	$db->execute($createDB);
	
	$db = new DataBase("$dbName");
	
	//Create tables
	
	/*if ($db->execute($CreateSubjectsTable)) {
		echo "Successfully Created Subjects Table<br/>";
	} else {
		echo "Error Creating Subjects Table: ".$db->getError()."<br/>";
		exit;
	}*/
	
	if ($db->execute($CreateCourseTable)) {
		echo "Successfully Created Course Table<br/>";
	} else {
		echo "Error Creating Course Table: ".$db->getError()."<br/>";
		exit;
	}
	
	/*if ($db->execute($CreateScheduleTable)) {
		echo "Successfully Created Schedule Table<br/>";
	} else {
		echo "Error Creating Schedule Table: ".$db->getError()."<br/>";
		exit;
	}*/
	
	if ($db->execute($CreatePrereqTypeTable)) {
		echo "Successfully Created Prerequisite Type Table<br/>";
	} else {
		echo "Error Creating Prerequisite Type Table: ".$db->getError()."<br/>";
		exit;
	}
	
	/*if ($db->execute($CreateCourseTypeTable)) {
		echo "Successfully Created Course Type Table<br/>";
	} else {
		echo "Error Creating Course Type Table: ".$db->getError()."<br/>";
		exit;
	}*/
	
	if ($db->execute($CreateProgramsTable)) {
		echo "Successfully Created Academic Programs Table<br/>";
	} else {
		echo "Error Creating Academic Programs Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreateSectionTable)) {
		echo "Successfully Created Section Table<br/>";
	} else {
		echo "Error Creating Section Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreateCTCMappingTable)) {
		echo "Successfully Created Course to Course Mapping Table<br/>";
	} else {
		echo "Error Creating Course to Course Mapping Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreatePTCMappingTable)) {
		echo "Successfully Created Program to Course Mapping Table<br/>";
	} else {
		echo "Error Creating Program to Course Mapping Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreateCTPMappingTable)) {
		echo "Successfully Created Course to Prerequisite Mapping Table<br/>";
	} else {
		echo "Error Creating Course to Prerequisite Mapping Table: ".$db->getError()."<br/>";
		exit;
	}
	
	/*-- Populate Tables --*/
	
	//Add Academic Programs
	$PopulateAcademicProgram = "INSERT INTO $table_Programs VALUES('CE', 'Communications Engineering'),
																   ('CSE', 'Computer Systems Engineering'),
																   ('SE', 'Software Engineering')
																   ON DUPLICATE KEY UPDATE;";
	
	if ($db->execute($PopulateAcademicProgram)) {
		echo "Successfully populated Academic Program Table<br/>";
	} else {
		echo "Error populating Academic Program Table: ".$db->getError()."<br/>";
		exit;
	}
	
	//Add Prerequisite Types
	$PopulatePrereqTypes = "INSERT INTO $table_PrereqTypes VALUES ('Must have completed Attribute1 AND Attribute2'),
																  ('Attribute1 amount of credits in Year 1'),
																  ('Attribute1 amount of credits in Year 2'),
																  ('Attribute1 amount of credits in Year 3'),
																  ('Attribute1 amount of credits in Year 4'),
																  ('Attribute1 amount of credits in current Year'),
																  ('Must be taking Attribute1 concurently'),
																  ('Must be in first year standing'),
																  ('Must be in second year standing'),
																  ('Must be in third year standing'),
																  ('Must be in fourth year standing'),
																  ('Must be registered in Attibute1 Program'),
																  ('Attribute1 points to other attribute set'),
																  ('Must have completed Attribute1 OR Attribute2')
																   ON DUPLICATE KEY UPDATE;";
	
	if ($db->execute($PopulatePrereqTypes)) {
		echo "Successfully populated Prerequisite Types Table<br/>";
	} else {
		echo "Error populating Prerequisite Types Table: ".$db->getError()."<br/>";
		exit;
	}
	
	/*
	$dataFile = fopen("data/data.csv","r");	//open data file for reading
	
	//SUBJ;"CRSE";"SEQ";"CATALOG_TITLE";"INSTR_TYPE";"DAYS";"START_TIME";"END_TIME";"ROOM_CAP"
	
	var $CRNCounter = 1;	//counter to generate MOCK CRN values for course sections since they are not provided in given data
	
	while (!feof($dataFile) ) {		//while not at end of file

		$line = fgetcsv($dataFile, 1024);	//read up to 1 kilobyte in a row
	
		//Course Table
		$SubjectID = $line[0];
		$CourseNumber = $line[1];
		$Title = $line[3];
		$Credits = 0.5;
		
		//Section Table
		$CRN = $CRNCounter;
		$ScheduleCode = $line[4];
		$SectionCode = $line[2];
		$Year = 2014;
		$Term = "fall";
		
		$StartTime =  NULL;	//set values to null initialliy. This will account for online courses that don't have a time/day/capacity
		@EndTime = NULL;
		$Time = NULL;
		$Days = NULL;
		$Capacity = NULL;
		
		if ($line[6] != "") {		//If course has a start time
			$StartTime = $line[6];
		}
		if ($line[7] != "") {		//If course has a end time
			$EndTime = $line[7];
		}
		if (!is_null($StartTime)) {				//If course has valid time (Start time is not null)
			$Time = $StartTime."-".$EndTime;
		}
		if ($line[5] != "") {		//If course has a Day value
			$Days = $line[5];
		}
		if ($line[8] != "") {		//If course has a capacity
			$Capacity = $line[8];
		}
		$NumberOfStudents = 0;
		
		//Insert into Course Table if not aready there
		
		//Insert into Section Table
		
		$CRNCounter = $CRNCounter + 1;	//increment CRN counter
		
	}

	fclose($dataFile);
	*/
		
?>