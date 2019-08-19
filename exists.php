<?php
#done with post and json norm

require_once("validate.php");

header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$answer->error = "no error";

$checker = json_decode(check_jwt());

if ($checker->error != "no error") {
  $answer->error = $checker->error;
  $answer->found= "-1";
  die(json_encode($answer));
}

$dbname = $checker->token->db;


$ID = stripslashes(htmlspecialchars($_POST['id']));

$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    $answer->found= "-1";
    die(json_encode($answer));
}

if ($ID == "") {
  $answer->error = "No id specified";
  $answer->found="-1";
  die(json_encode($answer));
}

$q = "SELECT * FROM furniture where id=?";

$stmt = $conn->prepare($q);
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $answer->error="no error";
  $answer->found="1";
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
  $answer->error="no error";
  $answer->found="0";
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
?>
