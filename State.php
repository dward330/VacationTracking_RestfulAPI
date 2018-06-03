<?php 
	require ("NullArgumentSuppliedForNonNullField.php");
	
	class State{
			
		//User Properties/Fields
		private $stateName;
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
		
		//Initiate Object Creation
		function __construct($stateName, $dbconn) {
			//Check State Name: It can not be blank or null
			if (is_null($stateName) || strlen(trim($stateName)) === 0 || trim($stateName) == ""){
				throw new NullArgumentSuppliedForNonNullField("State Name Supplied is Blank or Null.");
			}		
			
			//Load Database Connections
			$this->loadDBConnections($dbconn);
			
			//Check Database Connection
			if($this->conn){} else {throw new DatabaseDisconnectedException("Cannot Connect To Database!");}
			
			//Store username
			$this->stateName = $stateName;
			
			
			//Always try to create user
			$this->createState();
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
	
		//Does State Exists
		public function stateExists(){
			//If there is even a state name supplied
			if (isset($this->stateName) && strlen($this->stateName) >= 1){
				//write query
				$sqlQuery = "select * from states s where s.state_name = ltrim(rtrim(?)) or s.abbreviation = ltrim(rtrim(?))";
				//store query parameters
				$params = array($this->stateName."", $this->stateName."");
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
		private function createState(){
			if ($this->stateExists()){
				return true;
			}
			else if (strlen($this->toString()) > 2) {
				//State Does Not Exist:
				//Write query
				$sqlQuery = "insert into states (state_name) values (ltrim(rtrim(?)))";
				//store query parameters
				$params = array($this->stateName."");
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
			else {
				return false; 
			}
		}
	
		//Get all cities in this state
		public function getCitiesForState(){
			$citiesInState = array();
				
			if ($this->stateExists()){
				//Write query
				$sqlQuery = "select distinct c.city_name
							 from cities c
							 join states s on c.state_id = s.id
							 where s.state_name = (ltrim(rtrim(?))) or s.abbreviation = ltrim(rtrim(?)) 
							 ";
				//store query parameters
				$params = array($this->stateName."", $this->stateName."");
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
					//Loop Through Result Set
					while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
						array_push($citiesInState, $row['city_name']);
					}
				}
			}
				
			//Return List of States This User Visited
			return $citiesInState;
		}
		
		//Get Key
		public function getKey(){
			if ($this->stateExists()){
				//State Already Exists, Store the Primary Key:
				//Write query
				$sqlQuery = "select top 1 id from states s where s.state_name = ltrim(rtrim(?)) or s.abbreviation = ltrim(rtrim(?))";
				//store query parameters
				$params = array($this->stateName."", $this->stateName."");
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
			return $this->stateName."";
		}
	
		//Free the SQL Statement Object from Resources
		public function freeStmt($stmt){
			//Free the Statement resources
			sqlsrv_free_stmt( $stmt);
		}
	}
?>