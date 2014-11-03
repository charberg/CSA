<?php

	$login = $_GET['login'];
	$password = $_GET['password'];
	$program = $_GET['program'];
	$method = $_GET['operation']; // Possible methods to be performed on database

	echo "You login is $login and your password is $password in prog $program";
	
	if($method=="send"){
	
	}
	
	class DataBase{
	
		val $db;
		val $dbName = "SchedulerDatabase.db";
		
		val $table_CTCMapping = "CourseToCourseMapping",
			$table_Section = "Section",
			$table_Programs = "AcademicPrograms",
			$table_ProgramToCourseMapping = "AcademicProgramToCourseMapping",
			$table_CourseToPrereqMapping = "CourseToPrerequisiteMapping",
			$table_CourseTypes = "CourseType",
			$table_PrereqTypes = "PrerequisiteTypes",
			$table_Schedule = "Schedule",
			$table_Course = "Course",
			$table_Subjects = "Subjects";
			
	
		function __construct($user, $pass, $dataBaseName) {
			$db = new mysqli('localhost', '$user', '$pass', '$dataBaseName');
			
			if($db->connect_errno > 0){
				die('Unable to connect to database [' . $db->connect_error . ']');
			}	
		}
		
		function createDatabase() {
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
		}
		
	}
	
?>