<?php
/**
 * @file admin.functions.inc.php
 * @brief Helper functions for admin page.
 * @author Alfredo Ramirez
 * @date 03/16/2013
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
?>
