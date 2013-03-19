<?php
/**
 * @file html.header.php
 * @brief HTML Header for site.
 * @author Alfredo Ramirez
 * @date 03/16/2013
 */
?>
<html>
    <head><title>Michael Norman Leads App - <?php echo $pageTitle ?></title></head>
    <body>
        <?php if(basename($_SERVER['PHP_SELF']) != 'index.php') { ?>
        <nav>
            <p>You are logged in as <b><?php echo $_SESSION['user']->getUsername() ?></b></p>
            <ul>
                <?php if($_SESSION['user']->getAdmin()) { ?>
                <li><a href="<?php echo ROOTURI ?>/user/admin.php">Admin Page</a></li>
                <?php } else { ?>                
                <li><a href="<?php echo ROOTURI ?>/user/password.php">Change Password</a></li>
                <?php } ?>
                <li><a href="<?php echo ROOTURI ?>/lead/newLead.php">New Lead</a></li>
                <li><a href="<?php echo ROOTURI ?>/lead/buyHistory.php">Lead Purchase History</a></li>
                <li><a href="<?php echo ROOTURI ?>/index.php?action=logout">Logout</a></li>
            </ul>        
        </nav>
        <?php } ?>
        <div id="pageContent">
            <?php
            
            // Format and display messages.
            $messages = formatMessages();
            echo (isset($messages)) ? $messages : '';
            ?>