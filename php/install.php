<?php

/*
Have one file install.php which sets up the environment (create database, 
tables, read files to fill in the tables �).  If you had exported files from mySql, have 
your install.php read the file to import the data in the tables. This file will be 
executed first before your application is evaluated.
*/

	//Define database name and table names
	
	set_time_limit(60);	//allow 1 minute for execution

	$dbName = "SchedulerDatabase";
		
	$table_Section = "Section";
	$table_Programs = "AcademicPrograms";
	$table_Patterns = "Patterns";
	$table_CourseToPrereqMapping = "CourseToPrerequisiteMapping";
	$table_electives = "Electives";
	
	//Define queries to create database and its tables
		
	$createDB = "CREATE DATABASE IF NOT EXISTS $dbName;";
	$dropDB = "DROP DATABASE IF EXIST $dbName;";
								 
	$CreateSectionTable = "CREATE TABLE IF NOT EXISTS $table_Section
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 Year INT NOT NULL,
								 Term VARCHAR(10) NOT NULL,
								 Title VARCHAR(200) NOT NULL,
								 Credits DECIMAL(1,1) NOT NULL,
								 ScheduleCode VARCHAR(10) NOT NULL,
								 SectionCode CHAR(1) NOT NULL,
								 Time VARCHAR(200) NULL,
								 Days CHAR(5) NULL,
								 Capacity INT NULL,
								 NumberOfStudents INT NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber, Year, Term));";		
	
	$CreateProgramsTable = "CREATE TABLE IF NOT EXISTS $table_Programs
								(ProgramID VARCHAR(10) NOT NULL PRIMARY KEY,
								 ProgramCode VARCHAR(200) NOT NULL);";
								 
	$CreatePatternsTable = "CREATE TABLE IF NOT EXISTS $table_Patterns
								(ProgramID VARCHAR(10) NOT NULL,
								 CourseType VARCHAR(30) NOT NULL,
								 YearRequired INT NOT NULL,
								 TermRequired VARCHAR(30) NOT NULL,
								 SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 PRIMARY KEY(ProgramID, CourseType, YearRequired, TermRequired, SubjectID, CourseNumber),
								 FOREIGN KEY(ProgramID) REFERENCES $table_Programs(ProgramID));";

	$CreateCTPMappingTable = "CREATE TABLE IF NOT EXISTS $table_CourseToPrereqMapping
								(SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 Prerequisites VARCHAR(250) NOT NULL,
								 PRIMARY KEY(SubjectID, CourseNumber));";
	
	$CreateElectivesTable = "CREATE TABLE IF NOT EXISTS $table_electives
								(ProgramID VARCHAR(10) NOT NULL,
								 SubjectID VARCHAR(10) NOT NULL,
								 CourseNumber VARCHAR(200) NOT NULL,
								 ElectiveType VARCHAR(200) NOT NULL,
								 PRIMARY KEY(ProgramID, SubjectID, CourseNumber, ElectiveType),
								 FOREIGN KEY(ProgramID) REFERENCES $table_Programs(ProgramID));";
	
	//Create Database
	
	require_once("classes/database.php");	//Connect to database file 
	
	$db = new DataBase("");	//Connect to server
	
	 if ($db->execute($createDB)) {	//Create database in non exist
		//echo "Successfully Created Database<br/>";
	} else {
		echo "Error Creating Database: ".$db->getError()."<br/>";
		exit;
	}
	
	$db = new DataBase("$dbName");	//Connect to database created
	
	//Create tables
	
	if ($db->execute($CreateSectionTable)) {
		//echo "Successfully Created Section Table<br/>";
	} else {
		echo "Error Creating Section Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreateProgramsTable)) {
		//echo "Successfully Created Academic Programs Table<br/>";
	} else {
		echo "Error Creating Academic Programs Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreatePatternsTable)) {
		//echo "Successfully Created Pattern Table<br/>";
	} else {
		echo "Error Creating Pattern Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreateCTPMappingTable)) {
		//echo "Successfully Created Course to Prerequisite Mapping Table<br/>";
	} else {
		echo "Error Creating Course to Prerequisite Mapping Table: ".$db->getError()."<br/>";
		exit;
	}
	
	if ($db->execute($CreateElectivesTable)) {
		//echo "Successfully Created Electives Table<br/>";
	} else {
		echo "Error Creating Electives Table: ".$db->getError()."<br/>";
		exit;
	}
	
	/*-- Populate Tables --*/
	
	//Add Academic Programs
	$dataFile = fopen("../data/AcademicPrograms.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {
		
		$values = explode(";", $line);
		
		$PopulateAcademicProgram = "INSERT IGNORE INTO $table_Programs VALUES('$values[0]', '$values[1]');";
		if ($db->execute($PopulateAcademicProgram)) {
			//echo "Successfully populated Academic Program Table<br/>";
		} else {
			echo "Error populating Academic Program Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	//Enter Fall Course data
	$dataFile = fopen("../data/data.csv","r");	//open data file for reading

	$line = fgetcsv($dataFile, 1024);	//Get first line (Column Names)
	
	$CRNCounter = 1;	//counter to generate MOCK CRN values for course sections since they are not provided in given data
	
	while (!feof($dataFile) ) {		//while not at end of file

		$line = fgetcsv($dataFile, 1024);	//read up to 1 kilobyte in a row
	
		//	Get Column values for Course and Section Table insertion
	
		$values = explode(";",$line[0]);	//Split line into array based on ';'
	
		/*for ($i = 0; $i < 9;$i++) {
			if (!is_null($values[$i])) {
				echo "|".$values[$i]."|";
			}
		}
		echo "<br/>";*/
		
		//Course Table
		$SubjectID = "$values[0]";
		$CourseNumber = "$values[1]";
		$Title = "$values[3]";
		$Credits = 0.5;
		
		//Section Table
		$CRN = $CRNCounter;
		$ScheduleCode = "$values[4]";
		$SectionCode = "$values[2]";
		$Year = 2014;
		$Term = "fall";
		
		$StartTime =  "NULL";	//set values to null initialliy. This will account for online courses that don't have a time/day/capacity
		$EndTime = "NULL";
		$Time = "NULL";
		$Days = "NULL";
		$Capacity = "NULL";
		
		if ($values[6]!="") {					//If course has a start time
			$StartTime = $values[6];
		}
		if ($values[7]!="") {				//If course has a end time
			$EndTime = $values[7];
		}
		if ($values[6]!="") {					//If course has valid time (Start time is not null)
			$Time = $StartTime."-".$EndTime;
		}
		if ($values[5]!="") {					//If course has a Day value
			$Days = $values[5];
		}
		if ($values[8]!="") {					//If course has a capacity
			$Capacity = $values[8];
		}
		$NumberOfStudents = 0;					//Number of students in class always starts at 0
		
		//Insert into Section Table	
		$insertSectionIntoDB = "INSERT IGNORE INTO $table_Section VALUES ('$SubjectID',
																		   '$CourseNumber',
																		   $Year,
																		   '$Term',
																		   '$Title',
																			$Credits,
																		   '$ScheduleCode',
																		   '$SectionCode',
																		   '$Time',
																		   '$Days',
																		   $Capacity,
																		   $NumberOfStudents);";
		
		if ($db->execute($insertSectionIntoDB)) {
			//echo "Successfully populated Section Table<br/>";
		} else {
			echo "Error populating Section Table: ".$db->getError()."<br/>";
			exit;
		}
		
		$CRNCounter = $CRNCounter + 1;	//increment CRN counter
		
	}

	fclose($dataFile);
	
	//Enter Winter Course Data
	$dataFile = fopen("../data/datawinter.csv","r");	//open data file for reading

	$line = fgetcsv($dataFile, 1024);	//Get first line (Column Names)

	while (!feof($dataFile) ) {		//while not at end of file

		$line = fgetcsv($dataFile, 1024);	//read up to 1 kilobyte in a row
	
		//	Get Column values for Course and Section Table insertion
	
		$values = explode(";",$line[0]);	//Split line into array based on ';'
	
		/*for ($i = 0; $i < 9;$i++) {
			if (!is_null($values[$i])) {
				echo "|".$values[$i]."|";
			}
		}
		echo "<br/>";*/
		
		//Course Table
		$SubjectID = "$values[0]";
		$CourseNumber = "$values[1]";
		$Title = "$values[3]";
		$Credits = 0.5;
		
		//Section Table
		$CRN = $CRNCounter;
		$ScheduleCode = "$values[4]";
		$SectionCode = "$values[2]";
		$Year = 2015;
		$Term = "winter";
		
		$StartTime =  "NULL";	//set values to null initialliy. This will account for online courses that don't have a time/day/capacity
		$EndTime = "NULL";
		$Time = "NULL";
		$Days = "NULL";
		$Capacity = "NULL";
		
		if ($values[6]!="") {					//If course has a start time
			$StartTime = $values[6];
		}
		if ($values[7]!="") {				//If course has a end time
			$EndTime = $values[7];
		}
		if ($values[6]!="") {					//If course has valid time (Start time is not null)
			$Time = $StartTime."-".$EndTime;
		}
		if ($values[5]!="") {					//If course has a Day value
			$Days = $values[5];
		}
		if ($values[8]!="") {					//If course has a capacity
			$Capacity = $values[8];
		}
		$NumberOfStudents = 0;					//Number of students in class always starts at 0
		
		//Insert into Section Table	
		$insertSectionIntoDB = "INSERT IGNORE INTO $table_Section VALUES ('$SubjectID',
																		   '$CourseNumber',
																		   $Year,
																		   '$Term',
																		   '$Title',
																			$Credits,
																		   '$ScheduleCode',
																		   '$SectionCode',
																		   '$Time',
																		   '$Days',
																		   $Capacity,
																		   $NumberOfStudents);";
		
		if ($db->execute($insertSectionIntoDB)) {
			//echo "Successfully populated Section Table<br/>";
		} else {
			echo "Error populating Section Table: ".$db->getError()."<br/>";
			exit;
		}
		
		$CRNCounter = $CRNCounter + 1;	//increment CRN counter
		
	}

	fclose($dataFile);
	
	//Populate Pattern table
	$dataFile = fopen("../data/Patterns/SE.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {
		
		$values = explode(";", $line);
		
		/*for ($i = 0; $i < 4;$i++) {
			if (!is_null($values[$i])) {
				echo "|".$values[$i]."|";
			}
		}
		echo "<br/>";*/
		
		$PopulateP2CMapping = "INSERT IGNORE INTO $table_Patterns VALUES('SE',
																					   '$values[0]',
																					   $values[3],
																					   '$values[4]',
																					   '$values[1]',
																					   '$values[2]');";
		if ($db->execute($PopulateP2CMapping)) {
			//echo "Successfully populated Patterns Table<br/>";
		} else {
			echo "Error populating Patterns Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	//Populate prerequisites table
	$dataFile = fopen("../data/prereq.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {
		
		$values = explode(";", $line);
		
		$subID = explode(" ",$values[0]);
		
		$PopulatePrerequisites = "INSERT IGNORE INTO $table_CourseToPrereqMapping VALUES('$subID[0]',
																						 '$subID[1]',
																						 '$values[1]');";
		if ($db->execute($PopulatePrerequisites)) {
			//echo "Successfully populated Prerequisite Table<br/>";
		} else {
			echo "Error populating Prerequisite Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	//Populate Electives Table
	$dataFile = fopen("../data/Electives/complementaryElectives.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {

		$values = explode(" ",$line);
		
		$PopulateElectives = "INSERT IGNORE INTO $table_electives VALUES('SE',
																		 '$values[0]',
																		 '$values[1]',
																		 'complementary');";
		if ($db->execute($PopulateElectives)) {
			//echo "Successfully populated Electives Table<br/>";
		} else {
			echo "Error populating Electives Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	$dataFile = fopen("../data/Electives/ScienceElectives.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {

		$values = explode(" ",$line);
		
		$PopulateElectives = "INSERT IGNORE INTO $table_electives VALUES('SE',
																		 '$values[0]',
																		 '$values[1]',
																		 'science');";
		if ($db->execute($PopulateElectives)) {
			//echo "Successfully populated Electives Table<br/>";
		} else {
			echo "Error populating Electives Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	$dataFile = fopen("../data/Electives/noteAElectives.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {

		$values = explode(" ",$line);
		
		$PopulateElectives = "INSERT IGNORE INTO $table_electives VALUES('SE',
																		 '$values[0]',
																		 '$values[1]',
																		 'noteA');";
		if ($db->execute($PopulateElectives)) {
			//echo "Successfully populated Electives Table<br/>";
		} else {
			echo "Error populating Electives Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	$dataFile = fopen("../data/Electives/noteBElectives.txt","r");	//open data file for reading
	
	while (($line = fgets($dataFile)) !== false) {

		$values = explode(" ",$line);
		
		$PopulateElectives = "INSERT IGNORE INTO $table_electives VALUES('SE',
																		 '$values[0]',
																		 '$values[1]',
																		 'noteB');";
		if ($db->execute($PopulateElectives)) {
			//echo "Successfully populated Electives Table<br/>";
		} else {
			echo "Error populating Electives Table: ".$db->getError()."<br/>";
			exit;
		}
	}
	
	fclose($dataFile);
	
	echo "Successfull in creating database";
		
?>