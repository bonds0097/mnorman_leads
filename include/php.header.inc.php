<?php
/**
 * @file php.header.inc.php
 * @brief This file contains the PHP header that every other page will use.
 * @author Alfredo Ramirez
 * @date 3/12/2013
 */

// Load general includes.
require_once('config.inc.php');
require_once('functions.inc.php');
require_once('db.inc.php');
require_once('db.functions.inc.php');

// Load class library.
spl_autoload_register('classAutoloader');

// Start the session.
session_start();

// If user is not logged in, redirect to index.
if (!isset($_SESSION['user']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location:" . ROOTURI);
    exit;
}

?>