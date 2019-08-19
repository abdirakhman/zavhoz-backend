<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
$dbname = "other";

$whitelist = array(
    '127.0.0.1',
    '::1'
);

if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
    die("horowaya popytka");
}

$FIRST = stripslashes(htmlspecialchars($_POST['first']));

$conn = new mysqli($servername, $username, $password, $dbname);


$answer->error = "no error";

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}

if ($FIRST == "") {
  $answer->error = "No first specified";
  die(json_encode($answer));
}

$q = "SELECT * FROM constants where first=?";
$stmt = $conn->prepare($q);
$stmt->bind_param("i", $FIRST);
$stmt->execute();
#$result = $conn->query($q);

$result = $stmt->get_result();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
        $answer->second = $row["second"];
  }
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
  $answer->error="Not found";
  die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
?>
