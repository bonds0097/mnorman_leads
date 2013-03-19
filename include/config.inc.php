<?php
/**
 * @file config.inc.php
 * @brief Global configuration for the app.
 * @author Alfredo Ramirez
 * @date 03/12/2013
 */

// Set the folder where the app is located relative to the root.
$siteFolder = '/mnorman_leads';

// Define Global Path Constants.
DEFINE('ROOTURI', 'http://' . $_SERVER['HTTP_HOST'] . $siteFolder);
DEFINE('ROOTPATH', $_SERVER['DOCUMENT_ROOT'] . $siteFolder);

// Define some security constants.
DEFINE('HASHTIMES', 64000);
DEFINE('MINPASSWORDLENGTH', 8);
DEFINE('MAXPASSWORDLENGTH', 32);
DEFINE('MINUSERNAMELENGTH', 6);
DEFINE('MAXUSERNAMELENGTH', 16);
DEFINE('USERNAMEFORMAT', '/^[A-Za-z][A-Za-z0-9]*(?:_[A-Za-z0-9]+)*$/');
DEFINE('PASSWORDFORMAT', '/(?=.*[A-Z]+)(?=.*[a-z]+)(?=.*\d+)(?=.*[!@#$%^&\*()]+)/');
?>