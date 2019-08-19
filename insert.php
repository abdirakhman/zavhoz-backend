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


$INIT_COST = stripslashes(htmlspecialchars($_POST['init_cost']));
$NAME = stripslashes(htmlspecialchars($_POST['name']));
$AROM_PRICE = stripslashes(htmlspecialchars($_POST['arom_price']));
$RESPONSIBLE = stripslashes(htmlspecialchars($_POST['responsible']));
$PLACE = stripslashes(htmlspecialchars($_POST['place']));
$DATE = stripslashes(htmlspecialchars($_POST['date']));
$MONTH_EXPIRED = stripslashes(htmlspecialchars($_POST['month_expired']));


if ($INIT_COST == "" || $NAME == "" || $AROM_PRICE == "" ||
 $RESPONSIBLE == "" || $PLACE == "" || $DATE == "" || $MONTH_EXPIRED == "") {
  $answer->error = "Something not specified";
  die(json_encode($answer));
}

$tmppl = date('Y/m/d') . " - Changed place for " . $PLACE .  "\r\n";
$tmphis = date('Y/m/d') .  " - Item changed responsible for " . $RESPONSIBLE . "\r\n";

$q = "INSERT INTO furniture (init_cost, arom_price, responsible, place, date, name, month_expired, place_history, responsible_history) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($q);

if ($stmt != false) {
  $stmt->bind_param("iiiississ", $INIT_COST, $AROM_PRICE, $RESPONSIBLE, $PLACE, $DATE, $NAME, $MONTH_EXPIRED, $tmppl, $tmphis);
  $stmt->execute();
  die(json_encode($answer));
} else {
  $answer->error = "error";
  die(json_encode($answer));
}
$conn->close();
?>
