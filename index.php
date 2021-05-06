<?php

session_start();

use function DBFunctions\DBConnect;
use function DBFunctions\DBFetch;
use function DBFunctions\DBGetError;
use function DBFunctions\DBNumRows;

include_once dirname(__FILE__) . "/lib_database.php";
include_once dirname(__FILE__) . "/orm_se_settings.php";

$host     = "localhost";
$user     = "root";
$password = "root";
$database = "swiff_base";
$port     = 3307;
$socket   = "/Applications/MAMP/tmp/mysql/mysql.sock";

// We make the connection with the database
$return = DBConnect($host, $user, $password, $database, $port, $socket);

// We validate that a connection error has not been generated.
if(DBGetError() != false) {

    // We store in the return the errors generated during the connection
    var_dump(DBGetError());
    exit();
}

$orm = new se_settings();
$result = $orm->Update(array("sese_valie" => "320"), array("sese_id" => "TOKEN_LIFETIME"));

// Validate that there is no error in the query made
if(DBGetError() != false) {

    // We print the possible error (s) generated and cut the execution.
    var_dump(DBGetError());
    exit();
}

$result = $orm->Select(array("sese_id", "sese_value"), null, "sese_value ASC", 70);

// Validate that there is no error in the query made
if(DBGetError() != false) {

    // We print the possible error (s) generated and cut the execution.
    var_dump(DBGetError());
    exit();
}

// We print the number of rows found in the query.
var_dump(DBNumRows($result)); print "<br>";

// We go through the rows obtained.
while ($info = DBFetch($result)) {

    // We print the information of each row obtained.
    var_dump($info);
    print "<br>";
}
