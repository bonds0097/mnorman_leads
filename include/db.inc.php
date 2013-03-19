<?php
/**
 * File: db.inc.php
 * Author: Alfredo Ramirez
 * Date: 3/12/2013
 * Description: This file contains the database connection logic for the entire application.
 */

// Set error mode to exceptions.
$options = array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
); 

$local = true; 

// Database details.
if ($local) {
    $db_host = "localhost"; 
    $db_user = "mnorman";
    $db_pass = "password";
    $db_name = "mnorman_leads";
} else {
    $db_host = "localhost"; 
    $db_user = "onther7_mnorman";
    $db_pass = "Password123!";
    $db_name = "onther7_mnorman_leads";
}

// Create new DB object.
$dbh = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, $options);
?>