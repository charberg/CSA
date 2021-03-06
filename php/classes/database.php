<?php

/*
If you use database (mySql), have a class db.php or database.php where the
mysql host, user, password, database name  are specified. This file must include a 
method that creates the database where you will store your data. Your project 
must not use more than one database. When evaluating your work, that is the 
ONLY file that will be modified to set the actual values of the parameters (mysql 
host, user, password, database name) .  ALL queries in mySql must be done 
through a call of a method implemented in that php file.
*/

	class DataBase{

		private $host = "localhost";	//Specify information required to access server/database
		private $user = "root";
		private $password = "";
		private $dbName = "SchedulerDatabase";
	
		function __construct($dataBaseName) {
		
			if ($dataBaseName == "")
				$this->connection = mysqli_connect($this->host, $this->user, $this->password);
			else
				$this->connection = mysqli_connect($this->host, $this->user, $this->password, $dataBaseName);
		}
		
		function execute($sql) {
			return $this->connection->query($sql);
		}
		
		function getError(){
			return mysqli_error($this->connection);
		}
		
		//Exports given table to given output file in CVS format
		function exportTableToCVS($outFile, $tableName) {
		
			$output = fopen($outFile, "w");	// open file to write to
			
			$columns = $this->execute("SELECT COLUMN_NAME
									   FROM INFORMATION_SCHEMA.COLUMNS 
									   WHERE TABLE_SCHEMA='$dbName' 
									   AND TABLE_NAME='$tableName';");	//Get columns names of table
			
			$columnArray = array();	//create array for column names
			
			while ( ($row = $columns->fetch_object() ) ) {	//create array of column names
			
				array_push($columnArray, $row);
			}
			
			$rows = $this->execute("SELECT * FROM $tableName;");	//Select everything from table
			
			fputcsv($output, $columnArray);	//insert column names in first line of output file
			
			while ( ($row = $rows->fetch_object() ) ) {
			
				fputcsv($output, $row);	//Insert every row in table to output file
			}
			
			fclose($output);	//close file
		
		}
		
	}
	
?>