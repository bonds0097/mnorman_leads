<?php
/*
 * File: admin.php
 * Author: Alfredo Ramirez
 * Date: 03/16/2013
 * Description: Admin page for site. Allows admins to modify users.
 */

// Include PHP Header.
require_once('php.header.inc.php');

// Include admin functions.
require_once('/include/admin.functions.inc.php');

// Check for admin permission.

// Set initial variables.
$pageTitle = 'Login';
$display = 0;

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
            <input type="number" name="balance" min="0"
                   maxlength="16" size="16"
                   required="required" placeholder="Enter New Site ID"/>
        </label>
        <br />
        <label>
            Admin:
            <label><input type="radio" name="admin" vale="0"/>Yes</label>
            <label><input type="radio" name="admin" vale="1"/>No</label>
        </label>
        <br />
        <input name="addUser" type="submit" value="Add User"/>
    </form> 
<?php } ?>
<?php 
require_once('html.footer.inc.php');
?>