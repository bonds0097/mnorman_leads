<?php
/**
 * @file buyHistory.php
 * @author Alfredo Ramirez
 * @date 03/21/2013
 * @brief Purchase History page, allows user to see details of leads they've purchased.
 */
// Include PHP Header.
require_once('php.header.inc.php');

// Include new Lead functions.
require_once('./include/boughtLead.functions.inc.php');

// Set initial variables.
$pageTitle = 'Lead Purchase History';
$display = 0;

// This page has 1 displays.
// $display == 0: List bought leads.

// Include HTML Header.
require_once('html.header.inc.php');

if ($display == 0) { ?>
    <h1>Bought Leads List</h1>
    <!-- Print out the Bought Leads Table -->
    <?php echo displayBoughtLeadsTable() ?>
<?php } ?>
<?php require_once('html.footer.inc.php');?>