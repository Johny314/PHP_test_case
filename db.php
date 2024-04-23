<?php
$db_host = 'localhost';
$db_user = 'root';
$db_password = 'root';
$db_db = 'guest_book';

$mysqli = @new mysqli(
    $db_host,
    $db_user,
    $db_password,
    $db_db
);

if ($mysqli->connect_error) {
    $error_message = 'Errno: ' . $mysqli->connect_errno . PHP_EOL;
    $error_message .= 'Error: ' . $mysqli->connect_error . PHP_EOL;
    $error_message .= 'Time: ' . date('Y-m-d H:i:s') . PHP_EOL;

    file_put_contents('log/db_error_log.txt', $error_message, FILE_APPEND);

    exit();
}

//echo 'Success: A proper connection to MySQL was made.';
//echo '<br>';
//echo 'Host information: '.$mysqli->host_info;
//echo '<br>';
//echo 'Protocol version: '.$mysqli->protocol_version;

//$mysqli->close();