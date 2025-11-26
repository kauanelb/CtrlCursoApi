<?php

//ambiente marcosvirgilio.online
$servername = 'mysql-prog3-curso.g.aivencloud.com';
$port = 22471;
$username = 'avnadmin';
$password = 'AVNS_eHvVk74tCcWeBmDOadu';
$dbname = 'curso';

// Create connection
$con = new mysqli($servername, $username, $password, $dbname, $port);

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

?>