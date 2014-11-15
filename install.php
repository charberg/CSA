<?php

/*
Have one file install.php which sets up the environment (create database, 
tables, read files to fill in the tables …).  If you had exported files from mySql, have 
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
								 SubjectCode VARCHAR(MAX) NOT NULL);";
								 
	$CreateCourseTable = "CREATE TABLE IF NOT EXISTS $table_Course
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(MAX) NOT NULL,
								 Title VARCHAR(MAX) NOT NULL,
								 Credits DECIMAL(1,1) NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber),
								 FOREIGN KEY(SubjectID) REFERENCES $table_Subjects(SubjectID));";
								 
	$CreateScheduleTable = "CREATE TABLE IF NOT EXISTS $table_Schedule
								(ScheduleID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								 ScheduleCode VARCHAR(MAX) NOT NULL);";
								 
	$CreatePrereqTypeTable = "CREATE TABLE IF NOT EXISTS $table_PrereqTypes
								(PrerequisiteTypeID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								 PrerequisiteTypeCode VARCHAR(MAX) NOT NULL);";	

	$CreateCourseTypeTable = "CREATE TABLE IF NOT EXISTS $table_CourseTypes
								(CourseTypeID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
								 CourseTypeCode VARCHAR(MAX) NOT NULL);";	
	
	$CreateProgramsTable = "CREATE TABLE IF NOT EXISTS $table_Programs
								(ProgramID VARCHAR(10) NOT NULL PRIMARY KEY,
								 ProgramCode VARCHAR(MAX) NOT NULL);";
								 
	$CreateSectionTable = "CREATE TABLE IF NOT EXISTS $table_Section
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(MAX) NOT NULL,
								 CourseCRN VARCHAR(10) NOT NULL,
								 ScheduleID INT NOT NULL,
								 SectionCode CHAR(1) NOT NULL,
								 Year INT NOT NULL,
								 Term CHAR(1) NOT NULL,
								 Time VARCHAR(MAX) NOT NULL,
								 Days CHAR(5) NOT NULL,
								 Capacity INT NOT NULL,
								 NumberOfStudents INT NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber, CourseCRN),
								 FOREIGN KEY(SubjectID) REFERENCES $table_Subjects(SubjectID),
								 FOREIGN KEY(CourseNumber) REFERENCES $table_Course(CourseNumber),
								 FOREIGN KEY(ScheduleID) REFERENCES $table_Schedule(ScheduleID));";	

	$CreateCTCMappingTable = "CREATE TABLE IF NOT EXISTS $table_CTCMapping
								(PrimaryCRN VARCHAR(10) NOT NULL,
								 SecondaryCRN VARCHAR(10) NOT NULL,
								 PRIMARY KEY(PrimaryCRN, SecondaryCRN),
								 FOREIGN KEY(PrimaryCRN) REFERENCES $table_Section(CourseCRN),
								 FOREIGN KEY(SecondaryCRN) REFERENCES $table_Section(CourseCRN);";
								 
	$CreatePTCMappingTable = "CREATE TABLE IF NOT EXISTS $table_ProgramToCourseMapping
								(ProgramID VARCHAR(10) NOT NULL,
								 CourseTypeID INT NOT NULL,
								 CourseCRN VARCHAR(10) NOT NULL,
								 PRIMARY KEY(ProgramID, CourseTypeID, CourseCRN),
								 FOREIGN KEY(ProgramID) REFERENCES $table_Programs(ProgramID),
								 FOREIGN KEY(CourseTypeID) REFERENCES $table_CourseTypes(CourseTypeID),
								 FOREIGN KEY(CourseCRN) REFERENCES $table_Section(CourseCRN);";

	$CreateCTPMappingTable = "CREATE TABLE IF NOT EXISTS $table_CourseToPrereqMapping
								(CourseCRN VARCHAR(10) NOT NULL,
								 PrerequisiteID INT NOT NULL,
								 PrerequisiteTypeID INT NOT NULL,
								 PrerequisiteAttribute VARCHAR(10) NOT NULL,
								 PRIMARY KEY(ProgramID, CourseTypeID, CourseCRN),	
								 FOREIGN KEY(CourseCRN) REFERENCES $table_Section(CourseCRN),
								 FOREIGN KEY(PrerequisiteTypeID) REFERENCES $table_PrereqTypes(PrerequisiteTypeID);";
								 //review^^ PK
	
	//Create Database
	
	require_once("myDB.php");
	
	$db = new DataBase("");
								 
	$db->execute($createDB);
	
	$db = new DataBase("$dbName");
	
	//Create tables
	
	if ($db->execute($CreateSubjectsTable)) {
		echo "Successfully Create Subjects Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateCourseTable)) {
		echo "Successfully Create Course Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateScheduleTable)) {
		echo "Successfully Create Schedule Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreatePrereqTypeTable)) {
		echo "Successfully Create Prerequisite Type Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateCourseTypeTable)) {
		echo "Successfully Create Course Type Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateProgramsTable)) {
		echo "Successfully Create Academic Programs Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateSectionTable)) {
		echo "Successfully Create Section Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateCTCMappingTable)) {
		echo "Successfully Create Course to Course Mapping Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreatePTCMappingTable)) {
		echo "Successfully Create Program to Course Mapping Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	if ($db->execute($CreateCTPMappingTable)) {
		echo "Successfully Create Course to Prerequisite Mapping Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	/*-- Populate Tables --*/
	
	//Add Academic Programs
	$PopulateAcademicProgram = "INSERT INTO $table_Programs VALUES(('CE','Communications Engineering'),
																   ('CSE','Computer Systems Engineering'),
																   ('SE','Sooftware Engineering'));";
	
	if ($db->execute($PopulateAcademicProgram)) {
		echo "Successfully populates Academic Program Table"
	} else {
		echo $db->getError();
		exit;
	}
	
	$dataFile = fopen("data/data.csv","r");	//open data file for reading
	
	//SUBJ;"CRSE";"SEQ";"CATALOG_TITLE";"INSTR_TYPE";"DAYS";"START_TIME";"END_TIME";"ROOM_CAP"
	
	while (!feof($dataFile) ) {		//while not at end of file

		$line = fgetcsv($dataFile, 1024);	//read up to 1 kilobyte in a row
	
		//Course Table
		$SubjectID = $line[0];
		$CourseNumber = $line[1];
		$Title = $line[3];
		$Credits = 0.5;
		
		//Section Table
		$CRN = "";
		$ScheduleCode = $line[4];
		$SectionCode = $line[2];
		$Year = 2014;
		$Term = "fall"
		$StartTime = $line[6];
		$EndTime = $line[7];
		$Time = $StartTime."-".$EndTime;
		$Days = $line[5];
		$Capacity = $line[8];
		$NumberOfStudents = 0;
		
	}

	fclose($dataFile);
		
?>