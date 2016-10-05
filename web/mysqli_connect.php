<?php # mysqli_connect.php
// This file contains the database access information.
// This file also establishes a connection to MySQL
// and selects the database.

// Set the database access information as constants:
// DEFINE ('DB_USER', 'itp225');
// DEFINE ('DB_PASSWORD', 'itp225');
// DEFINE ('DB_HOST', 'localhost');
// DEFINE ('DB_NAME', 'cbennington225proj');

DEFINE ('DB_USER', 'b17eb82e7165b2');
DEFINE ('DB_PASSWORD', '639afb32');
DEFINE ('DB_HOST', 'us-cdbr-iron-east-04.cleardb.net');
DEFINE ('DB_NAME', 'heroku_375ba6db2105b5f');

// mysql://b17eb82e7165b2:639afb32@us-cdbr-iron-east-04.cleardb.net/heroku_375ba6db2105b5f?reconnect=true

// Make the connection:
$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Make the connection:
$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );

// Set the encoding...
mysqli_set_charset($dbc, 'utf8');
