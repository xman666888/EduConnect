<?php
session_start();

// variable declaration
$username = "";
$email    = "";
$errors = array(); 
$_SESSION['success'] = "";

// connect to database
$db = mysqli_connect('127.0.0.1', 'yang', '123456', 'registration');

if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled
  if (empty($username)) { array_push($errors, "Username is required"); }
  if (strlen($username) < 3 || strlen($username) > 20) { array_push($errors, "The username must be between 3 and 20 characters."); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { array_push($errors, "This is not a valid email."); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if (strlen($password_1) < 6 || strlen($password_1) > 40) { array_push($errors, "The password must be between 6 and 40 characters."); }
  if ($password_1 != $password_2) { array_push($errors, "The two passwords do not match."); }

  // check if the user already exists in the database
  $user_check_query = "SELECT * FROM users WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) {
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // register user if there are no errors in the form
  if (count($errors) == 0) {
    $password = md5($password_1); //encrypt the password before saving in the database
    $query = "INSERT INTO users (username, email, password) VALUES('$username', '$email', '$password')";
    mysqli_query($db, $query);
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "You have already successfully logged in";
    header('location: index.php');
  }
}

// LOGIN USER
if (isset($_POST['login_user'])) {
  // receive input values from the form
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  // form validation: ensure that the form is correctly filled
  if (empty($username)) {
    array_push($errors, "Username is required");
  }
  if (empty($password)) {
    array_push($errors, "Password is required");
  }

  // login user if there are no errors in the form
  if (count($errors) == 0) {
    $password = md5($password); // encrypt the password before comparing with the database
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $results = mysqli_query($db, $query);

    if (mysqli_num_rows($results) == 1) {
      $_SESSION['username'] = $username;
      $_SESSION['success'] = "You are now logged in";
      header('location: index.php');
    } else {
      array_push($errors, "Wrong username/password combination");
    }
  }
}

?>
