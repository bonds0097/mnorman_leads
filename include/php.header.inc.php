<?php
/**
 * File: php.header.inc.php
 * Author: Alfredo Ramirez
 * Date: 3/12/2013
 * Description: This file contains the PHP header that every other page will use.
 */

// Load general includes.
require_once('config.inc.php');
require_once('functions.inc.php');
require_once('db.inc.php');
require_once('db.functions.inc.php');

// Load class library.
spl_autoload_register('classAutoloader');

// Start session.
session_start();

// If user is not logged in, redirect to index.
if (!isset($_SESSION['user']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location:" . ROOTURI);
    exit;
}

?>