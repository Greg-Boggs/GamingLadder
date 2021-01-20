<?php
session_start();
$GLOBALS['prefix'] = "../";
require './../conf/variables.php';
require_once 'security.inc.php';
require './../top.php';

?>
<p class="header">Administration Options</p>
<p>You can complete all the functions that alter the administration interface or manage the database backup from this
    screen.</p>
<ul>
    <li><a href="backup-db.php">Backup the Database Content</a></li>
    <li><a href="addadmin.php">Maintain Administrative Users</a></li>
</ul>
<?php require '../bottom.php'; ?>
