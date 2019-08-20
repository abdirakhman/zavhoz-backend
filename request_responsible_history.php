<?php
#done with post and json norm
require_once("validate.php");
header('Content-Type: application/json');

// changing to normal language responsible
function kek_responsible($String_to_be_changed, $conn) {
  $ARRAY = explode(PHP_EOL, $String_to_be_changed);
  $newstroka = "";
  foreach ($ARRAY as $stroka) {
    $tmpString = "";
    $uakitwa = $stroka;
    $strArray = explode(' ', $uakitwa);
    $lastElement = array_pop($strArray);
    $q = "SELECT * FROM staff WHERE id=?;";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("i", $lastElement);
    $stmt->execute();
    $result = $stmt->get_result();
    $NAMAE = "";
    while($row = $result->fetch_assoc()) {
        $NAMAE = $row["name"];
    }
    foreach ($strArray as $i) {
      $tmpString .= $i . ' ';
    }
    $tmpString .= $NAMAE;
    $newstroka .= $tmpString . PHP_EOL;
  }
  return $newstroka;
}



$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$checker = json_decode(check_jwt());


$answer->error = "no error";
$answer->responsible_history = "";

if ($checker->error != "no error") {
  $answer->error = $checker->error;
  die(json_encode($answer));
}

$dbname = $checker->token->db;

$ID = stripslashes(htmlspecialchars($_POST['id']));

$conn = new mysqli($servername, $username, $password, $dbname);


$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}

if ($ID == "") {
  $answer->error = "No id specified";
  die(json_encode($answer));
}

$q = "SELECT * FROM furniture where id=?";

$stmt = $conn->prepare($q);
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
        $answer->responsible_history = kek_responsible($row["responsible_history"], $conn);
  }
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
  $answer->error="Not found";
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
?>
