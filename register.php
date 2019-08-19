<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
$dbname = "other";

$conn = new mysqli($servername, $username, $password, $dbname);

$answer->error = "no error";

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}


$LOGIN = stripslashes(htmlspecialchars($_POST['login']));
$PASS = stripslashes(htmlspecialchars($_POST['pass']));
$NAME = stripslashes(htmlspecialchars($_POST['name']));
$PLACE = stripslashes(htmlspecialchars($_POST['place']));
$TYPE = stripslashes(htmlspecialchars($_POST['type']));
$CODE = stripslashes(htmlspecialchars($_POST['code']));


#echo $LOGIN;
#echo $PASS;
#echo $NAME;
#echo $PLACE;
#echo $TYPE;
#echo $CODE;



if ($LOGIN == "" || $PASS == "" || $NAME == "" || $PLACE == "" || $TYPE == "" || $CODE == "") {
  $answer->error = "Something not specified";
  die(json_encode($answer));
}

$q = "SELECT * FROM login WHERE code=?;";

$stmt = $conn->prepare($q);
$stmt->bind_param("s", $CODE);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {

    if (!filter_var($LOGIN, FILTER_VALIDATE_EMAIL)) {
      $answer->error="Invalid email";
      die(json_encode($answer, JSON_UNESCAPED_UNICODE));
    }
    $options = [
      'cost' => 11
    ];
    $PASS = password_hash($PASS, PASSWORD_BCRYPT, $options);
    $q = "UPDATE login SET email=?, name=?, password=?, place=?, type=? WHERE code=?;";
    $stmt = $conn->prepare($q);
    if ($stmt != false) {
      $stmt->bind_param("sssiis", $LOGIN, $NAME, $PASS, $PLACE, $TYPE, $CODE);
      $stmt->execute();
      die(json_encode($answer));
    } else {
      $answer->error = "error";
      die(json_encode($answer));
    }
  }
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
  $answer->error="Not found";
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
?>
