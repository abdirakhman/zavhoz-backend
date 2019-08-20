<?php
require_once("validate.php");

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$checker = json_decode(check_jwt());

$answer->error = "no error";


if ($checker->error != "no error") {
  $answer->error = $checker->error;
  die(json_encode($answer));
}

if ($checker->token->type != 1) {
  $answer->error = "Not enough privilege";
  die(json_encode($answer));
}
$dbname = $checker->token->db;

$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}

$NAME = stripslashes(htmlspecialchars($_POST['name']));
$IIN = stripslashes(htmlspecialchars($_POST['iin']));

if ($NAME == "" || $IIN == "") {
  $answer->error = "Something not specified";
  die(json_encode($answer));
}

$tmp = "";

$q = "INSERT INTO staff (id, name, iin, history) VALUES (NULL, ?, ?, ?)";

$stmt = $conn->prepare($q);

if ($stmt != false) {
  $stmt->bind_param("sis", $NAME, $IIN, $tmp);
  $stmt->execute();
  die(json_encode($answer));
} else {
  $answer->error = "error";
  die(json_encode($answer));
}
$conn->close();
?>
