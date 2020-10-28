<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
$dbname = "other";
$conn = new mysqli($servername, $username, $password, $dbname);

$conn->set_charset("utf8");

function translit($str)
{
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}

function generateRandomString($length = 20)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$servername = "localhost";
$username = "root";
$password = "(S#,c}pQvr5XY8jE";
$dbname = "other";

$conn = new mysqli($servername, $username, $password, $dbname);

$answer->error = "no error";
$answer->admin = array();
$answer->viewer = array();

$conn->set_charset("utf8");

if ($conn->connect_error) {
    #die("Connection failed: " . $conn->connect_error);
    $answer->error = "Connection failed: " . $conn->connect_error;
    die(json_encode($answer));
}

$NAME = stripslashes(htmlspecialchars($_POST['name']));
$SHORT_TITLE = translit(stripslashes(htmlspecialchars($_POST['short'])));
$PLACE = generateRandomString();
#1/60^20 ~ 10^-32

if ($NAME == "") {
    $answer->error = "Something not specified";
    die(json_encode($answer));
}

$stmt = $conn->prepare($q);
$stmt->bind_param();
$stmt->execute();
$result = $stmt->get_result();
$NAMEOFADMIN = $SHORT_TITLE . generateRandomString(5) . "admin";
$PASSOFADMIN = generateRandomString(10);
$NAMEOFVIEWER = $SHORT_TITLE . generateRandomString(5) . "viewer";
$PASSOFVIEWER = generateRandomString(10);
$options = [
    'cost' => 11,
];
$HASHOFPASSOFADMIN = password_hash($PASSOFADMIN, PASSWORD_BCRYPT, $options);
$HASHOFPASSOFVIEWER = password_hash($PASSOFVIEWER, PASSWORD_BCRYPT, $options);

$q = "INSERT INTO login (id, name, short_title, password, type, place) VALUES (NULL, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($q);

if ($stmt != false) {
    $answer->admin = array($NAMEOFADMIN, $PASSOFADMIN);
    $answer->viewer = array($NAMEOFVIEWER, $PASSOFVIEWER);
    $stmt->bind_param("ssis", $NAME, $SHORT_TITLE, $HASHOFPASSOFADMIN, 1, $PLACE);
    $stmt->execute();
    $stmt->bind_param("ssis", $NAME, $SHORT_TITLE, $HASHOFPASSOFVIEWER, 2, $PLACE);
    $stmt->execute();

    die(json_encode($answer));
} else {
    $answer->error = "error";
    die(json_encode($answer));
}

$conn->close();
