<?php
require_once('constants.php');
require_once('JWT.php');

function getAuthorizationHeader(){
  $headers = null;
  if (isset($_SERVER['Authorization'])) {
      $headers = trim($_SERVER["Authorization"]);
  }
  else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
      $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
  } elseif (function_exists('apache_request_headers')) {
      $requestHeaders = apache_request_headers();
      // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
      $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
      if (isset($requestHeaders['Authorization'])) {
          $headers = trim($requestHeaders['Authorization']);
      }
  }
  return $headers;
}
/**
* get access token from header
* */
function getBearerToken() {
  $headers = getAuthorizationHeader();
  // HEADER: Get the access token from the header
  if (!empty($headers)) {
      if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
          return $matches[1];
      }
  }
  return "token not found";
}
function check_jwt() {
  $jwt = getBearerToken();
  if ($jwt == "token not found") {
    $answer->error = "token not found";
    return (json_encode($answer));
  }
  #$jwt = stripslashes(htmlspecialchars($_GET['token']));

  #$jwt = $argv[1];

  try {
    $decoded = JWT::decode($jwt, SECRET_KEY, array('HS256'));
    $decoded_array = (array) $decoded;
    if ($decoded_array["kaisi"] != "access") {
        $answer->error = "Wrong token";
        return (json_encode($answer));
    }
    $answer->token = $decoded_array;
    $answer->error = "no error";
    return (json_encode($answer));
  } catch (Exception $e) {
    $answer->error = $e->getMessage();
    die (json_encode($answer));
  }
}
?>
