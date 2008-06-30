<?php
session_start();
$GLOBALS['prefix'] = "../";
require('./../conf/variables.php');
require_once 'security.inc.php';
require('./../top.php');

?>
<p class="header">User Options</p>
<p>You can complete all the required user administration tasks from here.  Some tasks are not part of the Admin interface as you are better served by impersonating a user and completing the task as them in the regular interface.</p>
<ul>
    <li><a href="user-impersonate.php">Impersonate User</a></li>
    <li><a href="user-block.php">Block and Unblock Users</a></li>
    <li><a href="user-unconfirmed.php">List Unconfirmed Players</a></li>
</ul>
<?php require('../bottom.php'); ?>
