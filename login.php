<?php
#done with post and json norm
header('Content-Type: application/json');

require_once 'constants.php';
require_once 'JWT.php';
$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
$dbname = "other";

$conn = new mysqli($servername, $username, $password, $dbname);

$answer->error = "no error";
$answer->access_token = "0";
$answer->refresh_token = "0";
$answer->good_before = 0;
$answer->success = 0;

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    $answer->access_token = "0";
    $answer->refresh_token = "0";
    $answer->good_before = 0;
    $answer->success = 0;
    die(json_encode($answer));
}

$LOGIN = stripslashes(htmlspecialchars($_POST['login']));
$PASS = stripslashes(htmlspecialchars($_POST['pass']));

#$LOGIN = stripslashes(htmlspecialchars($argv[1]));
#$PASS = stripslashes(htmlspecialchars($argv[2]));

if ($LOGIN == "" || $PASS == "") {
    $answer->success = 0;
    $answer->error = "Something not specified";
    $answer->access_token = "0";
    $answer->refresh_token = "0";
    $answer->good_before = 0;
    die(json_encode($answer));
}

$q = "SELECT * FROM login WHERE email=?;";

$stmt = $conn->prepare($q);
$stmt->bind_param("s", $LOGIN);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $CHECK_PASS = $row["password"];

        if (password_verify($PASS, $CHECK_PASS)) {
            $answer->success = 1;
            $time = time();
            $token = array(
                "sub" => $row["id"],
                "email" => $LOGIN,
                "db" => $row["place"],
                "type" => $row["type"],
                "iat" => $time,
                "exp" => $time + 60 * 60 * 24 * 7 * 1,
                "kaisi" => "access",
            );

            $jwt = JWT::encode($token, SECRET_KEY);
            $answer->access_token = $jwt;
            $token = array(
                "sub" => $row["id"],
                "email" => $LOGIN,
                "db" => $row["place"],
                "type" => $row["type"],
                "iat" => $time,
                "exp" => $time + 60 * 60 * 24 * 7 * 2,
                "kaisi" => "refresh",
            );
            $jwt = JWT::encode($token, SECRET_KEY);
            $answer->refresh_token = $jwt;
            $answer->good_before = $time + 60 * 60 * 24 * 7 * 1;

            $q = "INSERT INTO tokens (id, token) VALUES(?, ?) ON DUPLICATE KEY UPDATE token=?;";
            $stmt = $conn->prepare($q);
            $stmt->bind_param("iss", $row["id"], $answer->refresh_token, $answer->refresh_token);
            $stmt->execute();
        } else {
            $answer->success = 0;
            $answer->error = "Wrong password";
            $answer->access_token = "0";
            $answer->refresh_token = "0";
            $answer->good_before = 0;
        }
    }
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    $answer->success = 0;
    $answer->error = "Incorrect email or password";
    $answer->access_token = "0";
    $answer->refresh_token = "0";
    $answer->good_before = 0;
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
