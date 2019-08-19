<?php
#done with post and json norm
require_once("validate.php");
header('Content-Type: application/json');

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

$ID = stripslashes(htmlspecialchars($_POST['id']));
$INIT_COST = stripslashes(htmlspecialchars($_POST['init_cost']));
$NAME = stripslashes(htmlspecialchars($_POST['name']));
$AROM_PRICE = stripslashes(htmlspecialchars($_POST['arom_price']));
$RESPONSIBLE = stripslashes(htmlspecialchars($_POST['responsible']));
$PLACE = stripslashes(htmlspecialchars($_POST['place']));
$DATE = stripslashes(htmlspecialchars($_POST['date']));
$MONTH_EXPIRED = stripslashes(htmlspecialchars($_POST['month_expired']));
// echo $ID;
// echo $INIT_COST;
// echo $NAME;
// echo $RESPONSIBLE;
// echo $PLACE;
// echo $DATE;
// echo $MONTH_EXPIRED;

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}

if ($ID == "" || $INIT_COST == "" || $NAME == "" || $AROM_PRICE == "" ||
 $RESPONSIBLE == "" || $PLACE == "" || $DATE == "" || $MONTH_EXPIRED == "") {
  $answer->error = "Something not specified";
  die(json_encode($answer));
}

$q = "SELECT * FROM furniture where id=?";

$stmt = $conn->prepare($q);
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();

$history_place = "";
$history_responsible = "";
$old_place = "";
$old_responsible = "";

while($row = $result->fetch_assoc()) {
  $history_place = $row["place_history"];
  $history_responsible = $row["responsible_history"];
  $old_place = $row["place"];
  $old_responsible = $row["responsible"];
}

if ($old_place != $PLACE) {
  //
  $history_place = $history_place . date('Y/m/d') . " - Changed place for " . $PLACE .  "\r\n";

  //
  $q = "SELECT * FROM places where id=?;";
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $PLACE);
  $stmt->execute();
  $result = $stmt->get_result();
  $tmp = "";
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $tmp = $row["history"];
    }
    $tmp = $tmp . date('Y/m/d') . " - Item " . $ID . " was introduced" . "\r\n";
    $q = "UPDATE places SET history='$tmp' where id=$PLACE;";
    $stmt = $conn->prepare($q);

    if ($stmt == false) {
      $answer->error = "error";
      die(json_encode($answer));
    }
    $stmt->bind_param("si", $tmp, $PLACE);
    $stmt->execute();
  } else {
    $answer->error="error";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
  }
  //

  $q = "SELECT * FROM places where id=?;";
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $old_place);
  $stmt->execute();
  $result = $stmt->get_result();

  $tmp = "";
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $tmp = $row["history"];
    }
    $tmp = $tmp . date('Y/m/d') . " - Item " . $ID . " was removed" . "\r\n";
    $q = "UPDATE places SET history=? where id=?;";
    $stmt = $conn->prepare($q);
    if ($stmt == false) {
      $answer->error = "error";
      die(json_encode($answer));
    }
    $stmt->bind_param("si", $tmp, $old_place);
    $stmt->execute();
  } else {
    $answer->error="Not found";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
  }


}

if ($old_responsible != $RESPONSIBLE) {

  $history_responsible = $history_responsible .  date('Y/m/d') .  " - Item changed responsible for " . $RESPONSIBLE . "\r\n";

  //

  //
  $q = "SELECT * FROM staff where id=?;";
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $RESPONSIBLE);
  $stmt->execute();
  $result = $stmt->get_result();
  $tmp = "";
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $tmp = $row["history"];
    }
    $tmp = $tmp . date('Y/m/d') . " - Item " . $ID . " under the responsibility" . "\r\n";
    $q = "UPDATE staff SET history=? where id=?;";
    $stmt = $conn->prepare($q);

    if ($stmt == false) {
      $answer->error = "error";
      die(json_encode($answer));
    }
    $stmt->bind_param("si", $tmp, $RESPONSIBLE);
    $stmt->execute();
  } else {
    $answer->error="error";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
  }

  $q = "SELECT * FROM staff where id=?;";
  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $old_responsible);
  $stmt->execute();
  $result = $stmt->get_result();

  $tmp = "";

  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $tmp = $row["history"];
    }
    $tmp = $tmp .  date('Y/m/d') . " - Item " . $ID . " was removed from responsibility" . "\r\n";
    $q = "UPDATE staff SET history=? where id=?;";
    $stmt = $conn->prepare($q);

    if ($stmt == false) {
      $answer->error = "error";
      die(json_encode($answer));
    }
    $stmt->bind_param("si", $tmp, $old_responsible);
    $stmt->execute();
  } else {
    $answer->error="Not found";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
  }

}



$q = "UPDATE furniture SET init_cost=?, name=?, arom_price=?, responsible=?,date=?,month_expired=?,place=?,place_history=?,responsible_history=? WHERE id=$ID;";
$stmt = $conn->prepare($q);

if ($stmt != false) {
  $stmt->bind_param("isiisiiss", $INIT_COST, $NAME, $AROM_PRICE, $RESPONSIBLE, $DATE, $MONTH_EXPIRED, $PLACE, $history_place, $history_responsible);
  $stmt->execute();
  die(json_encode($answer));
} else {
  $answer->error = "error";
  die(json_encode($answer));
}
$conn->close();
?>
