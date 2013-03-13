<?php
/**
 * @file index.php
 * @brief Landing page for site. Allows user to login.
 * @author Alfredo Ramirez
 * @date 03/12/2013
 */

// Include PHP Header.
require_once('php.header.inc.php');

// Set initial variables.
$display = 0;

// Check for autologin.
if (User::autoLogin()) {
    header('Location:' . ROOTURI . '/lead/newLead.php');
}

// This page has 1 displays.
// $display == 0: User login page.



?>