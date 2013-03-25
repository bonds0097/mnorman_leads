<?php
/**
 * @file Lead.class.php
 * @brief Lead class.
 * @author Alfredo Ramirez
 * @date 03/20/2013
 */

/**
 * Lead Class for app, contains all members and methods for the lead purchase process.
 *
 * @author Alfredo Ramirez
 * @version 1.0 3/19/2013
 */
class Lead {
    // Class Members
    private $lead;          /**< Lead ID **/
    private $date;          /**< Date lead was added. **/
    private $description;   /**< Description of lead. **/
    private $zipcode;       /**< Zipcode of lead. **/
    private $name;          /**< Name of lead. **/
    private $phone;         /**< Phone number of lead. Format is (xxx) xxx-xxxx **/
    private $email;         /**< E-Mail address of lead. **/
    private $cost;          /**< Price of Lead. **/
    private $bought;        /**< How many users have bought the lead. **/  
    private $buyDate;       /**< Date lead was purchased. **/
    
    /**
     * Getter for Lead ID.
     * 
     * @return int lead ID
     */
    public function getLead() {
        return $this->lead;
    }
    
    /**
     * Getter for Lead Date.
     * 
     * @return date of lead
     */
    public function getDate() {
        return $this->date;
    }
    
    /**
     * Getter for Lead Description.
     * 
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Getter for Lead Zipcode.
     * 
     * @return string 
     */
    public function getZipcode() {
        return $this->zipcode;
    }

    /**
     * Getter for Lead Name.
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Getter for Lead Phone Number.
     * 
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Getter for Lead E-Mail address.
     * 
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Getter for Lead Site ID.
     * 
     * @return int
     */
    public function getCost() {
        return $this->cost;
    }

    /**
     * Getter for number of times lead has been bought.
     * 
     * @return int
     */
    public function getBought() {
        return $this->bought;
    }
    
    /**
     * Getter for date lead was purchased.
     * 
     * @return date
     */
    public function getBuyDate() {
        return $this->buyDate;
    }

        /**
     * Fetches Lead data from DB that matches parameter and returns it as an object.
     * 
     * @param string $parameter is an initialized string.
     * @param string $parameterType is an initialized string.
     * @return Lead object that matches parameter.
     */
    public static function getLeadObj($parameter, $parameterType = 'lead') {
        $sql = 'SELECT lead, date, description, zipcode, name, phone, email, cost, bought 
            FROM lead, leadCost 
            WHERE lead.siteID = leadCost.siteID ';
        
        switch($parameterType) {
            case 'lead':
                $sql .= 'AND lead = :parameter';
        }
        
        $params = array(':parameter' => $parameter);
        $result = query($sql, $params);
        $result->setFetchMode(PDO::FETCH_CLASS, 'Lead');
        
        return $result->fetch();
    }   
    
    /**
     * Grabs all leads from database that should be available to logged in user.
     * 
     * @param string $sortType field by which list will be sorted.
     * @return array of Lead Objects that meet parameters.
     */
    public static function getLeadsForUser($sortType = 'date') {
        // Select for lead that haven't been hidden or bought by user.
        $sql = 'SELECT lead, date, description, zipcode, name, phone, email, cost, bought 
            FROM lead, leadCost 
            WHERE lead NOT IN (SELECT lead FROM boughtlead WHERE user = :user) 
            AND lead NOT IN (SELECT lead FROM hidelead WHERE user = :user) 
            AND lead.siteID = leadCost.siteID
            AND bought < ' . MAXBUYS . ' ';
        
        // Filter by Site ID if needed.
        if (SINGLESITE) {
            $sql .= 'AND lead.siteID = ' . SITEID . ' ';
        }
        
        // Figure out sorting.
        switch ($sortType) {
            case 'date':
                $sql .= 'ORDER BY date;';
        }
        
        $params = array(
            ':user'     => $_SESSION['user']->getUser());
        $result = query($sql, $params);
        $result->setFetchMode(PDO::FETCH_CLASS, 'Lead');
        
        return $result->fetchAll();
    }
    
    /**
     * Fetches all leads that have been purchased by user.
     * 
     * @param string $sortType field by which list will be sorted.
     * @return array of lead objects that have been purchased by user.
     */
    public static function getBoughtLeadsForUser($sortType = 'buyDate') {
        // Select for leads that have been purchased by user.
        $sql = 'SELECT lead.lead AS lead, lead.date AS date, description, zipcode, name, phone, 
            email, bought, boughtlead.date AS buyDate
            FROM lead, boughtlead
            WHERE boughtlead.user = :user
            AND lead.lead = boughtlead.lead ';
        
        // Figure out sorting.
        switch ($sortType) {
            case 'buyDate':
                $sql .= 'ORDER BY buyDate;';
        }
        
        $params = array(':user' => $_SESSION['user']->getUser());
        $result = query($sql, $params);
        $result->setFetchMode(PDO::FETCH_CLASS, 'Lead');
        
        return $result->fetchAll();
    }
    
    /**
     * Logged in User buys lead.
     * 
     * @return boolean true if transaction successful.
     */
    public function buyLead() {
        // Make sure lead is still available.
        $sql = 'SELECT bought FROM lead WHERE lead = :lead;';
        $params = array(':lead' => $this->lead);
        $result = query($sql, $params);
        $buyCount = $result->fetchColumn();
        
        if ($buyCount >= MAXBUYS) {
            throw new MaxBuysExceededException('Call to buyLead() failed. Lead has been purchaed 
                maximum number of times.');
        }
        
        // Try to deduct balance from user.
        if ($_SESSION['user']->deductBalance($this->cost)) {
            // Update user's bought leads.
            $sql = 'INSERT boughtlead(user, lead) VALUES(:user, :lead);';
            $params = array(
                ':user'     => $_SESSION['user']->getUser(),
                ':lead'     => $this->lead
            );
            
            // Update lead's 'bought' quantity.
            if (query($sql, $params)) {
                $this->bought++;
                $sql = 'UPDATE lead SET bought = :bought WHERE lead = :lead;';
                $params = array(
                    ':bought'   =>  $this->bought,
                    ':lead'     =>  $this->lead
                );
                
                return query($sql, $params);
            }
        }        
    }
    
    /**
     * Hides lead from user.
     * 
     * @return boolean true if hide DB update is successful.
     */
    public function hideLead() {
        // Update user's hidden leads.
        $sql = 'INSERT hidelead(user, lead) VALUES(:user, :lead);';
        $params = array(
            ':user' => $_SESSION['user']->getUser(),
            ':lead' => $this->lead
        );
        
        return query($sql, $params);
    }    
}

?>
