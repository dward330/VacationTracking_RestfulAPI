######Name: Derrick Ward
######Date: 3/27/16
######Database Server Choice: Microsoft SQL Server 2012
######Back-End/Server Side Programming Language: PHP
######Message Communication Medium to and from Client: JSON

######Important Note: Please See Project Documents folder for the following:
- Derrick Ward Project Details.docx
- Derrick Ward Project Details.pdf
- Derrick Ward_DB_Diagram_RV_Project.pdf

######Web Service’s Endpoints:
1. Get Request: /state/{state}/cities
  * Example Request: http://<server>/state/AK/cities
  * Purpose: Get a list of all cities in a state
2. Get Request: /user/{user}/visits
  * Example Request: http://<server>/user/firstname/lastname/visits
  * Purpose: Get a list of cities user has visited
3. Get Request: /user/{user}/visits/states
  * Example Request: http://<server>/user/firstname/lastname/visits/states
  * Purpose: Get a list of states user has visited
4. Post Request: /user/{user}/visits
  * Example Request: http://<server>/user/firstname/lastname/visits
  * Content Type Supplied in Network Request: application/json
    * Example of Content Supplied, in Network Request: {“City”:”Erie”,”State”:”PA”}
  * Purpose: Create a new visit for a user, to a particular city
5. Delete Request: /user/{user}/visit/{visit}
  * Example Request: http://<server>/user/firstname/lastname/visit/city
  * Purpose: Remove improperly pinned visit for user



######Error Handling:
1. Improperly Formed Requests
  * Checked the network request path and the appropriate request method type for each request. 
2. Depending on communication endpoint, Checked for:
  * Blank City Name
  * Blank State Name
  * Blank First Name
  * Blank Last Name
3. Fields of the JSON Message Returned to the Client:
  * ErrorCode
    * Values
    * 0 means no error
    * 1 means there was an error
  * Message
    * Value
    * String containing what went wrong with the request
  * Path Requested (only present on error)
    * Value
    * ‘>’ delimited Path/Endpoint, the client tried to reach
  * Request Method Type (only present on error)
    * Value
    * Request Method Type Client used in making network request
  * Example: DELETE



######Application Server Setup:
1. Application Web Server used: Apache 2.4.9
  * Package Installed, containing server: WAMPSERVER 2.5 (32 bit version)
2. PHP Version: 5.5.12
3. Modifications made to both PHP 5.5.12 and Apache 2.4.9:
  * Httpd.conf (For Apache)
    * Enable the rewrite_module
    * Replace your httpd.conf with the one included in DW_RV_Project.zip/RedVenture/APACHE HTTPD CONF
  * Created an .htaccess file and save it in the “www” folder on Wamp 
    * File is included in DW_RV_Project.zip
    * DW_RV_Project.zip/RedVenture/.htaccess
    * Place file in “www” Folder
  * Modified the php.ini file to include the following lines:
    * extension=php_sqlsrv_55_nts.dll
    * extension=php_sqlsrv_55_ts.dll
  * Added the following PHP DLL files to PHP:
    * php_sqlsrv_55_nts.dll
    * php_sqlsrv_55_ts.dll
    * Purpose: SQL Server 2012 Communication
    * DLL are included in DW_RV_Project.zip (DW_RV_Project.zip/RedVenture/PHP DLL Needed)
    * Place DLLs in wamp\bin\php\php5.5.12\ext\
4. PHP Scripts Should all be in the same directory (\wamp\www\RedVenture\): 
  * Move RedVenture folder (after unzipping DW_RV_Project.zip ) into www/RedVenture/



######Database Server Setup:
1. Database Server used: SQL Server 2012
2. Script to Create Database Tables and Schema
  * CreationScript.sql
3. Script to Drop Tables:
  * DropTables.sql
4. Script to Load Dataset into Tables:
  * Insert Fresh Dataset.sql
5. Script to Show all Database Tables and Contents
  * viewAllTables.sql
6. Database Name Chosen:
  * RV_Coding_Challenge_Derrick
7. Database Table Names and Schema:
  * Cities
    * Columns:
    * Id: Primary Key, Bigint, Not Null
    * City_name: varchar(100), Not Null
    * State_id: Foreign_Key, Bigint, Not Null
    * Status: varchar(100), Null
    * Latitude: Float, Null
    * Longitude: Float, Null
    * Datetimeadded: datetime, Not Null
    * Dateadded: date, Not Null
    * Lasttimeupdated: datetime, Not Null
    * Primary Keys
    * Id
    * Foreign Keys: 
    * State_id: References Primary Key in State table
    * Indexes:
    * Unique, non-clustered index on (City_name, State_id)
    * Triggers:
    * On Lasttimeupdated column
  * Set to update this column to the current system time, when a specific row modified
  * States
    * Columns:
    * Id: Primary Key, Bigint, Not Null
    * State_name: varchar(100) Not Null
    * Abbreviation: varchar(2), Null
    * Datetimeadded: datetime, Not Null
    * Dateadded: date, Not Null
    * Lasttimeupdated: datetime, Not Null
    * Primary Keys
    * Id
    * Foreign Keys: 
    * None
    * Indexes:
    * Unique, non-clustered index on (state_name)
    * Triggers:
    * On Lasttimeupdated column
  * Set to update this column to the current system time, when a specific row modified
  * Users
    * Columns:
    * Id: Primary Key, Bigint, Not Null
    * firstname: varchar(100) Not Null
    * lastname: varchar(100) Not Null
    * Datetimeadded: datetime, Not Null
    * Dateadded: date, Not Null
    * Lasttimeupdated: datetime, Not Null
    * Primary Keys
    * Id
    * Foreign Keys: 
    * None
    * Indexes:
    * Unique, non-clustered index on (firstname, lastname)
    * Triggers:
    * On Lasttimeupdated column
  * Set to update this column to the current system time, when a specific row modified
  * Visits
    * Columns:
    * Id: Primary Key, Bigint, Not Null
    * User_id: Foreign_Key, Bigint, Not Null
    * State_id: Foreign_Key, Bigint, Not Null
    * City_id: Foreign_Key, Bigint, Not Null
    * Datetimeadded: datetime, Not Null
    * Dateadded: date, Not Null
    * Lasttimeupdated: datetime, Not Null
    * Primary Keys
    * Id
    * Foreign Keys: 
    * User_id: References Primary Key in User table
    * State_id: References Primary Key in State table
    * City_id: References Primary Key in City table
    * Indexes:
    * Unique, non-clustered index on (user_id, state_id, city_id)
    * Triggers:
    * On Lasttimeupdated column
  * Set to update this column to the current system time, when a specific row modified



######PHP SCRIPTS:
1. Files:
  * City.php
    * Purpose: Model for City Objects
  * State.php
    * Purpose: Model for State Objects
  * User.php
    * Purpose: Model for User Objects
  * Endpoint.php
    * Purpose: Router/Controller, Operations Management (Complete Requested Tasks)
  * Dbconnection.php
    * Purpose: Contains helper function and Database Connection Information
  * InvalidEndpointOrRequest.php
    * Purpose: Thrown when client is trying to reach an Endpoint that does not exist or tries to access an endpoint with the incorrect Request Method Type
  * DatabaseDisconnectException.php
    * Purpose: Thrown when the Database connection to the Database Server is not present during the Object Creation of Data Models (User, City, State) 
  * NullArgumentSuppliedforNonNullField.php
    * Purpose: Thrown when client supplies a blank or null value for a field that must be populated
  * OperationFailedException.php
    * Purpose: Thrown when Operations such as the following fail:
    * Deleting a Visit
    * Creating a Visit





