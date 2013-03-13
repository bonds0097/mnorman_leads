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

    /** 
     * Initialized constructor.
     *
     * @param $user is initialized integer.
     * @param $username is initialized string.
     * @param $balance is initialized decimal.
     * @param $siteID is initialized integer.
     * @param $admin is initialized boolean.
     * @return creates User object with all members initialized.
     */
    public function __construct($user, $username, $balance, $siteID, $admin) {
        $this->user     = $user;
        $this->username = $username;
        $this->balance  = $balance;
        $this->siteID   = $siteID;
        $this->admin    = $admin;
    }

    /**
     * Attempts to automatically login a user.
     * 
     * Searches user table for matching autologin string and logs in that user if match was found.
     *
     * @return true if autoLogin was successful, false otherwise.
     */
    public static function autoLogin() {
        //If cookie exists, check if a match exists in DB.
        if (isset($_COOKIE['mnLeadsAutoLogin'])) {   
            $autoLoginCookie = $_COOKIE['mnLeadsAutoLogin'];

            // Find if there is a match in table.
            $sql = "SELECT COUNT(*) as count FROM user WHERE autologin = :autologin;";
            $params = array(':autologin' => $autoLoginCookie);
            $matchExists = query($sql, $params);
            $matchExists = fetch($matchExists);
            $matchExists = $matchExists['count'];

            // If a match exists, then log that user in.
            if ($matchExists) {
                $sql = "SELECT user, username, balance, siteID, admin 
                        FROM user
                        WHERE autologin = :autologin;";
                $user = query($sql, $params);
                $user = $user->fetch(PDO::FETCH_CLASS, 'User');

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
     * Verifies user's password matches input.
     *
     * Looks up user's password in user table and determines whether it matches user's input.
     *
     * @param $username is an initialized string.
     * @param $password is an initialized string.
     * @return success or error message.
     */
    public static function checkPassword($username, $password) {

    }

    /**
     * Logs in user.
     *
     * Logs in a user either after vrifying password or through autologin.
     *
     * @see {autoLogin(), checkPassword()}
     * @param $username is an initialized User object.
     * @return true if login successful, false otherwise.
     */
    public static function login($user) {
        // Update session variable.
        $_SESSION['user'] = $user;

        // Update user's last login date.
        $sql = "UPDATE user WHERE user = :user;";
        $params = array(':user' => $_SESSION['user']->get('user'));

        return true;
    }

    /**
     * Sets the user's password.
     *
     * Method to change a user's password. Generates a new salt that is combined with input string 
     * to generate a new hash as well.
     *
     * @param $user is an initialized integer.
     * @param $password is an initialized string.
     * @return sucess/failure message.
     */
    public function setPassword($user, $password) {

    }

    /**
     * Creates new user.
     *
     * Method to allow administrators to create new users and insert them into database.
     *
     * @param $username is an initialized string.
     * @param $password is an initialized string.
     * @param $balance is an initialized decimal.
     * @param $siteID is an initialized integer.
     * @param $admin is an initialized boolean.
     * @throws InvalidArgumentException if any parameter is the wrong type.
     * @throws UserAlreadyExistsException if user with $username is already in database.
     * @throws InvalidPasswordException if password is too long or contains invalid characters.
     * @return true if successful, false otherwise.
     */
    public static function newUser($username, $password, $balance, $siteID, $admin) {
        // Validate data.

        // Generate salt and hash.
        $salt = mcrypt_create_iv(20, MCRYPT_DEV_URANDOM);
        $hash = $password . hash('sha1', $salt);
        for ($i=0; $i < HASHTIMES; $i++) { 
            $hash = hash('sha1', $hash);
        }

        $autologin = mcrypt_create_iv(50, MCRYPT_DEV_URANDOM);

        // Create new user in database.
    }

    /**
     * Logs out current user.
     * 
     * Destroys autologin cookie if it exists.
     */
    public function logout() {

    }
}
?>