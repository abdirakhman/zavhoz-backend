<?php
#done with post and json norm
require_once("validate.php");
header('Content-Type: application/json');

// changing to normal language responsible
function kek_responsible($String_to_be_changed, $conn) {
  $ARRAY = explode(PHP_EOL, $String_to_be_changed);
var_dump($String_to_be_changed);
  $newstroka = array();
  var_dump($ARRAY);
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
    array_push($newstroka, $tmpString);
  }
  return $newstroka;
}

// changing to normal language place
function kek_place($String_to_be_changed, $conn) {
  $ARRAY = explode(PHP_EOL, $String_to_be_changed);
  $newstroka = array();
  foreach ($ARRAY as $stroka) {
    $tmp = "";
    $uakitwa = $stroka;
    $strArray = explode(' ', $uakitwa);
    $lastElement = array_pop($strArray);
    $q = "SELECT * FROM places WHERE id=?;";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("i", $lastElement);
    $stmt->execute();
    $result = $stmt->get_result();
    $NAMAE = "";
    while($row = $result->fetch_assoc()) {
        $NAMAE = $row["name"];
    }
    foreach ($strArray as $i) {
      $tmp .= $i . ' ';
    }
    $tmp .= $NAMAE;
    array_push($newstroka, $tmp);
  }
  return $newstroka;
}

function make_normal_place($String_to_be_changed, $conn) {
  $q = "SELECT * FROM places WHERE id=?;";
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $String_to_be_changed);
  $stmt->execute();
  $result = $stmt->get_result();
  $NAMAE = "";
  while($row = $result->fetch_assoc()) {
      $NAMAE = $row["name"];
  }
  return $NAMAE;
}

function make_normal_responsible($String_to_be_changed, $conn) {
  $q = "SELECT * FROM staff WHERE id=?;";
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $String_to_be_changed);
  $stmt->execute();
  $result = $stmt->get_result();
  $NAMAE = "";
  while($row = $result->fetch_assoc()) {
      $NAMAE = $row["name"];
  }
  return $NAMAE;
}


$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$checker = json_decode(check_jwt());


$answer->error = "no error";
$answer->init_cost = 0;
$answer->name = "";
$answer->arom_price = 0;
$answer->responsible = "";
$answer->place = "";
$answer->date = "";
$answer->place_history = array();
$answer->responsible_history = array();
$answer->month_expired = 0;

$tmp_responsible_history = "";
$tmp_place_history = "";

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
        $answer->init_cost = $row["init_cost"];
        $answer->name = $row["name"];
        $answer->arom_price = $row["arom_price"];
        $answer->responsible = make_normal_responsible($row["responsible"], $conn);
        $answer->place = make_normal_place($row["place"], $conn);
        $answer->date = $row["date"];
        $tmp_place_history = $row["place_history"];
        $tmp_responsible_history = $row["responsible_history"];
        $answer->month_expired = $row["month_expired"];
  }
  $answer->responsible_history = kek_responsible($tmp_responsible_history, $conn);
  $answer->place_history = kek_place($tmp_place_history, $conn);
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
  $answer->error="Not found";
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
?>
