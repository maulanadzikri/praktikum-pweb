<?php

function getConnection(){
    // database connection details
    $host = "localhost";
    $db_name = "pweb_pert4_npm";
    $username = "root";
    $password = "";

    // create connection
    $conn = new mysqli($host, $username, $password, $db_name);

    // check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
    
}