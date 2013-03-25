<?php
/**
 * @file boughtLead.functions.inc.php
 * @brief Helper functions for Bought Lead page.
 * @author Alfredo Ramirez
 * @date 03/24/2013
 */

/**
 * Displays all leads purchased by logged in user.
 * 
 * @return string $html that contains table of purchased leads.
 */
function displayBoughtLeadsTable() {
    // Get all leads for logged in user.
    $leads = Lead::getBoughtLeadsForUser();
    
    // Set up HTML header.
    $html = '<table>
                <thead>
                    <th>Purchase Date</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Zipcode</th>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>E-Mail</th>
                    <th>Bought</th>
                </thead>
                <tbody>';
    
    // Display each lead.
    foreach ($leads as $lead) {
        $html .= '<tr>';
        $html .= '<td>' . $lead->getBuyDate() . '</td>';
        $html .= '<td>' . $lead->getDate() . '</td>';
        $html .= '<td>' . $lead->getDescription() . '</td>';
        $html .= '<td>' . $lead->getZipcode() . '</td>';
        $html .= '<td>' . $lead->getName() . '</td>';
        $html .= '<td>' . $lead->getPhone() . '</td>';
        $html .= '<td>' . $lead->getEmail() . '</td>';
        $html .= '<td>' . $lead->getBought() . '</td>';
        $html .= '</tr>';
    }
    
    // Finish Table.
    $html .= '</tbody></table>';
    
    return $html;
}


?>
