<?php
/**
 * @file functions.inc.php
 * @brief Global functions for entire app.
 * @author Alfredo Ramirez
 * @date 03/12/2013
 */

/**
 * Autoloader for PHP classes.
 *
 * @param $class is an initialized string.
 */
function classAutoloader($class) {
    include ROOTPATH . '/include/classLibrary/' . $class . '.class.php';
}
?>