<?php
#done with post and json norm
require_once "validate.php";
header('Content-Type: application/json');

function findPlaceById($inputStr, $conn)
{
    $q = "SELECT * FROM places WHERE id=?;";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("i", $inputStr);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = "";
    while ($row = $result->fetch_assoc()) {
        $res = $row["name"];
    }
    return $res;
}

function findResponsibleById($inputStr, $conn)
{
    $q = "SELECT * FROM staff WHERE id=?;";
    $stmt = $conn->prepare($q);
    $stmt->bind_param("i", $inputStr);
    $stmt->execute();
    $result = $stmt->get_result();
    $res = "";
    while ($row = $result->fetch_assoc()) {
        $res = $row["name"];
    }
    return $res;
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
$answer->responsible_id = "";
$answer->place = "";
$answer->place_id = "";
$answer->date = "";
$answer->month_expired = 0;

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
        $answer->init_cost = $row["init_cost"];
        $answer->name = $row["name"];
        $answer->arom_price = $row["arom_price"];
        $answer->responsible = findResponsibleById($row["responsible"], $conn);
        $answer->responsible_id = (string) $row["responsible"];
        $answer->place = findPlaceById($row["place"], $conn);
        $answer->place_id = (string) $row["place"];
        $answer->date = $row["date"];
        $answer->month_expired = $row["month_expired"];
    }
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    $answer->error = "Not found";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
