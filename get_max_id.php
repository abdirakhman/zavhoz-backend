<?php
#done with post and json norm
require_once "validate.php";

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$checker = json_decode(check_jwt());

$answer->error = "no error";

if ($checker->error != "no error") {
    $answer->error = $checker->error;
    $answer->id = -1;
    die(json_encode($answer));
}

$dbname = $checker->token->db;

$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    $answer->id = 0;
    die(json_encode($answer));
}
$q = "SELECT max(id) FROM furniture";

$result = $conn->query($q);
$answer->error = "no error";
$row = $result->fetch_assoc();
if ($row["max(id)"]) {
    $answer->id = (int) $row["max(id)"];
} else {
    $answer->id = 0;
}
die(json_encode($answer));

$conn->close();
