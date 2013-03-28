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

/**
 * Adds message to session for later display.
 * 
 * @param string $type CSS class that message will encode to.
 * @param string $message message string
 */
function message($type, $message){

	$type = strtolower($type);
	
	$_SESSION['messages'][] = '<div class="'.$type.'">'.$message.'</div>';
}

/**
 * Formats messages for display.
 * 
 * @param string $message message or array of messages
 * @return string formatted messages.
 */
function formatMessages($message = 0){

	if (($message == 0) && (isset($_SESSION['messages']))) {		
		$message = $_SESSION['messages'];
		unset($_SESSION['messages']);
	}
	if (is_array($message)) {
            $message = array_unique($message);
            $html = '<div>';
            foreach ($message as $value) {
                    $html = $html.$value;
            }
            $html = $html.'</div>';
            if ($html != '<div></div>') {
                    return $html;
            }
	}
}

/**
 * Logs errors in the Server Error Log with a detailed message including the Exception's own message.
 * 
 * @param Exception $e
 * @param boolean $loggedIn
 */
function logError(Exception $e, $loggedIn = true) {
    if ($loggedIn) {
        error_log('[' . __FILE__ . '][' . __LINE__ . ']: User ' . $_SESSION['user']->getUser() . ' at ' . 
                $_SERVER['REMOTE_ADDR'] . ' triggered the following exception:\n' . 
                $e->getMessage() . '\nTrace:\n' . $e->getTraceAsString());
    } else {
        error_log('[' . __FILE__ . '][' . __LINE__ . ']: Unknown User at ' . $_SERVER['REMOTE_ADDR']
                . ' triggered the following exception:\n' . $e->getMessage() . '\nTrace:\n' . 
                $e->getTraceAsString());
    }
}
?>