<?php

// Check if the user is logged in using the session, if not display the login screen for them and quit.
if (!isset($_SESSION['real-username']) || !isset($_SESSION['admin-login'])) {
    include 'index.php';
    exit;
}
