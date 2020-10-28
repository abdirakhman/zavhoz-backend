<?php
require_once "validate.php";

#database connection
$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
#check for login
$checker = json_decode(check_jwt());
#init answer
$answer->error = "no error";
$answer->return_array = array();
#check for login
if ($checker->error != "no error") {
    $answer->error = $checker->error;
    die(json_encode($answer));
}
#take db name from jwt
$dbname = $checker->token->db;
#init conn
$conn = new mysqli($servername, $username, $password, $dbname);
$conn->set_charset("utf8");
#check for connection error
if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    $answer->return_array = array();
    die(json_encode($answer));
}
#make MySQL query
$q = "SELECT * FROM places";
$result = $conn->query($q);
#take data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tmp["id"] = (int) $row["id"];
        $tmp["name"] = $row["name"];
        array_push($answer->return_array, $tmp);
        #$answer->return_array[(int)$row["id"]] = $row["name"];
        #echo($row["name"]);
    }
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
} else {
    die(json_encode($answer, JSON_UNESCAPED_UNICODE));
}
$conn->close();
