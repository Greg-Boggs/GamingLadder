<?php
require '../conf/variables.php';
// If you have the download key, you can download the files without asking any questions.
if (isset($_GET['backupkey']) && $_GET['backupkey'] == $backupKey) {

    // Send the headers to download the file
    header('Content-type: plain/text');
    header('Content-Disposition: attachment; filename="ladder-backup.sql"');

    $tableResult = mysqli_query($db, "SHOW TABLES");

    while ($tableRow = mysqli_fetch_row($tableResult)) {
        $rowsResult = mysqli_query($db, "SELECT * FROM " . $tableRow[0]);

        while ($row = mysqli_fetch_row($rowsResult)) {
            echo "INSERT INTO `" . $tableRow[0] . "` VALUES (";
            for ($i = 0; $i < mysqli_num_fields($rowsResult); ++$i) {
                echo "'" . mysqli_real_escape_string($db, $row[$i]) . "'";
                if ($i + 1 < mysqli_num_fields($rowsResult)) {
                    echo ",";
                }
            }
            echo ");\n";
        }
        echo "\n\n";
    }
    exit;
}

// We aren't actually doing the backup, so generate the GUI page that an admin will see.A
session_start();

$GLOBALS['prefix'] = "../";
require 'security.inc.php';
require '../top.php';
?>
    <h1>Backup the database content</h1>
    <p>You can backup the database by clicking the following link.</p>
    <p>Note: This only backs up the content of the ladder tables, it does not backup the table structure. If you are
        restoring you will need a blank database in place to be able to restore the created file into. The backup should
        be applied as part of the installation process if you are restoring from backup.</p>
    <a href="backup-db.php?backupkey=<?php echo urlencode($backupKey) ?>">Backup</a>
    <p>You can also use the link location to download the file using a cron task on your computer. A backup key is used
        to allow you to download, you do not need to be logged into the administrative interface to download a backup.
        All you need to know is the key.</p>
<?php
require '../bottom.php';
