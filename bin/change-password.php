<?php

require('./../conf/variables.php');

if(count($argv) != 3){
    echo "Invalid arguments\n";
    echo "change-password.php <playerName> <pass>";
    die();
}

$newpassworddb = $salt . $argv[2];
$newpassworddb = md5($newpassworddb);
$newpassworddb = md5($newpassworddb);


$sql = sprintf(
    "SELECT player_id, passworddb FROM $playerstable WHERE name = '%s'",
    $argv[1]
);
$result = mysqli_query($db, $sql);
$row = mysqli_fetch_array($result);
if(empty($row)){
    printf("Invalid player '%s'\n", $argv[1]);
    die();
}
printf(
    "Player '%s', id %s, password signature : '%s'\n",
    $argv[1],
    $row['player_id'],
    $row['passworddb']
);
$result = mysqli_query($db, sprintf(
    "UPDATE $playerstable set passworddb='%s' WHERE player_id='%s'",
    $newpassworddb,
    $row['player_id']
));
