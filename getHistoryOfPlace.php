<?php
require_once "validate.php";
header('Content-Type: application/json');

function transform($inputString, $conn)
{
    # $tmp .= PHP_EOL . date('Y/m/d') . " - New responsibility for Item " . $ID;
    $arr = explode(PHP_EOL, $inputString);
    $newString = array();
    foreach ($arr as $str) {
        $tmp = $str;
        $strArray = explode(' ', $tmp);
        $lastElement = array_pop($strArray);
        $tmpArray = array($lastElement, $str);
        array_push($newString, $tmpArray);
    }
    return $newString;
}

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$checker = json_decode(check_jwt());

$answer->error = "no error";
$answer->return_array = array();

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

$q = "SELECT * FROM places where id=?";

$stmt = $conn->prepare($q);
$stmt->bind_param("i", $ID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $answer->return_array = transform($row["history"], $conn);
    }
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    $answer->error = "Not found";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
