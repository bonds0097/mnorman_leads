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

// Database details.
$db_host = "localhost"; 
$db_user = "mnorman";
$db_pass = "password";
$db_name = "mnorman_leads";

// Create new DB object.
$db = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass, $options);
?>