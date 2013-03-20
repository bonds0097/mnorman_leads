<?php
/**
 * @file admin.functions.inc.php
 * @brief Helper functions for admin page.
 * @author Alfredo Ramirez
 * @date 03/16/2013
 */

/**
 * Creates an output string to display the users table.
 * 
 * @return string $html containing the users table.
 */
function displayUsersTable() {
    // Get all users from the database.
    $users = User::GetAllUsers();
    
    // Prepare table header.
    $html = '<table>
                <thead>
                    <th>Username</th>
                    <th>Balance</th>
                    <th>Site ID</th>
                    <th>Admin</th>
                    <th>Edit User</th>                    
                    <th>Change Password</th>
                    <th>Delete User</th>
                </thead>
                <tbody>';
    
    // Populate table with users.
    foreach ($users as $user) {
        $html .= '<tr>';
        $html .= '<td>' . $user->getUsername() . '</td>';
        $html .= '<td>' . $user->getBalance() . '</td>';
        $html .= '<td>' . $user->getSiteID() . '</td>';
        $html .= '<td>' . $user->getAdmin() . '</td>';
        $html .= '<td>' . '<a href="' . $_SERVER['PHP_SELF'] . '?editUser=' . $user->getUser() . 
                '">Edit User</a>' . '</td>';
        $html .= '<td>' . '<a href="' . ROOTURI . '/user/password.php?userID=' . $user->getUser() . 
                '">Change Password</a>' . '</td>';
        $html .= '<td>' . '<a href="' . $_SERVER['PHP_SELF'] . '?deleteUser=' . $user->getUser() . 
                '">Delete User</a>' . '</td>';
        $html .= '</tr>';
    }
    
    // Add table footer.
    $html .= '</tbody></table>';
    
    return $html;
}

/**
 * Validates the Add/Edit User Form. 
 * 
 * @see User::ValidateAllUserFields()
 * @param string $addUser indicates whether a new user is being added.
 * @return boolean true if form is valid.
 * @throws MissingFormFieldsException if not all form fields are submitted.
 */
function validateUserForm($addUser = true) {
    // Make sure all form fields are filled in.
    if (!(isset($_POST['username'])  &&
            isset($_POST['balance']) &&
            isset($_POST['siteID']) &&
            isset($_POST['admin'])
            )) {
        throw new MissingFormFieldsException('Call to validateAddUserForm() failed: one or more 
            required form fields is missing.');
    }
    
    // If it is an add user form, check that the password field is completed as well.
    if ($addUser) {
        if(!isset($_POST['password'])) {
            throw new MissingFormFieldsException('Call to validateAddUserForm() failed: one or more 
                required form fields is missing.');
        }
    }

    // Validate data.
    if ($addUser) {
        if (User::NewUserValidation($_POST['username'], $_POST['password'], $_POST['balance'], 
                $_POST['siteID'], $_POST['admin'])) {
            return true;
        }           
    } else {
        if (User::UserValidation($_POST['username'], $_POST['balance'], $_POST['siteID'], 
                $_POST['admin'])) {
            return true;
        }
    }
}
?>
