<?php 
	require ("State.php");

	class City{
			
		//User Properties/Fields
		private $cityName;
		private $state;
		private $id;
		
		//Database Connection Information:
		private $database_host;
		private $database_name;
		private $database_user;
		private $database_pass;
		
		private $serverName;
		private $connectionInfo;
		private $conn;
		private $dbconn;
		
	//$city = new City($visit['city'], $state->toString(), $this->dbconn);
		//Initiate Object Creation
		function __construct($cityName, $state_name, $dbconn) {
			//Check City Name: It can not be blank or null
			if (is_null($cityName) || strlen(trim($cityName)) === 0 || trim($cityName) == ""){
				throw new NullArgumentSuppliedForNonNullField("City Name Supplied is Blank or Null.");
			}
			
			//Check State Name: It can not be blank or null
			if (is_null($state_name) || strlen(trim($state_name)) === 0 || trim($state_name) == ""){
				throw new NullArgumentSuppliedForNonNullField("City Name Supplied is Blank or Null.");
			}
			
			//Load Database Connections
			$this->loadDBConnections($dbconn);
			
			//Check Database Connection
			if($this->conn){} else {throw new DatabaseDisconnectedException("Cannot Connect To Database!");}
			
			//Store username
			$this->cityName = $cityName;
			$this->state = new State($state_name, $dbconn);		
				
			//Always try to create user
			$this->createCity();
			//Later throw an exception if there is no username supplied
		}
	
		//Load DB Connections
		private function loadDBConnections($dbconn){
			//Database Connection Information:
			$this->database_host = $dbconn['database_host'];
			$this->database_name = $dbconn['database_name'];
			$this->database_user = $dbconn['database_user'];
			$this->database_pass = $dbconn['database_pass'];
			$this->dbconn = $dbconn;
			
			$this->serverName = $this->database_host;
			$this->connectionInfo = array( "Database"=>$this->database_name, "UID"=>$this->database_user, "PWD"=>$this->database_pass);
			$this->conn = sqlsrv_connect( $this->serverName, $this->connectionInfo);
		}
		
		//Does City Exists
		public function cityExists(){
			//If there is even a city name supplied
			if (isset($this->cityName) && strlen($this->cityName) >= 1){
				//write query
				$sqlQuery = "select * from cities c where c.city_name = ltrim(rtrim(?))";
				//store query parameters
				$params = array($this->cityName."");
				//execute query
				$stmt = sqlsrv_query( $this->conn, $sqlQuery, $params);
			
				//Error Occured Executing the SQL Query
				if( $stmt === false ) {
					//Free SQL Statement from Resources
					
					//Eventually throw an exception
					die( print_r( sqlsrv_errors(), true));
				}
				//SQL Query Executed Fine
				else {
					//At least 1 row returned
					if (sqlsrv_has_rows( $stmt )){
						//Free SQL Statement from Resources
						
						return true;
					}
					//Query Result set was emptied
					else {
						//Free SQL Statement from Resources
						
						return false;
					}
				}
			
			}
			//This Object has no username
			else {
				return false;
			}
		}

		//Create State
		private function createCity(){
			if ($this->cityExists()){
				return true;
			}
			else {
				//City Does Not Exist:
				
				//Write query
				$sqlQuery = "insert into cities (city_name, state_id) values (ltrim(rtrim(?)), ltrim(rtrim(?)))";
				//store query parameters
				$params = array($this->cityName."",$this->state->getKey());
				//execute query
				$stmt = sqlsrv_query( $this->conn, $sqlQuery, $params);
			
				//Error Occured Executing the SQL Query
				if( $stmt === false ) {
					//Free SQL Statement from Resources
					
					//Eventually throw an exception
					die( print_r( sqlsrv_errors(), true));
						
					return false;
				}
				//SQL Query Executed Fine
				else {
					//Free SQL Statement from Resources
					
						
					return true;
				}
			}
		}
	
		//Get Key
		public function getKey(){
			if ($this->cityExists()){
				//City Already Exists, Store the Primary Key:
				//Write query
				$sqlQuery = "select top 1 id from cities c where c.city_name = ltrim(rtrim(?))";
				//store query parameters
				$params = array($this->cityName."");
				//execute query
				$stmt = sqlsrv_query( $this->conn, $sqlQuery, $params);
					
				//Error Occured Executing the SQL Query
				if( $stmt === false ) {
					//Free SQL Statement from Resources
					
					//Eventually throw an exception
					die( print_r( sqlsrv_errors(), true));
				
					return (-1);
				}
				//SQL Query Executed Fine
				else {
					//Store the Primary Key for this user
					$this->id = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['id'];
					//Free SQL Statement from Resources
					
				
					return ($this->id);
				}
			}
			//State Does Not Exist
			else {
				return (-1);
			}
		}
	
		//toString
		public function toString(){
			return $this->cityName."";
		}
	
		//Free the SQL Statement Object from Resources
		public function freeStmt($stmt){
			//Free the Statement resources
			sqlsrv_free_stmt( $stmt);
		}
		
	}
?>