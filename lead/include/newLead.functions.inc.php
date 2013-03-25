<?php
/**
 * @file newLead.functions.inc.php
 * @brief Helper functions for New Lead page.
 * @author Alfredo Ramirez
 * @date 03/21/2013
 */

/**
 * Displays all leads available to logged in user as a table with option to buy/hide selected leads.
 * 
 * @return string html with leads display table.
 */
function displayLeadsTable() {
    // Get all leads for logged in user.
    $leads = Lead::getLeadsForUser();
    
    // Set up HTML header.
    $html = '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">
                <table>
                    <thead>
                        <th>Select</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Zipcode</th>
                        <th>Price</th>
                        <th>Bought</th>
                    </thead>
                    <tbody>';
    
    // Display each lead.
    foreach ($leads as $lead) {
        $html .= '<tr>';
        $html .= '<td>
            <input type="checkbox" name="selectedLead[]" value="' . $lead->getLead() . '" />
                </td>';
        $html .= '<td>' . $lead->getDate() . '</td>';
        $html .= '<td>' . $lead->getDescription() . '</td>';
        $html .= '<td>' . $lead->getZipcode() . '</td>';
        $html .= '<td>' . $lead->getCost() . '</td>';
        $html .= '<td>' . $lead->getBought() . '</td>';
        $html .= '</tr>';
    }
    
    // Finish Table.
    $html .= '</tbody></table>';
            
    // Finish form.
    $html .= '<input type="submit" name="buyLead" value="Buy Lead(s)" />
        <input type="submit" name="hideLead" value="Hide Leads(s)" /></form>';    
    
    return $html;
}

/**
 * Loads leads into Session.
 * 
 * @param array $leadArray is initialized with integers.
 * @param string $sessionArray is initialized. 
 */
function leadToSession($leadArray, $sessionArray) {
    // Clear session variable first.
    unset($_SESSION[$sessionArray]);
    
    $index = 0;
    
    // Fetch each lead in $leadArray and store it in the Session.
    foreach ($leadArray as $lead) {
        $_SESSION[$sessionArray][$index++] = Lead::getLeadObj($lead);
    }
}

/**
 * Check whether user has enough balance to buy selected leads.
 * 
 * @return boolean true if user's balance is greater than or equal to total cost of leads.
 */
function confirmCanBuy() {
    $totalCost = 0;
    
    foreach($_SESSION['buyLead'] as $lead) {
        $totalCost += $lead->getCost();
    }
    
    if ($totalCost > $_SESSION['user']->getBalance()) {
        return false;
    } else {
        return true;
    }   
}

/**
 * Prints a list of leads to confirm.
 * 
 * @param string $sessionArray is initialized.
 * @return string list of leads to confirm.
 */
function confirmLeadList($sessionArray) {
    $html = '<ul>';
    foreach ($_SESSION[$sessionArray] as $lead) {
        $html .= '<li>';
        $html .= $lead->getDate() . ', ' . $lead->getDescription() . ', ' . $lead->getZipcode();
        $html .= '</li>';
    }
    
    $html .= '</ul>';
    
    return $html;
}

/**
 * Attempts to purchase all confirmed leads.
 * 
 * @return boolean true if all leads successfully bought.
 */
function buyConfirmedLead() {
    foreach ($_SESSION['buyLead'] as $lead) {
        $lead->buyLead();
    }
    
    return true;
}

/**
 * Attempts to hide all confirmed leads.
 * 
 * @return boolean true if all leads successfully hidden.
 */
function hideConfirmedLead() {
    foreach($_SESSION['hideLead'] as $lead) {
        $lead->hideLead();
    }
    
    return true;
}
?>
