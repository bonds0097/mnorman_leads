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
$pageTitle = 'Login';
$display = 0;
$loggedIn = false;

// Check for logout.
if(isset($_GET['action'])) {
    if ($_GET['action'] == 'logout') {
        if (isset($_SESSION['user'])) {
            if ($_SESSION['user']->logout()) {
                message('success', 'You have successfully logged out.');
            }
        }
    }
}

// Check for autologin.
if (User::autoLogin()) {
    $loggedIn = true;    
}

// Check for manual login.
if (isset($_POST['login'])) {
    $autologin = false;
    if (isset($_POST['autologin'])) {
        $autologin = true;
    } 
    
    // Try to log user in and handle all exceptions.
    try {
        if (User::ManualLogin($_POST['username'], $_POST['password'], $autologin)) {
            $loggedIn = true;
        } else {
            throw new RuntimeException('Unspecified error caused User::ManualLogin() to return 
                false.');
        }
    } catch (InvalidUsernameException $e) {
        message('error', 'Invalid/Wrong username or password. Please try again.');
        logError($e, false);
    } catch (InvalidPasswordException $e) {
        message('error', 'Invalid/Wrong username or password. Please try again.');
        logError($e, false);
    } catch (UserDoesNotExistException $e) {
        message('error', 'Invalid/Wrong username or password. Please try again.');
        logError($e, false);
    } catch (InactiveUserException $e) {
        message('error', 'Your account has been deactivated. Please contact the System Administrator 
            for further information.');
        logError($e, false);
    } catch (IncorrectPasswordException $e) {
        message('error', 'Invalid/Wrong username or password. Please try again.');
        logError($e, false);
    } catch (Exception $e) {
        message('error', 'Unspecified error occured. Please contact the System Administrator if this
            error persists.');
        logError($e, false);
    } 
}

// Check for aready logged in.
if (isset($_SESSION['user'])) {
    $loggedIn = true;
}

// Check if login occured.
if ($loggedIn) {
    if ($_SESSION['user']->getAdmin()) {
        header('Location:' . ROOTURI . '/user/admin.php');
    } else {
        header('Location:' . ROOTURI . '/lead/newLead.php');
    }
}

// This page has 1 displays.
// $display == 0: User login page.

// Include HTML Header.
require_once('html.header.inc.php');

if ($display == 0) { ?>
    <h1>Please Log In</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <label>
            Username: 
            <input type="text" name="username" autofocus="autofocus" 
                   maxlength="<?php echo MAXUSERNAMELENGTH ?>"  
                   required="required" size="<?php echo MAXUSERNAMELENGTH ?>" 
                   placeholder="Enter your Username""/>
        </label>
        <br />
        <label>
            Password:
            <input type="password" name="password" maxlength="<?php echo MAXPASSWORDLENGTH?>" 
                   required ="required" size="<?php echo MAXPASSWORDLENGTH ?>" 
                   placeholder="Enter your Password"/>
        </label>
        <br />
        <label>
            Autologin:
            <input type="checkbox" name="autologin" value="true">
        </label>
        <br />
        <input name="login" type="submit" value="Log In"/>
    </form>
<?php } ?>
<?php 
require_once('html.footer.inc.php');
?>
