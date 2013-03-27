<?php
/**
 * @file password.php
 * @brief Page for users or admins to change a user's password.
 * @author Alfredo Ramirez
 * @date 03/16/2013
 */
// TODO: Add userID validation.

// Include PHP Header.
require_once('php.header.inc.php');

// Set initial variables.
$pageTitle = 'Change Password';
$display = 0;

// Change user password if requested.
if (isset($_POST['changePassword'])) {
    try {
        if ($_SESSION['passwordChangeUser']->setPassword($_POST['newPassword1'], $_POST['newPassword2'])) {
            message('success', $_SESSION['passwordChangeUser']->getUsername() . '\'s password was 
                successfully changed.');
            unset($_SESSION['passwordChangeUser']);
        }
    } catch (PasswordsDontMatchException $e) {
        message('error', 'The two passwords you entered did not match. Please re-enter them 
            carefully and try again.');
    } catch (InvalidPasswordException $e) {
        message('error', 'The password you entered is not valid. Passwords must contain at least 
            one lowercase and one uppercase letter, one number and one special character 
            (!@#$%^&*()_+-=). Please try again.');
        logError($e);
    } catch (Exception $e) {
        message('error', 'Unspecified error occured. User\'s password was not changed. 
            Please contact the System Administrator if this problems persists.');
        logError($e);
    }
} else {
    // Check if admin rights are needed for password change.
    if (isset($_GET['userID'])) {
        if (!$_SESSION['user']->getAdmin()) {
            header("Location:" . ROOTURI . '/user/password.php');
            exit();
        } else {
            // Grab user for password change.
            $_SESSION['passwordChangeUser'] = User::GetUserObj($_GET['userID'], 'user');
        }
    } else {
        $_SESSION['passwordChangeUser'] = $_SESSION['user'];
    }  
}

// This page has 1 display.
// $display == 0: Password change form.

// Include HTML Header.
require_once('html.header.inc.php');

if ($display == 0) { ?>
    <h1>Change Password</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <label>
            Enter your New Password:
            <input type="password" name="newPassword1" id="newPassword1" autofocus="autofocus"
                   maxlength="<?php echo MAXPASSWORDLENGTH?>" size="<?php echo MAXPASSWORDLENGTH ?>"
                   required="required" placeholder="Enter your New Password" />
        </label>
        <br />
        <label>
            Re-Enter your New Password:
            <input type="password" name="newPassword2" id="newPassword2" 
                   maxlength="<?php echo MAXPASSWORDLENGTH?>" size="<?php echo MAXPASSWORDLENGTH ?>"
                   required="required" placeholder="Re-Enter your New Password"/>
        </label>
        <br />
        <input name="changePassword" type="submit" value="Change Password"/>
    </form> 
<?php } ?>
<?php 
require_once('html.footer.inc.php');
?>