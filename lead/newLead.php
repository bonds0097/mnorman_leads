<?php
/**
 * @file newLead.php
 * @author Alfredo Ramirez
 * @date 03/21/2013
 * @brief New Leads page, allows users to view, hide or buy leads.
 */
// Include PHP Header.
require_once('php.header.inc.php');

// Include new Lead functions.
require_once('./include/newLead.functions.inc.php');

// Set initial variables.
$pageTitle = 'New Lead';
$display = 0;

// This page has 4 displays.
// $display == 0: List leads.
// $display == 1: Hide leads confirmation.
// $display == 2: Buy leads confirmation.
// $display == 3: Message page.

// Check if hideLead or buyLead requests were sent and display confirmations.
if (isset($_POST['hideLead'])) {
    // Add leads to hide into session.
    leadToSession($_POST['selectedLead'], 'hideLead');
    
    $display = 1;
} else if (isset($_POST['buyLead'])) {
    // Add leads to buy into session.
    leadToSession($_POST['selectedLead'], 'buyLead');
    
    // Confirm the user can afford all the leads.
    if (confirmCanBuy()) {
        $display = 2;
    } else {
        throw new InsufficientBalanceException('User cannot buy selected leads: insufficient 
            balance.');
    }
}

// Check if hide/buy lead confirmation was sent and take appropriate action.
if (isset($_POST['hideLeadConfirmed'])) {
    if (hideConfirmedLead()) {
        message('success', 'Confirmed leads successfully hidden.');
        $display = 3;
    }
   
} else if (isset($_POST['buyLeadConfirmed'])) {
    if (buyConfirmedLead()) {
        message('success', 'Confirmed leads successfully purchased.');
        $display = 3;
    }
}

// Include HTML Header.
require_once('html.header.inc.php');

if ($display == 0) { ?>
    <h1>Leads List</h1>
    <!-- Print out the Leads Table -->
    <?php echo displayLeadsTable() ?>
<?php } else if ($display == 1) { ?>
    <h1>Hide Leads Confirmation</h1>
    <p>Please confirm that you want to hide the following leads:</p>
    <!-- Print out the Leads List -->
    <?php echo confirmLeadList('hideLead') ?>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <input name="hideLeadConfirmed" type="submit" value="Confirm"/>
    </form>
<?php } else if ($display == 2) { ?>
    <h1>Buy Leads Confirmation</h1>
    <p>Please confirm that you want to buy the following leads:</p>
    <!-- Print out the Leads List -->
    <?php echo confirmLeadList('buyLead') ?>
    <form action="<?php echo $_SERVER['PHP_SELF']?>" method="post">
        <input name="buyLeadConfirmed" type="submit" value="Confirm"/>
    </form>
<?php } else if ($display == 3) { ?>
    <a href="<?php echo $_SERVER['PHP_SELF'] ?>">Return to New Lead Page.</a>
<?php } ?>
<?php require_once('html.footer.inc.php');?>