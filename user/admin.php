<?php
/*
 * File: admin.php
 * Author: Alfredo Ramirez
 * Date: 03/16/2013
 * Description: Admin page for site. Allows admins to modify users.
 */
// TODO: Add Exception Handling for all transactions.
// TODO: Add check for out of range user ID for editUser.

// Include PHP Header.
require_once('php.header.inc.php');

// Include admin functions.
require_once('/include/admin.functions.inc.php');

// Check for admin permission.
if (!isset($_SESSION['user']) || !$_SESSION['user']->getAdmin()) {
    header('Location:' . ROOTURI);
}

// Set initial variables.
$pageTitle = 'Administration';
$display = 0;

// Check whether an edit user request was submitted.
// If so, store user in session.
if (isset($_GET['editUser'])) {
    $_SESSION['editUser'] = User::GetUserObj($_GET['editUser'], 'user');
    $display = 1;
}

// Check whether new user was submitted.
// If so, validate data and store in session.
if (isset($_POST['addUser'])) {
    if (validateUserForm(true)) {
        $_SESSION['addUser']['username'] = $_POST['username'];
        $_SESSION['addUser']['password'] = $_POST['password'];
        $_SESSION['addUser']['balance'] = $_POST['balance'];
        $_SESSION['addUser']['siteID'] = $_POST['siteID'];
        $_SESSION['addUser']['admin'] = $_POST['admin'];
        $display = 2;        
    }
}

// Check whether a delete user request was submitted.
// If so, store user in session.
if (isset($_GET['deleteUser'])) {
    $_SESSION['deleteUser'] = User::GetUserObj($_GET['deleteUser'], 'user');
    $display = 3;    
}

// Check whether user was edited.
// If so, validate form and make changes or request resubmit.
if (isset($_POST['editUser'])) {
    if (validateUserForm(false)) {
        if ($_SESSION['editUser']->update($_POST['username'], $_POST['balance'], $_POST['siteID'], 
                $_POST['admin'])) {
            message('success', $_SESSION['editUser']->getUsername() . ' was successfully updated.');
            unset($_SESSION['editUser']);
            $display = 4;           
        } 
    }
}

// Check whether use was added.
// If so, make change.
if (isset($_POST['addUserConfirmed'])) {
    if(User::NewUser($_SESSION['addUser']['username'], $_SESSION['addUser']['password'], 
            $_SESSION['addUser']['balance'], $_SESSION['addUser']['siteID'], 
            $_SESSION['addUser']['admin'])) {
        message('success', 'New user successfully added.');
        unset($_SESSION['addUser']);
        $display = 4;
    }
}

// Check whether use was deleted.
// If so, make change.
if (isset($_POST['deleteUserConfirmed'])) {
    if ($_SESSION['deleteUser']->delete()) {
        message('succes', $_SESSION['deleteUser']->getUsername() . ' was removed from the 
            database.');
        unset($_SESSION['deleteUser']);
        $display = 4;
    }
}

// This page has displays.
// $display == 0: User list.
// $display == 1: Edit user page.
// $display == 2: Add user confirmation.
// $display == 3: Delete user confirmation.
// $display == 4: Message page.

// Include HTML Header.
require_once('html.header.inc.php');

if ($display == 0) { ?>
    <h1>List of Users</h1>
    <?php echo displayUsersTable() ?>
    <h1>Add User</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <label>
            Username:
            <input type="text" name="username" autofocus="autofocus"
                   maxlength="<?php echo MAXUSERNAMELENGTH?>" size="<?php echo MAXUSERNAMELENGTH ?>"
                   required="required" placeholder="Enter New Username" />
        </label>
        <br />
        <label>
            Password:
            <input type="password" name="password" autofocus="autofocus"
                   maxlength="<?php echo MAXPASSWORDLENGTH?>" size="<?php echo MAXPASSWORDLENGTH ?>"
                   required="required" placeholder="Enter New Password"/>
        </label>
        <br />
        <label>
            Balance:
            <input type="number" name="balance" min="0"
                   maxlength="16" size="16"
                   required="required" placeholder="Enter New Balance"/>
        </label>
        <br />
        <label>
            Site ID:
            <input type="number" name="siteID" min="0"
                   maxlength="16" size="16"
                   required="required" placeholder="Enter New Site ID"/>
        </label>
        <br />
        <label>
            Admin:
            <label><input type="radio" name="admin" value="1"/>Yes</label>
            <label><input type="radio" name="admin" value="0"/>No</label>
        </label>
        <br />
        <input name="addUser" type="submit" value="Add User"/>
    </form> 
<?php } else if ($display == 1) { ?>
    <h1>Edit User</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <label>
            Username:
            <input type="text" name="username" autofocus="autofocus"
                   maxlength="<?php echo MAXUSERNAMELENGTH?>" size="<?php echo MAXUSERNAMELENGTH ?>"
                   required="required" placeholder="Enter New Username" 
                   value="<?php echo $_SESSION['editUser']->getUsername()?>"/>
        </label>
        <br />
        <label>
            Balance:
            <input type="number" name="balance" min="0"
                   maxlength="16" size="16"
                   required="required" placeholder="Enter New Balance"
                   value="<?php echo $_SESSION['editUser']->getBalance()?>"/>
        </label>
        <br />
        <label>
            Site ID:
            <input type="number" name="siteID" min="0"
                   maxlength="16" size="16"
                   required="required" placeholder="Enter New Site ID"
                   value="<?php echo $_SESSION['editUser']->getSiteID()?>"/>
        </label>
        <br />
        <label>
            Admin:
            <label><input type="radio" name="admin" value="1" <?php echo $_SESSION['editUser']->getAdmin() ? 'checked' : ''?>/>Yes</label>
            <label><input type="radio" name="admin" value="0"<?php echo !$_SESSION['editUser']->getAdmin() ? 'checked' : ''?>/>No</label>
        </label>
        <br />
        <input name="editUser" type="submit" value="Edit User"/>
    </form> 
<?php } else if ($display == 2) { ?>
    <h1>Add User Confirmation</h1>
    <p>Please verify the information below:</p>
    <ul>
        <li><b>Username:</b><?php echo $_SESSION['addUser']['username']?></li>
        <li><b>Password:</b><?php echo $_SESSION['addUser']['password']?></li>
        <li><b>Balance:</b><?php echo $_SESSION['addUser']['balance']?></li>
        <li><b>Site ID:</b><?php echo $_SESSION['addUser']['siteID']?></li>
        <li><b>Admin:</b><?php echo $_SESSION['addUser']['admin']? 'Yes' : 'No'?></li>
    </ul>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <input name="addUserConfirmed" type="submit" value="Confirm"/>
    </form>
<?php } else if ($display == 3) { ?>
    <h1>Delete User Confirmation</h1>
    <p>You are about to delete user '<b><?php echo $_SESSION['deleteUser']->getUsername()?></b>'. 
        This is <b>not</b> un-doable. Are you sure?</p>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <input name="deleteUserConfirmed" type="submit" value="Confirm"/>
    </form>
<?php } else if ($display == 4) { ?>
    <div><a href="<?php echo $_SERVER['PHP_SELF']?>">Return to Admin Page</a></div>
<?php } ?>
<?php 
require_once('html.footer.inc.php');
?>