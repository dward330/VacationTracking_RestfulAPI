<?php 
	require ("City.php");
	
	class User{
		
		//User Properties/Fields
		private $firstName;
		private $lastName;
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
		function __construct($firstName, $lastName, $dbconn) {
			//Check Username: It can not be blank or null
			if (is_null($firstName) || strlen(trim($firstName)) === 0 || trim($firstName) == ""){
				throw new NullArgumentSuppliedForNonNullField("First Name Supplied is Blank or Null.");
			}
			
			//If Last Name is blank or null
			if (is_null($lastName) || strlen(trim($lastName)) === 0 || trim($lastName) == ""){
				throw new NullArgumentSuppliedForNonNullField("Last Name Supplied is Blank or Null.");
			}
			
			//Load Database Connections
			$this->loadDBConnections($dbconn);
			
			//Check Database Connection
			if($this->conn){} else {throw new DatabaseDisconnectedException("Cannot Connect To Database!");}
			
			//Store username
			$this->firstName = $firstName;
			$this->lastName = $lastName;
			
			//Always try to create user
			$this->createUser();
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
		
		//Does User Exists
		public function userExists(){
			//return true;
			//write query
			$sqlQuery = "select * from users u 
						 where u.firstName = ltrim(rtrim(?)) and u.lastName = ltrim(rtrim(?))";
			//echo $sqlQuery;
			//store query parameters
			$params = array($this->firstName.'', $this->lastName.'');
			//execute query
			$stmt = sqlsrv_query($this->conn, $sqlQuery,$params);
			
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
					//echo '<br />'.'found'.'<br />';
					return true;
				}
				//Query Result set was emptied
				else {
					//Free SQL Statement from Resources
					//echo '<br />'.'not found'.'<br />';
					return false;
				}
			}
		}
		
		//Create this User
		private function createUser(){
			if ($this->userExists()){
				return -1;
			}
			else {
				//User Does Not Exist:
				//Write query
				$sqlQuery = "insert into users (firstname, lastname) values (ltrim(rtrim(?)), ltrim(rtrim(?)))";
				//store query parameters
				$params = array($this->firstName."", $this->lastName."");
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
					//Free SQL Statement from Resources
					
					
					return 1;
				}
			}
		}
		
		//Get Primary Key for username
		public function getKey(){
			if ($this->userExists()){
				//User Already Exists, Store the Primary Key:
				//Write query
				$sqlQuery = "select id from users u where u.firstname = ltrim(rtrim(?)) and u.lastname = ltrim(rtrim(?)) ";
				//store query parameters
				$params = array($this->firstName."", $this->lastName."");
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
			//This User does not exists
			else {
				return (-1);
			}
		}
				 
		//Get all states this user has visited
		public function getStatesVisited(){
			$statesVisited = array();
			
			if ($this->userExists()){
				//Write query
				$sqlQuery = "select distinct s.state_name
							 from visits v
							 join states s on v.state_id = s.id
							 join users u on u.id = v.user_id
							 where u.firstname = (ltrim(rtrim(?))) and u.lastname = (ltrim(rtrim(?)))
							 ";
				//store query parameters
				$params = array($this->firstName."", $this->lastName."");
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
						array_push($statesVisited, $row['state_name']);
					}
				}
			}
			
			//Return List of States This User Visited
			return $statesVisited;
		}
		
		//Get all cities this user has visited
		public function getCitiesVisited(){
			$citiesVisited = array();
				
			if ($this->userExists()){
				//Write query
				$sqlQuery = "select distinct c.city_name
							 from visits v
							 join cities c on v.city_id = c.id
							 join users u on u.id = v.user_id
							 where u.firstname = (ltrim(rtrim(?))) and u.lastname = (ltrim(rtrim(?)))
							 ";
				//store query parameters
				$params = array($this->firstName."", $this->lastName."");
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
						array_push($citiesVisited, $row['city_name']);
					}
				}
			}
				
			//Return List of States This User Visited
			return $citiesVisited;
		}
		
		//Remove a visit(s)->City for this User
		public function removeVisit($city){
			$visitKey = "";
			
			//Write query
			$sqlQuery = "select top 1 v.id
							 from visits v
							 join cities c on v.city_id = c.id
							 join users u on u.id = v.user_id
							 where u.firstname = (ltrim(rtrim(?))) and u.lastname = (ltrim(rtrim(?))) and c.city_name = (ltrim(rtrim(?)))";
			//store query parameters
			$params = array($this->firstName."",$this->lastName."", $city."");
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
					//Store the Primary Key for this user
					$visitKey = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC)['id'];
					//Free SQL Statement from Resources
					
					
					//Write query to delete visit
					$sqlQuery = "delete from visits where id = ?";
					//store query parameters
					$params = array($visitKey);
					//execute query
					$stmt = sqlsrv_query( $this->conn, $sqlQuery, $params);
						
					//Error Occured Executing the SQL Query
					if( $stmt === false ) {
						//Free SQL Statement from Resources
						
						//Eventually throw an exception
						die( print_r( sqlsrv_errors(), true));
					}
					//SQL Query Executed Fine
					else{
						return 1;
					}
				}
				//Query Result set was emptied
				else {
					//Free SQL Statement from Resources
					
					return -1;
				}
			}
		}
		
		//Create a visit for this user
		public function createVisit($visit){
			//echoD($visit['state']);
			//echoD($visit['city']);
			$state = new State($visit['state'], $this->dbconn);
			$city = new City($visit['city'], $state->toString(), $this->dbconn);
			
			$visitAlreadyExisits = false;
			
			//Write query
			$sqlQuery = "select top 1 v.id
							 from visits v
							 join cities c on v.city_id = c.id
							 join states s on v.state_id = s.id
							 join users u on u.id = v.user_id
							 where u.firstname = (ltrim(rtrim(?))) and u.lastname = (ltrim(rtrim(?))) and (s.state_name = (ltrim(rtrim(?))) or s.abbreviation = (ltrim(rtrim(?)))) and c.city_name = (ltrim(rtrim(?)))";
			//store query parameters
			$params = array($this->firstName."",$this->lastName."", $state->toString(), $state->toString(), $city->toString());
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
					
					$visitAlreadyExisits = true;
				}
				//Query Result set was emptied
				else {
					//Free SQL Statement from Resources
					
					$visitAlreadyExisits = false;
				}
			}
			
			if ($visitAlreadyExisits){
				//Visit Already Exisits
				return -1;
			}
			else {
				//Visit Does Not already Exists:
				//Create the needed State and City Records
				if (isset($state) && isset($city)){
					//Write query
					$sqlQuery = "insert into visits (user_id, state_id, city_id)
							     values ( ltrim(rtrim(?)), ltrim(rtrim(?)), ltrim(rtrim(?)) )";
					//store query parameters
					$params = array($this->getKey()."", $state->getKey()."", $city->getKey()."");
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
						return 1;
						//Free SQL Statement from Resources
						
					}	
				}
			}
		}
		
		
		//Free the SQL Statement Object from Resources
		public function freeStmt($stmt){
			//Free the Statement resources
			sqlsrv_free_stmt( $stmt);
		}
		
		//toString
		public function toString(){
			return ($this->firstName." ".$this->lastName);
		}
		
	}
		 
?>