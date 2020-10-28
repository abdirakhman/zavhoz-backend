<?php
#done with post and json norm
require_once "validate.php";
header('Content-Type: application/json');

// changing to normal language place
function getPlaceHistory($inputStr, $conn)
{

    $arr = explode(PHP_EOL, $inputStr);
    $newStr = "";

    foreach ($arr as $str) {
        $tmp = "";
        $tmpStr = $str;
        $strArray = explode(' ', $tmpStr);
        $lastElement = array_pop($strArray);
        $q = "SELECT * FROM places WHERE id=?;";
        $stmt = $conn->prepare($q);
        $stmt->bind_param("i", $lastElement);
        $stmt->execute();
        $result = $stmt->get_result();
        $res = "";
        while ($row = $result->fetch_assoc()) {
            $res = $row["name"];
        }
        foreach ($strArray as $i) {
            $tmp .= $i . ' ';
        }
        $tmp .= $res;
        $newStr .= $tmp . PHP_EOL;
    }
    return $newStr;
}

function getResponsibleHistory($inputStr, $conn)
{
    $arr = explode(PHP_EOL, $inputStr);
    $newStr = "";
    foreach ($arr as $str) {
        $tmpString = "";
        $tmpStr = $str;
        $strArray = explode(' ', $tmpStr);
        $lastElement = array_pop($strArray);
        $q = "SELECT * FROM staff WHERE id=?;";
        $stmt = $conn->prepare($q);
        $stmt->bind_param("i", $lastElement);
        $stmt->execute();
        $result = $stmt->get_result();
        $res = "";
        while ($row = $result->fetch_assoc()) {
            $res = $row["name"];
        }
        foreach ($strArray as $i) {
            $tmpString .= $i . ' ';
        }
        $tmpString .= $res;
        $newStr .= $tmpString . PHP_EOL;
    }
    return $newStr;
}

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";

$checker = json_decode(check_jwt());

$answer->error = "no error";
$answer->place_history = "";
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
    while ($row = $result->fetch_assoc()) {
        $answer->place_history = getPlaceHistory($row["place_history"], $conn);
        $answer->responsible_history = getResponsibleHistory($row["responsible_history"], $conn);
    }
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    $answer->error = "Not found";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
