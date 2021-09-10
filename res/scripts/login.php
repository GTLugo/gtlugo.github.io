<?php
session_start();

function isLoginInfoValid($email, $password) {
  if ($email == "") {
    $_SESSION["login_attempt"] = "invalid_email";
    return false;
  }
  if ($password == "") {
    $_SESSION["login_attempt"] = "invalid_password";
    return false;
  }
  return true;
}

function isUserInDatabase($email, $password, $hashed_password) {
  $database_hostname = "mysql.cs.odu.edu";
  $database_username = "glugo";
  $database_password = "01102327";
  $database_name = "glugo";

  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
  $mysqli = new mysqli(
      $database_hostname,
      $database_username,
      $database_password,
      $database_name
  );
  if ($mysqli->connect_errno) {
    $_SESSION["login_attempt"] = "database_lost";
    return false;
  }

  $esc_email = $mysqli->real_escape_string($email);
  $query = "SELECT * FROM users WHERE email = " . "'$esc_email'";
  $result = $mysqli->query($query);
  $result_check = $result->num_rows;

  if ($result_check == 0) {
    $_SESSION["login_attempt"] = "unknown_user";
    return false;
  }

  $rows = $result->fetch_row();
  $stored_password = $rows[2];
  //print_r($rows);
  $password_check = password_verify($password, $stored_password);
  if ($password_check == false) {
    $_SESSION["login_attempt"] = "incorrect_data";
    return false;
  }

  $result->free_result();
  $mysqli->close();
  return true;
}

function login($email, $hashed_email, $hashed_password) {
  // removes email suffix
  $email_suffix = strstr($email, "@");
  $name = trim($email, $email_suffix);

  $_SESSION["userid"] = $hashed_email.$hashed_password;
  $_SESSION["username"] = $name;
  $_SESSION["logged_in"] = true;
  $_SESSION["login_attempt"] = "success";
}

function onSubmit() {
  if (isset($_POST['submit'])) {
    $email = filter_input(
        INPUT_POST,
        "email",
        FILTER_VALIDATE_EMAIL
    );
    $password = filter_input(
        INPUT_POST,
        "password",
        FILTER_SANITIZE_STRING
    );
    $hashed_email = password_hash($email, PASSWORD_BCRYPT);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    //print $hashed_password."<br>";
    if (!isLoginInfoValid($email, $password)) {
      return;
    }

    if (!isUserInDatabase($email, $password, $hashed_password)) {
      return;
    }

    login($email, $hashed_email, $hashed_password);
  }
}

$_SESSION["logged_in"] = false;
onSubmit();
print "login_attempt | ".$_SESSION["login_attempt"]."<br>";
if ($_SESSION["logged_in"] == true) {
  print "Welcome, ".$_SESSION["username"]."<br>";
}

