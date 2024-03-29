<?php
/**
 * @file User.class.php
 * @brief User class.
 * @author Alfredo Ramirez
 * @date 03/12/2013
 */

/**
 * User class for app, contains all methods and members related to users.
 *
 * @author Alfredo Ramirez
 * @version 1.0 3/12/2013
 */
class User {
    // Class Members
    private $user;          /**< User ID. */
    private $username;      /**< Username. */
    private $balance;       /**< Points balance of user. */
    private $siteID;        /**< Site ID of user. */
    private $admin;         /**< Whether user is admin. */
    private $active;        /**< Whether use is active. */

    /**
     * Getter for userID.
     * 
     * @return int user ID
     */
    public function getUser() {
        return $this->user;
    }
    
    /**
     * Getter for username.
     * 
     * @return string username
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Getter for user's balance.
     * 
     * @return float balance
     */
    public function getBalance() {
        return $this->balance;
    }
    
    /**
     * Getter for user's site ID.
     * 
     * @return int user's Site ID
     */
    public function getSiteID() {
        return $this->siteID;
    }
    
    /**
     * Getter for user's admin status.
     * 
     * @return boolean user's admin status
     */
    public function getAdmin() {
        return $this->admin;
    }
    
    /**
     * Getter for user's active status.
     * 
     * @return boolean active status.
     */
    public function getActive() {
        return $this->active;
    }
        
    /**
     * Attempts to automatically login a user.
     * 
     * Searches user table for matching autologin string and logs in that user if match was found.
     *
     * @return true if autoLogin was successful, false otherwise.
     */
    public static function AutoLogin() {
        //If cookie exists, check if a match exists in DB.
        if (isset($_COOKIE['mnLeadsAutoLogin'])) {   
            $autoLoginCookie = $_COOKIE['mnLeadsAutoLogin'];

            // Find if there is a match in table.
            $sql = 'SELECT COUNT(*) as count FROM user 
                WHERE autologin = :autologin
                AND active = 1 ';
            
            // Filter by Site ID if needed.
            if (SINGLESITE) {
                $sql .= 'AND siteID = ' . SITEID . ' ';
            }
            
            $params = array(':autologin' => $autoLoginCookie);
            $query = query($sql, $params);
            $result = fetch($query);
            $matchExists = $result['count'];

            // If a match exists, then log that user in.
            if ($matchExists) {                
                $user = User::GetUserObj($autoLoginCookie, 'autologin');

                // Login user.
                if (User::login($user)) {
                    return true;
                }        
            }
        }

        // Return failure.
        return false;
    }

