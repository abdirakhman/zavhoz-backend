<?php
#done with post and json norm
require_once("validate.php");
header('Content-Type: application/json');


$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";


$checker = json_decode(check_jwt());

$answer->error = "no error";
$answer->return_array = array();

if ($checker->error != "no error") {
  $answer->error =$checker->error;
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

$PLACE = stripslashes(htmlspecialchars($_POST['place']));
$RESPONSIBLE = stripslashes(htmlspecialchars($_POST['responsible']));

$q = "";


if ($PLACE == "" && $RESPONSIBLE == "") {
  $q = "SELECT * from furniture";
  global $stmt;
  $stmt = $conn->prepare($q);
} else if ($PLACE == "") {
  $q = "SELECT id from furniture WHERE responsible=?";
  global $stmt;
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $RESPONSIBLE);
} else {
  $q = "SELECT id from furniture WHERE place=?";
  global $stmt;
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $PLACE);
}

$stmt->execute();
$result = $stmt->get_result();

while($row = $result->fetch_assoc()) {
  $kek['id'] = (string)$row["id"];
  $kek['name'] = $row["name"];
  array_push($answer->return_array, $kek);
}
die(json_encode($answer, JSON_UNESCAPED_UNICODE));


$conn->close();
?>
