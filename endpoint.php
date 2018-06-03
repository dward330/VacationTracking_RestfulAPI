
	    <?php
	    require ("dbconnection.php");
	    require ("User.php");
	    require ("InvalidEnpointOrRequest.php");
	    require ("DatabaseDisconnectedException.php");
	    require ("OperationFailedException.php");
	    
	    //Set Content Type We will be returning
	    header('Content-Type: application/json');
	    
	     $restAPIPathArray= explode('/',$_SERVER['REQUEST_URI']);
	     $restAPIPathArray=array_map('strtolower',$restAPIPathArray);
	     //echoD("Path: ".print_r($restAPIPathArray));
	     
	     $errorCode = 0;
	     $Result = 1;
	     $data = json_decode(file_get_contents('php://input'),true);
		 //echoD(file_get_contents('php://input'));
		 
	     
	    try{ 
			if ( isset($_SERVER['REQUEST_METHOD']) ){
					switch ($_SERVER['REQUEST_METHOD']){
						case (strpos($_SERVER['REQUEST_URI']," ") || strpos($_SERVER['REQUEST_URI'],"=")
						     || strpos($_SERVER['REQUEST_URI'],"%") || strpos($_SERVER['REQUEST_URI'],"'")):
							throw new InvalidEndpointOrRequest("Invalid Endpoint and,or Request Method!");
							break;
						case "GET":
							/* GET /state/{state}/cities -> Get all the cities in a state*/
							if (((sizeof($restAPIPathArray) === 4) || (sizeof($restAPIPathArray) === 5) )
							   && (strcmp($restAPIPathArray[2],"") !== 0)
							   && $restAPIPathArray[1] === "state" && $restAPIPathArray[3] === "cities" 
							   && (!isset($restAPIPathArray[4]) || (strcmp($restAPIPathArray[4],"") === 0) )){
								//echoD("I received a GET Request");
								//echoD("You want a List of all cities in: ".$restAPIPathArray[2]);
								
								$firstname = $restAPIPathArray[2]; //store first name
								$lastname = $restAPIPathArray[3]; //store last name
								
								$state = new State($restAPIPathArray[2], $dbconn); //create State
								if ($state->stateExists()){ //State exists/created
									//Get Cities in this State
									$citiesInState = $state->getCitiesForState();
								
									//Return, in JSON, the cities in this state
									echo json_encode(array("errorCode"=>$errorCode,"Cities"=>$citiesInState));
								}
								else{
									$blank = array();
									echo json_encode(array("errorCode"=>$errorCode,"Cities"=>$blank));
								}
							}
							/* GET /user/{firstname}/{lastname}/visits -> Get all the cities visited for a user*/
							else if (((sizeof($restAPIPathArray) === 5) || (sizeof($restAPIPathArray) === 6) ) && $restAPIPathArray[1] === "user" 
									&& (strcmp($restAPIPathArray[2],"") !== 0) && (strcmp($restAPIPathArray[3],"") !== 0)
									&& $restAPIPathArray[4] === "visits" && (!isset($restAPIPathArray[5]) || (strcmp($restAPIPathArray[5],"") === 0) ) ){
								//echoD("I received a GET Request");
								//echoD("You want a list of all the cities visited for: ".$restAPIPathArray[2]." ".$restAPIPathArray[3]);
								
								$firstname = $restAPIPathArray[2]; //store first name
								$lastname = $restAPIPathArray[3]; //store last name
								
								$user = new User($firstname, $lastname, $dbconn); //create User
								if ($user->userExists()){ //User exists/created
									//Get the Cities a User Visited
									$citiesVisited = $user->getCitiesVisited();
										
									//Return, in JSON, the cities a user visited
									echo json_encode(array("errorCode"=>$errorCode,"Cities"=>$citiesVisited));
								}
								else{
									$blank = array();
									echo json_encode(array("errorCode"=>$errorCode,"Cities"=>$blank));
								}
							}
							/* GET /user/{firstname}/{lastname}/visits/states -> Get all the states visited for a user*/
							else if ( ((sizeof($restAPIPathArray) === 6) || (sizeof($restAPIPathArray) === 7) ) && $restAPIPathArray[1] === "user" 
									&& (strcmp($restAPIPathArray[2],"") !== 0) && (strcmp($restAPIPathArray[3],"") !== 0)
									&& $restAPIPathArray[4] === "visits" && $restAPIPathArray[5] === "states"
									&& (!isset($restAPIPathArray[6]) || (strcmp($restAPIPathArray[6],"") === 0) )){
								//echoD("I received a GET Request");
								//echoD("You want a list of all the states visited for: ".$restAPIPathArray[2]." ".$restAPIPathArray[3]);
								
								$firstname = $restAPIPathArray[2]; //store first name
								$lastname = $restAPIPathArray[3]; //store last name
								
								$user = new User($firstname, $lastname, $dbconn); //create User
								if ($user->userExists()){ //User exists/created
									//Get the States a User Visited
									$statesVisited = $user->getStatesVisited();
									
									//Return, in JSON, the states a user visited
									echo json_encode(array("errorCode"=>$errorCode,"States"=>$statesVisited));
								}
								else{
									$blank = array();
									echo json_encode(array("errorCode"=>$errorCode,"States"=>$blank));
								}
								
							}
							//Non-Supported EndPoint
							else {throw new InvalidEndpointOrRequest("Invalid Endpoint and,or Request Method!");}
							break;
						case "POST":
							/* POST /user/{firstname}/{lastname}/visits -> Add a Visit*/
							if (((sizeof($restAPIPathArray) === 5) || (sizeof($restAPIPathArray) === 6) ) && $restAPIPathArray[1] === "user" 
									&& (strcmp($restAPIPathArray[2],"") !== 0) && (strcmp($restAPIPathArray[3],"") !== 0)
									&& $restAPIPathArray[4] === "visits" && (!isset($restAPIPathArray[5]) || (strcmp($restAPIPathArray[5],"") === 0))){
								//echoD("I received a POST Request");
								//echoD("You want to add a visit for: ".$restAPIPathArray[2]." ".$restAPIPathArray[3]);
								
								//If Json Data was sent
								if (isset($data)){
									$firstname = $restAPIPathArray[2]; //store first name
									$lastname = $restAPIPathArray[3]; //store last name
									
									//Make all keys, in json, lowercase
									$data = array_change_key_case($data, CASE_LOWER);
										
									$visit['city'] = $data['city']; // store city
									$visit['state'] = $data['state']; // store state
									
									$user = new User($firstname, $lastname, $dbconn); //create User
									
									if ($user->userExists()){ //User exists/created
										$visitCreated = $user->createVisit($visit); //Create Visit for User
										if ($visitCreated === 1 || $visitCreated === -1){
											//Visit Sucessfully Created
											$blank = array();
											echo json_encode(array("errorCode"=>$errorCode));
										}
										else {
											$errorCode = 1; //Visit was not successsfully created. 
											throw new OperationFailedException("Visit Failed to be Created for User:
													                            \n<br />City Sent: ".$visit['city']
																				 ."\n<br />State Sent: ".$visit['state']
																				 ."\n<br />First Name: ".$firstname
																				 ."\n<br />Last Name: ".$lastname);
										}
									}
								}
								
							}
							//Non-Supported EndPoint
							else {throw new InvalidEndpointOrRequest("Invalid Endpoint and,or Request Method!");}
							break;
						case "DELETE":
							/* DELETE /user/{firstname}/{lastname}/visit/{city} -> Delete a Visit*/
							if (((sizeof($restAPIPathArray) === 6) || (sizeof($restAPIPathArray) === 7) ) && $restAPIPathArray[1] === "user" 
									&& (strcmp($restAPIPathArray[2],"") !== 0) && (strcmp($restAPIPathArray[3],"") !== 0) && (strcmp($restAPIPathArray[5],"") !== 0)
									&& $restAPIPathArray[4] === "visit" && (!isset($restAPIPathArray[6]) || (strcmp($restAPIPathArray[6],"") === 0)) ){
								//echoD("I received a DELETE Request");
								//echoD("You want to remove: ".$restAPIPathArray[5]
								//	  ."\n<br/>As a visit for: ".$restAPIPathArray[2]." ".$restAPIPathArray[3]);
								
								$firstname = $restAPIPathArray[2]; //store first name
								$lastname = $restAPIPathArray[3]; //store last name
								$city = $restAPIPathArray[5]; //store city
								
								$user = new User($firstname, $lastname, $dbconn); //create User								
								if ($user->userExists()){ //User exists/created
									$visitRemoved = $user->removeVisit($city);
									if ($visitRemoved === 1 || $visitRemoved === -1){
										//Visit Sucessfully Removed or Never was there
										$blank = array();
										echo json_encode(array("errorCode"=>$errorCode));
									}
									else {
										$errorCode = 1; //Visit Removal Process through a php or sql error
										throw new OperationFailedException("Visit Removal Failed for User:
													                         \n<br />City Sent: ".$city
																			."\n<br />First Name: ".$firstname
																			."\n<br />Last Name: ".$lastname);
									}
								}
							}
							//Non-Supported EndPoint
							else {throw new InvalidEndpointOrRequest("Invalid Endpoint and,or Request Method!");}
							break;
						case "DEL":
							/* DEL /user/{firstname}/{lastname}/visit/{city} -> Delete a Visit*/
							if (((sizeof($restAPIPathArray) === 6) || (sizeof($restAPIPathArray) === 7) ) && $restAPIPathArray[1] === "user"
									&& (strcmp($restAPIPathArray[2],"") !== 0) && (strcmp($restAPIPathArray[3],"") !== 0) && (strcmp($restAPIPathArray[5],"") !== 0)
									&& $restAPIPathArray[4] === "visit" && (!isset($restAPIPathArray[6]) || (strcmp($restAPIPathArray[6],"") === 0)) ){
								//echoD("I received a DEL Request");
								//echoD("You want to remove a visit: ".$restAPIPathArray[5]
								//		."\n<br />for: ".$restAPIPathArray[2]." ".$restAPIPathArray[3]);
								
								$firstname = $restAPIPathArray[2]; //store first name
								$lastname = $restAPIPathArray[3]; //store last name
								$city = $restAPIPathArray[5]; //store city
								
								$user = new User($firstname, $lastname, $dbconn); //create User
								if ($user->userExists()){ //User exists/created
									$visitRemoved = $user->removeVisit($city);
									if ($visitRemoved === 1 || $visitRemoved === -1){
										//Visit Sucessfully Removed or Never was there
										$blank = array();
										echo json_encode(array("errorCode"=>$errorCode));
									}
									else {
										$errorCode = 1; //Visit Removal Process through a php or sql error
										throw new OperationFailedException("Visit Removal Failed for User:
													                         \n<br />City Sent: ".$city
												."\n<br />First Name: ".$firstname
												."\n<br />Last Name: ".$lastname);
									}
								}
							}
							//Non-Supported EndPoint
							else {throw new InvalidEndpointOrRequest("Invalid Endpoint and,or Request Method!");}
							break;
						default:
							//Non-Supported EndPoint
							throw new InvalidEndpointOrRequest("Invalid Endpoint and//or Request Method!");
					}
			}
	    }
	    catch (Exception $e){
	    	//echoD($e->__toString());
	    	$errorCode = 1;
	    	$jsonReply = array("errorCode"=>$errorCode,"Message"=>$e->__toString(), "Path Requested"=>str_replace("/",">",$_SERVER['REQUEST_URI']),"Request Method Type:"=>$_SERVER['REQUEST_METHOD']);
	    	echo json_encode($jsonReply);
	    }
				
		?>