    /**
     * Attempts a manual user login.
     *
     * Verifies whether $username is valid and if $password matches. If so, logs user in.
     *
     * @param string $username is an initialized string.
     * @param string $password is an initialized string.
     * @param boolean $autologin indicates whether user selected autologin.
     * @throws InvalidUsernameException if username is too long or contains invalid characters.
     * @throws InvalidPasswordException if password is too long or contains invalid characters.
     * @throws UserDoesNotExistException if no user with $username is in database.
     * @throws InactiveUserException if user account is not active.
     * @throws IncorrectPasswordException if check fails.
     * @return true if successful, false otherwise
     */
    public static function ManualLogin($username, $password, $autologin) {
        // Validate Username
        if(!User::ValidUsername($username)) {
            throw new InvalidUsernameException('Call to ManualLogin() failed: ' . $username . ' is an 
                    invalid username.');
        }        
        
        // Validate Password
        if (!User::ValidPassword($password)) {
            throw new InvalidPasswordException('Call to ManualLogin() failed: ' . $password . ' is an 
                invalid password.');
        }
        
        // Check if user exists.
        if (!User::UserExists($username)) {
            throw new UserDoesNotExistException('Call to ManualLogin() failed: ' . $username . ' does 
                not exist in database or has the wrong siteID.');
        }
        
        // Check if password matches database.
        if (User::CheckPassword($username, $password)) {
            // Get user from database.
            $user = User::GetUserObj($username, 'username');
            
            // Make sure user account is active.
            if (!$user->getActive()) {
                throw new InactiveUserException('Call to ManualLogin() failed: ' . $username . '\'s 
                    account is inactive.');
            }

            // Login user.
            if (User::login($user, $autologin)) {
                return true;
            } else {
                return false;
            }                
        } else {
            throw new IncorrectPasswordException('Call to ManualLogin() failed: ' . $password . ' is 
                not the correct password for user(' . $username . ').');
            return false;
        }
    }

    /**
     * Logs in user.
     *
     * Logs in a user either after vrifying password or through autologin.
     *
     * @see AutoLogin()
     * @see ManualLogin()
     * @param User $user is an initialized User object.
     * @param boolean $autologin indicates whether user should be autologged in in the future.
     * @return true if login successful, false otherwise.
     */
    public static function Login(User $user, $autologin = false) {
        // Start Session
        session_regenerate_id(true);
        
        // Update session variable.
        $_SESSION['user'] = $user;
        
        // Generate autologin string and set cookie if necessary.
        if ($autologin) {            
            $autologinString = mcrypt_create_iv(50, MCRYPT_DEV_URANDOM);
            
            $sql = 'UPDATE user SET autologin = :autologin WHERE user = :user;';
            $params = array(
                ':autologin'    => $autologinString,
                ':user'         => $_SESSION['user']->getUser()
            );
            
            query($sql, $params);
            
            setcookie('mnLeadsAutoLogin', $autologinString, time() + 2*7*24*60*60);
        }

        // Update user's last login date.
        $sql = 'UPDATE user SET lastLogin = DEFAULT WHERE user = :user;';
        $params = array(':user' => $_SESSION['user']->getUser());
        
        return query($sql, $params);
    }

    /**
     * Sets the user's password.
     *
     * Method to change a user's password. Generates a new salt that is combined with input string 
     * to generate a new hash as well.
     * 
     * @param string $password1 is an initialized string.
     * @param string $password2 is an initialized string.
     * @throws PasswordsDontMatchException if $password1 != $password2
     * @throws InvalidPasswordException if $password is not a valid password string.
     * @return boolean true if successful, false otherwise
     */
    public function setPassword($password1, $password2) {
        // Make sure passwords match.
        if (strcmp($password2, $password1) != 0) {
            throw new PasswordsDontMatchException('Call to setPassword() failed: ' . $password2 . 
                    ' and ' . $password1 . ' don\'t match.');
        }

        // Validate password string.
        if (!User::ValidPassword($password1)) {
            throw new InvalidPasswordException('Call to setPassword() failed: ' . $password1 . ' is 
                not a valid password.');
        }
        
        // Create new salt and hash and update database.
        $salt = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
        $hash = User::GenerateHash($password1, $salt);
        
        $sql = 'UPDATE user SET salt = :salt, hash = :hash WHERE user = :user;';
        $params = array(
            ':salt'     => $salt,
            ':hash'     => $hash,
            ':user'     => $this->getUser()
        );
        
        return query($sql, $params);
    }

    /**
     * Creates new user.
     *
     * Method to allow administrators to create new users and insert them into database.
     *
     * @see ValidateAllUserFields()
     * @param string $username is an initialized string.
     * @param string $password is an initialized string.
     * @param double $balance is an initialized decimal.
     * @param int $siteID is an initialized integer.
     * @param boolean $admin is an initialized boolean.     
     * @return boolean true if successful, false otherwise.
     */
    public static function NewUser($username, $password, $balance, $siteID, $admin) {
        // Validate data.
        if (User::NewUserValidation($username, $password, $balance, $siteID, $admin)) {
            // Generate salt and hash.
            $salt = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
            $hash = User::GenerateHash($password, $salt);

            $autologin = mcrypt_create_iv(50, MCRYPT_DEV_URANDOM);

            // Create new user in database.
            $sql = 'INSERT INTO user(username, salt, hash, autologin, balance, siteID, admin) 
               VALUES(:username, :salt, :hash, :autologin, :balance, :siteID, :admin);';

            $params = array(
                ':username' => $username,
                ':salt' => $salt,
                ':hash' => $hash,
                ':autologin' => $autologin,
                ':balance' => $balance,
                ':siteID' => $siteID,
                ':admin' => $admin ? 1 : 0
            );

            return query($sql, $params);
        }
    }
    
    /**
     * Update a user's information.
     * 
     * @param string $username is initialized.
     * @param double $balance is initialized.
     * @param int $siteID is initialized.
     * @param boolean $admin is initialized.
     * @return boolean true if successful, false otherwise.
     */
    public function update($username, $balance, $siteID, $admin) {
        // Validate data.
        if (User::UserValidation($username, $balance, $siteID, $admin)) {
            // Update database entry.
            $sql = 'UPDATE user 
                SET username = :username, balance = :balance, siteID = :siteID, admin = :admin
                WHERE user = :user;';
            
            $params = array(
                ':username'     => $username,
                ':balance'      => $balance,
                ':siteID'       => $siteID,
                ':admin'        => $admin,
                ':user'         => $this->user
            );
            
            return query($sql, $params);
        }
    }       
    
    /**
     * Deletes user from database.
     * 
     * @return boolean true if successful, false otherwise
     */
    public function delete() {
        // Delete user from database.
        $sql = 'DELETE FROM user WHERE user = :user;';
        $params = array(':user' => $this->user);
        
        return query($sql, $params);
    }
    
    /**
     * Checks whether input string is a valid username.
     * 
     * Valid usernames begin with a letter and contain only alphanumeric characters and the 
     * underscore character. MINUSERNAMELENGTH <= length <= MAXUSERNAMELENGTH
     * 
     * @param string $username is an initialized string.
     * @return boolean true if username is valid, false otherwise.
     */
    public static function ValidUsername($username) {
        // Check username length.
        if (strlen($username) < MINUSERNAMELENGTH ||
                strlen($username) > MAXUSERNAMELENGTH) {
            return false;
        }

        // TODO: Replace regex with ctype_alnum check instead.
        $options = array('options' =>
            array('regexp' => USERNAMEFORMAT));
        if (!(filter_var($username, FILTER_VALIDATE_REGEXP, $options))) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks whether a username is already in the database.
     * 
     * @param string $username is a validated username string.
     * @return boolean true if user already exists, false otherwise.
     */
    public static function UserExists($username) {
        $sql = 'SELECT COUNT(*) as count FROM user WHERE username = :username ';
        
        // Filter by Site ID if needed.
        if (SINGLESITE) {
            $sql .= 'AND siteID = ' . SITEID . ' ';
        }
        
        $params = array(':username' => $username); 
        $result = query($sql, $params);
        $userExists = $result->fetchColumn();
        
        if ($userExists == 0) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Checks whether a string is a valid password.
     * 
     * A string is a valid password if it contains at least one uppercase character, one lowercase 
     * character, one number, one special character in the set {!@#$%^&*()} and if of length 
     * >= MINPASSWORDLENGTH and <= MAXPASSWORDLENGTH.
     * 
     * @param string $password is an initialized string.
     * @return boolean true if password is valid, false otherwise.
     */
    public static function ValidPassword($password) {
        // Check password length. 
        if (strlen($password) < MINPASSWORDLENGTH || 
                strlen($password) > MAXPASSWORDLENGTH) {
            return false;
        }
        
        // Validate password characters.
        $options = array('options' => 
            array('regexp' => PASSWORDFORMAT));
        if (!(filter_var($password, FILTER_VALIDATE_REGEXP, $options))) {
            return false;
        } else {
            return true;
        }        
    }
    
    /**
     * Validate user fields.
     * 
     * @param string $username is initialized.
     * @param double $balance is initialized.
     * @param int $siteID is initialized.
     * @param boolean $admin is initialized.
     * @return boolean true if all fields are valid.
     * @throws InvalidArgumentException if a field does match expected type.
     * @throws InvalidUsernameException if $username is not a valid username.
     */
    public static function UserValidation($username, $balance, $siteID, $admin) {
        if (!filter_var($balance, FILTER_VALIDATE_FLOAT)) {
            throw new InvalidArgumentException('Call to ValidateAllUserFields() failed: ' . 
                    $balance . ' is not a valid float/double.');
        }
        if (!filter_var($siteID, FILTER_VALIDATE_INT)) {
           throw new InvalidArgumentException('Call to ValidateAllUserFields() failed: ' . $siteID . 
                    ' is not a integer'); 
        }
        
        if (is_null(filter_var($admin, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {
            throw new InvalidArgumentException('Call to ValidateAllUserFields() failed: ' . $admin . 
                    ' is not a boolean'); 
        }
        
        // Username Validation - String Format
        if (!User::ValidUsername($username)) {
            throw new InvalidUsernameException('Call to ValidateAllUserFields() failed: ' . 
                    $username . ' is not a valid username.');
        }   
        
        return true;
    }
    
    /**
     * Validate new user fields.
     * 
     * @param string $username is initialized.
     * @param string $password is initialized.
     * @param double $balance is initialized.
     * @param int $siteID is initialized.
     * @param boolean $admin is initialized.
     * @return boolean true if all fields are valid.
     * @throws UserAlreadyExistsException if user with $username is already in the database.
     * @throws InvalidPasswordException if $password is not a valid password.
     */
    static function NewUserValidation($username, $password, $balance, $siteID, $admin) {
        if (User::UserValidation($username, $balance, $siteID, $admin)) {
            // Username Validation - Already Exists
            if (User::UserExists($username)) {
                throw new UserAlreadyExistsException('Call to NewUserValidation() failed: ' . 
                        $username . ' is already in the system.');
            }
            // Password Validation
            if (!User::ValidPassword($password)) {
                throw new InvalidPasswordException('Call to NewUserValidation() failed: ' . 
                        $password . ' is not a valid password.');
            }
            
            return true;
        }
    }
    
    /**
     * Checks whether user's input matches database password.
     * 
     * @param string $username is a validated username.
     * @param string $password is a validated password.
     * @return boolean true if passwords match, false otherwise.
     */
    public static function CheckPassword($username, $password) {
        // Get user's salt and hash.
        $sql = 'SELECT salt, hash FROM user WHERE username = :username;';
        $params = array(':username' => $username);
        $result = query($sql, $params);
        
        $userInfo = fetch($result);
        
        // Generate hash from user-given password.
        $inputHash = User::GenerateHash($password, $userInfo['salt']);
        
        // Compare generated and stored hash.
        if (strcmp($userInfo['hash'], $inputHash) == 0) {
            return true;
        } else {            
            return false;
        }            
    }
    
    /**
     * Generates a password hash.
     * 
     * Hashes $salt once and appends it to $password, the combined string is then hashed HASTIMES (a 
     * global constant).
     * 
     * @param string $password is a validated password string.
     * @param string $salt is pre-generated 20-character salt string.
     * @return $hash is an SHA1-generated hash.
     */
    public static function GenerateHash($password, $salt) {
        $hash = $password . hash('sha1', $salt);
        for ($i=0; $i < HASHTIMES; $i++) { 
            $hash = hash('sha1', $hash);
        }
        
        return $hash;
    }
    
    /**
     * Returns User object with matching parameter.
     * 
     * @param string $parameter is an initialized string.
     * @param string $parameterType is a valid field in user table.
     * @return User object where $parameterType == $parameter.
     */
    public static function GetUserObj($parameter, $parameterType = 'username') {
        $sql = 'SELECT user, username, balance, siteID, admin, active FROM user '; 
        $params = array(':parameter' => $parameter);
             
        switch ($parameterType) {
            case 'username': 
                $sql .= 'WHERE username = :parameter;';
                break;
            case 'autologin':
                $sql .= 'WHERE autologin = :parameter';
                break;
            case 'user':
                $sql .='WHERE user = :parameter';
                break;
        }
        $result = query($sql, $params);
        
        $result->setFetchMode(PDO::FETCH_CLASS, 'User');
        
        return $result->fetch();
    }
    
    /**
     * Fetches all users in database.
     * 
     * @return Array of User objects.
     */
    public static function GetAllUsers($sortType = 'username') {
        $sql = 'SELECT user, username, balance, siteID, admin, active FROM user ';
        
        // Set sorting.
        if ($sortType == 'username') {
            $sql .= 'ORDER BY username;';
        }
        
        $result = query($sql);
        $result->setFetchMode(PDO::FETCH_CLASS, 'User');
        
        return $result->fetchAll();
    }
    
    /**
     * Deducts $cost from the user's balance.
     * 
     * @param double $cost is initialized and >= 0.
     * @return boolean true if balance was successfully updated in database.
     * @throws InvalidArgumentException if $cost is not a float.
     * @throws InvalidCostException if $cost < 0.
     * @throws InsufficientBalanceException if $cost > user's balance.
     */
    public function deductBalance($cost) {
        // Make sure $cost is a number.
        if (!filter_var($cost, FILTER_VALIDATE_FLOAT)) {
            throw new InvalidArgumentException('Call to deductBalance() failed: ' . $cost . ' is not 
                of type double.');
        }
        
        // Make sure $cost >= zero.
        if ($cost < 0) {
            throw new InvalidCostException('Call to deductBalance() failed: ' . $cost . ' is not 
                greater than or equal to zero.');
        }
        
        // Make sure user has enough money.
        if ($cost > $this->balance) {
            throw new InsufficientBalanceException('Call to deductBalance() failed: insufficient 
                balance.');
        }
        
        // Update user's balance in object.
        $this->balance -= $cost;
        
        // Update user's balance in database.
        $sql = 'UPDATE user SET balance = :balance WHERE user = :user;';
        $params = array(
            ':balance'  => $this->balance,
            ':user'     => $this->user
        );      
        
        return query($sql, $params);
    }

    /**
     * Logs out current user.
     * 
     * Destroys autologin cookie if it exists.
     * 
     * @return boolean true if logout is successful.
     */
    public function logout() {
        // Unset all of the session variables.
        $_SESSION = array();

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy the autologon cookie.
        if (isset($_COOKIE['mnLeadsAutoLogin'])) {
            setcookie ('mnLeadsAutoLogin', '', time() - 3600);
        }

        // Finally, destroy the session.
        session_destroy();

        return true;
    }
}
?>