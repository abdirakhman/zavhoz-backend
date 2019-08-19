<?php
#done with post and json norm
require_once('constants.php');
require_once('JWT.php');
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
$dbname = "other";

$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");

$answer->success=0;
$answer->access_token = "0";
$answer->refresh_token = "0";
$answer->good_before = 0;
$answer->error = "no error";


if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}

#$jwt = getBearerToken();
$jwt = stripslashes(htmlspecialchars($_POST['token']));
if ($jwt == "") {
  $answer->success=0;
  $answer->error = "Something not specified";
  $answer->access_token = "0";
  $answer->refresh_token = "0";
  $answer->good_before = 0;
  die(json_encode($answer));
}
#$jwt = $argv[1];
try {
  $decoded = JWT::decode($jwt, SECRET_KEY, array('HS256'));
  $decoded_array = (array) $decoded;
  $q = "SELECT * FROM tokens where id=?";

  $stmt = $conn->prepare($q);
  $stmt->bind_param("i", $decoded_array["sub"]);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      if ($jwt == $row["token"]) {
        /// make new tokens
        $answer->success = 1;
        $time = time();
        $token = array(
            "sub" => $decoded_array["sub"],
            "email" => $decoded_array["email"],
            "db" => $decoded_array["db"],
            "type" => $decoded_array["type"],
            "iat" => $time,
            "exp" => $time + 60*60*24*7*1,
            "kaisi" => "access"
        );

        $jwt = JWT::encode($token, SECRET_KEY);
        $answer->access_token = $jwt;
        $token = array(
            "sub" => $decoded_array["sub"],
            "email" => $decoded_array["email"],
            "db" => $decoded_array["db"],
            "type" => $decoded_array["type"],
            "iat" => $time,
            "exp" => $time + 60*60*24*7*2,
            "kaisi" => "refresh"
        );
        $jwt = JWT::encode($token, SECRET_KEY);
        $answer->refresh_token = $jwt;
        $answer->good_before = $time + 60*60*24*7*1;

        $q = "INSERT INTO tokens (id, token) VALUES(?, ?) ON DUPLICATE KEY UPDATE token=?;";
        $stmt = $conn->prepare($q);
        $stmt->bind_param("iss", $row["id"], $answer->refresh_token, $answer->refresh_token);
        $stmt->execute();

        ///
      } else {
        $answer->success=0;
        $answer->access_token = "0";
        $answer->refresh_token = "0";
        $answer->good_before = 0;
        $answer->error="old token";
        die(json_encode($answer, JSON_UNESCAPED_UNICODE));
      }
    }
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
  } else {
    $answer->success=0;
    $answer->access_token = "0";
    $answer->refresh_token = "0";
    $answer->good_before = 0;
    $answer->error="token not found in db";
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
  }
} catch (Exception $e) {
  $answer->success=0;
  $answer->access_token = "0";
  $answer->refresh_token = "0";
  $answer->good_before = 0;
  $answer->error = $e->getMessage();
  die(json_encode($answer));
}
?>
